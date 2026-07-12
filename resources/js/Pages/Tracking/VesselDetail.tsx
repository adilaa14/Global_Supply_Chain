import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';
import { MapContainer, TileLayer, Marker, Popup, Polyline } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Custom Vessel Icon (SVG)
const vesselIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="%232c3e50"><path d="M20 21c-1.39 0-2.78-.47-4-1.32-2.44 1.71-5.56 1.71-8 0C6.78 20.53 5.39 21 4 21H2v2h2c1.38 0 2.74-.35 4-.99 2.52 1.29 5.48 1.29 8 0 1.26.65 2.62.99 4 .99h2v-2h-2zM3.95 19H4c1.6 0 3.02-.88 4-2 .98 1.12 2.4 2 4 2s3.02-.88 4-2c.98 1.12 2.4 2 4 2h.05l1.89-6.68c.08-.26.06-.54-.06-.78s-.34-.42-.6-.5L20 11v-1c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v1l-1.28.16c-.26.08-.48.26-.6.5s-.14.52-.06.78L3.95 19zM11 5h2v3h-2V5zM6 10h12v1H6v-1zm-1.34 2.89H19.34l-1.13 4H5.79l-1.13-4z"/></svg>`;

const vesselIcon = new L.Icon({
    iconUrl: vesselIconSvg,
    iconSize: [32, 32],
    iconAnchor: [16, 16],
    popupAnchor: [0, -16]
});

export default function VesselDetail({ vesselId }: { vesselId: string }) {
    const [liveData, setLiveData] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [simPosition, setSimPosition] = useState<[number, number] | null>(null);
    const [autoPan, setAutoPan] = useState(true);

    useEffect(() => {
        const fetchLiveData = async () => {
            try {
                // Append timestamp to prevent aggressive browser caching of the old corrupted history
                const res = await axios.get(`/api/tracking/vessels/${vesselId}/live?t=${new Date().getTime()}`);
                if (res.data.status === 'success') {
                    setLiveData(res.data.data);
                    setSimPosition([res.data.data.latitude, res.data.data.longitude]);
                }
            } catch (error) {
                console.error('Failed to fetch live data', error);
            } finally {
                setLoading(false);
            }
        };

        fetchLiveData();
        const interval = setInterval(fetchLiveData, 60000);
        return () => clearInterval(interval);
    }, [vesselId]);

    // Live smooth movement simulation
    useEffect(() => {
        if (!liveData || !simPosition) return;
        
        // speed in knots -> degree per frame (rough approx for visual effect)
        // Multiplied by 20 so it's extremely obvious to the user that it is moving
        const speedFactor = Math.max((liveData.speed / 100000) * 20, 0.002); 

        const simInterval = setInterval(() => {
            setSimPosition(prev => {
                if (!prev) return prev;
                
                let headingRad = liveData.heading * (Math.PI / 180);
                
                // If we have a planned route, override heading to steer exactly towards the next unreached sea waypoint
                if (liveData.route_geometry && liveData.route_geometry.length > 1) {
                    let closestIndex = 0;
                    let minDist = 999999;
                    for (let i = 0; i < liveData.route_geometry.length; i++) {
                        const wp = liveData.route_geometry[i];
                        const dist = Math.sqrt(Math.pow(wp[0] - prev[0], 2) + Math.pow(wp[1] - prev[1], 2));
                        if (dist < minDist) {
                            minDist = dist;
                            closestIndex = i;
                        }
                    }

                    let nextWP = liveData.route_geometry[liveData.route_geometry.length - 1];
                    for (let i = closestIndex + 1; i < liveData.route_geometry.length; i++) {
                        const wp = liveData.route_geometry[i];
                        const dist = Math.sqrt(Math.pow(wp[0] - prev[0], 2) + Math.pow(wp[1] - prev[1], 2));
                        if (dist > 0.01) {
                            nextWP = wp;
                            break;
                        }
                    }
                    // atan2(x, y) where x is dLng, y is dLat gives navigation bearing (0=North, pi/2=East)
                    headingRad = Math.atan2(nextWP[1] - prev[1], nextWP[0] - prev[0]);
                }

                const dLat = Math.cos(headingRad) * speedFactor;
                const dLng = Math.sin(headingRad) * speedFactor;
                
                return [prev[0] + dLat, prev[1] + dLng];
            });
        }, 1000); // update every second

        return () => clearInterval(simInterval);
    }, [liveData]);

    if (loading) {
        return (
            <AuthenticatedLayout>
                <div className="container-fluid p-0 d-flex justify-content-center align-items-center" style={{ height: '80vh' }}>
                    <div className="text-muted text-center">
                        <span className="material-symbols-outlined mb-2" style={{ fontSize: '48px', animation: 'spin 2s linear infinite' }}>refresh</span>
                        <h5>Establishing AIS Connection...</h5>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    if (!liveData || !simPosition) {
        return (
            <AuthenticatedLayout>
                <div className="container-fluid p-0 text-center py-5">
                    <h4 className="text-muted">Vessel Data Unavailable</h4>
                    <p className="text-muted">Failed to establish connection with AIS Provider.</p>
                    <Link href="/tracking/vessels" className="btn btn-outline-primary mt-3">Back to List</Link>
                </div>
            </AuthenticatedLayout>
        );
    }

    // Determine the planned path for the map by filtering out waypoints we have already passed
        let remainingGeometry: [number, number][] = [];
        let travelledGeometry: [number, number][] = [];

        if (simPosition && liveData && liveData.route_geometry) {
            let closestIdx = 0;
            let minDist = 999999;
            for (let i = 0; i < liveData.route_geometry.length; i++) {
                const wp = liveData.route_geometry[i];
                const dist = Math.sqrt(Math.pow(wp[0] - simPosition[0], 2) + Math.pow(wp[1] - simPosition[1], 2));
                if (dist < minDist) {
                    minDist = dist;
                    closestIdx = i;
                }
            }
            
            // Travelled route snaps to the exact geometry path to prevent straight-line corner cutting across land
            travelledGeometry = liveData.route_geometry.slice(0, closestIdx + 1);
            
            remainingGeometry = [liveData.route_geometry[liveData.route_geometry.length - 1]];
            for (let i = closestIdx + 1; i < liveData.route_geometry.length; i++) {
                const wp = liveData.route_geometry[i];
                const dist = Math.sqrt(Math.pow(wp[0] - simPosition[0], 2) + Math.pow(wp[1] - simPosition[1], 2));
                if (dist > 0.01) {
                    remainingGeometry = liveData.route_geometry.slice(i);
                    break;
                }
            }
        }
        
    const plannedPath = remainingGeometry.length > 0
        ? [simPosition, ...remainingGeometry] 
        : (liveData.destination_coords ? [simPosition, liveData.destination_coords] : null);

    return (
        <AuthenticatedLayout>
            <Head title="Vessel Detail" />

            <div className="container-fluid p-0">
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up">
                    <div>
                        <Link href="/tracking/vessels" className="text-muted text-decoration-none small mb-2 d-inline-block">
                            &larr; Back to Tracking
                        </Link>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>Live Telemetry</h2>
                        <div className="d-flex align-items-center gap-3">
                            <span className="badge bg-success-subtle text-success border border-success-subtle">
                                ● AIS Connected
                            </span>
                            <span className="text-muted small">Provider: {liveData.ais_provider}</span>
                            <span className="text-muted small">Updated: {new Date(liveData.timestamp).toLocaleTimeString()}</span>
                            {liveData.destination_name && (
                                <span className="text-primary fw-bold small">Dest: {liveData.destination_name}</span>
                            )}
                        </div>
                    </div>
                </div>

                <div className="row g-4">
                    <div className="col-xl-4 fade-up" style={{ animationDelay: '0.2s' }}>
                        <div className="panel-card mb-4">
                            <h5 className="panel-title mb-4">Vessel Instruments</h5>
                            
                            <div className="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded-3">
                                <div>
                                    <h6 className="text-muted small fw-bold mb-1">SPEED</h6>
                                    <h4 className="fw-bold text-secondary mb-0">{liveData.speed} <span className="fs-6 fw-normal text-muted">knots</span></h4>
                                </div>
                                <span className="material-symbols-outlined text-primary" style={{ fontSize: '32px' }}>speed</span>
                            </div>

                            <div className="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded-3">
                                <div>
                                    <h6 className="text-muted small fw-bold mb-1">HEADING</h6>
                                    <h4 className="fw-bold text-secondary mb-0">{liveData.heading}°</h4>
                                </div>
                                <span className="material-symbols-outlined text-info" style={{ fontSize: '32px', transform: `rotate(${liveData.heading}deg)` }}>navigation</span>
                            </div>
                            
                            <div className="d-flex align-items-center justify-content-between mb-3 p-3 bg-light rounded-3">
                                <div>
                                    <h6 className="text-muted small fw-bold mb-1">STATUS</h6>
                                    <h5 className="fw-bold text-secondary mb-0">{liveData.nav_status}</h5>
                                </div>
                                <span className="material-symbols-outlined text-success" style={{ fontSize: '32px' }}>settings_ethernet</span>
                            </div>
                            
                            <div className="mt-4">
                                <h6 className="text-muted small fw-bold mb-2">LIVE COORDINATES</h6>
                                <div className="row g-2 text-center">
                                    <div className="col-6">
                                        <div className="p-2 border rounded-3 text-muted small">
                                            LAT: {simPosition[0].toFixed(5)}
                                        </div>
                                    </div>
                                    <div className="col-6">
                                        <div className="p-2 border rounded-3 text-muted small">
                                            LNG: {simPosition[1].toFixed(5)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-xl-8 fade-up" style={{ animationDelay: '0.3s' }}>
                        <div className="panel-card p-0 h-100 overflow-hidden d-flex flex-column" style={{ minHeight: '500px' }}>
                            <div className="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                                <h5 className="panel-title mb-0">Live Position</h5>
                                <div className="d-flex align-items-center">
                                    {!autoPan && (
                                        <button onClick={() => setAutoPan(true)} className="btn btn-sm btn-primary rounded-pill d-flex align-items-center gap-1 me-2 shadow-sm">
                                            <span className="material-symbols-outlined" style={{ fontSize: '16px' }}>my_location</span>
                                            Follow Vessel
                                        </button>
                                    )}
                                    <button className="btn btn-sm btn-outline-secondary rounded-pill d-flex align-items-center gap-1">
                                        <span className="material-symbols-outlined" style={{ fontSize: '16px' }}>fullscreen</span>
                                    </button>
                                </div>
                            </div>
                            <div className="flex-grow-1">
                                <MapContainer center={simPosition} zoom={7} style={{ height: '100%', width: '100%', zIndex: 1 }}>
                                    <TileLayer
                                        url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
                                    />
                                    
                                    {/* Planned Route (Dashed) */}
                                    {plannedPath && (
                                        <Polyline 
                                            positions={plannedPath} 
                                            pathOptions={{ color: '#0d6efd', weight: 3, dashArray: '8, 8', opacity: 0.6 }} 
                                        />
                                    )}

                                    {/* Destination Marker */}
                                    {liveData.destination_coords && (
                                        <Marker position={liveData.destination_coords}>
                                            <Popup>Destination: {liveData.destination_name}</Popup>
                                        </Marker>
                                    )}

                                    {/* Travelled Route (Solid) - Snapped to Geometry */}
                                    {travelledGeometry.length > 0 && simPosition && (
                                        <Polyline 
                                            positions={[...travelledGeometry, simPosition]} 
                                            pathOptions={{ color: '#F03164', weight: 4, opacity: 0.8 }} 
                                        />
                                    )}

                                    {/* Live Moving Vessel Marker with Rotation */}
                                    <Marker 
                                        position={simPosition}
                                        icon={new L.DivIcon({
                                            html: `<div style="transform: rotate(${liveData.heading}deg); transition: transform 1s linear; width: 32px; height: 32px; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.3));">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="#2c3e50">
                                                        <path d="M20 21c-1.39 0-2.78-.47-4-1.32-2.44 1.71-5.56 1.71-8 0C6.78 20.53 5.39 21 4 21H2v2h2c1.38 0 2.74-.35 4-.99 2.52 1.29 5.48 1.29 8 0 1.26.65 2.62.99 4 .99h2v-2h-2zM3.95 19H4c1.6 0 3.02-.88 4-2 .98 1.12 2.4 2 4 2s3.02-.88 4-2c.98 1.12 2.4 2 4 2h.05l1.89-6.68c.08-.26.06-.54-.06-.78s-.34-.42-.6-.5L20 11v-1c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v1l-1.28.16c-.26.08-.48.26-.6.5s-.14.52-.06.78L3.95 19zM11 5h2v3h-2V5zM6 10h12v1H6v-1zm-1.34 2.89H19.34l-1.13 4H5.79l-1.13-4z"/>
                                                    </svg>
                                                    <div style="position: absolute; top: -5px; right: -5px; width: 10px; height: 10px; background-color: #10b981; border-radius: 50%; border: 2px solid white; animation: pulse 1.5s infinite;"></div>
                                                   </div>`,
                                            className: 'custom-vessel-icon',
                                            iconSize: [32, 32],
                                            iconAnchor: [16, 16],
                                            popupAnchor: [0, -16]
                                        })}
                                    >
                                        <Popup>
                                            <div className="text-center">
                                                <strong>Live Position</strong><br/>
                                                Speed: {liveData.speed} kn<br/>
                                                Heading: {liveData.heading}°
                                            </div>
                                        </Popup>
                                    </Marker>

                                    {/* Auto-Centering Component */}
                                    <MapUpdater center={simPosition} autoPan={autoPan} setAutoPan={setAutoPan} />
                                </MapContainer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

// Child component to handle map auto-panning
import { useMap } from 'react-leaflet';
function MapUpdater({ center, autoPan, setAutoPan }: { center: [number, number], autoPan: boolean, setAutoPan: (v: boolean) => void }) {
    const map = useMap();
    useEffect(() => {
        if (map && center && autoPan) {
            map.panTo(center, { animate: true, duration: 1 });
        }
    }, [center, map, autoPan]);

    useEffect(() => {
        const disableAutoPan = () => setAutoPan(false);
        map.on('dragstart', disableAutoPan);
        map.on('zoomstart', disableAutoPan);
        return () => {
            map.off('dragstart', disableAutoPan);
            map.off('zoomstart', disableAutoPan);
        };
    }, [map, setAutoPan]);
    return null;
}

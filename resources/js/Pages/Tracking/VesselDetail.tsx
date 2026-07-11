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

    useEffect(() => {
        const fetchLiveData = async () => {
            try {
                const res = await axios.get(`/api/tracking/vessels/${vesselId}/live`);
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
        const speedFactor = (liveData.speed / 100000) * 2; 
        const headingRad = liveData.heading * (Math.PI / 180);

        const dLat = Math.cos(headingRad) * speedFactor;
        const dLng = Math.sin(headingRad) * speedFactor;

        const simInterval = setInterval(() => {
            setSimPosition(prev => prev ? [prev[0] + dLat, prev[1] + dLng] : prev);
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
                                <button className="btn btn-sm btn-outline-secondary rounded-pill d-flex align-items-center gap-1">
                                    <span className="material-symbols-outlined" style={{ fontSize: '16px' }}>fullscreen</span>
                                </button>
                            </div>
                            <div className="flex-grow-1">
                                <MapContainer center={simPosition} zoom={7} style={{ height: '100%', width: '100%', zIndex: 1 }}>
                                    <TileLayer
                                        url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
                                    />
                                    
                                    {/* Planned Route (Dashed) */}
                                    {liveData.destination_coords && (
                                        <Polyline 
                                            positions={[simPosition, liveData.destination_coords]} 
                                            pathOptions={{ color: '#0d6efd', weight: 3, dashArray: '8, 8', opacity: 0.6 }} 
                                        />
                                    )}

                                    {/* Destination Marker */}
                                    {liveData.destination_coords && (
                                        <Marker position={liveData.destination_coords}>
                                            <Popup>Destination: {liveData.destination_name}</Popup>
                                        </Marker>
                                    )}

                                    {/* Travelled Route (Solid) */}
                                    {liveData.history && liveData.history.length > 1 && (
                                        <Polyline 
                                            positions={[...liveData.history, simPosition]} 
                                            pathOptions={{ color: '#F03164', weight: 4, opacity: 0.8 }} 
                                        />
                                    )}

                                    {/* Live Moving Vessel Marker */}
                                    <Marker 
                                        position={simPosition}
                                        icon={vesselIcon}
                                    >
                                        <Popup>
                                            <div className="text-center">
                                                <strong>Live Position</strong><br/>
                                                Speed: {liveData.speed} kn<br/>
                                                Heading: {liveData.heading}°
                                            </div>
                                        </Popup>
                                    </Marker>
                                </MapContainer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

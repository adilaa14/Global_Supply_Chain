import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState, useRef } from 'react';
import axios from 'axios';
import { MapContainer, TileLayer, Marker, Popup, Polyline } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix for default marker icons in React-Leaflet
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

// Custom Vessel Icon (SVG)
const vesselIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="%232c3e50"><path d="M20 21c-1.39 0-2.78-.47-4-1.32-2.44 1.71-5.56 1.71-8 0C6.78 20.53 5.39 21 4 21H2v2h2c1.38 0 2.74-.35 4-.99 2.52 1.29 5.48 1.29 8 0 1.26.65 2.62.99 4 .99h2v-2h-2zM3.95 19H4c1.6 0 3.02-.88 4-2 .98 1.12 2.4 2 4 2s3.02-.88 4-2c.98 1.12 2.4 2 4 2h.05l1.89-6.68c.08-.26.06-.54-.06-.78s-.34-.42-.6-.5L20 11v-1c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v1l-1.28.16c-.26.08-.48.26-.6.5s-.14.52-.06.78L3.95 19zM11 5h2v3h-2V5zM6 10h12v1H6v-1zm-1.34 2.89H19.34l-1.13 4H5.79l-1.13-4z"/></svg>`;

const vesselIcon = new L.Icon({
    iconUrl: vesselIconSvg,
    iconSize: [32, 32],
    iconAnchor: [16, 16],
    popupAnchor: [0, -16]
});

export default function GlobalMap() {
    const [mapData, setMapData] = useState<any>({ vessels: [] });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchMapData = async () => {
            try {
                const res = await axios.get('/api/tracking/map-data');
                if (res.data.status === 'success') {
                    setMapData(res.data.data);
                }
            } catch (error) {
                console.error('Failed to fetch map data', error);
            } finally {
                setLoading(false);
            }
        };

        fetchMapData();

        // Optional: Reverb listening for live updates
        // window.Echo.channel('tracking').listen('VesselMoved', (e) => { ... });
    }, []);

    return (
        <AuthenticatedLayout>
            <Head title="Global Vessel Map" />

            <div className="container-fluid p-0 d-flex flex-column" style={{ height: 'calc(100vh - 100px)' }}>
                {/* Header */}
                <div className="d-flex justify-content-between align-items-center mb-3 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>Global Vessel Map</h2>
                        <p className="text-muted mb-0">Live tracking of active fleet operations.</p>
                    </div>
                    <div className="d-flex gap-2">
                        <Link href="/tracking/vessels" className="btn btn-outline-secondary rounded-pill">
                            <span className="material-symbols-outlined align-middle me-1">format_list_bulleted</span> List View
                        </Link>
                    </div>
                </div>

                {/* Map Panel */}
                <div className="panel-card flex-grow-1 p-0 overflow-hidden fade-up d-flex flex-column" style={{ animationDelay: '0.2s', position: 'relative' }}>
                    {loading && (
                        <div className="position-absolute w-100 h-100 d-flex justify-content-center align-items-center" style={{ zIndex: 1000, background: 'rgba(255,255,255,0.7)' }}>
                            <div className="text-muted text-center">
                                <span className="material-symbols-outlined mb-2" style={{ fontSize: '48px', animation: 'spin 2s linear infinite' }}>refresh</span>
                                <h5>Loading Map Engine...</h5>
                            </div>
                        </div>
                    )}
                    
                    <MapContainer center={[20, 0]} zoom={3} style={{ flexGrow: 1, width: '100%', zIndex: 1, borderRadius: '20px' }}>
                        <TileLayer
                            url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
                            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        />
                        
                        {mapData.vessels && mapData.vessels.map((vessel: any) => (
                            <Marker 
                                key={vessel.id} 
                                position={[vessel.position.lat, vessel.position.lng]}
                                icon={vesselIcon}
                            >
                                <Popup>
                                    <div style={{ minWidth: '200px' }}>
                                        <h6 className="fw-bold mb-1">{vessel.name}</h6>
                                        <span className="badge bg-primary mb-2">{vessel.type}</span>
                                        <div className="small mb-1"><strong>Status:</strong> {vessel.status}</div>
                                        <div className="small mb-1"><strong>Speed:</strong> {vessel.position.speed} knots</div>
                                        <div className="small mb-1"><strong>Heading:</strong> {vessel.position.heading}°</div>
                                        <div className="small mb-2"><strong>Dest:</strong> {vessel.destination}</div>
                                        <Link href={`/tracking/vessels/${vessel.id}`} className="btn btn-sm btn-outline-primary w-100">
                                            View Details
                                        </Link>
                                    </div>
                                </Popup>
                            </Marker>
                        ))}
                    </MapContainer>
                    
                    {/* Floating Overlay for Map Stats */}
                    <div className="position-absolute bottom-0 start-0 m-4 p-3 bg-white shadow" style={{ zIndex: 1000, width: '250px', borderRadius: '15px' }}>
                        <h6 className="fw-bold text-secondary mb-2">Map Summary</h6>
                        <div className="d-flex justify-content-between mb-1 small">
                            <span className="text-muted">Active Vessels</span>
                            <span className="fw-bold">{mapData.vessels?.length || 0}</span>
                        </div>
                        <div className="d-flex justify-content-between mb-1 small">
                            <span className="text-muted">Weather Overlay</span>
                            <span className="badge bg-light text-muted border">Disabled</span>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState, useEffect, useMemo } from 'react';
import { MapContainer, TileLayer, Marker, Popup, useMap } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import axios from 'axios';
import L from 'leaflet';

// Fix for default marker icons in Leaflet
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

// Function to create a custom colored port icon
const createPortIcon = (congestionLevel: string) => {
    let color = '#10B981'; // Green (Low)
    if (congestionLevel === 'Medium') color = '#F59E0B'; // Yellow
    if (congestionLevel === 'High') color = '#EF4444'; // Red
    if (congestionLevel === 'Critical') color = '#991B1B'; // Dark Red

    const svgIcon = `
        <div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">
            <span class="material-symbols-outlined" style="color: white; font-size: 14px;">anchor</span>
        </div>
    `;

    return L.divIcon({
        html: svgIcon,
        className: 'custom-port-icon',
        iconSize: [24, 24],
        iconAnchor: [12, 12],
        popupAnchor: [0, -12]
    });
};

function MapUpdater({ center, zoom }: { center: [number, number], zoom: number }) {
    const map = useMap();
    useEffect(() => {
        map.flyTo(center, zoom, { duration: 1.5 });
    }, [center, zoom, map]);
    return null;
}

export default function PortsMap() {
    const [ports, setPorts] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [searchCountry, setSearchCountry] = useState('');
    const [searchPort, setSearchPort] = useState('');
    const [mapCenter, setMapCenter] = useState<[number, number]>([20, 0]);
    const [mapZoom, setMapZoom] = useState(3);
    const [activePort, setActivePort] = useState<any>(null);

    useEffect(() => {
        const fetchPorts = async () => {
            try {
                // Fetch from our local proxy route to avoid CORS and SSL blockages
                const res = await axios.get('/tracking/api/world-ports');
                const portArray = Array.isArray(res.data) ? res.data : [];
                
                // Allow valid points with latitude and longitude (0 is a valid coordinate, null is not)
                const validPorts = portArray.filter((p: any) => p.latitude !== null && p.longitude !== null && p.point_of_interest).map((p: any) => {
                    // Simulate congestion based on port size for dashboard demonstration
                    const rand = Math.random();
                    let congestion = 'Low';
                    let congestionPercent = Math.floor(Math.random() * 30) + 10; // 10-40%

                    if (p.port_size === 'Large' || p.port_size === 'Very Large') {
                        if (rand > 0.4) {
                            congestion = 'High';
                            congestionPercent = Math.floor(Math.random() * 20) + 75; // 75-95%
                        } else {
                            congestion = 'Medium';
                            congestionPercent = Math.floor(Math.random() * 30) + 40; // 40-70%
                        }
                    } else if (p.port_size === 'Medium') {
                        if (rand > 0.7) {
                            congestion = 'High';
                            congestionPercent = Math.floor(Math.random() * 20) + 75;
                        } else if (rand > 0.3) {
                            congestion = 'Medium';
                            congestionPercent = Math.floor(Math.random() * 30) + 40;
                        }
                    } else {
                        // Small ports rarely get critical, mostly low to medium
                        if (rand > 0.9) {
                            congestion = 'Critical';
                            congestionPercent = Math.floor(Math.random() * 5) + 95; // 95-100%
                        } else if (rand > 0.6) {
                            congestion = 'Medium';
                            congestionPercent = Math.floor(Math.random() * 30) + 40;
                        }
                    }

                    return { ...p, congestion, congestionPercent };
                });
                
                setPorts(validPorts);
            } catch (error) {
                console.error("Error fetching ports", error);
            } finally {
                setLoading(false);
            }
        };
        fetchPorts();
    }, []);

    // Extract unique countries
    const countries = useMemo(() => {
        const unique = new Set(ports.map(p => p.country).filter(Boolean));
        return Array.from(unique).sort();
    }, [ports]);

    // Filter ports based on search
    const filteredPorts = useMemo(() => {
        return ports.filter(p => {
            const matchCountry = searchCountry ? p.country === searchCountry : true;
            const matchPort = searchPort ? p.point_of_interest?.toLowerCase().includes(searchPort.toLowerCase()) : true;
            return matchCountry && matchPort;
        }).slice(0, 500); // Limit to 500 to prevent map lag
    }, [ports, searchCountry, searchPort]);

    const handleCountryChange = (e: any) => {
        const country = e.target.value;
        setSearchCountry(country);
        if (country) {
            const countryPorts = ports.filter(p => p.country === country);
            if (countryPorts.length > 0) {
                setMapCenter([countryPorts[0].latitude, countryPorts[0].longitude]);
                setMapZoom(5);
            }
        } else {
            setMapCenter([20, 0]);
            setMapZoom(3);
        }
    };

    const handlePortClick = (port: any) => {
        setActivePort(port);
        setMapCenter([port.latitude, port.longitude]);
        setMapZoom(10);
    };

    const [showFilters, setShowFilters] = useState(true);

    return (
        <AuthenticatedLayout>
            <Head title="World Port Location Dashboard" />

            <div className="container-fluid p-0 position-relative" style={{ height: 'calc(100vh - 60px)' }}>
                {/* Floating Glassmorphism Controls */}
                <div 
                    className="position-absolute z-3" 
                    style={{ 
                        top: '20px', 
                        right: '20px', 
                        width: showFilters ? '320px' : 'auto',
                        transition: 'all 0.3s ease'
                    }}
                >
                    {showFilters ? (
                        <div 
                            className="bg-white bg-opacity-75 backdrop-blur shadow-lg rounded-4 p-4 border border-white"
                            style={{ backdropFilter: 'blur(16px)' }}
                        >
                            <div className="d-flex justify-content-between align-items-start mb-3">
                                <div className="d-flex align-items-center">
                                    <div className="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary d-flex">
                                        <span className="material-symbols-outlined fs-4">anchor</span>
                                    </div>
                                    <div>
                                        <h6 className="fw-bold mb-0 text-dark">Global Ports</h6>
                                        <small className="text-muted" style={{ fontSize: '11px' }}>Open Port Index</small>
                                    </div>
                                </div>
                                <button 
                                    className="btn btn-sm btn-light bg-transparent border-0 text-muted p-1 rounded-circle"
                                    onClick={() => setShowFilters(false)}
                                    title="Hide Controls"
                                >
                                    <span className="material-symbols-outlined fs-5">close</span>
                                </button>
                            </div>

                            <div className="d-flex align-items-center border-bottom pb-3 mb-4">
                                <div className="ms-auto text-end w-100">
                                    <h4 className="fw-bold text-primary mb-0 lh-1">{loading ? '...' : filteredPorts.length}</h4>
                                    <span className="text-muted fw-medium" style={{ fontSize: '10px' }}>SHOWN PORTS</span>
                                </div>
                            </div>
                            
                            <div className="mb-3">
                                <label className="form-label small fw-semibold text-muted mb-1">Filter by Country</label>
                                <div className="input-group input-group-sm">
                                    <span className="input-group-text bg-white bg-opacity-50 border-end-0">
                                        <span className="material-symbols-outlined fs-6 text-muted">public</span>
                                    </span>
                                    <select 
                                        className="form-select bg-white bg-opacity-50 border-start-0 shadow-none text-dark" 
                                        value={searchCountry} 
                                        onChange={handleCountryChange}
                                        style={{ cursor: 'pointer' }}
                                    >
                                        <option value="">All Countries (Global)</option>
                                        {countries.map((c: any, i) => (
                                            <option key={i} value={c}>{c}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="form-label small fw-semibold text-muted mb-1">Search Port Name</label>
                                <div className="input-group input-group-sm">
                                    <span className="input-group-text bg-white bg-opacity-50 border-end-0">
                                        <span className="material-symbols-outlined fs-6 text-muted">search</span>
                                    </span>
                                    <input 
                                        type="text" 
                                        className="form-control bg-white bg-opacity-50 border-start-0 shadow-none" 
                                        placeholder="Enter port name..." 
                                        value={searchPort}
                                        onChange={(e) => setSearchPort(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    ) : (
                        <button 
                            className="btn btn-white shadow-lg rounded-circle p-3 d-flex align-items-center justify-content-center bg-white bg-opacity-75 backdrop-blur border border-white"
                            style={{ backdropFilter: 'blur(16px)' }}
                            onClick={() => setShowFilters(true)}
                            title="Show Controls"
                        >
                            <span className="material-symbols-outlined text-primary">search</span>
                        </button>
                    )}
                </div>

                {/* Map Area */}
                <div className="h-100 w-100 position-relative">
                    {loading && (
                        <div className="position-absolute top-50 start-50 translate-middle z-3 bg-white bg-opacity-75 backdrop-blur p-4 rounded-4 shadow-lg text-center border border-white" style={{ backdropFilter: 'blur(10px)' }}>
                            <div className="spinner-border text-primary mb-3" role="status" style={{ width: '3rem', height: '3rem' }}></div>
                            <h5 className="fw-bold mb-1 text-dark">Initializing Radar...</h5>
                            <small className="text-muted">Connecting to World Port Index</small>
                        </div>
                    )}

                    <MapContainer 
                        center={mapCenter} 
                        zoom={mapZoom} 
                        style={{ height: '100%', width: '100%', zIndex: 0 }}
                        zoomControl={false}
                    >
                        <TileLayer
                            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                            url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
                        />
                        <MapUpdater center={mapCenter} zoom={mapZoom} />

                        {filteredPorts.map((port, idx) => (
                            <Marker 
                                key={idx} 
                                position={[port.latitude, port.longitude]}
                                icon={createPortIcon(port.congestion)}
                                eventHandlers={{
                                    click: () => handlePortClick(port)
                                }}
                            >
                                <Popup>
                                    <div className="p-1" style={{ minWidth: '200px' }}>
                                        <h6 className="fw-bold mb-1 border-bottom pb-1">{port.point_of_interest}</h6>
                                        <div className="text-muted small mb-3">{port.state ? `${port.state}, ` : ''}{port.country}</div>
                                        
                                        <div className="d-flex flex-column gap-2 small mb-3">
                                            <div className="d-flex justify-content-between align-items-center">
                                                <span className="text-muted">Status:</span>
                                                <span className={`badge ${
                                                    port.congestion === 'Low' ? 'bg-success' : 
                                                    port.congestion === 'Medium' ? 'bg-warning text-dark' : 
                                                    port.congestion === 'High' ? 'bg-danger' : 'bg-dark'
                                                }`}>
                                                    {port.congestion} Traffic
                                                </span>
                                            </div>
                                            <div className="d-flex justify-content-between align-items-center">
                                                <span className="text-muted">Congestion:</span>
                                                <div className="w-50">
                                                    <div className="progress" style={{ height: '6px' }}>
                                                        <div className={`progress-bar ${
                                                            port.congestion === 'Low' ? 'bg-success' : 
                                                            port.congestion === 'Medium' ? 'bg-warning' : 
                                                            port.congestion === 'High' ? 'bg-danger' : 'bg-dark'
                                                        }`} style={{ width: `${port.congestionPercent}%` }}></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="d-flex flex-column gap-1 small bg-light p-2 rounded border">
                                            <div className="d-flex justify-content-between">
                                                <span className="text-muted">Latitude:</span>
                                                <span className="fw-medium text-dark">{port.latitude.toFixed(4)}</span>
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <span className="text-muted">Longitude:</span>
                                                <span className="fw-medium text-dark">{port.longitude.toFixed(4)}</span>
                                            </div>
                                            {port.port_size && (
                                                <div className="d-flex justify-content-between mt-1 pt-1 border-top">
                                                    <span className="text-muted">Size:</span>
                                                    <span className="fw-bold text-dark">{port.port_size}</span>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </Popup>
                            </Marker>
                        ))}
                    </MapContainer>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

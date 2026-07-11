import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from 'axios';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix for default marker icons in React-Leaflet
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

export default function Dashboard() {
    const [summary, setSummary] = useState<any>({
        metrics: {
            total_shipments: 0,
            active_shipments: 0,
            delayed_shipments: 0,
            revenue_mtd: 0
        }
    });
    
    const [alerts, setAlerts] = useState<any[]>([]);
    const [shipments, setShipments] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchDashboardData = async () => {
            try {
                // Fetch summary
                const summaryRes = await axios.get('/api/dashboard/summary');
                if (summaryRes.data.status === 'success' && summaryRes.data.data) {
                    // Merge with defaults to prevent undefined errors
                    setSummary((prev: any) => ({
                        ...prev,
                        ...summaryRes.data.data
                    }));
                }

                // Fetch alerts
                const alertsRes = await axios.get('/api/dashboard/alerts?limit=5');
                if (alertsRes.data.status === 'success') {
                    setAlerts(alertsRes.data.data);
                }
                
                // Fetch shipments for map
                const shipmentsRes = await axios.get('/api/shipments');
                if (shipmentsRes.data.status === 'success') {
                    setShipments(shipmentsRes.data.data.data || []);
                }
            } catch (error) {
                console.error("Error fetching dashboard data:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchDashboardData();

        // Optional: Reverb listening could be hooked up here
        // window.Echo.private('dashboard.company.' + user.company_id)
        //     .listen('ShipmentCreated', (e) => { fetchDashboardData(); });

    }, []);

    // Helper to format currency
    const formatCurrency = (value: number) => {
        if (!value) return '$0.0M';
        if (value >= 1000000) return '$' + (value / 1000000).toFixed(1) + 'M';
        if (value >= 1000) return '$' + (value / 1000).toFixed(1) + 'K';
        return '$' + value;
    };

    return (
        <AuthenticatedLayout>
            <Head title="Global Command Center" />

            <div className="container-fluid p-0">
                {/* Header */}
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up" style={{ animationDelay: '0.3s' }}>
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>Global Command Center</h2>
                        <p className="text-muted mb-0">Real-time intelligence on global supply chain operations.</p>
                    </div>
                    <div>
                        <Link href="/shipments/create" className="btn-primary-custom d-flex align-items-center gap-2" style={{ textDecoration: 'none' }}>
                            <span className="material-symbols-outlined" style={{ fontSize: '20px' }}>add</span>
                            Create Shipment
                        </Link>
                    </div>
                </div>

                {/* Stat Cards Row 1 */}
                <div className="row g-4 mb-4 fade-up" style={{ animationDelay: '0.4s' }}>
                    <div className="col-xl-3 col-lg-6">
                        <div className="stat-card">
                            <div className="stat-info">
                                <h6>TOTAL<br />SHIPMENTS</h6>
                                <h3>{loading ? '...' : (summary.metrics?.total_shipments || 0).toLocaleString()}</h3>
                                <div className="text-success-custom">
                                    <span className="material-symbols-outlined" style={{ fontSize: '16px', verticalAlign: 'text-bottom', fontWeight: 400 }}>trending_up</span> Live Data
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-6">
                        <div className="stat-card">
                            <div className="stat-info">
                                <h6>CARGO IN<br />TRANSIT</h6>
                                <h3>{loading ? '...' : (summary.metrics?.active_shipments || 0).toLocaleString()}</h3>
                                <div className="text-success-custom">
                                    <span className="material-symbols-outlined" style={{ fontSize: '16px', verticalAlign: 'text-bottom', fontWeight: 400 }}>trending_up</span> Live Data
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-6">
                        <div className="stat-card">
                            <div className="stat-info">
                                <h6>DELAYED<br />SHIPMENTS</h6>
                                <h3>{loading ? '...' : (summary.metrics?.delayed_shipments || 0).toLocaleString()}</h3>
                                <div className="text-danger-custom">
                                    <span className="material-symbols-outlined" style={{ fontSize: '16px', verticalAlign: 'text-bottom', fontWeight: 400 }}>trending_down</span> Live Data
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-6">
                        <div className="stat-card">
                            <div className="stat-info">
                                <h6>REVENUE (MTD)<br />&nbsp;</h6>
                                <h3>{loading ? '...' : formatCurrency(summary.metrics?.revenue_mtd || 0)}</h3>
                                <div className="text-success-custom">
                                    <span className="material-symbols-outlined" style={{ fontSize: '16px', verticalAlign: 'text-bottom', fontWeight: 400 }}>trending_up</span> Live Data
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Main Dashboard Row */}
                <div className="row g-4 mb-4 fade-up" style={{ animationDelay: '0.5s' }}>
                    {/* Global Map */}
                    <div className="col-xl-8 col-lg-12">
                        <div className="panel-card d-flex flex-column h-100">
                            <div className="panel-header mb-3">
                                <h5 className="panel-title">Global Fleet Live Tracking</h5>
                                <div className="d-flex gap-2">
                                    <span className="badge bg-light text-dark border"><span className="text-success">●</span> {summary.metrics?.active_shipments || 0} Active</span>
                                    <span className="badge bg-light text-dark border"><span className="text-danger">●</span> {summary.metrics?.delayed_shipments || 0} Delayed</span>
                                </div>
                            </div>
                            <div className="flex-grow-1" style={{ minHeight: '400px', borderRadius: '15px', overflow: 'hidden' }}>
                                <MapContainer center={[20, 0]} zoom={2} style={{ height: '100%', width: '100%', zIndex: 1 }}>
                                    <TileLayer
                                        url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png"
                                        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                                    />
                                    {shipments.map((shipment: any) => {
                                        // Randomize slightly for demo since we don't have real coordinates yet
                                        const lat = (Math.random() * 80) - 40;
                                        const lng = (Math.random() * 180) - 90;
                                        return (
                                            <Marker key={shipment.id} position={[lat, lng]}>
                                                <Popup>
                                                    <strong>{shipment.shipment_number}</strong><br/>
                                                    Status: {shipment.status}<br/>
                                                    Type: {shipment.shipment_type}
                                                </Popup>
                                            </Marker>
                                        );
                                    })}
                                </MapContainer>
                            </div>
                        </div>
                    </div>

                    {/* Risk Alerts */}
                    <div className="col-xl-4 col-lg-12">
                        <div className="panel-card h-100">
                            <div className="panel-header mb-3">
                                <h5 className="panel-title">Active Risk Alerts</h5>
                                <a href="#" style={{ color: 'var(--primary)', fontSize: '0.85rem', fontWeight: 600, textDecoration: 'none' }}>View All</a>
                            </div>
                            <div className="alert-list" style={{ maxHeight: '420px', overflowY: 'auto', overflowX: 'hidden', paddingRight: '10px' }}>
                                {loading ? (
                                    <div className="text-center text-muted mt-4">Loading alerts...</div>
                                ) : alerts.length > 0 ? (
                                    alerts.map((alert: any) => (
                                        <div key={alert.id} className="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom alert-item-hover">
                                            <div className={`stat-icon ${alert.severity === 'Critical' || alert.severity === 'High' ? 'danger' : 'warning'}`} style={{ width: '48px', height: '48px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                                                <span className="material-symbols-outlined" style={{ fontSize: '24px' }}>
                                                    {alert.category === 'Weather Alert' ? 'storm' : 'warning'}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 className="mb-1 fw-bold" style={{ fontSize: '0.95rem', color: 'var(--secondary)' }}>{alert.title}</h6>
                                                <p className="text-muted mb-1" style={{ fontSize: '0.85rem' }}>{alert.message}</p>
                                                <span className={alert.severity === 'Critical' || alert.severity === 'High' ? 'text-danger-custom' : 'text-warning'} style={{ fontSize: '0.75rem', fontWeight: 600 }}>
                                                    {alert.severity} Impact
                                                </span>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center mt-4">
                                        <span className="material-symbols-outlined text-success" style={{ fontSize: '48px' }}>check_circle</span>
                                        <p className="text-muted mt-2">No active risk alerts. All clear.</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

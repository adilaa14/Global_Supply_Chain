import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function ShipmentShow({ shipmentId }: { shipmentId: string }) {
    const [shipment, setShipment] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchShipment = async () => {
            try {
                const res = await axios.get(`/api/shipments/${shipmentId}`);
                if (res.data.status === 'success') {
                    setShipment(res.data.data);
                }
            } catch (error) {
                console.error('Failed to fetch shipment', error);
            } finally {
                setLoading(false);
            }
        };
        fetchShipment();
    }, [shipmentId]);

    if (loading) {
        return (
            <AuthenticatedLayout>
                <div className="container-fluid p-0 d-flex justify-content-center align-items-center" style={{ height: '80vh' }}>
                    <div className="text-muted text-center">
                        <span className="material-symbols-outlined mb-2" style={{ fontSize: '48px', animation: 'spin 2s linear infinite' }}>refresh</span>
                        <h5>Loading Shipment Details...</h5>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    if (!shipment) {
        return (
            <AuthenticatedLayout>
                <div className="container-fluid p-0 text-center py-5">
                    <h4 className="text-muted">Shipment Not Found</h4>
                    <Link href="/shipments" className="btn btn-outline-primary mt-3">Back to List</Link>
                </div>
            </AuthenticatedLayout>
        );
    }

    return (
        <AuthenticatedLayout>
            <Head title={`Shipment ${shipment.shipment_number}`} />

            <div className="container-fluid p-0">
                {/* Header */}
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up">
                    <div>
                        <Link href="/shipments" className="text-muted text-decoration-none small mb-2 d-inline-block">
                            &larr; Back to Shipments
                        </Link>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>{shipment.shipment_number}</h2>
                        <div className="d-flex align-items-center gap-3">
                            <span className={`badge ${shipment.shipment_type === 'Export' ? 'bg-primary' : 'bg-info'}`}>
                                {shipment.shipment_type}
                            </span>
                            <span className={`badge bg-light text-dark border ${shipment.current_status === 'Draft' ? 'text-secondary' : 'text-success'}`}>
                                ● {shipment.current_status}
                            </span>
                            <span className="text-muted small">Created on {new Date(shipment.created_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                    <div className="d-flex gap-2">
                        <button className="btn btn-outline-secondary rounded-pill">
                            <span className="material-symbols-outlined align-middle me-1">edit</span> Edit
                        </button>
                        <button className="btn-primary-custom d-flex align-items-center gap-2">
                            <span className="material-symbols-outlined" style={{ fontSize: '20px' }}>local_shipping</span>
                            Update Status
                        </button>
                    </div>
                </div>

                <div className="row g-4">
                    {/* Left Column: Details */}
                    <div className="col-xl-8 fade-up" style={{ animationDelay: '0.2s' }}>
                        <div className="panel-card mb-4">
                            <h5 className="panel-title mb-4">Routing Information</h5>
                            <div className="row">
                                <div className="col-md-5">
                                    <h6 className="text-muted small fw-bold mb-1">ORIGIN</h6>
                                    <h5 className="fw-bold text-secondary mb-0">{shipment.origin_port?.port_name || 'TBA'}</h5>
                                    <p className="text-muted">{shipment.origin_country?.country_name || 'TBA'}</p>
                                </div>
                                <div className="col-md-2 d-flex align-items-center justify-content-center">
                                    <span className="material-symbols-outlined text-muted" style={{ fontSize: '32px' }}>arrow_right_alt</span>
                                </div>
                                <div className="col-md-5 text-md-end">
                                    <h6 className="text-muted small fw-bold mb-1">DESTINATION</h6>
                                    <h5 className="fw-bold text-secondary mb-0">{shipment.destination_port?.port_name || 'TBA'}</h5>
                                    <p className="text-muted">{shipment.destination_country?.country_name || 'TBA'}</p>
                                </div>
                            </div>
                        </div>

                        <div className="panel-card">
                            <div className="d-flex justify-content-between align-items-center mb-4">
                                <h5 className="panel-title mb-0">Containers</h5>
                                <button className="btn btn-sm btn-outline-primary rounded-pill">Add Container</button>
                            </div>
                            <div className="table-responsive">
                                <table className="table table-hover mb-0" style={{ color: 'var(--secondary)' }}>
                                    <thead style={{ background: 'rgba(255,255,255,0.4)' }}>
                                        <tr>
                                            <th className="border-0">Container #</th>
                                            <th className="border-0">Type</th>
                                            <th className="border-0">Size</th>
                                            <th className="border-0">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {shipment.containers && shipment.containers.length > 0 ? (
                                            shipment.containers.map((container: any) => (
                                                <tr key={container.id}>
                                                    <td className="fw-bold">{container.container_number}</td>
                                                    <td>{container.container_type}</td>
                                                    <td>{container.container_size}</td>
                                                    <td>{container.container_status}</td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan={4} className="text-center py-3 text-muted">No containers added yet.</td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Timeline & Side Info */}
                    <div className="col-xl-4 fade-up" style={{ animationDelay: '0.3s' }}>
                        <div className="panel-card mb-4 h-100">
                            <h5 className="panel-title mb-4">Shipment Timeline</h5>
                            
                            <div className="timeline-container ps-3" style={{ borderLeft: '2px solid var(--glass-border)', position: 'relative' }}>
                                {/* Dummy Timeline Item 1 */}
                                <div className="timeline-item mb-4" style={{ position: 'relative' }}>
                                    <span className="bg-success rounded-circle" style={{ width: '12px', height: '12px', position: 'absolute', left: '-23px', top: '4px' }}></span>
                                    <h6 className="fw-bold mb-1" style={{ fontSize: '0.95rem' }}>Shipment Created</h6>
                                    <p className="text-muted small mb-0">{new Date(shipment.created_at).toLocaleString()}</p>
                                </div>
                                {/* Dummy Timeline Item 2 */}
                                <div className="timeline-item mb-4" style={{ position: 'relative' }}>
                                    <span className="bg-secondary rounded-circle" style={{ width: '12px', height: '12px', position: 'absolute', left: '-23px', top: '4px' }}></span>
                                    <h6 className="fw-bold mb-1" style={{ fontSize: '0.95rem' }}>Awaiting Confirmation</h6>
                                    <p className="text-muted small mb-0">Pending carrier booking</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

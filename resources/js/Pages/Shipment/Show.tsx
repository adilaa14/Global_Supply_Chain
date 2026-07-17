import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function ShipmentShow({ shipmentId }: { shipmentId: string }) {
    const [shipment, setShipment] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    // Modal states
    const [showStatusModal, setShowStatusModal] = useState(false);
    const [showCargoModal, setShowCargoModal] = useState(false);
    const [showContainerModal, setShowContainerModal] = useState(false);

    // Form states
    const [statusForm, setStatusForm] = useState({ status: '' });
    const [cargoForm, setCargoForm] = useState({ quantity: '', weight: '', estimated_value: '' });
    const [containerForm, setContainerForm] = useState({ container_number: '', container_type: 'Dry', container_size: '20ft', container_status: 'Sealed' });

    const fetchShipment = async () => {
        try {
            const res = await axios.get(`/api/shipments/${shipmentId}`);
            if (res.data.status === 'success') {
                setShipment(res.data.data);
                setStatusForm({ status: res.data.data.status });
                setCargoForm({ 
                    quantity: res.data.data.quantity || '', 
                    weight: res.data.data.weight || '', 
                    estimated_value: res.data.data.estimated_value || '' 
                });
            }
        } catch (error) {
            console.error('Failed to fetch shipment', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchShipment();
    }, [shipmentId]);

    const handleUpdateStatus = async (e: any) => {
        e.preventDefault();
        try {
            await axios.put(`/api/shipments/${shipmentId}`, statusForm);
            setShowStatusModal(false);
            fetchShipment();
        } catch (error) {
            console.error('Error updating status', error);
        }
    };

    const handleUpdateCargo = async (e: any) => {
        e.preventDefault();
        try {
            await axios.put(`/api/shipments/${shipmentId}`, cargoForm);
            setShowCargoModal(false);
            fetchShipment();
        } catch (error) {
            console.error('Error updating cargo', error);
        }
    };

    const handleAddContainer = async (e: any) => {
        e.preventDefault();
        try {
            await axios.post(`/api/shipments/containers`, { ...containerForm, shipment_id: shipmentId });
            setShowContainerModal(false);
            setContainerForm({ container_number: '', container_type: 'Dry', container_size: '20ft', container_status: 'Sealed' });
            fetchShipment();
        } catch (error) {
            console.error('Error adding container', error);
        }
    };

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
                            <span className={`badge bg-light text-dark border ${shipment.status === 'Draft' ? 'text-secondary' : 'text-success'}`}>
                                ● {shipment.status}
                            </span>
                            <span className="text-muted small">Created on {new Date(shipment.created_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                    <div className="d-flex gap-2">
                        <button className="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-1" onClick={() => setShowCargoModal(true)}>
                            <span className="material-symbols-outlined" style={{ fontSize: '18px' }}>edit</span> Edit Cargo
                        </button>
                        <button className="btn-primary-custom d-flex align-items-center gap-2" onClick={() => setShowStatusModal(true)}>
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

                        {/* Cargo Details Panel */}
                        <div className="panel-card mb-4 fade-up" style={{ animationDelay: '0.25s' }}>
                            <h5 className="panel-title mb-4">Cargo Details</h5>
                            <div className="row g-4">
                                <div className="col-md-3">
                                    <h6 className="text-muted small fw-bold mb-1">COMMODITY</h6>
                                    <h5 className="fw-bold text-secondary mb-0">
                                        {shipment.commodity?.commodity_name || 'N/A'}
                                    </h5>
                                </div>
                                <div className="col-md-3">
                                    <h6 className="text-muted small fw-bold mb-1">QUANTITY</h6>
                                    <h5 className="fw-bold text-secondary mb-0">
                                        {shipment.quantity ? `${Number(shipment.quantity).toLocaleString()} ${shipment.unit || ''}` : 'N/A'}
                                    </h5>
                                </div>
                                <div className="col-md-3">
                                    <h6 className="text-muted small fw-bold mb-1">TOTAL WEIGHT</h6>
                                    <h5 className="fw-bold text-secondary mb-0">
                                        {shipment.weight ? `${Number(shipment.weight).toLocaleString()} KG` : 'N/A'}
                                    </h5>
                                </div>
                                <div className="col-md-3">
                                    <h6 className="text-muted small fw-bold mb-1">ESTIMATED VALUE</h6>
                                    <h5 className="fw-bold text-success mb-0">
                                        {shipment.estimated_value ? `$${Number(shipment.estimated_value).toLocaleString()}` : 'N/A'}
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div className="panel-card fade-up" style={{ animationDelay: '0.3s' }}>
                            <div className="d-flex justify-content-between align-items-center mb-4">
                                <h5 className="panel-title mb-0">Containers</h5>
                                <button className="btn btn-sm btn-outline-primary rounded-pill" onClick={() => setShowContainerModal(true)}>Add Container</button>
                            </div>
                            <div className="table-responsive">
                                <table className="table table-hover mb-0" style={{ color: 'var(--secondary)' }}>
                                    <thead style={{ background: 'rgba(255,255,255,0.4)' }}>
                                        <tr>
                                            <th className="border-0">Container #</th>
                                            <th className="border-0">Type</th>
                                            <th className="border-0">Size</th>
                                            <th className="border-0">Seal Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {shipment.containers && shipment.containers.length > 0 ? (
                                            shipment.containers.map((container: any) => (
                                                <tr key={container.id}>
                                                    <td className="fw-bold">{container.container_number}</td>
                                                    <td>{container.container_type}</td>
                                                    <td>{container.container_size}</td>
                                                    <td>{container.seal_number || 'Pending'}</td>
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
                        <div className="panel-card mb-4">
                            <h5 className="panel-title mb-4">Shipment Timeline</h5>
                            
                            <div className="timeline-container ps-3" style={{ borderLeft: '2px solid var(--glass-border)', position: 'relative' }}>
                                <div className="timeline-item mb-4" style={{ position: 'relative' }}>
                                    <span className="bg-success rounded-circle" style={{ width: '12px', height: '12px', position: 'absolute', left: '-23px', top: '4px' }}></span>
                                    <h6 className="fw-bold mb-1" style={{ fontSize: '0.95rem' }}>Shipment Created</h6>
                                    <p className="text-muted small mb-0">{new Date(shipment.created_at).toLocaleString()}</p>
                                </div>
                                <div className="timeline-item mb-4" style={{ position: 'relative' }}>
                                    <span className="bg-primary rounded-circle" style={{ width: '12px', height: '12px', position: 'absolute', left: '-23px', top: '4px' }}></span>
                                    <h6 className="fw-bold mb-1" style={{ fontSize: '0.95rem' }}>Current Status</h6>
                                    <p className="text-muted small mb-0">{shipment.status}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Modals using basic overlay logic */}
            {showStatusModal && (
                <div className="modal d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content form-control-glass border-0">
                            <div className="modal-header border-0">
                                <h5 className="modal-title fw-bold">Update Status</h5>
                                <button type="button" className="btn-close" onClick={() => setShowStatusModal(false)}></button>
                            </div>
                            <form onSubmit={handleUpdateStatus}>
                                <div className="modal-body">
                                    <select className="form-control-glass w-100" value={statusForm.status} onChange={(e) => setStatusForm({ status: e.target.value })}>
                                        <option value="Preparing">Preparing</option>
                                        <option value="In Transit">In Transit</option>
                                        <option value="At Port">At Port</option>
                                        <option value="Customs Clearance">Customs Clearance</option>
                                        <option value="Delivered">Delivered</option>
                                        <option value="Delayed">Delayed</option>
                                    </select>
                                </div>
                                <div className="modal-footer border-0">
                                    <button type="button" className="btn btn-outline-secondary rounded-pill" onClick={() => setShowStatusModal(false)}>Cancel</button>
                                    <button type="submit" className="btn-primary-custom">Save Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {showCargoModal && (
                <div className="modal d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content form-control-glass border-0">
                            <div className="modal-header border-0">
                                <h5 className="modal-title fw-bold">Edit Cargo Details</h5>
                                <button type="button" className="btn-close" onClick={() => setShowCargoModal(false)}></button>
                            </div>
                            <form onSubmit={handleUpdateCargo}>
                                <div className="modal-body">
                                    <div className="mb-3">
                                        <label className="small fw-bold text-muted">Quantity</label>
                                        <input type="number" className="form-control-glass w-100" value={cargoForm.quantity} onChange={(e) => setCargoForm({ ...cargoForm, quantity: e.target.value })} />
                                    </div>
                                    <div className="mb-3">
                                        <label className="small fw-bold text-muted">Weight (KG)</label>
                                        <input type="number" className="form-control-glass w-100" value={cargoForm.weight} onChange={(e) => setCargoForm({ ...cargoForm, weight: e.target.value })} />
                                    </div>
                                    <div className="mb-3">
                                        <label className="small fw-bold text-muted">Estimated Value ($)</label>
                                        <input type="number" className="form-control-glass w-100" value={cargoForm.estimated_value} onChange={(e) => setCargoForm({ ...cargoForm, estimated_value: e.target.value })} />
                                    </div>
                                </div>
                                <div className="modal-footer border-0">
                                    <button type="button" className="btn btn-outline-secondary rounded-pill" onClick={() => setShowCargoModal(false)}>Cancel</button>
                                    <button type="submit" className="btn-primary-custom">Save Cargo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {showContainerModal && (
                <div className="modal d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content form-control-glass border-0">
                            <div className="modal-header border-0">
                                <h5 className="modal-title fw-bold">Add Container</h5>
                                <button type="button" className="btn-close" onClick={() => setShowContainerModal(false)}></button>
                            </div>
                            <form onSubmit={handleAddContainer}>
                                <div className="modal-body">
                                    <div className="mb-3">
                                        <label className="small fw-bold text-muted">Container Number</label>
                                        <input type="text" className="form-control-glass w-100" required placeholder="e.g. MSKU1234567" value={containerForm.container_number} onChange={(e) => setContainerForm({ ...containerForm, container_number: e.target.value })} />
                                    </div>
                                    <div className="mb-3">
                                        <label className="small fw-bold text-muted">Type</label>
                                        <select className="form-control-glass w-100" value={containerForm.container_type} onChange={(e) => setContainerForm({ ...containerForm, container_type: e.target.value })}>
                                            <option value="Dry">Dry</option>
                                            <option value="Reefer">Reefer</option>
                                            <option value="Flat Rack">Flat Rack</option>
                                        </select>
                                    </div>
                                    <div className="mb-3">
                                        <label className="small fw-bold text-muted">Size</label>
                                        <select className="form-control-glass w-100" value={containerForm.container_size} onChange={(e) => setContainerForm({ ...containerForm, container_size: e.target.value })}>
                                            <option value="20ft">20ft</option>
                                            <option value="40ft">40ft</option>
                                            <option value="40ft HC">40ft HC</option>
                                        </select>
                                    </div>
                                </div>
                                <div className="modal-footer border-0">
                                    <button type="button" className="btn btn-outline-secondary rounded-pill" onClick={() => setShowContainerModal(false)}>Cancel</button>
                                    <button type="submit" className="btn-primary-custom">Add Container</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}

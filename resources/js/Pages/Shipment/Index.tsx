import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function ShipmentIndex({ initialShipments }: { initialShipments: any[] }) {
    const shipments = initialShipments || [];
    const loading = false;

    return (
        <AuthenticatedLayout>
            <Head title="Shipment Management" />

            <div className="container-fluid p-0">
                {/* Header */}
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>Shipment Management</h2>
                        <p className="text-muted mb-0">Track and manage global active shipments.</p>
                    </div>
                    <div className="d-flex gap-2">
                        <button className="btn btn-outline-secondary" style={{ borderRadius: '15px' }}>
                            <span className="material-symbols-outlined align-middle me-1">filter_list</span> Filter
                        </button>
                        <Link href="/shipments/create" className="btn-primary-custom d-flex align-items-center gap-2" style={{ textDecoration: 'none' }}>
                            <span className="material-symbols-outlined" style={{ fontSize: '20px' }}>add</span>
                            Create Shipment
                        </Link>
                    </div>
                </div>

                {/* Table Panel */}
                <div className="panel-card fade-up" style={{ animationDelay: '0.2s' }}>
                    <div className="table-responsive">
                        <table className="table table-hover mb-0" style={{ color: 'var(--secondary)' }}>
                            <thead style={{ background: 'rgba(255,255,255,0.4)' }}>
                                <tr>
                                    <th className="border-0">Shipment Number</th>
                                    <th className="border-0">Type</th>
                                    <th className="border-0">Origin</th>
                                    <th className="border-0">Destination</th>
                                    <th className="border-0">ETA</th>
                                    <th className="border-0">Status</th>
                                    <th className="border-0 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {loading ? (
                                    <tr>
                                        <td colSpan={7} className="text-center py-4 text-muted">Loading shipments...</td>
                                    </tr>
                                ) : shipments.length > 0 ? (
                                    shipments.map((shipment: any) => (
                                        <tr key={shipment.id} className="align-middle">
                                            <td className="fw-bold">{shipment.shipment_number}</td>
                                            <td>
                                                <span className={`badge ${shipment.shipment_type === 'Export' ? 'bg-primary' : 'bg-info'}`}>
                                                    {shipment.shipment_type}
                                                </span>
                                            </td>
                                            <td>{shipment.origin_port?.port_name || shipment.origin_country?.country_name || shipment.origin_country_id || 'TBA'}</td>
                                            <td>{shipment.destination_port?.port_name || shipment.destination_country?.country_name || shipment.destination_country_id || 'TBA'}</td>
                                            <td>{shipment.estimated_arrival ? new Date(shipment.estimated_arrival).toLocaleDateString() : 'TBA'}</td>
                                            <td>
                                                <span className={`badge bg-light text-dark border ${shipment.current_status === 'Draft' ? 'text-secondary' : 'text-success'}`}>
                                                    ● {shipment.current_status}
                                                </span>
                                            </td>
                                            <td className="text-end">
                                                <Link href={`/shipments/${shipment.id}`} className="btn btn-sm btn-light rounded-pill">
                                                    <span className="material-symbols-outlined align-middle" style={{ fontSize: '18px' }}>visibility</span>
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={7} className="text-center py-5">
                                            <div className="d-flex flex-column align-items-center">
                                                <span className="material-symbols-outlined text-muted mb-2" style={{ fontSize: '48px' }}>inventory_2</span>
                                                <h6 className="fw-bold text-muted">No Shipments Found</h6>
                                                <p className="text-muted small mb-3">You don't have any active shipments.</p>
                                                <Link href="/shipments/create" className="btn btn-outline-primary rounded-pill btn-sm">Create First Shipment</Link>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

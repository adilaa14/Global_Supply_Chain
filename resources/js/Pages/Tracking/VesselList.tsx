import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function VesselList() {
    const [vessels, setVessels] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchVessels = async () => {
            try {
                // Utilizing map-data as it returns active vessels
                const res = await axios.get('/api/tracking/map-data');
                if (res.data.status === 'success') {
                    setVessels(res.data.data.vessels || []);
                }
            } catch (error) {
                console.error('Failed to fetch vessels', error);
            } finally {
                setLoading(false);
            }
        };

        fetchVessels();
    }, []);

    return (
        <AuthenticatedLayout>
            <Head title="Live Vessel Tracking" />

            <div className="container-fluid p-0">
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>Live Vessel Tracking</h2>
                        <p className="text-muted mb-0">Monitor active vessels and their current status.</p>
                    </div>
                    <div className="d-flex gap-2">
                        <Link href="/tracking" className="btn btn-outline-secondary rounded-pill">
                            <span className="material-symbols-outlined align-middle me-1">map</span> Map View
                        </Link>
                    </div>
                </div>

                <div className="panel-card fade-up" style={{ animationDelay: '0.2s' }}>
                    <div className="table-responsive">
                        <table className="table table-hover mb-0" style={{ color: 'var(--secondary)' }}>
                            <thead style={{ background: 'rgba(255,255,255,0.4)' }}>
                                <tr>
                                    <th className="border-0">Vessel Name</th>
                                    <th className="border-0">Type</th>
                                    <th className="border-0">Destination</th>
                                    <th className="border-0">Speed</th>
                                    <th className="border-0">Status</th>
                                    <th className="border-0 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {loading ? (
                                    <tr>
                                        <td colSpan={6} className="text-center py-4 text-muted">Loading vessels...</td>
                                    </tr>
                                ) : vessels.length > 0 ? (
                                    vessels.map((vessel: any) => (
                                        <tr key={vessel.id} className="align-middle">
                                            <td className="fw-bold">
                                                <div className="d-flex align-items-center gap-2">
                                                    <span className="material-symbols-outlined text-muted" style={{ fontSize: '20px' }}>directions_boat</span>
                                                    {vessel.name}
                                                </div>
                                            </td>
                                            <td>
                                                <span className="badge bg-light text-dark border">
                                                    {vessel.type}
                                                </span>
                                            </td>
                                            <td>{vessel.destination}</td>
                                            <td>{vessel.position?.speed || 0} knots</td>
                                            <td>
                                                <span className="badge bg-success-subtle text-success border border-success-subtle">
                                                    ● {vessel.status}
                                                </span>
                                            </td>
                                            <td className="text-end">
                                                <Link href={`/tracking/vessels/${vessel.id}`} className="btn btn-sm btn-light rounded-pill">
                                                    <span className="material-symbols-outlined align-middle" style={{ fontSize: '18px' }}>visibility</span>
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={6} className="text-center py-5">
                                            <div className="d-flex flex-column align-items-center">
                                                <span className="material-symbols-outlined text-muted mb-2" style={{ fontSize: '48px' }}>sailing</span>
                                                <h6 className="fw-bold text-muted">No Active Vessels</h6>
                                                <p className="text-muted small mb-0">There are currently no vessels en route.</p>
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

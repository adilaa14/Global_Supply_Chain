import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function ApiList({ apis }: any) {
    return (
        <AuthenticatedLayout>
            <Head title="API Integrations" />

            <div className="container-fluid p-0 fade-up">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>API Integrations</h2>
                        <p className="text-muted mb-0">Overview of external APIs and services connected to the platform.</p>
                    </div>
                    <div>
                        <Link href="/admin/dashboard" className="btn btn-outline-secondary d-flex align-items-center gap-2">
                            <span className="material-symbols-outlined" style={{ fontSize: '20px' }}>arrow_back</span>
                            Back to Admin Dashboard
                        </Link>
                    </div>
                </div>

                <div className="panel-card p-0 overflow-hidden">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="px-4 py-3">Service Name</th>
                                    <th className="px-4 py-3">Endpoint / URL</th>
                                    <th className="px-4 py-3">Category</th>
                                    <th className="px-4 py-3">Description</th>
                                    <th className="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {apis.map((api: any) => (
                                    <tr key={api.id}>
                                        <td className="px-4 py-3 fw-bold" style={{ color: 'var(--dark)' }}>
                                            <div className="d-flex align-items-center gap-2">
                                                <span className="material-symbols-outlined text-muted" style={{ fontSize: '20px' }}>api</span>
                                                {api.name}
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 text-muted"><code>{api.url}</code></td>
                                        <td className="px-4 py-3">
                                            <span className="badge bg-light text-dark border">{api.type}</span>
                                        </td>
                                        <td className="px-4 py-3 text-muted" style={{ maxWidth: '300px' }}>
                                            {api.description}
                                        </td>
                                        <td className="px-4 py-3">
                                            {api.status === 'Active' ? (
                                                <span className="badge bg-success-subtle text-success">
                                                    <span className="d-inline-block rounded-circle bg-success me-1" style={{ width: '6px', height: '6px' }}></span>
                                                    Active
                                                </span>
                                            ) : (
                                                <span className="badge bg-warning-subtle text-warning">
                                                    <span className="d-inline-block rounded-circle bg-warning me-1" style={{ width: '6px', height: '6px' }}></span>
                                                    Standby
                                                </span>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

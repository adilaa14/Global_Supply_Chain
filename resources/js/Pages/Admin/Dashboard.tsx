import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function AdminDashboard({ users, ports, articles }: any) {
    return (
        <AuthenticatedLayout>
            <Head title="Admin Dashboard" />

            <div className="container-fluid p-0">
                <div className="d-flex justify-content-between align-items-center mb-4 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>Administrator Dashboard</h2>
                        <p className="text-muted mb-0">Manage platform users, dataset configurations, and analysis content.</p>
                    </div>
                    <div>
                        <Link href="/dashboard" className="btn btn-outline-primary d-flex align-items-center gap-2">
                            <span className="material-symbols-outlined" style={{ fontSize: '20px' }}>dashboard</span>
                            Access Global Command Center
                        </Link>
                    </div>
                </div>

                <div className="row g-4 mb-4 fade-up" style={{ animationDelay: '0.2s' }}>
                    {/* Manage Users */}
                    <div className="col-md-4">
                        <div className="panel-card h-100 p-4 text-center d-flex flex-column justify-content-center">
                            <div className="mb-3">
                                <span className="material-symbols-outlined text-primary" style={{ fontSize: '48px' }}>group</span>
                            </div>
                            <h4 className="fw-bold">Users Management</h4>
                            <p className="text-muted">Total Active Users: <strong>{users}</strong></p>
                            <div className="mt-auto pt-3">
                                <Link href="/admin/users" className="btn btn-primary w-100">Manage Users</Link>
                            </div>
                        </div>
                    </div>

                    {/* Manage Ports */}
                    <div className="col-md-4">
                        <div className="panel-card h-100 p-4 text-center d-flex flex-column justify-content-center">
                            <div className="mb-3">
                                <span className="material-symbols-outlined text-success" style={{ fontSize: '48px' }}>directions_boat</span>
                            </div>
                            <h4 className="fw-bold">Ports Dataset</h4>
                            <p className="text-muted">Total Registered Ports: <strong>{ports}</strong></p>
                            <div className="mt-auto pt-3">
                                <Link href="/admin/ports" className="btn btn-success w-100">Manage Ports</Link>
                            </div>
                        </div>
                    </div>

                    {/* Manage Articles */}
                    <div className="col-md-4">
                        <div className="panel-card h-100 p-4 text-center d-flex flex-column justify-content-center">
                            <div className="mb-3">
                                <span className="material-symbols-outlined text-warning" style={{ fontSize: '48px' }}>article</span>
                            </div>
                            <h4 className="fw-bold">Analysis Articles</h4>
                            <p className="text-muted">Total Published Articles: <strong>{articles}</strong></p>
                            <div className="mt-auto pt-3">
                                <Link href="/admin/articles" className="btn btn-warning text-white w-100">Manage Articles</Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="panel-card fade-up" style={{ animationDelay: '0.4s' }}>
                    <h5 className="fw-bold border-bottom pb-3 mb-3">System Information</h5>
                    <div className="row g-3">
                        <div className="col-md-3 text-muted">Environment</div>
                        <div className="col-md-9 fw-bold">Production (v1.2.0)</div>
                        
                        <div className="col-md-3 text-muted">Sentiment Analyzer</div>
                        <div className="col-md-9 fw-bold text-success">Online (Lexicon-Based Engine)</div>

                        <div className="col-md-3 text-muted">Risk Scoring Engine</div>
                        <div className="col-md-9 fw-bold text-success">Online (Weighted AI Model)</div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

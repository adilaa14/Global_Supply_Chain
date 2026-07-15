import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Market() {
    return (
        <AuthenticatedLayout
            header={
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Market Intelligence</h2>
                        <p className="text-muted mb-0">Advanced Market analytics and insights.</p>
                    </div>
                    <Link href={route('trade.index')} className="btn btn-outline-secondary rounded-pill px-4">Back to Dashboard</Link>
                </div>
            }
        >
            <Head title="Market Intelligence" />
            <div className="container-fluid py-4">
                <div className="panel-card fade-up text-center py-5">
                    <span className="material-symbols-outlined text-primary mb-3" style={{ fontSize: '48px' }}>construction</span>
                    <h4 className="fw-bold">Module Under Construction</h4>
                    <p className="text-muted">The Market module is currently being finalized in this sprint.</p>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

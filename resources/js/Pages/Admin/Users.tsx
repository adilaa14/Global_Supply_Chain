import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Users({ users }: any) {
    return (
        <AuthenticatedLayout>
            <Head title="Manage Users" />
            
            <div className="container-fluid p-0">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <Link href="/admin/dashboard" className="text-decoration-none mb-2 d-inline-block">&larr; Back to Admin Dashboard</Link>
                        <h2 className="fw-bold mb-0">Users Management</h2>
                    </div>
                    <button className="btn btn-primary">Add New User</button>
                </div>

                <div className="panel-card">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle">
                            <thead className="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.data.map((user: any) => (
                                    <tr key={user.id}>
                                        <td className="fw-bold">{user.name}</td>
                                        <td>{user.email}</td>
                                        <td>
                                            <span className={`badge ${user.status === 'active' ? 'bg-success' : 'bg-secondary'}`}>
                                                {user.status}
                                            </span>
                                        </td>
                                        <td>{new Date(user.created_at).toLocaleDateString()}</td>
                                        <td>
                                            <button onClick={() => alert('Fitur Edit User (ID: ' + user.id + ') sedang dalam pengembangan.')} className="btn btn-sm btn-outline-primary me-2">Edit</button>
                                            <button onClick={() => {
                                                if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                                                    // router.delete(`/admin/users/${user.id}`)
                                                    alert('Fitur Delete akan segera aktif setelah proses migrasi data selesai.');
                                                }
                                            }} className="btn btn-sm btn-outline-danger">Delete</button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {users.links && users.links.length > 3 && (
                        <div className="d-flex justify-content-center mt-3 pb-3">
                            {users.links.map((link: any, idx: number) => (
                                link.url ? (
                                    <Link 
                                        key={idx} 
                                        href={link.url} 
                                        className={`btn btn-sm mx-1 ${link.active ? 'btn-primary' : 'btn-outline-secondary'}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ) : (
                                    <span 
                                        key={idx} 
                                        className="btn btn-sm mx-1 btn-outline-secondary disabled"
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                )
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Articles({ articles, countries = [] }: any) {
    const [isAdding, setIsAdding] = useState(false);
    const [editingArticle, setEditingArticle] = useState<any>(null);
    const { data, setData, post, put, processing, reset, errors } = useForm({
        title: '',
        country_id: '',
        source: '',
        category: '',
        summary: '',
        sentiment: 'Neutral',
        published_at: new Date().toISOString().slice(0, 10)
    });

    const openAddModal = () => {
        reset();
        setIsAdding(true);
    };

    const openEditModal = (article: any) => {
        setEditingArticle(article);
        setData({
            title: article.title,
            country_id: article.country_id || '',
            source: article.source || '',
            category: article.category || '',
            summary: article.summary || '',
            sentiment: article.sentiment || 'Neutral',
            published_at: article.published_at ? article.published_at.slice(0, 10) : new Date().toISOString().slice(0, 10)
        });
    };

    const closeModal = () => {
        setIsAdding(false);
        setEditingArticle(null);
        reset();
    };

    const submitForm = (e: React.FormEvent) => {
        e.preventDefault();
        if (isAdding) {
            post('/admin/articles', { onSuccess: () => closeModal() });
        } else {
            put(`/admin/articles/${editingArticle.id}`, { onSuccess: () => closeModal() });
        }
    };

    const deleteArticle = (id: string) => {
        if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
            router.delete(`/admin/articles/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Manage Articles" />
            
            <div className="container-fluid p-0">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <Link href="/admin/dashboard" className="text-decoration-none mb-2 d-inline-block">&larr; Back to Admin Dashboard</Link>
                        <h2 className="fw-bold mb-0">Analysis Articles</h2>
                    </div>
                    <button onClick={openAddModal} className="btn btn-primary shadow-sm fw-medium d-flex align-items-center">
                        <span className="material-symbols-outlined me-1 fs-5">add</span>
                        Create Article
                    </button>
                </div>

                <div className="panel-card shadow-sm border-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="ps-3 border-0">Title</th>
                                    <th className="border-0">Country</th>
                                    <th className="border-0">Source</th>
                                    <th className="border-0">Sentiment</th>
                                    <th className="border-0">Published Date</th>
                                    <th className="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {articles.data.map((article: any) => (
                                    <tr key={article.id}>
                                        <td className="ps-3 fw-medium text-dark" style={{ maxWidth: '300px', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                            {article.title}
                                        </td>
                                        <td className="text-muted">{article.country ? article.country.country_name : '-'}</td>
                                        <td className="text-muted">{article.source}</td>
                                        <td>
                                            <span className={`badge rounded-pill ${article.sentiment === 'Positive' ? 'bg-success' : (article.sentiment === 'Negative' ? 'bg-danger' : 'bg-secondary')}`}>
                                                {article.sentiment || 'Unanalyzed'}
                                            </span>
                                        </td>
                                        <td className="text-muted">{new Date(article.published_at || article.created_at).toLocaleDateString()}</td>
                                        <td>
                                            <div className="d-flex gap-2">
                                                <button onClick={() => openEditModal(article)} className="btn btn-sm btn-light text-primary fw-medium border shadow-sm">Edit</button>
                                                <button onClick={() => deleteArticle(article.id)} className="btn btn-sm btn-light text-danger fw-medium border shadow-sm">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {articles.links && articles.links.length > 3 && (
                        <div className="d-flex justify-content-center mt-4 pb-2">
                            {articles.links.map((link: any, idx: number) => (
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

                {/* Modal Form */}
                {(isAdding || editingArticle) && (
                    <div className="modal show d-block" tabIndex={-1} style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                        <div className="modal-dialog modal-dialog-centered modal-lg">
                            <div className="modal-content border-0 shadow-lg">
                                <div className="modal-header border-bottom-0 pt-4 pb-2 px-4">
                                    <h5 className="modal-title fw-bold">
                                        {isAdding ? 'Create New Article' : 'Edit Article'}
                                    </h5>
                                    <button type="button" className="btn-close" onClick={closeModal}></button>
                                </div>
                                <form onSubmit={submitForm}>
                                    <div className="modal-body px-4">
                                        <div className="mb-3">
                                            <label className="form-label text-muted small fw-bold mb-1">Title</label>
                                            <input type="text" className="form-control form-control-lg" value={data.title} onChange={e => setData('title', e.target.value)} required />
                                            {errors.title && <div className="text-danger small">{errors.title}</div>}
                                        </div>
                                        
                                        <div className="row mb-3">
                                            <div className="col-md-6">
                                                <label className="form-label text-muted small fw-bold mb-1">Country</label>
                                                <select className="form-select" value={data.country_id} onChange={e => setData('country_id', e.target.value)} required>
                                                    <option value="">-- Select Country --</option>
                                                    {countries.map((c: any) => (
                                                        <option key={c.id} value={c.id}>{c.country_name}</option>
                                                    ))}
                                                </select>
                                                {errors.country_id && <div className="text-danger small">{errors.country_id}</div>}
                                            </div>
                                            <div className="col-md-6">
                                                <label className="form-label text-muted small fw-bold mb-1">Source</label>
                                                <input type="text" className="form-control" value={data.source} onChange={e => setData('source', e.target.value)} required />
                                                {errors.source && <div className="text-danger small">{errors.source}</div>}
                                            </div>
                                        </div>

                                        <div className="row mb-3">
                                            <div className="col-md-4">
                                                <label className="form-label text-muted small fw-bold mb-1">Category</label>
                                                <select className="form-select" value={data.category} onChange={e => setData('category', e.target.value)} required>
                                                    <option value="">-- Select Category --</option>
                                                    <option value="Logistics">Logistics</option>
                                                    <option value="Trade">Trade</option>
                                                    <option value="Shipping">Shipping</option>
                                                    <option value="Economy">Economy</option>
                                                </select>
                                                {errors.category && <div className="text-danger small">{errors.category}</div>}
                                            </div>
                                            <div className="col-md-4">
                                                <label className="form-label text-muted small fw-bold mb-1">Sentiment</label>
                                                <select className="form-select" value={data.sentiment} onChange={e => setData('sentiment', e.target.value)}>
                                                    <option value="Positive">Positive</option>
                                                    <option value="Neutral">Neutral</option>
                                                    <option value="Negative">Negative</option>
                                                </select>
                                            </div>
                                            <div className="col-md-4">
                                                <label className="form-label text-muted small fw-bold mb-1">Published Date</label>
                                                <input type="date" className="form-control" value={data.published_at} onChange={e => setData('published_at', e.target.value)} required />
                                            </div>
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label text-muted small fw-bold mb-1">Summary / Content</label>
                                            <textarea className="form-control" rows={4} value={data.summary} onChange={e => setData('summary', e.target.value)} required></textarea>
                                            {errors.summary && <div className="text-danger small">{errors.summary}</div>}
                                        </div>
                                    </div>
                                    <div className="modal-footer border-top-0 px-4 pb-4">
                                        <button type="button" className="btn btn-light border px-4" onClick={closeModal}>Cancel</button>
                                        <button type="submit" className="btn btn-primary px-4 shadow-sm" disabled={processing}>
                                            {processing ? 'Saving...' : 'Save Article'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

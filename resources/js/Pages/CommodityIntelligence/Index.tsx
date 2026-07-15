import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function CommodityIndex() {
    const [commodities, setCommodities] = useState<any[]>([]);
    const [categories, setCategories] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [category, setCategory] = useState('All');
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });

    useEffect(() => {
        axios.get('/api/commodities/categories').then(res => setCategories(res.data));
    }, []);

    useEffect(() => {
        setPage(1);
    }, [search, category]);

    useEffect(() => {
        const fetchCommodities = async () => {
            setLoading(true);
            try {
                const params: any = { search, page, category };
                const res = await axios.get('/api/commodities', { params });
                setCommodities(res.data.data);
                setPagination({
                    current_page: res.data.current_page,
                    last_page: res.data.last_page,
                    total: res.data.total
                });
            } catch (error) {
                console.error("Error fetching commodities", error);
            } finally {
                setLoading(false);
            }
        };

        const timeoutId = setTimeout(() => {
            fetchCommodities();
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [search, category, page]);

    const formatCurrency = (val: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);

    const getCommodityIcon = (name: string, category: string) => {
        const n = (name || '').toLowerCase();
        if (n.includes('oil')) return 'oil_barrel';
        if (n.includes('gas')) return 'local_fire_department';
        if (n.includes('coal')) return 'landscape';
        if (n.includes('gold') || n.includes('silver')) return 'monetization_on';
        if (n.includes('copper') || n.includes('steel') || n.includes('iron') || n.includes('aluminium') || n.includes('tin') || n.includes('nickel') || n.includes('lithium')) return 'hardware';
        if (n.includes('coffee') || n.includes('tea') || n.includes('cocoa')) return 'local_cafe';
        if (n.includes('rice') || n.includes('wheat') || n.includes('corn') || n.includes('soybean') || n.includes('sugar')) return 'agriculture';
        if (n.includes('cotton') || n.includes('rubber')) return 'category';
        if (n.includes('seafood')) return 'set_meal';
        if (n.includes('livestock')) return 'pets';
        if (category === 'Energy') return 'bolt';
        if (category === 'Agriculture') return 'eco';
        if (category === 'Metals') return 'diamond';
        return 'inventory_2';
    };

    return (
        <AuthenticatedLayout>
            <Head title="Commodity Intelligence" />

            <div className="container-fluid p-4">
                <div className="d-flex justify-content-between align-items-center mb-4 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Commodity Intelligence</h2>
                        <p className="text-muted mb-0">Analyze global commodity markets before making import and export decisions.</p>
                    </div>
                    <div className="d-flex gap-3">
                        <Link href="/intelligence/commodities/compare" className="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center gap-2 shadow-sm">
                            <span className="material-symbols-outlined">compare_arrows</span>
                            Compare Commodities
                        </Link>
                    </div>
                </div>

                <div className="panel-card mb-4 fade-up" style={{ animationDelay: '0.1s' }}>
                    <div className="d-flex justify-content-between align-items-center mb-4">
                        <div className="search-bar w-50 m-0">
                            <span className="material-symbols-outlined">search</span>
                            <input 
                                type="text" 
                                placeholder="Search by commodity name, HS code..." 
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>
                        <div className="d-flex gap-2">
                            <select 
                                className="form-select rounded-pill border-light bg-light text-muted"
                                value={category}
                                onChange={(e) => setCategory(e.target.value)}
                            >
                                <option value="All">All Categories</option>
                                {categories.map(c => <option key={c.id} value={c.name}>{c.name}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="table-responsive">
                        <table className="table custom-table align-middle">
                            <thead>
                                <tr>
                                    <th>Commodity</th>
                                    <th>Category</th>
                                    <th>Current Price</th>
                                    <th>Daily Change</th>
                                    <th>Market Trend</th>
                                    <th>Demand / Supply Score</th>
                                    <th>Global Ranking</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {loading ? (
                                    <tr>
                                        <td colSpan={8} className="text-center py-5">
                                            <div className="spinner-border text-primary" role="status"></div>
                                        </td>
                                    </tr>
                                ) : commodities.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="text-center py-5 text-muted">
                                            No commodities found.
                                        </td>
                                    </tr>
                                ) : (
                                    commodities.map((commodity) => {
                                        const price = commodity.prices?.[0] || {};
                                        const demand = commodity.demands?.[0] || {};
                                        const supply = commodity.supplies?.[0] || {};
                                        const ranking = commodity.ranking || {};
                                        
                                        return (
                                        <tr key={commodity.id}>
                                            <td>
                                                <div className="d-flex align-items-center gap-3">
                                                    <div className="icon-wrapper bg-light-primary text-primary">
                                                        <span className="material-symbols-outlined">{getCommodityIcon(commodity.commodity_name, commodity.category?.name)}</span>
                                                    </div>
                                                    <div>
                                                        <h6 className="mb-0 fw-bold">{commodity.commodity_name}</h6>
                                                        <span className="text-muted small">HS: {commodity.commodity_code} • {commodity.unit}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span className="badge bg-light text-dark">{commodity.category?.name}</span>
                                            </td>
                                            <td>
                                                <span className="fw-bold">{formatCurrency(price.current_price || 0)}</span>
                                            </td>
                                            <td>
                                                <span className={`badge bg-soft-${(price.daily_change || 0) >= 0 ? 'success text-success' : 'danger text-danger'}`}>
                                                    {(price.daily_change || 0) >= 0 ? '+' : ''}{price.daily_change || 0}%
                                                </span>
                                            </td>
                                            <td>
                                                <span className={`text-${price.trend === 'Up' ? 'success' : price.trend === 'Down' ? 'danger' : 'muted'} d-flex align-items-center gap-1`}>
                                                    <span className="material-symbols-outlined" style={{ fontSize: '18px' }}>
                                                        {price.trend === 'Up' ? 'trending_up' : price.trend === 'Down' ? 'trending_down' : 'trending_flat'}
                                                    </span>
                                                    {price.trend || 'Unknown'}
                                                </span>
                                            </td>
                                            <td>
                                                <div className="d-flex flex-column gap-1">
                                                    <div className="d-flex align-items-center justify-content-between small">
                                                        <span className="text-muted">Demand</span>
                                                        <span className="fw-bold text-success">{demand.demand_score || 0}/100</span>
                                                    </div>
                                                    <div className="d-flex align-items-center justify-content-between small">
                                                        <span className="text-muted">Supply</span>
                                                        <span className="fw-bold text-warning">{supply.supply_score || 0}/100</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span className="badge bg-primary rounded-pill px-3 py-2">
                                                    #{ranking.global_ranking || '-'}
                                                </span>
                                            </td>
                                            <td>
                                                <Link href={`/intelligence/commodities/${commodity.id}`} className="btn btn-light btn-sm rounded-pill px-3">
                                                    Analyze
                                                </Link>
                                            </td>
                                        </tr>
                                    )})
                                )}
                            </tbody>
                        </table>
                    </div>
                    
                    {!loading && pagination.last_page > 1 && (
                        <div className="d-flex justify-content-between align-items-center mt-4">
                            <span className="text-muted small">
                                Showing page {pagination.current_page} of {pagination.last_page}
                            </span>
                            <div className="d-flex gap-2">
                                <button 
                                    className="btn btn-outline-secondary btn-sm" 
                                    disabled={pagination.current_page === 1}
                                    onClick={() => setPage(page - 1)}
                                >
                                    Previous
                                </button>
                                <button 
                                    className="btn btn-outline-secondary btn-sm"
                                    disabled={pagination.current_page === pagination.last_page}
                                    onClick={() => setPage(page + 1)}
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import axios from 'axios';

export default function CommodityCompare() {
    const [allCommodities, setAllCommodities] = useState<any[]>([]);
    const [selectedIds, setSelectedIds] = useState<string[]>([]);
    const [commoditiesData, setCommoditiesData] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);
    
    // For dropdown search
    const [search, setSearch] = useState('');
    const dropdownRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        axios.get('/api/commodities/list').then(res => {
            setAllCommodities(res.data);
            // Default select top 3
            if (res.data.length >= 3) {
                setSelectedIds([res.data[0].id, res.data[1].id, res.data[2].id]);
            }
        });
    }, []);

    useEffect(() => {
        if (selectedIds.length === 0) {
            setCommoditiesData([]);
            return;
        }
        setLoading(true);
        axios.get(`/api/commodities/comparison?ids=${selectedIds.join(',')}`).then(res => {
            setCommoditiesData(res.data);
            setLoading(false);
        });
    }, [selectedIds]);

    const toggleCommodity = (id: string) => {
        setSelectedIds(prev => {
            if (prev.includes(id)) return prev.filter(cId => cId !== id);
            if (prev.length >= 5) return prev; // max 5
            return [...prev, id];
        });
    };

    const formatCurrency = (val: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
    const formatNumber = (val: number) => new Intl.NumberFormat('en-US').format(val);

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

    const filteredCommodities = allCommodities.filter(c => 
        c.commodity_name.toLowerCase().includes(search.toLowerCase()) || 
        (c.commodity_code && c.commodity_code.toLowerCase().includes(search.toLowerCase()))
    );

    return (
        <AuthenticatedLayout>
            <Head title="Compare Commodities - Commodity Intelligence" />

            <div className="container-fluid p-4">
                <div className="d-flex align-items-center gap-3 mb-4 fade-up">
                    <Link href="/intelligence/commodities" className="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm">
                        <span className="material-symbols-outlined">arrow_back</span>
                    </Link>
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Commodity Comparison</h2>
                        <p className="text-muted mb-0">Compare up to 5 commodities side-by-side.</p>
                    </div>
                </div>

                <div className="panel-card mb-4 fade-up position-relative" style={{ animationDelay: '0.1s', zIndex: 50 }}>
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="fw-bold mb-0">Selected Commodities ({selectedIds.length}/5)</h5>
                        
                        <div className="dropdown" ref={dropdownRef}>
                            <button 
                                className="btn btn-primary rounded-pill px-4 shadow-sm" 
                                type="button" 
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                            >
                                Select Commodities
                            </button>
                            <div className="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0 bg-white rounded-3" style={{ width: '300px' }}>
                                <input 
                                    type="text" 
                                    className="form-control mb-3" 
                                    placeholder="Search commodity..." 
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                />
                                <div style={{ maxHeight: '300px', overflowY: 'auto' }}>
                                    {filteredCommodities.map(c => (
                                        <div key={c.id} className="form-check custom-checkbox mb-2 p-2 rounded hover-light">
                                            <input 
                                                className="form-check-input ms-0 me-2" 
                                                type="checkbox" 
                                                id={`comm_${c.id}`} 
                                                checked={selectedIds.includes(c.id)}
                                                onChange={() => toggleCommodity(c.id)}
                                                disabled={!selectedIds.includes(c.id) && selectedIds.length >= 5}
                                            />
                                            <label className="form-check-label w-100" htmlFor={`comm_${c.id}`} style={{ cursor: 'pointer' }}>
                                                {c.commodity_name} <span className="text-muted small">({c.commodity_code})</span>
                                            </label>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {loading ? (
                    <div className="d-flex justify-content-center align-items-center" style={{ height: '50vh' }}>
                        <div className="spinner-border text-primary" role="status"></div>
                    </div>
                ) : commoditiesData.length === 0 ? (
                    <div className="panel-card text-center py-5 fade-up" style={{ zIndex: 1 }}>
                        <h5 className="text-muted">No commodities selected for comparison.</h5>
                        <p className="text-muted small">Please select at least one commodity from the dropdown above.</p>
                    </div>
                ) : (
                    <div className="table-responsive fade-up" style={{ animationDelay: '0.2s', zIndex: 1, position: 'relative' }}>
                        <table className="table table-bordered bg-white custom-table" style={{ minWidth: '800px' }}>
                            <tbody>
                                {/* Header Row */}
                                <tr className="bg-light">
                                    <th className="py-4 align-middle" style={{ width: '250px' }}>
                                        <h5 className="fw-bold mb-0 text-primary">Overview</h5>
                                    </th>
                                    {commoditiesData.map(c => (
                                        <td key={c.id} className="py-4 align-middle text-center" style={{ minWidth: '200px' }}>
                                            <div className="icon-wrapper bg-light-primary text-primary mx-auto mb-3" style={{ width: '50px', height: '50px' }}>
                                                <span className="material-symbols-outlined" style={{ fontSize: '24px' }}>{getCommodityIcon(c.commodity_name, c.category?.name)}</span>
                                            </div>
                                            <h5 className="fw-bold mb-1">{c.commodity_name}</h5>
                                            <span className="badge bg-light text-dark">{c.category?.name}</span>
                                            <div className="mt-2 text-muted small">HS Code: {c.commodity_code}</div>
                                            <div className="text-muted small">Unit: {c.unit}</div>
                                            <button 
                                                className="btn btn-sm btn-outline-danger mt-3 rounded-pill"
                                                onClick={() => toggleCommodity(c.id)}
                                            >
                                                Remove
                                            </button>
                                        </td>
                                    ))}
                                </tr>

                                {/* Price Data */}
                                <tr>
                                    <th colSpan={commoditiesData.length + 1} className="bg-light-primary text-primary py-2 px-3 fw-bold">
                                        Price Analysis
                                    </th>
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Current Price</th>
                                    {commoditiesData.map(c => {
                                        const p = c.prices?.[0] || {};
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                <h5 className="fw-bold mb-0">{formatCurrency(p.current_price || 0)}</h5>
                                            </td>
                                        );
                                    })}
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Daily Change</th>
                                    {commoditiesData.map(c => {
                                        const p = c.prices?.[0] || {};
                                        const isPositive = (p.daily_change || 0) >= 0;
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                <span className={`badge bg-soft-${isPositive ? 'success text-success' : 'danger text-danger'}`}>
                                                    {isPositive ? '+' : ''}{p.daily_change || 0}%
                                                </span>
                                            </td>
                                        );
                                    })}
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Volatility</th>
                                    {commoditiesData.map(c => {
                                        const p = c.prices?.[0] || {};
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                {p.volatility || 0}%
                                            </td>
                                        );
                                    })}
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Trend</th>
                                    {commoditiesData.map(c => {
                                        const p = c.prices?.[0] || {};
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                <span className={`text-${p.trend === 'Up' ? 'success' : p.trend === 'Down' ? 'danger' : 'muted'} fw-bold`}>
                                                    {p.trend || 'Stable'}
                                                </span>
                                            </td>
                                        );
                                    })}
                                </tr>

                                {/* Market Data */}
                                <tr>
                                    <th colSpan={commoditiesData.length + 1} className="bg-light-primary text-primary py-2 px-3 fw-bold mt-4">
                                        Market Fundamentals
                                    </th>
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Demand Score</th>
                                    {commoditiesData.map(c => {
                                        const d = c.demands?.[0] || {};
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                <span className="fw-bold text-success" style={{ fontSize: '1.2rem' }}>{d.demand_score || 0}</span>/100
                                            </td>
                                        );
                                    })}
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Supply Score</th>
                                    {commoditiesData.map(c => {
                                        const s = c.supplies?.[0] || {};
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                <span className="fw-bold text-warning" style={{ fontSize: '1.2rem' }}>{s.supply_score || 0}</span>/100
                                            </td>
                                        );
                                    })}
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Global Demand</th>
                                    {commoditiesData.map(c => {
                                        const d = c.demands?.[0] || {};
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                {formatNumber(d.current_demand || 0)} {c.unit}
                                            </td>
                                        );
                                    })}
                                </tr>
                                <tr>
                                    <th className="align-middle text-muted fw-bold">Global Supply</th>
                                    {commoditiesData.map(c => {
                                        const s = c.supplies?.[0] || {};
                                        return (
                                            <td key={c.id} className="text-center align-middle">
                                                {formatNumber(s.current_supply || 0)} {c.unit}
                                            </td>
                                        );
                                    })}
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

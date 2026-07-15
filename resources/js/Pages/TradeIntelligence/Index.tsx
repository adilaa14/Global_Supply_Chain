import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function TradeDashboard() {
    const [data, setData] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get('/api/trade/dashboard').then(res => {
            setData(res.data);
            setLoading(false);
        });
    }, []);

    const formatCurrency = (val: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);

    return (
        <AuthenticatedLayout
            header={
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Trade Intelligence</h2>
                        <p className="text-muted mb-0">Command center for global trade operations and market analytics.</p>
                    </div>
                    <div className="d-flex gap-2">
                        <Link href={route('trade.simulation')} className="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                            <span className="material-symbols-outlined">calculate</span>
                            Profit Simulation
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title="Trade Intelligence" />

            <div className="container-fluid py-4">
                {/* Navigation Pills */}
                <div className="mb-4 overflow-auto pb-2 fade-up" style={{ animationDelay: '0.1s' }}>
                    <div className="d-flex gap-2" style={{ minWidth: '800px' }}>
                        <Link href={route('trade.index')} className="btn btn-primary rounded-pill px-4">Dashboard</Link>
                        <Link href={route('trade.opportunity')} className="btn btn-outline-primary rounded-pill bg-white px-4">Opportunities</Link>
                        <Link href={route('trade.market')} className="btn btn-outline-primary rounded-pill bg-white px-4">Market</Link>
                        <Link href={route('trade.risk')} className="btn btn-outline-primary rounded-pill bg-white px-4">Risk Analysis</Link>
                        <Link href={route('trade.alternative_destination')} className="btn btn-outline-primary rounded-pill bg-white px-4">Alt. Destinations</Link>
                        <Link href={route('trade.forecast')} className="btn btn-outline-primary rounded-pill bg-white px-4">Forecasts</Link>
                        <Link href={route('trade.insights')} className="btn btn-outline-primary rounded-pill bg-white px-4">Insights</Link>
                    </div>
                </div>

                {loading ? (
                    <div className="d-flex justify-content-center align-items-center" style={{ height: '50vh' }}>
                        <div className="spinner-border text-primary" role="status"></div>
                    </div>
                ) : (
                    <>
                        {/* KPI Cards */}
                        <div className="row g-4 mb-4 fade-up" style={{ animationDelay: '0.2s' }}>
                            <div className="col-md-3">
                                <div className="stat-card">
                                    <div className="icon-wrapper bg-light-primary text-primary mb-3">
                                        <span className="material-symbols-outlined">public</span>
                                    </div>
                                    <h6 className="text-muted mb-2">Active Markets</h6>
                                    <h3 className="fw-bold mb-0">{data?.summary?.active_markets || 0}</h3>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="stat-card">
                                    <div className="icon-wrapper bg-light-success text-success mb-3">
                                        <span className="material-symbols-outlined">trending_up</span>
                                    </div>
                                    <h6 className="text-muted mb-2">Total Opportunities</h6>
                                    <h3 className="fw-bold mb-0">{data?.summary?.total_opportunities || 0}</h3>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="stat-card">
                                    <div className="icon-wrapper bg-light-warning text-warning mb-3">
                                        <span className="material-symbols-outlined">category</span>
                                    </div>
                                    <h6 className="text-muted mb-2">Monitored Commodities</h6>
                                    <h3 className="fw-bold mb-0">{data?.summary?.monitored_commodities || 0}</h3>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="stat-card">
                                    <div className="icon-wrapper bg-light-danger text-danger mb-3">
                                        <span className="material-symbols-outlined">warning</span>
                                    </div>
                                    <h6 className="text-muted mb-2">Avg Global Risk Score</h6>
                                    <h3 className="fw-bold mb-0">{Number(data?.summary?.avg_risk_score || 0).toFixed(1)}/100</h3>
                                </div>
                            </div>
                        </div>

                        <div className="row g-4 mb-4">
                            {/* Best Market Today */}
                            <div className="col-lg-6 fade-up" style={{ animationDelay: '0.3s' }}>
                                <div className="panel-card h-100" style={{ background: 'linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.4))' }}>
                                    <div className="d-flex justify-content-between align-items-center mb-4">
                                        <h5 className="fw-bold mb-0 d-flex align-items-center gap-2">
                                            <span className="material-symbols-outlined text-success">star</span>
                                            Best Market Today
                                        </h5>
                                        <span className="badge bg-success">Score: {data?.best_market_today?.opportunity_score}</span>
                                    </div>
                                    {data?.best_market_today ? (
                                        <div>
                                            <div className="d-flex align-items-center gap-3 mb-3">
                                                <div className="rounded overflow-hidden shadow-sm d-flex justify-content-center align-items-center" style={{ width: '60px', height: '40px', background: '#f8f9fa' }}>
                                                    {data.best_market_today.country?.iso_code ? (
                                                        <img src={`https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${data.best_market_today.country.iso_code.toLowerCase()}.svg`} alt={data.best_market_today.country.name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                                    ) : (
                                                        <span className="material-symbols-outlined text-muted">flag</span>
                                                    )}
                                                </div>
                                                <div>
                                                    <h4 className="fw-bold mb-0">{data.best_market_today.country?.name}</h4>
                                                    <p className="text-muted mb-0">{data.best_market_today.country?.region}</p>
                                                </div>
                                            </div>
                                            <div className="p-3 bg-light rounded-4">
                                                <p className="mb-2 fw-bold text-primary">Target Commodity: {data.best_market_today.commodity?.commodity_name}</p>
                                                <p className="mb-2 text-muted small">{data.best_market_today.reason}</p>
                                                <h4 className="text-success fw-bold mb-0">{formatCurrency(data.best_market_today.estimated_profit)} Est. Profit</h4>
                                            </div>
                                        </div>
                                    ) : (
                                        <p className="text-muted">No data available.</p>
                                    )}
                                </div>
                            </div>

                            {/* Lowest Risk Country */}
                            <div className="col-lg-6 fade-up" style={{ animationDelay: '0.4s' }}>
                                <div className="panel-card h-100">
                                    <div className="d-flex justify-content-between align-items-center mb-4">
                                        <h5 className="fw-bold mb-0 d-flex align-items-center gap-2">
                                            <span className="material-symbols-outlined text-info">shield</span>
                                            Lowest Risk Country
                                        </h5>
                                        <span className="badge bg-info">Risk: {data?.lowest_risk_country?.total_risk_score}/100</span>
                                    </div>
                                    {data?.lowest_risk_country ? (
                                        <div>
                                            <div className="d-flex align-items-center gap-3 mb-3">
                                                <div className="rounded overflow-hidden shadow-sm d-flex justify-content-center align-items-center" style={{ width: '60px', height: '40px', background: '#f8f9fa' }}>
                                                    {data.lowest_risk_country.country?.iso_code ? (
                                                        <img src={`https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${data.lowest_risk_country.country.iso_code.toLowerCase()}.svg`} alt={data.lowest_risk_country.country.name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                                    ) : (
                                                        <span className="material-symbols-outlined text-muted">flag</span>
                                                    )}
                                                </div>
                                                <div>
                                                    <h4 className="fw-bold mb-0">{data.lowest_risk_country.country?.name}</h4>
                                                    <p className="text-muted mb-0">{data.lowest_risk_country.country?.region}</p>
                                                </div>
                                            </div>
                                            <div className="row g-2 mt-2">
                                                <div className="col-6">
                                                    <div className="p-2 border rounded-3 bg-light">
                                                        <div className="small text-muted">Political Risk</div>
                                                        <div className="fw-bold text-success">{data.lowest_risk_country.political_risk}/100</div>
                                                    </div>
                                                </div>
                                                <div className="col-6">
                                                    <div className="p-2 border rounded-3 bg-light">
                                                        <div className="small text-muted">Economic Risk</div>
                                                        <div className="fw-bold text-success">{data.lowest_risk_country.economic_risk}/100</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ) : (
                                        <p className="text-muted">No data available.</p>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="row g-4 fade-up" style={{ animationDelay: '0.5s' }}>
                            {/* Top Export Countries */}
                            <div className="col-lg-6">
                                <div className="panel-card h-100">
                                    <h5 className="fw-bold mb-4">Top Demand Markets</h5>
                                    <div className="table-responsive">
                                        <table className="table table-hover align-middle mb-0">
                                            <thead className="table-light">
                                                <tr>
                                                    <th>Country</th>
                                                    <th>Commodity</th>
                                                    <th className="text-end">Demand Score</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {data?.top_exports?.map((m: any) => (
                                                    <tr key={m.id}>
                                                        <td>
                                                            <div className="d-flex align-items-center gap-2">
                                                                <span className="fw-bold">{m.country?.name}</span>
                                                            </div>
                                                        </td>
                                                        <td>{m.commodity?.commodity_name}</td>
                                                        <td className="text-end fw-bold text-primary">{m.demand_score}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {/* Trade Insights Feed */}
                            <div className="col-lg-6">
                                <div className="panel-card h-100">
                                    <h5 className="fw-bold mb-4">AI Trade Insights</h5>
                                    <div className="d-flex flex-column gap-3">
                                        {data?.insights?.map((insight: any) => (
                                            <div key={insight.id} className="p-3 bg-light rounded-4 border-start border-4 border-primary">
                                                <div className="d-flex justify-content-between align-items-center mb-2">
                                                    <span className="badge bg-primary">{insight.type}</span>
                                                    <span className="small text-muted">{new Date(insight.created_at).toLocaleDateString()}</span>
                                                </div>
                                                <h6 className="fw-bold mb-1">{insight.title}</h6>
                                                <p className="text-muted small mb-0">{insight.description}</p>
                                            </div>
                                        ))}
                                        {(!data?.insights || data.insights.length === 0) && (
                                            <p className="text-muted text-center py-4">No recent insights.</p>
                                        )}
                                    </div>
                                    <div className="text-center mt-3">
                                        <Link href={route('trade.insights')} className="btn btn-sm btn-link text-decoration-none">View All Insights &rarr;</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

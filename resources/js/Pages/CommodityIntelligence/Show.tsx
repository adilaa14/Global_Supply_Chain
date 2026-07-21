import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';
import { Line } from 'react-chartjs-2';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

export default function CommodityShow({ commodityId }: { commodityId: string }) {
    const [commodity, setCommodity] = useState<any>(null);
    const [history, setHistory] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [historyDays, setHistoryDays] = useState(30);

    useEffect(() => {
        const fetchDetails = async () => {
            setLoading(true);
            try {
                const [commRes, histRes] = await Promise.all([
                    axios.get(`/api/commodities/${commodityId}`),
                    axios.get(`/api/commodities/${commodityId}/history?days=${historyDays}`)
                ]);
                setCommodity(commRes.data);
                setHistory(histRes.data);
            } catch (error) {
                console.error("Error fetching commodity details", error);
            } finally {
                setLoading(false);
            }
        };
        fetchDetails();
    }, [commodityId, historyDays]);

    const formatCurrency = (val: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
    const formatNumber = (val: number) => new Intl.NumberFormat('en-US').format(val);

    if (loading) {
        return (
            <AuthenticatedLayout>
                <div className="d-flex justify-content-center align-items-center" style={{ height: '80vh' }}>
                    <div className="spinner-border text-primary" role="status"></div>
                </div>
            </AuthenticatedLayout>
        );
    }

    if (!commodity) return <AuthenticatedLayout><div className="p-5 text-center">Commodity not found</div></AuthenticatedLayout>;

    const price = commodity.prices?.[0] || {};
    const market = commodity.market || {};
    const demand = commodity.demands?.[0] || {};
    const supply = commodity.supplies?.[0] || {};

    const chartData = {
        labels: history.map(h => h.date),
        datasets: [
            {
                label: 'Price (USD)',
                data: history.map(h => h.price),
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }
        ]
    };

    const chartOptions = {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: { beginAtZero: false }
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={`${commodity.commodity_name} - Commodity Intelligence`} />

            <div className="container-fluid p-4">
                <div className="d-flex align-items-center gap-3 mb-4 fade-up">
                    <Link href="/intelligence/commodities" className="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm">
                        <span className="material-symbols-outlined">arrow_back</span>
                    </Link>
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>{commodity.commodity_name}</h2>
                        <p className="text-muted mb-0">HS Code: {commodity.commodity_code} • Category: {commodity.category?.name}</p>
                    </div>
                </div>

                <div className="row g-4 mb-4 fade-up" style={{ animationDelay: '0.1s' }}>
                    <div className="col-lg-3 col-md-6">
                        <div className="panel-card d-flex flex-column justify-content-center">
                            <span className="text-muted small fw-bold text-uppercase mb-2">Current Price</span>
                            <div className="d-flex align-items-end gap-2">
                                <h3 className="fw-bold mb-0 text-primary">{formatCurrency(price.current_price || 0)}</h3>
                                <span className={`badge bg-soft-${(price.daily_change || 0) >= 0 ? 'success text-success' : 'danger text-danger'} mb-1`}>
                                    {(price.daily_change || 0) >= 0 ? '+' : ''}{price.daily_change || 0}%
                                </span>
                            </div>
                            <span className="text-muted small mt-2">per {commodity.unit}</span>
                        </div>
                    </div>
                    <div className="col-lg-3 col-md-6">
                        <div className="panel-card d-flex flex-column justify-content-center">
                            <span className="text-muted small fw-bold text-uppercase mb-2">Demand Score</span>
                            <div className="d-flex align-items-end gap-2">
                                <h3 className="fw-bold mb-0 text-success">{demand.demand_score || 0}/100</h3>
                                <span className="material-symbols-outlined text-success mb-1">trending_up</span>
                            </div>
                            <span className="text-muted small mt-2">Global Market Demand</span>
                        </div>
                    </div>
                    <div className="col-lg-3 col-md-6">
                        <div className="panel-card d-flex flex-column justify-content-center">
                            <span className="text-muted small fw-bold text-uppercase mb-2">Supply Score</span>
                            <div className="d-flex align-items-end gap-2">
                                <h3 className="fw-bold mb-0 text-warning">{supply.supply_score || 0}/100</h3>
                            </div>
                            <span className="text-muted small mt-2">Global Stock Availability</span>
                        </div>
                    </div>
                    <div className="col-lg-3 col-md-6">
                        <div className="panel-card d-flex flex-column justify-content-center">
                            <span className="text-muted small fw-bold text-uppercase mb-2">Price Volatility</span>
                            <div className="d-flex align-items-end gap-2">
                                <h3 className="fw-bold mb-0 text-danger">{price.volatility || 0}%</h3>
                            </div>
                            <span className="text-muted small mt-2">30-Day Fluctuation Risk</span>
                        </div>
                    </div>
                </div>

                <div className="row g-4 mb-4 fade-up" style={{ animationDelay: '0.2s' }}>
                    <div className="col-lg-8">
                        <div className="panel-card h-100">
                            <div className="d-flex justify-content-between align-items-center mb-4">
                                <h5 className="fw-bold mb-0">Price History</h5>
                                <select className="form-select w-auto" value={historyDays} onChange={e => setHistoryDays(parseInt(e.target.value))}>
                                    <option value={7}>7 Days</option>
                                    <option value={30}>30 Days</option>
                                    <option value={90}>90 Days</option>
                                    <option value={365}>1 Year</option>
                                </select>
                            </div>
                            <div style={{ height: '300px' }}>
                                <Line data={chartData} options={chartOptions} />
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-lg-4">
                        <div className="panel-card h-100">
                            <h5 className="fw-bold mb-4">Price Analysis</h5>
                            <ul className="list-group list-group-flush">
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Open</span>
                                    <span className="fw-bold">{formatCurrency(price.open_price || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Close</span>
                                    <span className="fw-bold">{formatCurrency(price.close_price || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">High</span>
                                    <span className="fw-bold text-success">{formatCurrency(price.high || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Low</span>
                                    <span className="fw-bold text-danger">{formatCurrency(price.low || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Average</span>
                                    <span className="fw-bold">{formatCurrency(price.average || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Market Trend</span>
                                    <span className={`badge bg-${price.trend === 'Up' ? 'success' : price.trend === 'Down' ? 'danger' : 'secondary'}`}>
                                        {price.trend || 'Stable'}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div className="row g-4 fade-up" style={{ animationDelay: '0.3s' }}>
                    <div className="col-lg-4">
                        <div className="panel-card h-100">
                            <h5 className="fw-bold mb-4">Market Analysis</h5>
                            <ul className="list-group list-group-flush">
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Global Demand</span>
                                    <span className="fw-bold">{formatNumber(market.global_demand || 0)} {commodity.unit}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Global Supply</span>
                                    <span className="fw-bold">{formatNumber(market.global_supply || 0)} {commodity.unit}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Market Share</span>
                                    <span className="fw-bold">{market.market_share || 0}%</span>
                                </li>
                            </ul>
                            
                            <div className="mt-4">
                                <h6 className="fw-bold text-muted small text-uppercase">Top Exporting Countries</h6>
                                <div className="d-flex flex-wrap gap-2 mt-2">
                                    {market.top_exporting_countries?.map((c: string, idx: number) => (
                                        <span key={idx} className="badge bg-light-primary text-primary">{c}</span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-lg-4">
                        <div className="panel-card h-100">
                            <h5 className="fw-bold mb-4">Demand Insights</h5>
                            <ul className="list-group list-group-flush">
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Current Demand</span>
                                    <span className="fw-bold">{formatNumber(demand.current_demand || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Demand Growth</span>
                                    <span className="fw-bold text-success">+{demand.demand_growth || 0}%</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Consumption Trend</span>
                                    <span className="fw-bold">{demand.consumption_trend || 'Unknown'}</span>
                                </li>
                            </ul>

                            <div className="mt-4">
                                <h6 className="fw-bold text-muted small text-uppercase">Emerging Markets</h6>
                                <div className="d-flex flex-wrap gap-2 mt-2">
                                    {demand.emerging_markets?.map((c: string, idx: number) => (
                                        <span key={idx} className="badge bg-light-success text-success">{c}</span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-lg-4">
                        <div className="panel-card h-100">
                            <h5 className="fw-bold mb-4">Supply Insights</h5>
                            <ul className="list-group list-group-flush">
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Production Volume</span>
                                    <span className="fw-bold">{formatNumber(supply.production_volume || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Stock Level</span>
                                    <span className="fw-bold">{formatNumber(supply.stock_level || 0)}</span>
                                </li>
                                <li className="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                    <span className="text-muted">Supply Growth</span>
                                    <span className="fw-bold">{supply.supply_growth || 0}%</span>
                                </li>
                            </ul>

                            <div className="mt-4">
                                <h6 className="fw-bold text-muted small text-uppercase">Major Producers</h6>
                                <div className="d-flex flex-wrap gap-2 mt-2">
                                    {supply.major_producers?.map((c: string, idx: number) => (
                                        <span key={idx} className="badge bg-light-warning text-warning">{c}</span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="row mt-4 fade-up" style={{ animationDelay: '0.4s' }}>
                    <div className="col-12">
                        <div className="panel-card" style={{ transform: 'none', transition: 'none' }}>
                            <div className="d-flex justify-content-between align-items-center mb-4">
                                <h5 className="fw-bold mb-0">Global Price Distribution</h5>
                                <span className="badge bg-light text-dark">Data across {commodity.country_prices?.length || 0} countries</span>
                            </div>
                            <div className="table-responsive">
                                <table className="table custom-table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Country</th>
                                            <th>Selling Price</th>
                                            <th>Buying Price</th>
                                            <th>Import Cost</th>
                                            <th>Export Cost</th>
                                            <th>Shipping Cost</th>
                                            <th>Est. Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {commodity.country_prices && commodity.country_prices.length > 0 ? (
                                            commodity.country_prices.map((cp: any) => (
                                                <tr key={cp.id}>
                                                    <td>
                                                        <div className="d-flex align-items-center gap-2">
                                                            <div className="d-flex align-items-center justify-content-center bg-light rounded overflow-hidden" style={{ width: '30px', height: '24px' }}>
                                                                {cp.country?.iso_code && (
                                                                    <img 
                                                                        src={`https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/flags/4x3/${cp.country.iso_code.toLowerCase()}.svg`} 
                                                                        alt={cp.country.iso_code} 
                                                                        style={{ width: '100%', height: '100%', objectFit: 'cover' }} 
                                                                        onError={(e) => {
                                                                            e.currentTarget.style.display = 'none';
                                                                            if (e.currentTarget.nextElementSibling) {
                                                                                (e.currentTarget.nextElementSibling as HTMLElement).style.display = 'block';
                                                                            }
                                                                        }}
                                                                    />
                                                                )}
                                                                <span style={{ fontSize: '14px', display: cp.country?.iso_code ? 'none' : 'block' }}>
                                                                    {cp.country?.flag || '🏳️'}
                                                                </span>
                                                            </div>
                                                            <span className="fw-bold">{cp.country?.country_name || 'Unknown'}</span>
                                                        </div>
                                                    </td>
                                                    <td className="fw-bold text-success">{formatCurrency(cp.selling_price || 0)}</td>
                                                    <td>{formatCurrency(cp.buying_price || 0)}</td>
                                                    <td>{formatCurrency(cp.import_cost || 0)}</td>
                                                    <td>{formatCurrency(cp.export_cost || 0)}</td>
                                                    <td>{formatCurrency(cp.shipping_cost || 0)}</td>
                                                    <td className="fw-bold text-primary">{formatCurrency(cp.estimated_profit || 0)}</td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan={7} className="text-center py-4 text-muted">
                                                    No global price data available for this commodity.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function TradeSimulation() {
    const [countries, setCountries] = useState<any[]>([]);
    const [commodities, setCommodities] = useState<any[]>([]);
    const [history, setHistory] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState<any>(null);

    const [form, setForm] = useState({
        commodity_id: '',
        origin_country_id: '',
        destination_country_id: '',
        quantity: 1,
        container_type: '20ft Standard',
        shipping_cost: 0,
        insurance: 0,
        import_tax: 0,
        export_tax: 0,
        currency: 'USD'
    });

    useEffect(() => {
        // Load options
        axios.get('/api/countries/list').then(res => setCountries(res.data));
        axios.get('/api/commodities/list').then(res => setCommodities(res.data));
        loadHistory();
    }, []);

    const loadHistory = () => {
        axios.get('/api/trade/simulation').then(res => setHistory(res.data.data || []));
    };

    const handleSimulate = (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        axios.post('/api/trade/simulation', form)
            .then(res => {
                setResult(res.data.data);
                loadHistory();
            })
            .catch(err => {
                alert('Error running simulation: ' + err.message);
            })
            .finally(() => setLoading(false));
    };

    const formatCurrency = (val: number) => new Intl.NumberFormat('en-US', { style: 'currency', currency: form.currency }).format(val || 0);

    return (
        <AuthenticatedLayout
            header={
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Profit Simulation</h2>
                        <p className="text-muted mb-0">Simulate international trade scenarios and calculate ROI instantly.</p>
                    </div>
                    <Link href={route('trade.index')} className="btn btn-outline-secondary rounded-pill px-4">Back to Dashboard</Link>
                </div>
            }
        >
            <Head title="Profit Simulation" />

            <div className="container-fluid py-4">
                <div className="row g-4">
                    {/* Left Column: Input Form */}
                    <div className="col-lg-5 fade-up" style={{ animationDelay: '0.1s' }}>
                        <div className="panel-card h-100">
                            <h5 className="fw-bold mb-4">Simulation Parameters</h5>
                            <form onSubmit={handleSimulate}>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Commodity</label>
                                    <select className="form-select form-control-glass" value={form.commodity_id} onChange={e => setForm({...form, commodity_id: e.target.value})} required>
                                        <option value="">Select Commodity</option>
                                        {commodities.map(c => <option key={c.id} value={c.id}>{c.commodity_name}</option>)}
                                    </select>
                                </div>
                                <div className="row g-3 mb-3">
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Origin Country</label>
                                        <select className="form-select form-control-glass" value={form.origin_country_id} onChange={e => setForm({...form, origin_country_id: e.target.value})} required>
                                            <option value="">Select Origin</option>
                                            {countries.map(c => <option key={c.id} value={c.id}>{c.country_name}</option>)}
                                        </select>
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Destination Country</label>
                                        <select className="form-select form-control-glass" value={form.destination_country_id} onChange={e => setForm({...form, destination_country_id: e.target.value})} required>
                                            <option value="">Select Destination</option>
                                            {countries.map(c => <option key={c.id} value={c.id}>{c.country_name}</option>)}
                                        </select>
                                    </div>
                                </div>
                                <div className="row g-3 mb-3">
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Quantity</label>
                                        <input type="number" className="form-control form-control-glass" min="1" step="0.01" value={form.quantity} onChange={e => setForm({...form, quantity: parseFloat(e.target.value)})} required />
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Container Type</label>
                                        <select className="form-select form-control-glass" value={form.container_type} onChange={e => setForm({...form, container_type: e.target.value})}>
                                            <option value="20ft Standard">20ft Standard</option>
                                            <option value="40ft Standard">40ft Standard</option>
                                            <option value="40ft HC">40ft High Cube</option>
                                            <option value="Reefer">Refrigerated</option>
                                            <option value="Bulk">Bulk Cargo</option>
                                        </select>
                                    </div>
                                </div>
                                <hr className="my-4 text-muted" />
                                <h6 className="fw-bold text-primary mb-3">Costs & Taxes</h6>
                                <div className="row g-3 mb-3">
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Shipping Cost</label>
                                        <div className="input-group">
                                            <span className="input-group-text bg-light border-0">$</span>
                                            <input type="number" className="form-control form-control-glass" min="0" value={form.shipping_cost} onChange={e => setForm({...form, shipping_cost: parseFloat(e.target.value)})} required />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Insurance</label>
                                        <div className="input-group">
                                            <span className="input-group-text bg-light border-0">$</span>
                                            <input type="number" className="form-control form-control-glass" min="0" value={form.insurance} onChange={e => setForm({...form, insurance: parseFloat(e.target.value)})} required />
                                        </div>
                                    </div>
                                </div>
                                <div className="row g-3 mb-4">
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Export Tax (Origin)</label>
                                        <div className="input-group">
                                            <span className="input-group-text bg-light border-0">$</span>
                                            <input type="number" className="form-control form-control-glass" min="0" value={form.export_tax} onChange={e => setForm({...form, export_tax: parseFloat(e.target.value)})} required />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label text-muted small fw-bold">Import Tax (Dest)</label>
                                        <div className="input-group">
                                            <span className="input-group-text bg-light border-0">$</span>
                                            <input type="number" className="form-control form-control-glass" min="0" value={form.import_tax} onChange={e => setForm({...form, import_tax: parseFloat(e.target.value)})} required />
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" className="btn btn-primary w-100 rounded-pill py-2 shadow-sm fw-bold" disabled={loading}>
                                    {loading ? 'Running AI Simulation...' : 'Calculate Profit & ROI'}
                                </button>
                            </form>
                        </div>
                    </div>

                    {/* Right Column: Result */}
                    <div className="col-lg-7 fade-up" style={{ animationDelay: '0.2s' }}>
                        {result ? (
                            <div className="panel-card h-100" style={{ borderTop: `4px solid ${result.profit > 0 ? 'var(--success)' : 'var(--danger)'}` }}>
                                <div className="d-flex justify-content-between align-items-center mb-4">
                                    <h5 className="fw-bold mb-0">Simulation Result</h5>
                                    <span className={`badge bg-${result.profit > 0 ? 'success' : 'danger'}`}>
                                        {result.profit > 0 ? 'Profitable' : 'Loss'}
                                    </span>
                                </div>
                                
                                <div className="row g-4 mb-4 text-center">
                                    <div className="col-md-4">
                                        <div className="p-3 bg-light rounded-4">
                                            <h6 className="text-muted mb-1">Total Revenue</h6>
                                            <h4 className="fw-bold text-dark mb-0">{formatCurrency(result.revenue)}</h4>
                                        </div>
                                    </div>
                                    <div className="col-md-4">
                                        <div className="p-3 bg-light rounded-4">
                                            <h6 className="text-muted mb-1">Total Cost</h6>
                                            <h4 className="fw-bold text-danger mb-0">{formatCurrency(result.cost)}</h4>
                                        </div>
                                    </div>
                                    <div className="col-md-4">
                                        <div className="p-3 bg-light-primary rounded-4">
                                            <h6 className="text-primary mb-1">Net Profit</h6>
                                            <h4 className="fw-bold text-primary mb-0">{formatCurrency(result.profit)}</h4>
                                        </div>
                                    </div>
                                </div>

                                <div className="row g-4 mb-4">
                                    <div className="col-6">
                                        <div className="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span className="text-muted">Profit Margin</span>
                                            <span className="fw-bold fs-5">{Number(result.margin).toFixed(2)}%</span>
                                        </div>
                                    </div>
                                    <div className="col-6">
                                        <div className="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span className="text-muted">ROI</span>
                                            <span className={`fw-bold fs-5 ${result.roi > 20 ? 'text-success' : 'text-warning'}`}>{Number(result.roi).toFixed(2)}%</span>
                                        </div>
                                    </div>
                                    <div className="col-12">
                                        <div className="d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span className="text-muted">Break Even Point</span>
                                            <span className="fw-bold">{Number(result.break_even_point).toFixed(2)} units</span>
                                        </div>
                                    </div>
                                </div>

                                <div className="p-3 bg-soft-info rounded-3">
                                    <h6 className="fw-bold text-info mb-1"><span className="material-symbols-outlined align-middle me-1" style={{ fontSize: '18px' }}>lightbulb</span> AI Recommendation</h6>
                                    <p className="small text-muted mb-0">
                                        {result.roi > 30 ? "Excellent trade opportunity. The ROI is exceptional and margins are solid. Recommended to proceed with scaling."
                                        : result.roi > 10 ? "Moderate trade opportunity. Ensure shipping delays and hidden costs are minimized to protect margins."
                                        : "High risk trade. Profit margins are too thin to absorb potential supply chain shocks. Consider alternative destinations or bulk shipping discounts."}
                                    </p>
                                </div>
                            </div>
                        ) : (
                            <div className="panel-card h-100 d-flex flex-column justify-content-center align-items-center text-center p-5 text-muted">
                                <span className="material-symbols-outlined mb-3" style={{ fontSize: '64px', opacity: 0.2 }}>monitoring</span>
                                <h4>No Simulation Data</h4>
                                <p>Fill out the parameters on the left and calculate to see profit projections, margins, and AI trade recommendations.</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* History Section */}
                {history.length > 0 && (
                    <div className="panel-card mt-4 fade-up" style={{ animationDelay: '0.3s' }}>
                        <h5 className="fw-bold mb-4">Recent Simulations</h5>
                        <div className="table-responsive bg-white rounded-4 shadow-sm border-0 overflow-hidden">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Route</th>
                                        <th>Commodity</th>
                                        <th>Revenue</th>
                                        <th>Cost</th>
                                        <th>Profit</th>
                                        <th>Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {history.map((h: any) => (
                                        <tr key={h.id}>
                                            <td className="text-muted small">{new Date(h.created_at).toLocaleDateString()}</td>
                                            <td>
                                                <div className="d-flex align-items-center gap-2">
                                                    <span className="fw-bold">{h.origin_country?.country_name}</span>
                                                    <span className="material-symbols-outlined text-muted" style={{ fontSize: '16px' }}>arrow_forward</span>
                                                    <span className="fw-bold">{h.destination_country?.country_name}</span>
                                                </div>
                                            </td>
                                            <td>{h.commodity?.commodity_name}</td>
                                            <td>${Number(h.revenue).toLocaleString()}</td>
                                            <td className="text-danger">${Number(h.cost).toLocaleString()}</td>
                                            <td className="text-success fw-bold">${Number(h.profit).toLocaleString()}</td>
                                            <td><span className={`badge bg-${h.margin > 15 ? 'success' : 'warning'}`}>{Number(h.margin).toFixed(1)}%</span></td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

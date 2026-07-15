import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function Risk() {
    const [data, setData] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get('/api/trade/risk').then(res => {
            setData(res.data.data || []);
            setLoading(false);
        });
    }, []);

    return (
        <AuthenticatedLayout
            header={
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Risk Scoring Engine</h2>
                        <p className="text-muted mb-0">AI-driven calculation of global trade risks.</p>
                    </div>
                    <Link href={route('trade.index')} className="btn btn-outline-secondary rounded-pill px-4">Back to Dashboard</Link>
                </div>
            }
        >
            <Head title="Risk Scoring Engine" />

            <div className="container-fluid py-4">
                <div className="row g-4 mb-4">
                    <div className="col-12 fade-up">
                        <div className="panel-card bg-primary-subtle border-primary-subtle p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 className="fw-bold text-primary mb-2">How it works</h4>
                                <p className="text-muted mb-0">The Risk Scoring Engine calculates a country's total risk score based on real-time data integration: <br/><strong>Risk Score = (Weather + Inflation + Exchange Rate + News Sentiment) / 4</strong></p>
                            </div>
                            <span className="material-symbols-outlined text-primary" style={{ fontSize: '64px', opacity: 0.2 }}>analytics</span>
                        </div>
                    </div>
                </div>

                <div className="panel-card fade-up text-center py-4" style={{ animationDelay: '0.1s' }}>
                    <div className="d-flex justify-content-between align-items-center mb-4 px-3">
                        <h5 className="fw-bold mb-0">Global Risk Monitor</h5>
                        <div className="d-flex gap-2">
                            <span className="badge bg-success">Low Risk (0-30)</span>
                            <span className="badge bg-warning text-dark">Medium Risk (31-60)</span>
                            <span className="badge bg-danger">High Risk (61-100)</span>
                        </div>
                    </div>

                    {loading ? (
                        <div className="py-5 text-center">
                            <div className="spinner-border text-primary" role="status"></div>
                            <p className="text-muted mt-3">Running AI Risk Calculation Engine...</p>
                        </div>
                    ) : (
                        <div className="table-responsive bg-white rounded-4 shadow-sm border-0 overflow-hidden">
                            <table className="table table-hover align-middle mb-0 text-start">
                                <thead className="table-light">
                                    <tr>
                                        <th>Country</th>
                                        <th>Weather Risk</th>
                                        <th>Inflation Risk</th>
                                        <th>Exchange Risk</th>
                                        <th>News / Political Risk</th>
                                        <th className="text-end">Total Risk Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.map((item: any) => (
                                        <tr key={item.id}>
                                            <td>
                                                <div className="d-flex align-items-center gap-3">
                                                    <div className="rounded overflow-hidden shadow-sm d-flex justify-content-center align-items-center" style={{ width: '40px', height: '28px', background: '#f8f9fa' }}>
                                                        {item.country?.iso_code ? (
                                                            <img src={`https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/${item.country.iso_code.toLowerCase()}.svg`} alt={item.country.name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                                        ) : (
                                                            <span className="material-symbols-outlined text-muted" style={{ fontSize: '16px' }}>flag</span>
                                                        )}
                                                    </div>
                                                    <span className="fw-bold">{item.country?.name}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span className={`text-${item.weather_risk > 70 ? 'danger' : item.weather_risk > 40 ? 'warning' : 'success'}`}>
                                                    {item.weather_risk}/100
                                                </span>
                                            </td>
                                            <td>
                                                <span className={`text-${item.economic_risk > 70 ? 'danger' : item.economic_risk > 40 ? 'warning' : 'success'}`}>
                                                    {item.economic_risk}/100
                                                </span>
                                            </td>
                                            <td>
                                                <span className={`text-${item.currency_risk > 70 ? 'danger' : item.currency_risk > 40 ? 'warning' : 'success'}`}>
                                                    {item.currency_risk}/100
                                                </span>
                                            </td>
                                            <td>
                                                <span className={`text-${item.political_risk > 70 ? 'danger' : item.political_risk > 40 ? 'warning' : 'success'}`}>
                                                    {item.political_risk}/100
                                                </span>
                                            </td>
                                            <td className="text-end">
                                                <span className={`badge px-3 py-2 fs-6 bg-${item.total_risk_score > 60 ? 'danger' : item.total_risk_score > 30 ? 'warning' : 'success'}`}>
                                                    {item.total_risk_score} - {item.total_risk_score > 60 ? 'High Risk' : item.total_risk_score > 30 ? 'Medium Risk' : 'Low Risk'}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                    {data.length === 0 && (
                                        <tr>
                                            <td colSpan={6} className="text-center py-5 text-muted">No data available.</td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function CountryShow({ countryId }: { countryId: string }) {
    const [country, setCountry] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('overview');

    useEffect(() => {
        const fetchCountry = async () => {
            try {
                const res = await axios.get(`/api/countries/${countryId}`);
                setCountry(res.data);
            } catch (error) {
                console.error("Error fetching country detail", error);
            } finally {
                setLoading(false);
            }
        };
        fetchCountry();
    }, [countryId]);

    if (loading) {
        return (
            <AuthenticatedLayout>
                <div className="d-flex justify-content-center align-items-center" style={{ height: '80vh' }}>
                    <div className="spinner-border text-primary" role="status"></div>
                </div>
            </AuthenticatedLayout>
        );
    }

    if (!country) return null;

    return (
        <AuthenticatedLayout>
            <Head title={`Country Intelligence - ${country.country_name}`} />

            <div className="container-fluid p-4">
                <div className="d-flex align-items-center gap-2 mb-4 fade-up">
                    <Link href="/intelligence/countries" className="btn btn-sm btn-light border rounded-pill d-flex align-items-center">
                        <span className="material-symbols-outlined" style={{ fontSize: '18px' }}>arrow_back</span>
                    </Link>
                    <div className="d-flex align-items-center gap-3 ms-2">
                        <div className="d-flex align-items-center justify-content-center bg-white border rounded shadow-sm" style={{ width: '56px', height: '42px', fontSize: '28px' }}>
                            {country.flag}
                        </div>
                        <div>
                            <h2 className="fw-bold mb-0" style={{ color: 'var(--text-primary)' }}>{country.country_name} Intelligence</h2>
                            <p className="text-muted mb-0">{country.region} • ISO: {country.iso_code}</p>
                        </div>
                    </div>
                </div>

                <div className="row g-4 mb-4">
                    <div className="col-xl-3 col-md-6 fade-up" style={{ animationDelay: '0.1s' }}>
                        <div className="panel-card text-center py-4">
                            <span className="material-symbols-outlined text-danger mb-2" style={{ fontSize: '32px' }}>warning</span>
                            <h6 className="text-muted fw-bold">Risk Score</h6>
                            <h2 className="fw-bold text-danger mb-0">{country.risk_score}/100</h2>
                        </div>
                    </div>
                    <div className="col-xl-3 col-md-6 fade-up" style={{ animationDelay: '0.2s' }}>
                        <div className="panel-card text-center py-4">
                            <span className="material-symbols-outlined text-success mb-2" style={{ fontSize: '32px' }}>trending_up</span>
                            <h6 className="text-muted fw-bold">Opportunity Score</h6>
                            <h2 className="fw-bold text-success mb-0">{country.opportunity_score}/100</h2>
                        </div>
                    </div>
                    <div className="col-xl-3 col-md-6 fade-up" style={{ animationDelay: '0.3s' }}>
                        <div className="panel-card text-center py-4">
                            <span className="material-symbols-outlined text-primary mb-2" style={{ fontSize: '32px' }}>account_balance</span>
                            <h6 className="text-muted fw-bold">GDP (Billion)</h6>
                            <h2 className="fw-bold text-primary mb-0">${country.economy ? (country.economy.gdp / 1000000000).toFixed(2) : '-'}</h2>
                        </div>
                    </div>
                    <div className="col-xl-3 col-md-6 fade-up" style={{ animationDelay: '0.4s' }}>
                        <div className="panel-card text-center py-4">
                            <span className="material-symbols-outlined text-info mb-2" style={{ fontSize: '32px' }}>swap_horiz</span>
                            <h6 className="text-muted fw-bold">Trade Balance (Billion)</h6>
                            <h2 className={`fw-bold mb-0 ${country.trade_statistics && country.trade_statistics.length > 0 && country.trade_statistics[0].trade_balance > 0 ? 'text-success' : 'text-danger'}`}>
                                ${country.trade_statistics && country.trade_statistics.length > 0 ? (country.trade_statistics[0].trade_balance / 1000000000).toFixed(2) : '-'}
                            </h2>
                        </div>
                    </div>
                </div>

                <div className="panel-card fade-up mb-4" style={{ animationDelay: '0.5s' }}>
                    <ul className="nav nav-tabs border-bottom-0 mb-4 gap-3">
                        {['overview', 'economy', 'trade', 'risk', 'opportunity', 'logistics'].map(tab => (
                            <li className="nav-item" key={tab}>
                                <button 
                                    className={`nav-link border-0 fw-bold pb-3 ${activeTab === tab ? 'active border-bottom border-primary border-3 text-primary bg-transparent' : 'text-muted'}`}
                                    onClick={() => setActiveTab(tab)}
                                    style={{ textTransform: 'capitalize' }}
                                >
                                    {tab}
                                </button>
                            </li>
                        ))}
                    </ul>

                    {activeTab === 'overview' && (
                        <div className="row g-4">
                            <div className="col-md-6">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">General Information</h6>
                                <table className="table table-borderless table-sm">
                                    <tbody>
                                        <tr><td className="text-muted w-50">Capital</td><td className="fw-medium">{country.capital}</td></tr>
                                        <tr><td className="text-muted">Population</td><td className="fw-medium">{parseInt(country.population).toLocaleString()}</td></tr>
                                        <tr><td className="text-muted">Currency</td><td className="fw-medium">{country.currency_name} ({country.currency_code})</td></tr>
                                        <tr><td className="text-muted">Timezone</td><td className="fw-medium">{country.timezone}</td></tr>
                                        <tr><td className="text-muted">Language</td><td className="fw-medium">{country.language}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div className="col-md-6">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Macro Indicators</h6>
                                <table className="table table-borderless table-sm">
                                    <tbody>
                                        <tr><td className="text-muted w-50">GDP Growth</td><td className="fw-medium text-success">{country.economy?.gdp_growth}%</td></tr>
                                        <tr><td className="text-muted">Inflation Rate</td><td className="fw-medium text-danger">{country.economy?.inflation_rate}%</td></tr>
                                        <tr><td className="text-muted">Interest Rate</td><td className="fw-medium">{country.economy?.interest_rate}%</td></tr>
                                        <tr><td className="text-muted">Exchange Rate (vs USD)</td><td className="fw-medium">{country.economy?.exchange_rate}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                    {activeTab === 'risk' && country.risk && (
                        <div className="row g-4">
                            <div className="col-12">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Risk Breakdown (Higher is worse)</h6>
                            </div>
                            {[
                                { label: 'Political Risk', value: country.risk.political_risk },
                                { label: 'Economic Risk', value: country.risk.economic_risk },
                                { label: 'Natural Disaster', value: country.risk.natural_disaster_risk },
                                { label: 'War & Security', value: country.risk.war_risk },
                                { label: 'Trade Restriction', value: country.risk.trade_restriction_risk },
                                { label: 'Supply Chain', value: country.risk.supply_chain_risk },
                            ].map((risk, idx) => (
                                <div className="col-md-4" key={idx}>
                                    <div className="p-3 border rounded-3 bg-light">
                                        <div className="d-flex justify-content-between mb-2">
                                            <span className="fw-bold text-secondary">{risk.label}</span>
                                            <span className={`fw-bold ${risk.value > 70 ? 'text-danger' : (risk.value > 40 ? 'text-warning' : 'text-success')}`}>{risk.value}</span>
                                        </div>
                                        <div className="progress" style={{ height: '8px' }}>
                                            <div className={`progress-bar ${risk.value > 70 ? 'bg-danger' : (risk.value > 40 ? 'bg-warning' : 'bg-success')}`} style={{ width: `${risk.value}%` }}></div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                    
                    {activeTab === 'opportunity' && country.opportunity && (
                        <div className="row g-4">
                            <div className="col-12">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Opportunity Analysis</h6>
                            </div>
                            <div className="col-md-4">
                                <div className="p-3 border rounded-3 bg-success-subtle border-success-subtle">
                                    <h6 className="text-success fw-bold">Expected Profit Margin</h6>
                                    <h3 className="fw-bold text-success mb-0">{country.opportunity.expected_profit_margin}%</h3>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <div className="p-3 border rounded-3 bg-primary-subtle border-primary-subtle">
                                    <h6 className="text-primary fw-bold">Market Growth</h6>
                                    <h3 className="fw-bold text-primary mb-0">{country.opportunity.market_growth}%</h3>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <div className="p-3 border rounded-3 bg-info-subtle border-info-subtle">
                                    <h6 className="text-info fw-bold">Competitor Level</h6>
                                    <h3 className="fw-bold text-info mb-0">{country.opportunity.competitor_level}</h3>
                                </div>
                            </div>
                            
                            <div className="col-12 mt-4">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Recommended Commodities</h6>
                                <div className="d-flex gap-2 flex-wrap">
                                    {JSON.parse(country.opportunity.recommended_commodities || '[]').map((item: string, idx: number) => (
                                        <span key={idx} className="badge bg-light text-primary border px-3 py-2 fs-6">{item}</span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}

                    {(activeTab !== 'overview' && activeTab !== 'risk' && activeTab !== 'opportunity') && (
                        <div className="py-5 text-center text-muted">
                            <span className="material-symbols-outlined mb-3" style={{ fontSize: '48px', opacity: 0.5 }}>construction</span>
                            <h5>Module Under Development</h5>
                            <p>This section is being synchronized with the global database.</p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

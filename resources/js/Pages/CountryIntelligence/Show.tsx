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
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Global Country Dashboard</h6>
                                <table className="table table-borderless table-sm">
                                    <tbody>
                                        <tr><td className="text-muted w-50">GDP</td><td className="fw-medium text-primary">{country.macro_indicators?.gdp}</td></tr>
                                        <tr><td className="text-muted">Inflation</td><td className="fw-medium text-danger">{country.macro_indicators?.inflation?.rate}</td></tr>
                                        <tr><td className="text-muted">Population</td><td className="fw-medium">{country.macro_indicators?.population}</td></tr>
                                        <tr><td className="text-muted">Region</td><td className="fw-medium">{country.macro_indicators?.region}</td></tr>
                                        <tr><td className="text-muted">Languages</td><td className="fw-medium">{country.macro_indicators?.languages}</td></tr>
                                        <tr><td className="text-muted">Exports</td><td className="fw-medium text-success">{country.macro_indicators?.exports}</td></tr>
                                        <tr><td className="text-muted">Imports</td><td className="fw-medium text-warning">{country.macro_indicators?.imports}</td></tr>
                                        <tr><td className="text-muted">Currency</td><td className="fw-medium">{country.macro_indicators?.currency}</td></tr>
                                        <tr><td className="text-muted">Exchange Rate</td><td className="fw-medium">{country.macro_indicators?.exchange_rate}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div className="col-md-6">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Live Weather & Environment</h6>
                                <div className="p-3 bg-light rounded-4">
                                    <div className="d-flex align-items-center gap-4 mb-3">
                                        <div className="text-center">
                                            <span className="material-symbols-outlined text-info" style={{ fontSize: '48px' }}>
                                                {country.macro_indicators?.weather?.condition === 'Clear' ? 'sunny' : 
                                                 country.macro_indicators?.weather?.condition === 'Rain' ? 'rainy' : 
                                                 (country.macro_indicators?.weather?.condition === 'Storm' || country.macro_indicators?.weather?.condition === 'Typhoon') ? 'thunderstorm' : 'cloud'}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 className="fw-bold mb-0">{country.macro_indicators?.weather?.temperature}</h3>
                                            <p className="text-muted mb-0">{country.macro_indicators?.weather?.condition}</p>
                                        </div>
                                    </div>
                                    <div className="row g-2">
                                        <div className="col-6">
                                            <div className="border rounded-3 p-2 bg-white text-center">
                                                <span className="text-muted small d-block mb-1">Curah Hujan</span>
                                                <span className="fw-bold text-primary">{country.macro_indicators?.weather?.rainfall}</span>
                                            </div>
                                        </div>
                                        <div className="col-6">
                                            <div className="border rounded-3 p-2 bg-white text-center">
                                                <span className="text-muted small d-block mb-1">Kec. Angin</span>
                                                <span className="fw-bold text-info">{country.macro_indicators?.weather?.wind_speed}</span>
                                            </div>
                                        </div>
                                        <div className="col-12 mt-2">
                                            <div className="border rounded-3 p-2 bg-white d-flex justify-content-between align-items-center">
                                                <span className="text-muted small">Risiko Badai</span>
                                                <span className={`badge ${parseInt(country.macro_indicators?.weather?.storm_risk) > 50 ? 'bg-danger' : 'bg-success'}`}>{country.macro_indicators?.weather?.storm_risk}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

                    {activeTab === 'economy' && country.economy && (
                        <div className="row g-4 fade-up">
                            <div className="col-12">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Macroeconomic Indicators</h6>
                            </div>
                            <div className="col-md-3">
                                <div className="p-3 border rounded-3 bg-light">
                                    <span className="text-muted small d-block mb-1">GDP Growth (Annual)</span>
                                    <h4 className={`fw-bold mb-0 ${country.economy.gdp_growth > 0 ? 'text-success' : 'text-danger'}`}>
                                        {country.economy.gdp_growth}%
                                    </h4>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="p-3 border rounded-3 bg-light">
                                    <span className="text-muted small d-block mb-1">Interest Rate</span>
                                    <h4 className="fw-bold text-primary mb-0">{country.economy.interest_rate}%</h4>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="p-3 border rounded-3 bg-light">
                                    <span className="text-muted small d-block mb-1">Unemployment Rate</span>
                                    <h4 className="fw-bold text-warning mb-0">{country.economy.unemployment_rate}%</h4>
                                </div>
                            </div>
                            <div className="col-md-3">
                                <div className="p-3 border rounded-3 bg-light">
                                    <span className="text-muted small d-block mb-1">Purchasing Power Parity</span>
                                    <h4 className="fw-bold text-info mb-0">${Number(country.economy.purchasing_power).toLocaleString()}</h4>
                                </div>
                            </div>
                            <div className="col-12 mt-4">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Consumer & Producer Indices</h6>
                                <div className="d-flex gap-4">
                                    <div className="border rounded-3 p-3 flex-fill text-center">
                                        <h2 className="fw-bold mb-1">{country.economy.consumer_price_index}</h2>
                                        <span className="text-muted">CPI (Consumer Price Index)</span>
                                    </div>
                                    <div className="border rounded-3 p-3 flex-fill text-center">
                                        <h2 className="fw-bold mb-1">{country.economy.producer_price_index}</h2>
                                        <span className="text-muted">PPI (Producer Price Index)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {activeTab === 'trade' && (
                        <div className="row g-4 fade-up">
                            <div className="col-12">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Trade Statistics & Policies</h6>
                            </div>
                            
                            <div className="col-md-4">
                                <div className="p-3 border rounded-3 bg-success-subtle border-success-subtle h-100">
                                    <h6 className="text-success fw-bold">Trade Status</h6>
                                    <h3 className="fw-bold text-success mb-0">{country.trade_status || 'Active'}</h3>
                                    <p className="small text-muted mt-2 mb-0">General orientation of national trade.</p>
                                </div>
                            </div>
                            
                            {country.trade_statistics && country.trade_statistics.length > 0 && (
                                <>
                                    <div className="col-md-4">
                                        <div className="p-3 border rounded-3 bg-primary-subtle border-primary-subtle h-100">
                                            <h6 className="text-primary fw-bold">Trade Balance</h6>
                                            <h3 className="fw-bold text-primary mb-0">${Number(country.trade_statistics[0].trade_balance / 1000000).toFixed(2)}M</h3>
                                            <p className="small text-muted mt-2 mb-0">Current annual trade balance.</p>
                                        </div>
                                    </div>
                                    <div className="col-md-4">
                                        <div className="p-3 border rounded-3 bg-info-subtle border-info-subtle h-100">
                                            <h6 className="text-info fw-bold">Average Import Duty</h6>
                                            <h3 className="fw-bold text-info mb-0">{country.trade_statistics[0].import_duty_avg}%</h3>
                                            <p className="small text-muted mt-2 mb-0">Tariff barriers for inbound goods.</p>
                                        </div>
                                    </div>

                                    <div className="col-md-6 mt-4">
                                        <h6 className="fw-bold mb-3 text-secondary">Top Exported Commodities</h6>
                                        <div className="d-flex flex-wrap gap-2">
                                            {JSON.parse(country.trade_statistics[0].top_exported_commodities || '[]').map((item: string, idx: number) => (
                                                <span key={idx} className="badge bg-success-subtle text-success border border-success px-3 py-2">{item}</span>
                                            ))}
                                        </div>
                                    </div>

                                    <div className="col-md-6 mt-4">
                                        <h6 className="fw-bold mb-3 text-secondary">Top Imported Commodities</h6>
                                        <div className="d-flex flex-wrap gap-2">
                                            {JSON.parse(country.trade_statistics[0].top_imported_commodities || '[]').map((item: string, idx: number) => (
                                                <span key={idx} className="badge bg-warning-subtle text-warning border border-warning px-3 py-2">{item}</span>
                                            ))}
                                        </div>
                                    </div>
                                </>
                            )}
                        </div>
                    )}

                    {activeTab === 'logistics' && (
                        <div className="row g-4 fade-up">
                            <div className="col-12">
                                <h6 className="fw-bold mb-3 text-secondary border-bottom pb-2">Logistics & Infrastructure</h6>
                            </div>
                            {country.ports && country.ports.length > 0 ? (
                                <div className="col-12">
                                    <div className="table-responsive">
                                        <table className="table table-hover align-middle border">
                                            <thead className="table-light">
                                                <tr>
                                                    <th>Port Name</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {country.ports.map((port: any, idx: number) => (
                                                    <tr key={idx}>
                                                        <td className="fw-medium">{port.port_name}</td>
                                                        <td><span className="badge bg-light text-dark border">{port.port_type || 'Seaport'}</span></td>
                                                        <td><span className="badge bg-success-subtle text-success border border-success">Operational</span></td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            ) : (
                                <div className="col-12 text-center py-5">
                                    <span className="material-symbols-outlined text-muted mb-3" style={{ fontSize: '48px', opacity: 0.5 }}>directions_boat</span>
                                    <h5 className="text-muted">No Major Ports Data</h5>
                                    <p className="text-muted">Logistics infrastructure data is currently being mapped.</p>
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function CountryCompare() {
    const [countries, setCountries] = useState<any[]>([]);
    const [allCountries, setAllCountries] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [selectedIds, setSelectedIds] = useState<string[]>([]);
    const [showSelector, setShowSelector] = useState(false);

    useEffect(() => {
        // Fetch base country list for selector
        axios.get('/api/countries/list').then(res => {
            setAllCountries(res.data);
            if (res.data.length > 0) {
                setSelectedIds(res.data.slice(0, 3).map((c: any) => c.id));
            }
        });
    }, []);

    useEffect(() => {
        const fetchCompare = async () => {
            if (selectedIds.length === 0) {
                setCountries([]);
                setLoading(false);
                return;
            }
            setLoading(true);
            try {
                const res = await axios.get(`/api/countries/comparison?ids=${selectedIds.join(',')}`);
                setCountries(res.data);
            } catch (error) {
                console.error("Error fetching comparison data", error);
            } finally {
                setLoading(false);
            }
        };
        fetchCompare();
    }, [selectedIds]);

    const toggleCountry = (id: string) => {
        if (selectedIds.includes(id)) {
            setSelectedIds(selectedIds.filter(selId => selId !== id));
        } else {
            if (selectedIds.length >= 5) {
                alert("You can only compare up to 5 countries at once.");
                return;
            }
            setSelectedIds([...selectedIds, id]);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Country Comparison" />

            <div className="container-fluid p-4">
                <div className="d-flex justify-content-between align-items-center mb-4 fade-up" style={{ position: 'relative', zIndex: 50 }}>
                    <div className="d-flex align-items-center gap-2">
                        <Link href="/intelligence/countries" className="btn btn-sm btn-light border rounded-pill d-flex align-items-center">
                            <span className="material-symbols-outlined" style={{ fontSize: '18px' }}>arrow_back</span>
                        </Link>
                        <div>
                            <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Country Comparison</h2>
                            <p className="text-muted mb-0">Compare economic, risk, and opportunity factors side by side.</p>
                        </div>
                    </div>
                    
                    <div className="dropdown position-relative">
                        <button 
                            className="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2 shadow-sm"
                            onClick={() => setShowSelector(!showSelector)}
                        >
                            <span className="material-symbols-outlined">add_circle</span>
                            Select Countries ({selectedIds.length}/5)
                        </button>

                        {showSelector && (
                            <div className="dropdown-menu show shadow-lg border border-light bg-white p-3" style={{ position: 'absolute', right: 0, top: '110%', width: '300px', maxHeight: '400px', overflowY: 'auto', zIndex: 1050 }}>
                                <div className="d-flex justify-content-between align-items-center mb-3">
                                    <h6 className="fw-bold mb-0 text-secondary">Available Countries</h6>
                                    <button className="btn-close" style={{ fontSize: '12px' }} onClick={() => setShowSelector(false)}></button>
                                </div>
                                {allCountries.map(c => (
                                    <div key={c.id} className="form-check mb-2 d-flex align-items-center gap-2 hover-bg-light p-1 rounded">
                                        <input 
                                            className="form-check-input m-0" 
                                            type="checkbox" 
                                            id={`country-${c.id}`} 
                                            checked={selectedIds.includes(c.id)}
                                            onChange={() => toggleCountry(c.id)}
                                            disabled={!selectedIds.includes(c.id) && selectedIds.length >= 5}
                                            style={{ cursor: 'pointer' }}
                                        />
                                        <label className="form-check-label text-truncate m-0 w-100" htmlFor={`country-${c.id}`} style={{ cursor: 'pointer' }}>
                                            <span className="me-2">{c.flag}</span> {c.country_name}
                                        </label>
                                    </div>
                                ))}
                                <hr className="my-2" />
                                <button className="btn btn-sm btn-primary w-100 rounded-pill" onClick={() => setShowSelector(false)}>Apply Comparison</button>
                            </div>
                        )}
                    </div>
                </div>

                <div className="panel-card fade-up" style={{ animationDelay: '0.1s', overflowX: 'auto', position: 'relative', zIndex: 1 }}>
                    {loading ? (
                        <div className="text-center py-5">
                            <div className="spinner-border text-primary" role="status"></div>
                        </div>
                    ) : countries.length === 0 ? (
                        <div className="text-center py-5 text-muted">
                            <span className="material-symbols-outlined mb-2" style={{ fontSize: '48px' }}>public_off</span>
                            <h5>No Countries Selected</h5>
                            <p>Please select up to 5 countries from the menu above to compare.</p>
                        </div>
                    ) : (
                        <table className="table table-bordered align-middle">
                            <thead className="bg-light">
                                <tr>
                                    <th className="text-secondary fw-bold" style={{ width: '20%' }}>Indicator</th>
                                    {countries.map(c => (
                                        <th key={c.id} className="text-center" style={{ width: `${80 / countries.length}%` }}>
                                            <div className="d-flex flex-column align-items-center justify-content-center gap-2 py-2">
                                                <div className="bg-white border rounded shadow-sm d-flex align-items-center justify-content-center" style={{ width: '48px', height: '36px', fontSize: '24px' }}>
                                                    {c.flag}
                                                </div>
                                                <h5 className="fw-bold mb-0 text-primary">{c.country_name}</h5>
                                                <span className="badge bg-secondary-subtle text-secondary">{c.region}</span>
                                            </div>
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                <tr className="bg-light">
                                    <td colSpan={countries.length + 1} className="fw-bold text-muted text-uppercase" style={{ fontSize: '12px', letterSpacing: '1px' }}>Global Scores</td>
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Risk Score</td>
                                    {countries.map(c => (
                                        <td key={c.id} className="text-center">
                                            <span className={`fw-bold fs-5 ${c.risk_score > 70 ? 'text-danger' : (c.risk_score > 40 ? 'text-warning' : 'text-success')}`}>{c.risk_score}</span>
                                        </td>
                                    ))}
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Opportunity Score</td>
                                    {countries.map(c => (
                                        <td key={c.id} className="text-center">
                                            <span className={`fw-bold fs-5 ${c.opportunity_score > 70 ? 'text-success' : (c.opportunity_score > 40 ? 'text-info' : 'text-secondary')}`}>{c.opportunity_score}</span>
                                        </td>
                                    ))}
                                </tr>

                                <tr className="bg-light">
                                    <td colSpan={countries.length + 1} className="fw-bold text-muted text-uppercase" style={{ fontSize: '12px', letterSpacing: '1px' }}>Economic Indicators</td>
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">GDP (Billion USD)</td>
                                    {countries.map(c => (
                                        <td key={c.id} className="text-center fw-medium">${c.economy ? (c.economy.gdp / 1000000000).toFixed(2) : '-'}</td>
                                    ))}
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Inflation Rate</td>
                                    {countries.map(c => (
                                        <td key={c.id} className={`text-center fw-medium ${c.economy && c.economy.inflation_rate > 5 ? 'text-danger' : 'text-success'}`}>
                                            {c.economy ? c.economy.inflation_rate + '%' : '-'}
                                        </td>
                                    ))}
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Local Currency</td>
                                    {countries.map(c => (
                                        <td key={c.id} className="text-center fw-medium text-primary">
                                            <span className="badge bg-primary-subtle text-primary border border-primary-subtle">{c.currency || 'USD'}</span>
                                        </td>
                                    ))}
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Typical Weather / Climate</td>
                                    {countries.map(c => {
                                        // Simple realistic climate mock based on region
                                        let climate = "Temperate, Mild";
                                        let icon = "partly_cloudy_day";
                                        let color = "text-warning";
                                        if (c.region?.includes('Africa') || c.region?.includes('Middle East')) { climate = "Hot & Arid (Desert)"; icon = "sunny"; color = "text-danger"; }
                                        else if (c.region?.includes('Asia')) { climate = "Tropical / Monsoon"; icon = "rainy"; color = "text-info"; }
                                        else if (c.region?.includes('Europe')) { climate = "Temperate / Continental"; icon = "ac_unit"; color = "text-primary"; }
                                        else if (c.country_name === 'Australia') { climate = "Arid to Semi-arid"; icon = "sunny"; color = "text-warning"; }
                                        
                                        return (
                                            <td key={c.id} className="text-center">
                                                <div className="d-flex flex-column align-items-center gap-1">
                                                    <span className={`material-symbols-outlined ${color}`}>{icon}</span>
                                                    <span className="small text-muted">{climate}</span>
                                                </div>
                                            </td>
                                        );
                                    })}
                                </tr>

                                <tr className="bg-light">
                                    <td colSpan={countries.length + 1} className="fw-bold text-muted text-uppercase" style={{ fontSize: '12px', letterSpacing: '1px' }}>Trade & Market</td>
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Total Import (Billion USD)</td>
                                    {countries.map(c => (
                                        <td key={c.id} className="text-center fw-medium text-info">
                                            ${c.trade_statistics && c.trade_statistics.length > 0 ? (c.trade_statistics[0].total_import / 1000000000).toFixed(2) : '-'}
                                        </td>
                                    ))}
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Total Export (Billion USD)</td>
                                    {countries.map(c => (
                                        <td key={c.id} className="text-center fw-medium text-primary">
                                            ${c.trade_statistics && c.trade_statistics.length > 0 ? (c.trade_statistics[0].total_export / 1000000000).toFixed(2) : '-'}
                                        </td>
                                    ))}
                                </tr>
                                <tr>
                                    <td className="fw-bold text-secondary">Recommended Commodities</td>
                                    {countries.map(c => (
                                        <td key={c.id} className="text-center">
                                            <div className="d-flex flex-wrap justify-content-center gap-1">
                                                {c.opportunity ? JSON.parse(c.opportunity.recommended_commodities || '[]').map((item: string, idx: number) => (
                                                    <span key={idx} className="badge bg-light text-secondary border">{item}</span>
                                                )) : '-'}
                                            </div>
                                        </td>
                                    ))}
                                </tr>
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

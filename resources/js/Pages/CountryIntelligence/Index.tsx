import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function CountryIndex() {
    const [countries, setCountries] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [region, setRegion] = useState('All Regions');
    const [sortBy, setSortBy] = useState('default');
    const [page, setPage] = useState(1);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0 });

    // Reset to page 1 when search, region, or sort changes
    useEffect(() => {
        setPage(1);
    }, [search, region, sortBy]);

    useEffect(() => {
        const fetchCountries = async () => {
            setLoading(true);
            try {
                const params: any = { search, page };
                if (region !== 'All Regions') params.region = region;
                if (sortBy !== 'default') params.sortBy = sortBy;
                
                const res = await axios.get('/api/countries', { params });
                setCountries(res.data.data);
                setPagination({
                    current_page: res.data.current_page,
                    last_page: res.data.last_page,
                    total: res.data.total
                });
            } catch (error) {
                console.error("Error fetching countries", error);
            } finally {
                setLoading(false);
            }
        };

        const timeoutId = setTimeout(() => {
            fetchCountries();
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [search, region, sortBy, page]);

    return (
        <AuthenticatedLayout>
            <Head title="Country Intelligence" />

            <div className="container-fluid p-4">
                <div className="d-flex justify-content-between align-items-center mb-4 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>Country Intelligence</h2>
                        <p className="text-muted mb-0">Analyze global economic conditions, political stability, and logistics performance.</p>
                    </div>
                    <div className="d-flex gap-3">
                        <Link href="/intelligence/countries/compare" className="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center gap-2 shadow-sm">
                            <span className="material-symbols-outlined">compare_arrows</span>
                            Compare Countries
                        </Link>
                    </div>
                </div>

                <div className="panel-card mb-4 fade-up" style={{ animationDelay: '0.1s' }}>
                    <div className="d-flex justify-content-between align-items-center mb-4">
                        <div className="search-bar w-50 m-0">
                            <span className="material-symbols-outlined">search</span>
                            <input 
                                type="text" 
                                placeholder="Search by country name, ISO code..." 
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>
                        <div className="d-flex gap-2">
                            <select 
                                className="form-select rounded-pill border-light bg-light text-muted"
                                value={region}
                                onChange={(e) => setRegion(e.target.value)}
                            >
                                <option>All Regions</option>
                                <option>Asia</option>
                                <option>Europe</option>
                                <option>Americas</option>
                                <option>Africa</option>
                                <option>Oceania</option>
                            </select>
                            <div className="dropdown">
                                <button 
                                    className="btn btn-outline-secondary rounded-pill d-flex align-items-center gap-2 text-nowrap" 
                                    type="button" 
                                    data-bs-toggle="dropdown"
                                >
                                    <span className="material-symbols-outlined" style={{ fontSize: '18px' }}>filter_list</span>
                                    Sort By
                                </button>
                                <ul className="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><button className={`dropdown-item ${sortBy === 'default' ? 'active bg-primary' : ''}`} onClick={() => setSortBy('default')}>Default</button></li>
                                    <li><button className={`dropdown-item ${sortBy === 'risk_desc' ? 'active bg-primary' : ''}`} onClick={() => setSortBy('risk_desc')}>Highest Risk</button></li>
                                    <li><button className={`dropdown-item ${sortBy === 'opp_desc' ? 'active bg-primary' : ''}`} onClick={() => setSortBy('opp_desc')}>Highest Opportunity</button></li>
                                    <li><button className={`dropdown-item ${sortBy === 'gdp_desc' ? 'active bg-primary' : ''}`} onClick={() => setSortBy('gdp_desc')}>Highest GDP</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div className="table-responsive">
                        <table className="table custom-table align-middle">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Region</th>
                                    <th>Risk Score</th>
                                    <th>Opportunity</th>
                                    <th>GDP (Billion)</th>
                                    <th>Inflation</th>
                                    <th>Status</th>
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
                                ) : countries.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="text-center py-5 text-muted">
                                            No countries found.
                                        </td>
                                    </tr>
                                ) : (
                                    countries.map((country) => (
                                        <tr key={country.id}>
                                            <td>
                                                <div className="d-flex align-items-center gap-3">
                                                    <div className="d-flex align-items-center justify-content-center bg-light rounded" style={{ width: '40px', height: '30px', fontSize: '20px' }}>
                                                        {country.flag}
                                                    </div>
                                                    <div>
                                                        <h6 className="mb-0 fw-bold">{country.country_name}</h6>
                                                        <span className="text-muted small">{country.iso_code} • {country.capital}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span className="badge bg-light text-secondary border">{country.region}</span>
                                            </td>
                                            <td>
                                                <div className="d-flex align-items-center gap-2">
                                                    <div className="progress flex-grow-1" style={{ height: '6px', width: '60px' }}>
                                                        <div className={`progress-bar ${country.risk_score > 70 ? 'bg-danger' : (country.risk_score > 40 ? 'bg-warning' : 'bg-success')}`} style={{ width: `${country.risk_score}%` }}></div>
                                                    </div>
                                                    <span className="fw-bold small">{country.risk_score}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div className="d-flex align-items-center gap-2">
                                                    <div className="progress flex-grow-1" style={{ height: '6px', width: '60px' }}>
                                                        <div className={`progress-bar ${country.opportunity_score > 70 ? 'bg-success' : (country.opportunity_score > 40 ? 'bg-info' : 'bg-secondary')}`} style={{ width: `${country.opportunity_score}%` }}></div>
                                                    </div>
                                                    <span className="fw-bold small">{country.opportunity_score}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span className="fw-medium">${country.economy ? (country.economy.gdp / 1000000000).toFixed(2) : '-'}B</span>
                                            </td>
                                            <td>
                                                <span className={`fw-medium ${country.economy && country.economy.inflation_rate > 5 ? 'text-danger' : 'text-success'}`}>
                                                    {country.economy ? country.economy.inflation_rate + '%' : '-'}
                                                </span>
                                            </td>
                                            <td>
                                                <span className={`badge ${country.trade_status === 'Active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'} border`}>
                                                    {country.trade_status || 'Unknown'}
                                                </span>
                                            </td>
                                            <td>
                                                <Link href={`/intelligence/countries/${country.id}`} className="btn btn-sm btn-light rounded-pill border shadow-sm">
                                                    Analyze
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                    
                    {/* Pagination */}
                    <div className="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <span className="text-muted small">Showing {pagination.total} countries</span>
                        <div className="d-flex gap-1">
                            <button 
                                className="btn btn-sm btn-light border"
                                onClick={() => setPage(Math.max(1, page - 1))}
                                disabled={page === 1 || loading}
                            >
                                &larr;
                            </button>
                            <button className="btn btn-sm btn-primary">{page}</button>
                            <button 
                                className="btn btn-sm btn-light border"
                                onClick={() => setPage(Math.min(pagination.last_page, page + 1))}
                                disabled={page === pagination.last_page || loading}
                            >
                                &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

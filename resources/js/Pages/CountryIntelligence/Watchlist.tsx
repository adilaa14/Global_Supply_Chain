import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function Watchlist() {
    const [countries, setCountries] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    const fetchFavorites = async () => {
        setLoading(true);
        try {
            const res = await axios.get('/api/countries/favorites');
            // Attach is_favorited = true since these are favorites
            setCountries(res.data.map((c: any) => ({ ...c, is_favorited: true })));
        } catch (error) {
            console.error("Error fetching favorites", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchFavorites();
    }, []);

    const toggleFavorite = async (id: string) => {
        try {
            const res = await axios.post(`/api/countries/${id}/favorite`);
            if (res.data.status === 'removed') {
                setCountries(countries.filter(c => c.id !== id));
            }
        } catch (error) {
            console.error("Error removing favorite", error);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="My Watchlist" />

            <div className="container-fluid p-4">
                <div className="d-flex justify-content-between align-items-center mb-4 fade-up">
                    <div className="d-flex align-items-center gap-2">
                        <Link href="/intelligence/countries" className="btn btn-sm btn-light border rounded-pill d-flex align-items-center">
                            <span className="material-symbols-outlined" style={{ fontSize: '18px' }}>arrow_back</span>
                        </Link>
                        <div>
                            <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>My Watchlist</h2>
                            <p className="text-muted mb-0">Monitor your favorite and high-priority countries.</p>
                        </div>
                    </div>
                </div>

                <div className="panel-card mb-4 fade-up" style={{ animationDelay: '0.1s' }}>
                    <div className="table-responsive">
                        <table className="table custom-table align-middle">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Region</th>
                                    <th>Risk Score</th>
                                    <th>Opportunity</th>
                                    <th>GDP (Billion)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {loading ? (
                                    <tr>
                                        <td colSpan={7} className="text-center py-5">
                                            <div className="spinner-border text-primary" role="status"></div>
                                        </td>
                                    </tr>
                                ) : countries.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="text-center py-5 text-muted">
                                            <span className="material-symbols-outlined mb-2" style={{ fontSize: '48px' }}>star_border</span>
                                            <h5>Watchlist is Empty</h5>
                                            <p>You haven't added any countries to your watchlist yet. Go to Country Explorer and click the star icon to add them.</p>
                                            <Link href="/intelligence/countries" className="btn btn-primary rounded-pill mt-2">Explore Countries</Link>
                                        </td>
                                    </tr>
                                ) : (
                                    countries.map((country) => (
                                        <tr key={country.id}>
                                            <td>
                                                <div className="d-flex align-items-center gap-3">
                                                    <div className="d-flex align-items-center justify-content-center bg-light rounded overflow-hidden shadow-sm" style={{ width: '40px', height: '30px' }}>
                                                        {country.iso_code ? (
                                                            <img src={`https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/flags/4x3/${country.iso_code.toLowerCase()}.svg`} alt={country.iso_code} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                                        ) : (
                                                            <span style={{ fontSize: '20px' }}>{country.flag || '🏳️'}</span>
                                                        )}
                                                    </div>
                                                    <div>
                                                        <h6 className="mb-0 fw-bold d-flex align-items-center gap-2">
                                                            {country.country_name}
                                                            <button 
                                                                className="btn btn-link p-0 text-decoration-none"
                                                                onClick={() => toggleFavorite(country.id)}
                                                                title="Remove from watchlist"
                                                            >
                                                                <span className="material-symbols-outlined fs-6 text-warning" style={{ fontVariationSettings: "'FILL' 1" }}>star</span>
                                                            </button>
                                                        </h6>
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
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

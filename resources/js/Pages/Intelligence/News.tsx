import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function NewsIntelligence() {
    const [news, setNews] = useState<{ [key: string]: any[] }>({
        Logistics: [],
        Trade: [],
        Shipping: [],
        Economy: []
    });
    const [loading, setLoading] = useState(true);
    const [country, setCountry] = useState('us');
    const [countries, setCountries] = useState<any[]>([]);
    const [errorMsg, setErrorMsg] = useState<string | null>(null);

    useEffect(() => {
        // Fetch countries for the dropdown
        axios.get('/api/countries/list').then(res => setCountries(res.data));
    }, []);

    const fetchNews = async () => {
        setLoading(true);
        setErrorMsg(null);
        try {
            const categories = ['Logistics', 'Trade', 'Shipping', 'Economy'];
            const requests = categories.map(cat => axios.get('/api/news', { params: { category: cat, country } }));
            const responses = await Promise.all(requests);
            
            setNews({
                Logistics: responses[0].data.articles || [],
                Trade: responses[1].data.articles || [],
                Shipping: responses[2].data.articles || [],
                Economy: responses[3].data.articles || []
            });
        } catch (error) {
            console.error("Error fetching news", error);
            setErrorMsg("GNews API Error. Please verify your API Key or Network.");
            // Reset to empty
            setNews({ Logistics: [], Trade: [], Shipping: [], Economy: [] });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchNews();
    }, [country]);

    const renderArticleCard = (article: any, idx: number) => (
        <div className="col-md-6 col-lg-3" key={idx}>
            <div className="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-decoration-none">
                <div className="position-relative" style={{ height: '160px', backgroundColor: '#f8f9fa' }}>
                    {article.image ? (
                        <img src={article.image} alt={article.title} className="w-100 h-100 object-fit-cover" />
                    ) : (
                        <div className="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                            <span className="material-symbols-outlined" style={{ fontSize: '48px' }}>image</span>
                        </div>
                    )}
                    <span className="position-absolute top-0 start-0 m-2 badge bg-primary border" style={{ fontSize: '10px' }}>
                        {article.source.name}
                    </span>
                </div>
                <div className="card-body d-flex flex-column p-3">
                    <div className="text-muted mb-2 d-flex align-items-center gap-1" style={{ fontSize: '11px' }}>
                        <span className="material-symbols-outlined" style={{ fontSize: '12px' }}>schedule</span>
                        {new Date(article.publishedAt).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })}
                    </div>
                    <h6 className="card-title fw-bold mb-2 line-clamp-2" style={{ color: 'var(--text-primary)', fontSize: '14px' }}>
                        <a href={article.url} target="_blank" rel="noreferrer" className="text-decoration-none text-reset">
                            {article.title}
                        </a>
                    </h6>
                    <a href={article.url} target="_blank" rel="noreferrer" className="btn btn-sm btn-light border rounded-pill mt-auto fw-medium d-flex align-items-center justify-content-center gap-1" style={{ fontSize: '12px' }}>
                        Read <span className="material-symbols-outlined" style={{ fontSize: '14px' }}>open_in_new</span>
                    </a>
                </div>
            </div>
        </div>
    );

    return (
        <AuthenticatedLayout>
            <Head title="News Intelligence" />

            <div className="container-fluid p-4">
                <div className="d-flex justify-content-between align-items-center mb-4 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>News Intelligence</h2>
                        <p className="text-muted mb-0">Live macro-economic, logistics, and geopolitical news feed.</p>
                    </div>
                    <div className="d-flex align-items-center gap-3">
                        <span className="text-muted fw-medium">Region:</span>
                        <select 
                            className="form-select w-auto border-light shadow-sm rounded-pill"
                            value={country}
                            onChange={(e) => setCountry(e.target.value)}
                        >
                            <option value="us">🇺🇸 United States</option>
                            <option value="gb">🇬🇧 United Kingdom</option>
                            <option value="cn">🇨🇳 China</option>
                            <option value="jp">🇯🇵 Japan</option>
                            <option value="de">🇩🇪 Germany</option>
                            <option value="in">🇮🇳 India</option>
                            <option value="au">🇦🇺 Australia</option>
                            <option value="sg">🇸🇬 Singapore</option>
                            <option value="id">🇮🇩 Indonesia</option>
                            {countries.map(c => (
                                <option key={c.iso_code} value={c.iso_code.toLowerCase()}>
                                    {c.flag || '🌐'} {c.country_name}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>

                {loading ? (
                    <div className="text-center py-5 fade-up">
                        <div className="spinner-border text-primary" role="status"></div>
                        <p className="text-muted mt-2">Aggregating live news from global sources...</p>
                    </div>
                ) : errorMsg ? (
                    <div className="text-center py-5 text-danger fade-up">
                        <span className="material-symbols-outlined mb-2" style={{ fontSize: '48px' }}>wifi_off</span>
                        <h5>Connection Failed</h5>
                        <p>{errorMsg}</p>
                        <p className="small text-muted">Please check your internet connection or try again later.</p>
                    </div>
                ) : (
                    <div className="row g-4 fade-up" style={{ animationDelay: '0.1s' }}>
                        {Object.entries(news).map(([sectionName, articles]) => (
                            <div className="col-12 mb-4" key={sectionName}>
                                <div className="d-flex align-items-center gap-2 mb-3">
                                    <span className="material-symbols-outlined text-primary bg-primary-subtle p-2 rounded-circle">
                                        {sectionName === 'Logistics' ? 'local_shipping' : 
                                         sectionName === 'Trade' ? 'handshake' : 
                                         sectionName === 'Shipping' ? 'directions_boat' : 'trending_up'}
                                    </span>
                                    <h4 className="fw-bold mb-0">{sectionName} Intelligence</h4>
                                </div>
                                
                                {articles.length === 0 ? (
                                    <div className="p-4 bg-light rounded-4 text-center text-muted">
                                        <p className="mb-0">No recent articles found for {sectionName} in this region.</p>
                                    </div>
                                ) : (
                                    <div className="row g-3">
                                        {/* Display top 4 articles per section for a neat grid */}
                                        {articles.slice(0, 4).map((article, idx) => renderArticleCard(article, idx))}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

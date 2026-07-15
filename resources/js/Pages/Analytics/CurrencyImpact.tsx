import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState, useEffect } from 'react';
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
import axios from 'axios';

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

export default function CurrencyImpact() {
    const [loading, setLoading] = useState(false);
    const [baseCurrency, setBaseCurrency] = useState('USD');
    const [targetCurrency, setTargetCurrency] = useState('IDR');
    const [dateRange, setDateRange] = useState('1year');
    
    const [chartData, setChartData] = useState<any>(null);
    const [stats, setStats] = useState({
        latest: 0,
        change: 0,
        high: 0,
        low: 0,
        average: 0
    });

    const currencies = [
        { code: 'USD', name: 'US Dollar' },
        { code: 'EUR', name: 'Euro' },
        { code: 'GBP', name: 'British Pound' },
        { code: 'JPY', name: 'Japanese Yen' },
        { code: 'CNY', name: 'Chinese Yuan' },
        { code: 'IDR', name: 'Indonesian Rupiah' },
        { code: 'SGD', name: 'Singapore Dollar' },
        { code: 'MYR', name: 'Malaysian Ringgit' },
        { code: 'THB', name: 'Thai Baht' },
        { code: 'AUD', name: 'Australian Dollar' },
    ];

    const fetchExchangeRates = async () => {
        if (baseCurrency === targetCurrency) {
            alert("Base and Target currency cannot be the same.");
            return;
        }

        setLoading(true);

        const endDate = new Date();
        const startDate = new Date();
        
        if (dateRange === '1month') startDate.setMonth(endDate.getMonth() - 1);
        if (dateRange === '6months') startDate.setMonth(endDate.getMonth() - 6);
        if (dateRange === '1year') startDate.setFullYear(endDate.getFullYear() - 1);
        if (dateRange === '5years') startDate.setFullYear(endDate.getFullYear() - 5);

        const formatIsoDate = (d: Date) => d.toISOString().split('T')[0];

        try {
            const res = await axios.get(`/tracking/api/exchange-rates/${baseCurrency}/${targetCurrency}/${formatIsoDate(startDate)}/${formatIsoDate(endDate)}`);
            
            if (res.data && res.data.rates) {
                const dates = Object.keys(res.data.rates);
                const values = dates.map(date => res.data.rates[date][targetCurrency]);

                if (values.length > 0) {
                    const latest = values[values.length - 1];
                    const first = values[0];
                    const high = Math.max(...values);
                    const low = Math.min(...values);
                    const avg = values.reduce((a,b) => a+b, 0) / values.length;
                    const change = ((latest - first) / first) * 100;

                    setStats({
                        latest,
                        change,
                        high,
                        low,
                        average: avg
                    });

                    setChartData({
                        labels: dates,
                        datasets: [{
                            label: `Exchange Rate (${baseCurrency} to ${targetCurrency})`,
                            data: values,
                            borderColor: change >= 0 ? '#198754' : '#dc3545',
                            backgroundColor: change >= 0 ? 'rgba(25, 135, 84, 0.1)' : 'rgba(220, 53, 69, 0.1)',
                            fill: true,
                            tension: 0.1,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHitRadius: 10,
                        }]
                    });
                }
            } else {
                setChartData(null);
            }
        } catch (error) {
            console.error("Failed to fetch exchange rates", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchExchangeRates();
    }, [baseCurrency, targetCurrency, dateRange]);

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                mode: 'index' as const,
                intersect: false,
                callbacks: {
                    label: function(context: any) {
                        return `1 ${baseCurrency} = ${context.parsed.y.toLocaleString()} ${targetCurrency}`;
                    }
                }
            }
        },
        scales: {
            y: {
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: {
                grid: { display: false },
                ticks: { maxTicksLimit: 12 }
            }
        },
        interaction: {
            mode: 'index' as const,
            intersect: false,
        },
    };

    return (
        <AuthenticatedLayout>
            <Head title="Currency Impact Dashboard" />

            <div className="container-fluid py-4">
                <div className="d-flex justify-content-between align-items-center mb-4 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--text-primary)' }}>
                            Currency Impact Dashboard
                            {loading && <span className="spinner-border spinner-border-sm text-primary ms-3" role="status"></span>}
                        </h2>
                        <p className="text-muted mb-0">Live daily exchange rates and historical trend analysis via <strong className="text-primary">Frankfurter API</strong>.</p>
                    </div>
                </div>

                <div className="panel-card mb-4 fade-up" style={{ animationDelay: '0.1s' }}>
                    <div className="row g-3 align-items-end">
                        <div className="col-md-3">
                            <label className="form-label text-muted small fw-bold">Base Currency</label>
                            <select className="form-select bg-light" value={baseCurrency} onChange={e => setBaseCurrency(e.target.value)}>
                                {currencies.map(c => <option key={c.code} value={c.code}>{c.code} - {c.name}</option>)}
                            </select>
                        </div>
                        <div className="col-md-auto d-flex align-items-center justify-content-center pt-4">
                            <button className="btn btn-light border rounded-circle p-2 d-flex" onClick={() => {
                                const temp = baseCurrency;
                                setBaseCurrency(targetCurrency);
                                setTargetCurrency(temp);
                            }}>
                                <span className="material-symbols-outlined">swap_horiz</span>
                            </button>
                        </div>
                        <div className="col-md-3">
                            <label className="form-label text-muted small fw-bold">Target Currency</label>
                            <select className="form-select bg-light" value={targetCurrency} onChange={e => setTargetCurrency(e.target.value)}>
                                {currencies.map(c => <option key={c.code} value={c.code}>{c.code} - {c.name}</option>)}
                            </select>
                        </div>
                        <div className="col-md-3 ms-auto">
                            <label className="form-label text-muted small fw-bold">Time Range</label>
                            <select className="form-select bg-light" value={dateRange} onChange={e => setDateRange(e.target.value)}>
                                <option value="1month">Last 1 Month</option>
                                <option value="6months">Last 6 Months</option>
                                <option value="1year">Last 1 Year</option>
                                <option value="5years">Last 5 Years</option>
                            </select>
                        </div>
                    </div>
                </div>

                {chartData ? (
                    <div className="row g-4">
                        <div className="col-md-4 fade-up" style={{ animationDelay: '0.2s' }}>
                            <div className="panel-card h-100 text-center py-5 d-flex flex-column justify-content-center">
                                <h6 className="text-muted fw-bold mb-3">Current Exchange Rate</h6>
                                <h1 className="fw-bold mb-2">
                                    <span className="fs-3 text-muted">{baseCurrency}</span> 1 <span className="text-muted fs-4">=</span> {stats.latest.toLocaleString(undefined, {maximumFractionDigits: 4})} <span className="fs-3 text-muted">{targetCurrency}</span>
                                </h1>
                                
                                <div className={`d-inline-flex align-items-center justify-content-center px-3 py-2 rounded-pill mt-3 ${stats.change >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'}`}>
                                    <span className="material-symbols-outlined me-1 fs-5">
                                        {stats.change >= 0 ? 'trending_up' : 'trending_down'}
                                    </span>
                                    <span className="fw-bold">{Math.abs(stats.change).toFixed(2)}% {stats.change >= 0 ? 'Increase' : 'Decrease'}</span>
                                </div>
                                <p className="text-muted small mt-2">Over the selected time range</p>
                            </div>
                        </div>

                        <div className="col-md-8 fade-up" style={{ animationDelay: '0.3s' }}>
                            <div className="panel-card h-100">
                                <div className="d-flex justify-content-between align-items-center mb-4">
                                    <h5 className="fw-bold mb-0">Historical Trend Curve</h5>
                                </div>
                                <div style={{ height: '350px' }}>
                                    <Line options={chartOptions} data={chartData} />
                                </div>
                            </div>
                        </div>

                        <div className="col-md-4 fade-up" style={{ animationDelay: '0.4s' }}>
                            <div className="panel-card h-100 d-flex align-items-center">
                                <div className="bg-success bg-opacity-10 p-3 rounded-circle me-3 text-success d-flex">
                                    <span className="material-symbols-outlined fs-3">vertical_align_top</span>
                                </div>
                                <div>
                                    <p className="text-muted small mb-0 fw-bold">Highest Rate</p>
                                    <h4 className="fw-bold text-dark mb-0">{stats.high.toLocaleString(undefined, {maximumFractionDigits: 4})}</h4>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-4 fade-up" style={{ animationDelay: '0.5s' }}>
                            <div className="panel-card h-100 d-flex align-items-center">
                                <div className="bg-danger bg-opacity-10 p-3 rounded-circle me-3 text-danger d-flex">
                                    <span className="material-symbols-outlined fs-3">vertical_align_bottom</span>
                                </div>
                                <div>
                                    <p className="text-muted small mb-0 fw-bold">Lowest Rate</p>
                                    <h4 className="fw-bold text-dark mb-0">{stats.low.toLocaleString(undefined, {maximumFractionDigits: 4})}</h4>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-4 fade-up" style={{ animationDelay: '0.6s' }}>
                            <div className="panel-card h-100 d-flex align-items-center">
                                <div className="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary d-flex">
                                    <span className="material-symbols-outlined fs-3">functions</span>
                                </div>
                                <div>
                                    <p className="text-muted small mb-0 fw-bold">Average Rate</p>
                                    <h4 className="fw-bold text-dark mb-0">{stats.average.toLocaleString(undefined, {maximumFractionDigits: 4})}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    !loading && (
                        <div className="panel-card text-center py-5 fade-up">
                            <span className="material-symbols-outlined text-muted mb-3" style={{ fontSize: '48px', opacity: 0.5 }}>warning</span>
                            <h5 className="text-muted">Data Not Available</h5>
                            <p className="text-muted">Historical data for the selected currency pair and date range is currently unavailable.</p>
                        </div>
                    )
                )}
            </div>
        </AuthenticatedLayout>
    );
}

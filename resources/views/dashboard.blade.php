@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up" style="animation-delay: 0.3s;">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--secondary);">Global Command Center</h2>
            <p class="text-muted mb-0">Real-time intelligence on global supply chain operations.</p>
        </div>
        <div>
            <button class="btn-primary-custom d-flex align-items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 20px;">add</span>
                Create Shipment
            </button>
        </div>
    </div>

    <!-- Stat Cards Row 1 -->
    <div class="row g-4 mb-4 fade-up" style="animation-delay: 0.4s;">
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h6>TOTAL<br>SHIPMENTS</h6>
                    <h3 id="kpi-shipments">0</h3>
                    <div class="text-success-custom">
                        <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom; font-weight: 400;">trending_up</span> +5.2% from<br>last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h6>CARGO IN<br>TRANSIT</h6>
                    <h3 id="kpi-transit">0</h3>
                    <div class="text-success-custom">
                        <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom; font-weight: 400;">trending_up</span> +2.1% from<br>last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h6>DELAYED<br>SHIPMENTS</h6>
                    <h3 id="kpi-delayed">0</h3>
                    <div class="text-danger-custom">
                        <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom; font-weight: 400;">trending_down</span> -1.5% from<br>last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h6>REVENUE (MTD)<br>&nbsp;</h6>
                    <h3 id="kpi-revenue">0</h3>
                    <div class="text-success-custom">
                        <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom; font-weight: 400;">trending_up</span> +8.4% from<br>last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Executive Summary Row (NEW) -->
    <div class="row g-4 mb-4 fade-up" style="animation-delay: 0.45s;">
        <div class="col-12">
            <div class="panel-card" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.15) 100%); border-left: 4px solid var(--primary);">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background: var(--primary); color: white; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 15px rgba(240, 49, 100, 0.4);">
                        <span class="material-symbols-outlined" style="font-size: 24px;">psychology</span>
                    </div>
                    <div>
                        <h5 class="panel-title mb-2 fw-bold" style="font-size: 1.1rem;">AI Executive Summary</h5>
                        <p class="mb-1 text-secondary" style="font-size: 0.95rem; font-weight: 500;">
                            Current highest demand is <span class="text-primary fw-bold">Japan</span> for <span class="fw-bold">Coffee</span>. Palm Oil price is increasing (+2.4%). USA import demand decreased by 1.2%.
                        </p>
                        <p class="mb-0 text-danger-custom fw-bold" style="font-size: 0.95rem;">
                            Recommendation: Shipment SHP-001 should be redirected to South Korea. Expected additional profit: USD 250,000.
                        </p>
                    </div>
                    <div class="ms-auto">
                        <button class="btn-primary-custom" style="padding: 8px 20px; font-size: 0.85rem;">Review Redirect</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Row -->
    <div class="row g-4 mb-4 fade-up" style="animation-delay: 0.5s;">
        <!-- Global Map (70%) -->
        <div class="col-xl-8 col-lg-12">
            <div class="panel-card d-flex flex-column">
                <div class="panel-header">
                    <h5 class="panel-title">Global Fleet Live Tracking</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-dark border"><span class="text-success">●</span> 8,230 Active</span>
                        <span class="badge bg-light text-dark border"><span class="text-danger">●</span> 142 Delayed</span>
                    </div>
                </div>
                <div id="global-map" class="flex-grow-1"></div>
            </div>
        </div>

        <!-- Risk Alerts (30%) -->
        <div class="col-xl-4 col-lg-12">
            <div class="panel-card">
                <div class="panel-header">
                    <h5 class="panel-title">Active Risk Alerts</h5>
                    <a href="#" style="color: var(--primary); font-size: 0.85rem; font-weight: 600; text-decoration: none;">View All</a>
                </div>
                <div class="alert-list" style="max-height: 420px; overflow-y: auto; overflow-x: hidden; padding-right: 10px;">
                    <!-- Alert Item 1 -->
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom alert-item-hover" onclick="focusMapAlert(31.23, 121.47)">
                        <div class="stat-icon danger" style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-symbols-outlined" style="font-size: 24px;">storm</span>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold" style="font-size: 0.95rem; color: var(--secondary);">Typhoon Warning</h6>
                            <p class="text-muted mb-1" style="font-size: 0.85rem;">Port of Shanghai (CNSHG) - Operations suspended.</p>
                            <span class="text-danger-custom" style="font-size: 0.75rem;">High Impact • 12 ships affected</span>
                        </div>
                    </div>
                    <!-- Alert Item 2 -->
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom alert-item-hover" onclick="focusMapAlert(33.72, -118.26)">
                        <div class="stat-icon warning" style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-symbols-outlined" style="font-size: 24px;">anchor</span>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold" style="font-size: 0.95rem; color: var(--secondary);">Port Congestion</h6>
                            <p class="text-muted mb-1" style="font-size: 0.85rem;">Port of Los Angeles (USLAX) - 4 days average delay.</p>
                            <span class="text-warning" style="font-size: 0.75rem; font-weight: 600;">Medium Impact • 8 ships affected</span>
                        </div>
                    </div>
                    <!-- Alert Item 3 -->
                    <div class="d-flex align-items-start gap-3 border-bottom mb-3 pb-3 alert-item-hover" onclick="focusMapAlert(51.50, -0.12)">
                        <div class="stat-icon primary" style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-symbols-outlined" style="font-size: 24px;">currency_exchange</span>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold" style="font-size: 0.95rem; color: var(--secondary);">Currency Fluctuation</h6>
                            <p class="text-muted mb-1" style="font-size: 0.85rem;">EUR/USD dropped by 1.2% in last 24h.</p>
                            <span style="color: var(--primary); font-size: 0.75rem; font-weight: 600;">Low Impact • Cost update recommended</span>
                        </div>
                    </div>
                    <!-- Alert Item 4 -->
                    <div class="d-flex align-items-start gap-3 alert-item-hover" onclick="focusMapAlert(29.97, 32.55)">
                        <div class="stat-icon danger" style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-symbols-outlined" style="font-size: 24px;">warning</span>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold" style="font-size: 0.95rem; color: var(--secondary);">Political Conflict</h6>
                            <p class="text-muted mb-1" style="font-size: 0.85rem;">Suez Canal Region - Increased security protocols.</p>
                            <span class="text-danger-custom" style="font-size: 0.75rem;">Critical Impact • Rerouting active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <!-- Trade Intelligence & Commodities Row (NEW) -->
    <div class="row g-4 mb-4 fade-up" style="animation-delay: 0.55s;">
        <!-- Trade Intelligence -->
        <div class="col-xl-6">
            <div class="panel-card">
                <div class="panel-header mb-3">
                    <h5 class="panel-title">Trade Intelligence</h5>
                    <span class="badge bg-light text-primary border" style="font-weight: 600;">Live</span>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.4); border: 1px solid var(--glass-border);">
                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Best Export Market</span>
                            <span class="fw-bold text-secondary d-flex justify-content-between align-items-center">
                                Japan <span class="text-success-custom">Demand High</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.4); border: 1px solid var(--glass-border);">
                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Fastest Growing</span>
                            <span class="fw-bold text-secondary d-flex justify-content-between align-items-center">
                                India <span class="text-success-custom">+12.4%</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.4); border: 1px solid var(--glass-border);">
                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Highest Profit Opp.</span>
                            <span class="fw-bold text-secondary d-flex justify-content-between align-items-center">
                                South Korea <span class="text-primary fw-bold">Coffee</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.4); border: 1px solid var(--glass-border);">
                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Safest Destination</span>
                            <span class="fw-bold text-secondary d-flex justify-content-between align-items-center">
                                Singapore <span class="text-success-custom">Risk Score: 12</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commodity Market Snapshot -->
        <div class="col-xl-6">
            <div class="panel-card">
                <div class="panel-header mb-3">
                    <h5 class="panel-title">Commodity Market Snapshot</h5>
                    <a href="#" style="color: var(--primary); font-size: 0.85rem; font-weight: 600; text-decoration: none;">Full Market</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0" style="font-size: 0.9rem;">
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--glass-border);">
                                <td class="fw-bold text-secondary" style="padding-left: 0;">Coffee (Arabica)</td>
                                <td class="text-end fw-bold">$4.12 / kg</td>
                                <td class="text-end text-success-custom">+2.4% <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">trending_up</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--glass-border);">
                                <td class="fw-bold text-secondary" style="padding-left: 0;">Palm Oil (CPO)</td>
                                <td class="text-end fw-bold">$850.00 / MT</td>
                                <td class="text-end text-success-custom">+1.1% <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">trending_up</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-secondary" style="padding-left: 0;">Nickel</td>
                                <td class="text-end fw-bold">$18,200 / MT</td>
                                <td class="text-end text-danger-custom">-0.8% <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: text-bottom;">trending_down</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4 fade-up" style="animation-delay: 0.6s;">
        <!-- Shipment Trend -->
        <div class="col-xl-6">
            <div class="panel-card">
                <div class="panel-header">
                    <h5 class="panel-title">Import & Export Trends</h5>
                    <select class="form-select form-select-sm w-auto border-0 bg-light fw-medium" style="border-radius: 20px;">
                        <option>Last 6 Months</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="shipmentTrendChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Revenue Chart -->
        <div class="col-xl-3">
            <div class="panel-card">
                <div class="panel-header">
                    <h5 class="panel-title">Weekly Revenue</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Risk Distribution -->
        <div class="col-xl-3">
            <div class="panel-card">
                <div class="panel-header">
                    <h5 class="panel-title">Global Risk Score</h5>
                </div>
                <div style="height: 250px; position: relative;" class="d-flex justify-content-center align-items-center">
                    <canvas id="riskChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <span class="d-block fw-bold text-secondary" style="font-size: 2rem;">82</span>
                        <span class="text-muted" style="font-size: 0.9rem; font-weight: 500;">Safe</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

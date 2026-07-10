<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Material Symbols (Thin/Minimalist) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" />
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar fade-up" style="animation-delay: 0.1s;">
            <div class="sidebar-header">
                <span class="material-symbols-outlined brand-icon">public</span>
                <span class="nav-text">G-SCRI</span>
            </div>
            <ul class="sidebar-menu">
                <li class="nav-section-title nav-text" style="padding: 10px 20px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase;">Core</li>
                <li><a href="#" class="active"><span class="material-symbols-outlined">dashboard</span> <span class="nav-text">Global Dashboard</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">local_shipping</span> <span class="nav-text">Shipment Management</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">explore</span> <span class="nav-text">Live Tracking</span></a></li>
                
                <li class="nav-section-title nav-text" style="padding: 10px 20px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; margin-top: 10px;">Intelligence</li>
                <li><a href="#"><span class="material-symbols-outlined">psychology</span> <span class="nav-text">Trade Intelligence</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">alt_route</span> <span class="nav-text">Smart Redirect</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">query_stats</span> <span class="nav-text">Profit Simulation</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">compare_arrows</span> <span class="nav-text">Country Comparison</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">shopping_basket</span> <span class="nav-text">Commodity Market</span></a></li>
                
                <li class="nav-section-title nav-text" style="padding: 10px 20px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; margin-top: 10px;">Global Data</li>
                <li><a href="#"><span class="material-symbols-outlined">flag</span> <span class="nav-text">Country Directory</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">partly_cloudy_day</span> <span class="nav-text">Weather Center</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">currency_exchange</span> <span class="nav-text">Currency Center</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">security</span> <span class="nav-text">Risk Intelligence</span></a></li>
                
                <li class="nav-section-title nav-text" style="padding: 10px 20px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; margin-top: 10px;">System</li>
                <li><a href="#"><span class="material-symbols-outlined">analytics</span> <span class="nav-text">Analytics</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">summarize</span> <span class="nav-text">Reports</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">business</span> <span class="nav-text">Company Profile</span></a></li>
                <li><a href="#"><span class="material-symbols-outlined">settings</span> <span class="nav-text">System Settings</span></a></li>
            </ul>

            <div class="sidebar-cta">
                <p class="nav-text">Unlock Advanced Features for Enterprise</p>
                <button class="btn-cta nav-text">UPGRADE PRO</button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar fade-up" style="animation-delay: 0.2s;">
                <div class="search-bar">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" placeholder="Search shipments, ports, countries...">
                </div>
                <div class="topbar-right">
                    <div class="topbar-icon" title="Language">
                        <span class="material-symbols-outlined">language</span>
                    </div>
                    <div class="topbar-icon" title="Dark Mode">
                        <span class="material-symbols-outlined">dark_mode</span>
                    </div>
                    <div class="topbar-icon" title="Notifications">
                        <span class="material-symbols-outlined">notifications_active</span>
                        <span class="badge">3</span>
                    </div>
                    <div class="user-profile">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=F03164&color=fff" alt="User">
                        <div class="user-info">
                            <span class="user-name">Admin User</span>
                            <span class="user-role">Administrator</span>
                        </div>
                        <span class="material-symbols-outlined" style="color: var(--text-muted); margin-left: 5px;">arrow_drop_down</span>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.8/countUp.umd.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>

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
        <aside class="sidebar">
            <div class="sidebar-header">
                <span class="material-symbols-outlined brand-icon">public</span>
                <span>G-SCRI</span>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><span class="material-symbols-outlined">dashboard</span> Dashboard</a></li>
                <li><a href="#"><span class="material-symbols-outlined">local_shipping</span> Shipments</a></li>
                <li><a href="#"><span class="material-symbols-outlined">map</span> World Map</a></li>
                <li><a href="#"><span class="material-symbols-outlined">flag</span> Countries</a></li>
                <li><a href="#"><span class="material-symbols-outlined">anchor</span> Ports</a></li>
                <li><a href="#"><span class="material-symbols-outlined">partly_cloudy_day</span> Weather</a></li>
                <li><a href="#"><span class="material-symbols-outlined">currency_exchange</span> Currency</a></li>
                <li><a href="#"><span class="material-symbols-outlined">security</span> Risk Intelligence</a></li>
                <li><a href="#"><span class="material-symbols-outlined">analytics</span> Analytics</a></li>
                <li><a href="#"><span class="material-symbols-outlined">newspaper</span> News</a></li>
                <li><a href="#"><span class="material-symbols-outlined">summarize</span> Reports</a></li>
            </ul>

            <div class="sidebar-cta">
                <p>Unlock Advanced Features for Enterprise</p>
                <button class="btn-cta">UPGRADE PRO</button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
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
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>

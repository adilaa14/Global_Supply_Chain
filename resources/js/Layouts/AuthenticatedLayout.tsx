import { PropsWithChildren, ReactNode } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Dropdown from '@/Components/Dropdown';

export default function AuthenticatedLayout({
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const user = usePage().props.auth.user;

    return (
        <>
            <Head>
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
                <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" />
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            </Head>

            <div className="app-container">
                {/* Sidebar */}
                <aside className="sidebar fade-up" style={{ animationDelay: '0.1s' }}>
                    <div className="sidebar-header">
                        <span className="material-symbols-outlined brand-icon" style={{ background: 'linear-gradient(135deg, var(--primary), #ff6b8b)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent', display: 'inline-block', fontWeight: 'bold' }}>hub</span>
                        <span className="nav-text" style={{ fontWeight: 800, background: 'linear-gradient(135deg, var(--secondary), #4a5568)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent', letterSpacing: '-0.5px', marginLeft: '-4px' }}>Global Chain</span>
                    </div>
                    <ul className="sidebar-menu">
                        <li className="nav-section-title nav-text" style={{ padding: '10px 20px', fontSize: '0.75rem', fontWeight: 700, color: 'var(--text-muted)', letterSpacing: '1px', textTransform: 'uppercase' }}>Core</li>
                        <li><Link href={route('dashboard')} className={route().current('dashboard') ? 'active' : ''}><span className="material-symbols-outlined">dashboard</span> <span className="nav-text">Global Dashboard</span></Link></li>
                        <li><Link href="/shipments" className={route().current('shipments.*') ? 'active' : ''}><span className="material-symbols-outlined">local_shipping</span> <span className="nav-text">Shipment Management</span></Link></li>
                        <li><Link href="/tracking" className={route().current('tracking.map') || route().current('tracking.vessels*') ? 'active' : ''}><span className="material-symbols-outlined">explore</span> <span className="nav-text">Live Vessel Tracking</span></Link></li>
                        <li><Link href="/tracking/ports" className={route().current('tracking.ports') ? 'active' : ''}><span className="material-symbols-outlined">anchor</span> <span className="nav-text">Port Locations</span></Link></li>
                        <li className="nav-section-title nav-text" style={{ padding: '10px 20px', fontSize: '0.75rem', fontWeight: 700, color: 'var(--text-muted)', letterSpacing: '1px', textTransform: 'uppercase', marginTop: '10px' }}>Intelligence</li>
                        <li><Link href="/intelligence/countries" className={route().current('intelligence.countries.*') && !route().current('intelligence.countries.compare') && !route().current('intelligence.countries.watchlist') ? 'active' : ''}><span className="material-symbols-outlined">psychology</span> <span className="nav-text">Country Intelligence</span></Link></li>
                        <li><Link href="/intelligence/countries/compare" className={route().current('intelligence.countries.compare') ? 'active' : ''}><span className="material-symbols-outlined">compare_arrows</span> <span className="nav-text">Country Comparison</span></Link></li>
                        <li><Link href="/intelligence/countries/watchlist" className={route().current('intelligence.countries.watchlist') ? 'active' : ''}><span className="material-symbols-outlined">star</span> <span className="nav-text">My Watchlist</span></Link></li>
                        <li><Link href="/intelligence/commodities" className={route().current('intelligence.commodities.*') && !route().current('intelligence.commodities.compare') ? 'active' : ''}><span className="material-symbols-outlined">inventory_2</span> <span className="nav-text">Commodity Intelligence</span></Link></li>
                        <li><Link href="/intelligence/commodities/compare" className={route().current('intelligence.commodities.compare') ? 'active' : ''}><span className="material-symbols-outlined">analytics</span> <span className="nav-text">Commodity Comparison</span></Link></li>
                        <li><Link href="/intelligence/news" className={route().current('intelligence.news') ? 'active' : ''}><span className="material-symbols-outlined">newspaper</span> <span className="nav-text">News Intelligence</span></Link></li>
                        <li><Link href="/analytics/visualization" className={route().current('analytics.visualization') ? 'active' : ''}><span className="material-symbols-outlined">monitoring</span> <span className="nav-text">Macro Visualization</span></Link></li>
                        <li><Link href="/analytics/currency-impact" className={route().current('analytics.currency-impact') ? 'active' : ''}><span className="material-symbols-outlined">currency_exchange</span> <span className="nav-text">Currency Impact</span></Link></li>
                        
                        <li className="nav-section-title nav-text" style={{ padding: '10px 20px', fontSize: '0.75rem', fontWeight: 700, color: 'var(--text-muted)', letterSpacing: '1px', textTransform: 'uppercase', marginTop: '10px' }}>System</li>
                        <li><a href="#"><span className="material-symbols-outlined">settings</span> <span className="nav-text">System Settings</span></a></li>
                        
                        {(usePage().props.auth as any).roles?.includes('Administrator') && (
                            <li>
                                <Link href="/admin/dashboard" className={route().current('admin.*') ? 'active' : ''}>
                                    <span className="material-symbols-outlined">admin_panel_settings</span> 
                                    <span className="nav-text">Admin Panel</span>
                                </Link>
                            </li>
                        )}
                    </ul>

                </aside>

                {/* Main Content */}
                <div className="main-content">
                    {/* Topbar */}
                    <header className="topbar fade-up" style={{ animationDelay: '0.2s' }}>
                        <div className="search-bar">
                            <span className="material-symbols-outlined">search</span>
                            <input type="text" placeholder="Search shipments, ports, countries..." />
                        </div>
                        <div className="topbar-right">
                            <Dropdown>
                                <Dropdown.Trigger>
                                    <div className="topbar-icon" title="Language" style={{ cursor: 'pointer' }}>
                                        <span className="material-symbols-outlined">language</span>
                                    </div>
                                </Dropdown.Trigger>
                                <Dropdown.Content>
                                    <button 
                                        onClick={() => {
                                            const select = document.querySelector('.goog-te-combo') as HTMLSelectElement;
                                            if (select) { select.value = 'en'; select.dispatchEvent(new Event('change')); }
                                        }}
                                        className="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                    >
                                        English (EN)
                                    </button>
                                    <button 
                                        onClick={() => {
                                            const select = document.querySelector('.goog-te-combo') as HTMLSelectElement;
                                            if (select) { select.value = 'id'; select.dispatchEvent(new Event('change')); }
                                        }}
                                        className="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                    >
                                        Bahasa Indonesia (ID)
                                    </button>
                                    <button 
                                        onClick={() => {
                                            const select = document.querySelector('.goog-te-combo') as HTMLSelectElement;
                                            if (select) { select.value = 'zh-CN'; select.dispatchEvent(new Event('change')); }
                                        }}
                                        className="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                    >
                                        中文 (Chinese)
                                    </button>
                                    <button 
                                        onClick={() => {
                                            const select = document.querySelector('.goog-te-combo') as HTMLSelectElement;
                                            if (select) { select.value = 'ja'; select.dispatchEvent(new Event('change')); }
                                        }}
                                        className="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                    >
                                        日本語 (Japanese)
                                    </button>
                                </Dropdown.Content>
                            </Dropdown>
                            

                            
                            <Dropdown>
                                <Dropdown.Trigger>
                                    <div className="user-profile" style={{ cursor: 'pointer' }}>
                                        <img src={`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=F03164&color=fff`} alt="User" />
                                        <div className="user-info">
                                            <span className="user-name">{user.name}</span>
                                            <span className="user-role">Administrator</span>
                                        </div>
                                        <span className="material-symbols-outlined" style={{ color: 'var(--text-muted)', marginLeft: '5px' }}>arrow_drop_down</span>
                                    </div>
                                </Dropdown.Trigger>
                                <Dropdown.Content>
                                    <Dropdown.Link href={route('profile.edit')}>Profile Settings</Dropdown.Link>
                                    <Dropdown.Link href={route('logout')} method="post" as="button">Log Out</Dropdown.Link>
                                </Dropdown.Content>
                            </Dropdown>
                        </div>
                    </header>

                    {/* Content Area */}
                    <main className="content-area">
                        {children}
                    </main>
                </div>
            </div>
        </>
    );
}

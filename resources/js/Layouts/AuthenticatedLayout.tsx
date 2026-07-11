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
                        <span className="material-symbols-outlined brand-icon">public</span>
                        <span className="nav-text">G-SCRI</span>
                    </div>
                    <ul className="sidebar-menu">
                        <li className="nav-section-title nav-text" style={{ padding: '10px 20px', fontSize: '0.75rem', fontWeight: 700, color: 'var(--text-muted)', letterSpacing: '1px', textTransform: 'uppercase' }}>Core</li>
                        <li><Link href={route('dashboard')} className={route().current('dashboard') ? 'active' : ''}><span className="material-symbols-outlined">dashboard</span> <span className="nav-text">Global Dashboard</span></Link></li>
                        <li><Link href="/shipments" className={route().current('shipments.*') ? 'active' : ''}><span className="material-symbols-outlined">local_shipping</span> <span className="nav-text">Shipment Management</span></Link></li>
                        <li><Link href="/tracking" className={route().current('tracking.*') ? 'active' : ''}><span className="material-symbols-outlined">explore</span> <span className="nav-text">Live Tracking</span></Link></li>
                        
                        <li className="nav-section-title nav-text" style={{ padding: '10px 20px', fontSize: '0.75rem', fontWeight: 700, color: 'var(--text-muted)', letterSpacing: '1px', textTransform: 'uppercase', marginTop: '10px' }}>Intelligence</li>
                        <li><a href="#"><span className="material-symbols-outlined">psychology</span> <span className="nav-text">Trade Intelligence</span></a></li>
                        <li><a href="#"><span className="material-symbols-outlined">alt_route</span> <span className="nav-text">Smart Redirect</span></a></li>
                        <li><a href="#"><span className="material-symbols-outlined">query_stats</span> <span className="nav-text">Profit Simulation</span></a></li>
                        <li><a href="#"><span className="material-symbols-outlined">compare_arrows</span> <span className="nav-text">Country Comparison</span></a></li>
                        <li><a href="#"><span className="material-symbols-outlined">shopping_basket</span> <span className="nav-text">Commodity Market</span></a></li>
                        
                        <li className="nav-section-title nav-text" style={{ padding: '10px 20px', fontSize: '0.75rem', fontWeight: 700, color: 'var(--text-muted)', letterSpacing: '1px', textTransform: 'uppercase', marginTop: '10px' }}>System</li>
                        <li><a href="#"><span className="material-symbols-outlined">settings</span> <span className="nav-text">System Settings</span></a></li>
                    </ul>

                    <div className="sidebar-cta">
                        <p className="nav-text">Unlock Advanced Features for Enterprise</p>
                        <button className="btn-cta nav-text">UPGRADE PRO</button>
                    </div>
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
                            <div className="topbar-icon" title="Language">
                                <span className="material-symbols-outlined">language</span>
                            </div>
                            <div className="topbar-icon" title="Dark Mode">
                                <span className="material-symbols-outlined">dark_mode</span>
                            </div>
                            <div className="topbar-icon" title="Notifications">
                                <span className="material-symbols-outlined">notifications_active</span>
                                <span className="badge">3</span>
                            </div>
                            
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

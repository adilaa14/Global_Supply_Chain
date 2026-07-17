const fs = require('fs');

const html = fs.readFileSync('landingpage.html', 'utf8');

// Extract style
const styleMatch = html.match(/<style>([\s\S]*?)<\/style>/);
const style = styleMatch ? styleMatch[1] : '';

// Extract body (excluding scripts and styles)
let body = html;
body = body.replace(/<style>[\s\S]*?<\/style>/, '');
body = body.replace(/<script>[\s\S]*?<\/script>/, '');
const bodyMatch = body.match(/<body>([\s\S]*?)<\/body>/);
body = bodyMatch ? bodyMatch[1] : body;

// Remove pricing section from HTML!
body = body.replace(/\s*<!-- PRICING -->[\s\S]*?<!-- FAQ -->/, '\n\n    <!-- FAQ -->');

// Remove CTA section from HTML!
body = body.replace(/\s*<!-- CTA -->[\s\S]*?<\/main>/, '\n</main>');

// Replace TradeOptix with Global Chain
body = body.replace(/TradeOptix/g, 'Global Chain');

// Replace the logo box with the Network SVG for Global Chain
body = body.replace(/<div style="width:32px;height:32px;border-radius:10px;background:linear-gradient\(135deg,var\(--primary\),var\(--primary-dark\)\);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:1rem;">T<\/div>/g, `<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2.5"></circle><line x1="12" y1="9.5" x2="12" y2="4.5"></line><circle cx="12" cy="3.5" r="1"></circle><line x1="9.5" y1="12" x2="4.5" y2="12"></line><circle cx="3.5" cy="12" r="1"></circle><line x1="14.5" y1="12" x2="19.5" y2="12"></line><circle cx="20.5" cy="12" r="1"></circle><line x1="10.2" y1="13.8" x2="6.8" y2="18.2"></line><circle cx="5.5" cy="19.5" r="1"></circle><line x1="13.8" y1="13.8" x2="17.2" y2="18.2"></line><circle cx="18.5" cy="19.5" r="1"></circle></svg>`);

// Add IDs to sections for smooth scrolling
body = body.replace('<span class="badge mb-4"><i class="fas fa-bolt"></i> Platform Features</span>', '<span id="features" style="scroll-margin-top: 100px; display: inline-block;" class="badge mb-4"><i class="fas fa-bolt"></i> Platform Features</span>');
body = body.replace('<span class="badge mb-4"><i class="fas fa-cubes"></i> Platform Modules</span>', '<span id="modules" style="scroll-margin-top: 100px; display: inline-block;" class="badge mb-4"><i class="fas fa-cubes"></i> Platform Modules</span>');
body = body.replace('<span class="badge mb-4"><i class="fas fa-question-circle"></i> FAQ</span>', '<span id="faq" style="scroll-margin-top: 100px; display: inline-block;" class="badge mb-4"><i class="fas fa-question-circle"></i> FAQ</span>');

// Replace Hero Buttons
body = body.replace('<button class="btn-primary"><i class="fas fa-rocket"></i> Start Free Trial</button>', '<button id="btn-hero-dashboard" class="btn-primary"><i class="fas fa-chart-line"></i> Go to Dashboard</button>');
body = body.replace('<button class="btn-outline"><i class="fas fa-play"></i> Book Live Demo</button>', '<button id="btn-hero-tracking" class="btn-outline"><i class="fas fa-map-marked-alt"></i> Live Tracking Map</button>');

// Ensure we don't have literal \n inside string by stringifying it
const escapedHtmlBody = JSON.stringify(body);

const finalReactCode = `// @ts-nocheck
import { PageProps } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export default function Welcome({ auth, stats }: PageProps) {
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!containerRef.current) return;
        
    // DATA LAYER
    const navItems = [
        { label: "Platform", href: "#features" },
        { label: "Modules", href: "#modules" },
        { label: "FAQ", href: "#faq" }
    ];

    const trustedLogos = [
        { name: "Maersk", icon: "fa-anchor" },
        { name: "MSC", icon: "fa-ship" },
        { name: "CMA CGM", icon: "fa-ship" },
        { name: "Hapag-Lloyd", icon: "fa-ship" },
        { name: "COSCO", icon: "fa-ship" },
        { name: "Evergreen", icon: "fa-leaf" },
        { name: "ONE", icon: "fa-ship" }
    ];

    const features = [
        { icon: "fa-truck-fast", title: "Shipment Management", desc: "End-to-end visibility across all your active shipments with real-time status updates from origin to destination." },
        { icon: "fa-map-pin", title: "Live Vessel Tracking", desc: "Track vessels in real time using satellite data across the global hub-and-spoke maritime routes." },
        { icon: "fa-building", title: "Company Management", desc: "Manage multi-tier enterprise partners, consignees, and logistics vendors in one centralized database." },
        { icon: "fa-anchor", title: "Ports Intelligence", desc: "Monitor vessel arrivals, port congestion, and throughput for hundreds of global ports." },
        { icon: "fa-chart-pie", title: "Financial Dashboard", desc: "Track revenue streams, operational costs, and shipment valuations in real-time." },
        { icon: "fa-coins", title: "Currency Impact", desc: "Analyze how foreign exchange rates affect your global supply chain margins and profitability." },
        { icon: "fa-bell", title: "Smart Alerts", desc: "Receive automated alerts for delayed shipments, route deviations, and high-risk events." },
        { icon: "fa-shield-halved", title: "Risk Analysis", desc: "AI-driven risk indexing to evaluate geopolitical and operational disruptions." }
    ];

    const steps = [
        { number: "01", icon: "fa-plus-circle", title: "Create Shipment", desc: "Enter your cargo details, select origin and destination ports." },
        { number: "02", icon: "fa-map-marked-alt", title: "Live Tracking", desc: "Our system maps the optimal maritime route and tracks the vessel continuously." },
        { number: "03", icon: "fa-chart-line", title: "AI Optimization", desc: "AI algorithms assess risks, costs, and delays, suggesting the best alternatives." },
        { number: "04", icon: "fa-box-open", title: "Successful Delivery", desc: "Ensure your cargo arrives on time, with full financial and performance reports." }
    ];

    const modules = [
        { icon: "fa-globe-americas", label: "Global Routing" },
        { icon: "fa-building", label: "Companies" },
        { icon: "fa-file-invoice-dollar", label: "Financials" },
        { icon: "fa-cloud-sun", label: "Weather Risk" },
        { icon: "fa-money-bill-wave", label: "Currencies" },
        { icon: "fa-ship", label: "Vessel API" },
        { icon: "fa-chart-bar", label: "Analytics" },
        { icon: "fa-shield-alt", label: "Risk Index" }
    ];

    const aiInputs = [
        { icon: "fa-gas-pump", label: "Bunker Fuel Prices" },
        { icon: "fa-anchor", label: "Port Congestion Index" },
        { icon: "fa-cloud-showers-heavy", label: "Maritime Weather Data" },
        { icon: "fa-balance-scale", label: "Geopolitical Risk" }
    ];

    const aiOutputs = [
        { icon: "fa-clock", label: "Estimated Arrival", value: "High Precision ETA" },
        { icon: "fa-shield", label: "Risk Assessment", value: "Low Operational Risk" },
        { icon: "fa-money-bill-trend-up", label: "Currency Impact", value: "Margin +2.4%" },
        { icon: "fa-route", label: "Route Optimization", value: "Direct Trunk Path" }
    ];

    const globalStats = [
        { value: stats?.vessels || "19", label: "Live Vessels" },
        { value: stats?.companies || "3", label: "Enterprise Partners" },
        { value: stats?.shipments || "5", label: "Active Shipments" },
        { value: "99.9%", label: "System Uptime" }
    ];

    const testimonials = [
        { name: "Sofia Chen", role: "VP of Supply Chain", text: "Global Chain Platform transformed how we manage our ocean freight. The visual tracking and real-time financial dashboards are game changers.", rating: 5 },
        { name: "James Okonkwo", role: "Logistics Director", text: "The accuracy of the vessel routing algorithm is unparalleled. We no longer worry about blind spots in the South China Sea or the Atlantic.", rating: 5 },
        { name: "Elena Rostova", role: "Head of Global Trade", text: "We reduced our operational delays by 22% within the first month. The AI risk analysis literally pays for itself.", rating: 5 }
    ];

    const faqs = [
        { q: "How accurate is the vessel tracking?", a: "We use a combination of terrestrial and satellite AIS data, updating every 30-120 seconds depending on the vessel's location, ensuring near-perfect real-time tracking." },
        { q: "Can I integrate my own ERP system?", a: "Yes, our enterprise plan includes custom API access to push and pull shipment, financial, and risk data directly into SAP, Oracle, or your custom ERP." },
        { q: "How does the AI risk engine work?", a: "Our AI model ingests data from over 50 global sources including weather agencies, political news feeds, and port authorities to calculate a dynamic risk score for every route." }
    ];

    const footerCols = [
        { title: "Platform", links: ["Vessel Tracking", "Shipment Management", "Currency Impact", "Admin Panel"] },
        { title: "Company", links: ["About Us", "Partners", "Careers", "Contact"] },
        { title: "Legal", links: ["Privacy Policy", "Terms of Service", "Security"] }
    ];

    // RENDER FUNCTIONS
    function renderNav(items) {
        return items.map(i => \`<li><a href="\${i.href}" class="text-sm font-medium transition" style="color:var(--gray);">\${i.label}</a></li>\`).join("");
    }

    function renderTrustedLogos(items) {
        return items.map(i => \`<div class="flex items-center gap-2 text-lg font-semibold" style="color:var(--dark);"><i class="fas \${i.icon}"></i> \${i.name}</div>\`).join("");
    }

    function renderFeatures(items) {
        return items.map(f => \`
            <div class="glass-card p-7 flex flex-col gap-4 cursor-pointer">
                <div class="glass-icon"><i class="fas \${f.icon}"></i></div>
                <h3 class="font-bold text-base" style="color:var(--dark);">\${f.title}</h3>
                <p class="text-sm" style="color:var(--gray);line-height:1.7;">\${f.desc}</p>
            </div>
        \`).join("");
    }

    function renderSteps(items) {
        return items.map((s, idx) => \`
            <div class="text-center">
                <div style="width:72px;height:72px;border-radius:24px;background:rgba(240,49,100,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:var(--primary);font-size:1.6rem;"><i class="fas \${s.icon}"></i></div>
                <div class="text-xs font-semibold mb-1" style="color:var(--primary);">Step \${s.number}</div>
                <h3 class="font-bold text-lg mb-2" style="color:var(--dark);">\${s.title}</h3>
                <p class="text-sm" style="color:var(--gray);line-height:1.7;">\${s.desc}</p>
                \${idx < items.length - 1 ? '<div class="step-connector mt-6 mx-auto"></div>' : ''}
            </div>
        \`).join("");
    }

    function renderModules(items) {
        return items.map(m => \`
            <div class="glass-card p-4 flex flex-col items-center justify-center gap-2 text-center cursor-pointer" style="min-height:110px;">
                <div style="width:40px;height:40px;border-radius:14px;background:rgba(240,49,100,0.08);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.1rem;"><i class="fas \${m.icon}"></i></div>
                <span class="text-xs font-semibold" style="color:var(--dark);">\${m.label}</span>
            </div>
        \`).join("");
    }

    function renderAIInputs(items) {
        return items.map(i => \`
            <li class="flex items-center gap-3 text-sm" style="color:var(--gray);">
                <i class="fas \${i.icon}" style="color:var(--primary);width:18px;text-align:center;"></i> \${i.label}
            </li>
        \`).join("");
    }

    function renderAIOutputs(items, offset) {
        return items.slice(offset, offset + 2).map(o => \`
            <div class="glass-card p-4 flex items-center gap-3">
                <div style="width:40px;height:40px;border-radius:14px;background:rgba(240,49,100,0.08);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1rem;"><i class="fas \${o.icon}"></i></div>
                <div>
                    <div class="text-xs font-semibold" style="color:var(--dark);">\${o.label}</div>
                    <span style="color:var(--gray);font-size:0.75rem;">\${o.value}</span>
                </div>
            </div>
        \`).join("");
    }

    function renderGlobalStats(items) {
        return items.map(s => \`
            <div class="glass p-5 rounded-3xl text-center">
                <div class="text-2xl font-extrabold" style="color:var(--dark);">\${s.value}</div>
                <div class="text-xs" style="color:var(--gray);">\${s.label}</div>
            </div>
        \`).join("");
    }

    function renderTestimonials(items) {
        return items.map(t => \`
            <div class="glass-card p-8 flex flex-col gap-4">
                <div class="flex gap-1" style="color:#F6AD55;">
                    \${Array(t.rating).fill('<i class="fas fa-star"></i>').join('')}
                </div>
                <p class="text-sm leading-relaxed" style="color:var(--gray);">"\${t.text}\\"</p>
                <div>
                    <div class="font-semibold text-sm" style="color:var(--dark);">\${t.name}</div>
                    <div class="text-xs" style="color:var(--gray);">\${t.role}</div>
                </div>
            </div>
        \`).join("");
    }

    function renderFAQs(items) {
        return items.map((f, idx) => \`
            <div class="faq-item" data-index="\${idx}">
                <div class="faq-question">
                    \${f.q}
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">\${f.a}</div>
            </div>
        \`).join("");
    }

    function renderFooterCols(cols) {
        return cols.map(c => \`
            <div>
                <h4 class="font-semibold text-sm mb-4" style="color:var(--dark);">\${c.title}</h4>
                <ul class="space-y-2">
                    \${c.links.map(l => \`<li><a href="#" class="text-sm" style="color:var(--gray);">\${l}</a></li>\`).join('')}
                </ul>
            </div>
        \`).join("");
    }

    // MOUNT
    const safeSetInnerHTML = (id, html) => {
        const el = document.getElementById(id);
        if (el) el.innerHTML = html;
    };

    safeSetInnerHTML("nav-list", renderNav(navItems));
    safeSetInnerHTML("trusted-logos", renderTrustedLogos(trustedLogos));
    safeSetInnerHTML("feature-grid", renderFeatures(features));
    safeSetInnerHTML("steps-container", renderSteps(steps));
    safeSetInnerHTML("modules-grid", renderModules(modules));
    safeSetInnerHTML("ai-input-list", renderAIInputs(aiInputs));
    safeSetInnerHTML("ai-output-cards", renderAIOutputs(aiOutputs, 0));
    safeSetInnerHTML("ai-output-cards-2", renderAIOutputs(aiOutputs, 2));
    safeSetInnerHTML("global-stats", renderGlobalStats(globalStats));
    safeSetInnerHTML("testimonial-grid", renderTestimonials(testimonials));
    safeSetInnerHTML("faq-container", renderFAQs(faqs));

    const footerHtmlArray = footerCols.map(c => \`
        <div>
            <h4 class="font-semibold text-sm mb-4" style="color:var(--dark);">\${c.title}</h4>
            <ul class="space-y-2">
                \${c.links.map(l => \`<li><a href="#" class="text-sm" style="color:var(--gray);">\${l}</a></li>\`).join('')}
            </ul>
        </div>
    \`);
    
    safeSetInnerHTML("footer-col-1", footerHtmlArray[0] || '');
    safeSetInnerHTML("footer-col-2", footerHtmlArray[1] || '');
    safeSetInnerHTML("footer-col-3", footerHtmlArray[2] || '');

    // EVENT DELEGATION
    document.addEventListener("click", function(e) {
        const link = e.target.closest("a[href^='#']");
        if (link) {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute("href"));
            if (target) {
                target.scrollIntoView({ behavior: "smooth" });
            }
        }

        // FAQ toggle
        const faqItem = e.target.closest(".faq-item");
        if (faqItem) {
            faqItem.classList.toggle("active");
        }

        // Button clicks from CTA (prevent default if needed)
        const btn = e.target.closest("button");
        if (btn && !btn.closest("a")) {
            // e.preventDefault();
            // Could add analytics or modals here
        }
    });

        
        // Add auth links dynamically to the header
        const authContainer = document.createElement('div');
        authContainer.className = "flex items-center gap-3";
        authContainer.innerHTML = auth.user 
            ? '<button id="btn-dashboard" class="btn-primary text-sm py-2.5 px-5">Go to Dashboard <i class="fas fa-arrow-right text-xs"></i></button>'
            : '<button id="btn-login" class="hidden sm:inline text-sm font-medium" style="color:var(--dark);">Sign In</button><button id="btn-register" class="btn-primary text-sm py-2.5 px-5">Get Started <i class="fas fa-arrow-right text-xs"></i></button>';
            
        const headerRight = containerRef.current.querySelector('header > div > div:last-child');
        if (headerRight) {
            headerRight.replaceWith(authContainer);
        }

        const btnDashboard = containerRef.current.querySelector('#btn-dashboard');
        const btnLogin = containerRef.current.querySelector('#btn-login');
        const btnRegister = containerRef.current.querySelector('#btn-register');
        const btnHeroDashboard = containerRef.current.querySelector('#btn-hero-dashboard');
        const btnHeroTracking = containerRef.current.querySelector('#btn-hero-tracking');

        if (btnDashboard) btnDashboard.addEventListener('click', () => router.visit('/dashboard'));
        if (btnLogin) btnLogin.addEventListener('click', () => router.visit('/login'));
        if (btnRegister) btnRegister.addEventListener('click', () => router.visit('/register'));
        if (btnHeroDashboard) btnHeroDashboard.addEventListener('click', (e) => { e.preventDefault(); router.visit('/dashboard'); });
        if (btnHeroTracking) btnHeroTracking.addEventListener('click', (e) => { e.preventDefault(); router.visit('/tracking'); });

        // INIT INTERACTIVE MAPS FOR PLACEHOLDERS
        if (!window.L) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = initMaps;
            document.head.appendChild(script);
        } else {
            initMaps();
        }

        function initMaps() {
            const mapEls = containerRef.current.querySelectorAll('.map-placeholder');
            mapEls.forEach((el, index) => {
                const id = 'preview-map-' + index;
                el.id = id;
                el.innerHTML = ''; // clear the placeholder icons
                // The container must have a fixed height which it already does via inline styles
                const map = window.L.map(id, { zoomControl: false, attributionControl: false, dragging: false, scrollWheelZoom: false, doubleClickZoom: false }).setView([20, 10], 1);
                window.L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
                
                // Add some dummy live markers
                window.L.circleMarker([40.71, -74.00], { color: '#F03164', radius: 6, fillOpacity: 1, weight: 2, fillColor: '#F03164' }).addTo(map);
                window.L.circleMarker([51.50, -0.12], { color: '#48BB78', radius: 5, fillOpacity: 1, weight: 2, fillColor: '#48BB78' }).addTo(map);
                window.L.circleMarker([1.35, 103.81], { color: '#F6AD55', radius: 7, fillOpacity: 1, weight: 2, fillColor: '#F6AD55' }).addTo(map);
                window.L.circleMarker([31.23, 121.47], { color: '#FC8181', radius: 5, fillOpacity: 1, weight: 2, fillColor: '#FC8181' }).addTo(map);
                window.L.circleMarker([-23.55, -46.63], { color: '#6366F1', radius: 6, fillOpacity: 1, weight: 2, fillColor: '#6366F1' }).addTo(map);
                window.L.circleMarker([25.20, 55.27], { color: '#F03164', radius: 4, fillOpacity: 1, weight: 2, fillColor: '#F03164' }).addTo(map);
                
                // Trigger resize after a small delay to ensure tiles load correctly in dynamic containers
                setTimeout(() => map.invalidateSize(), 500);
            });
        }
    }, []);

    return (
        <>
            <Head title="Global Chain — AI-Powered Global Trade Intelligence">
                <style>{\`${style}\`}</style>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
            </Head>
            <div ref={containerRef} dangerouslySetInnerHTML={{ __html: ${escapedHtmlBody} }} />
        </>
    );
}
`;

fs.writeFileSync('resources/js/Pages/Welcome.tsx', finalReactCode);

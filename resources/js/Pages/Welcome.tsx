// @ts-nocheck
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
        return items.map(i => `<li><a href="${i.href}" class="text-sm font-medium transition" style="color:var(--gray);">${i.label}</a></li>`).join("");
    }

    function renderTrustedLogos(items) {
        return items.map(i => `<div class="flex items-center gap-2 text-lg font-semibold" style="color:var(--dark);"><i class="fas ${i.icon}"></i> ${i.name}</div>`).join("");
    }

    function renderFeatures(items) {
        return items.map(f => `
            <div class="glass-card p-7 flex flex-col gap-4 cursor-pointer">
                <div class="glass-icon"><i class="fas ${f.icon}"></i></div>
                <h3 class="font-bold text-base" style="color:var(--dark);">${f.title}</h3>
                <p class="text-sm" style="color:var(--gray);line-height:1.7;">${f.desc}</p>
            </div>
        `).join("");
    }

    function renderSteps(items) {
        return items.map((s, idx) => `
            <div class="text-center">
                <div style="width:72px;height:72px;border-radius:24px;background:rgba(240,49,100,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:var(--primary);font-size:1.6rem;"><i class="fas ${s.icon}"></i></div>
                <div class="text-xs font-semibold mb-1" style="color:var(--primary);">Step ${s.number}</div>
                <h3 class="font-bold text-lg mb-2" style="color:var(--dark);">${s.title}</h3>
                <p class="text-sm" style="color:var(--gray);line-height:1.7;">${s.desc}</p>
                ${idx < items.length - 1 ? '<div class="step-connector mt-6 mx-auto"></div>' : ''}
            </div>
        `).join("");
    }

    function renderModules(items) {
        return items.map(m => `
            <div class="glass-card p-4 flex flex-col items-center justify-center gap-2 text-center cursor-pointer" style="min-height:110px;">
                <div style="width:40px;height:40px;border-radius:14px;background:rgba(240,49,100,0.08);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.1rem;"><i class="fas ${m.icon}"></i></div>
                <span class="text-xs font-semibold" style="color:var(--dark);">${m.label}</span>
            </div>
        `).join("");
    }

    function renderAIInputs(items) {
        return items.map(i => `
            <li class="flex items-center gap-3 text-sm" style="color:var(--gray);">
                <i class="fas ${i.icon}" style="color:var(--primary);width:18px;text-align:center;"></i> ${i.label}
            </li>
        `).join("");
    }

    function renderAIOutputs(items, offset) {
        return items.slice(offset, offset + 2).map(o => `
            <div class="glass-card p-4 flex items-center gap-3">
                <div style="width:40px;height:40px;border-radius:14px;background:rgba(240,49,100,0.08);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1rem;"><i class="fas ${o.icon}"></i></div>
                <div>
                    <div class="text-xs font-semibold" style="color:var(--dark);">${o.label}</div>
                    <span style="color:var(--gray);font-size:0.75rem;">${o.value}</span>
                </div>
            </div>
        `).join("");
    }

    function renderGlobalStats(items) {
        return items.map(s => `
            <div class="glass p-5 rounded-3xl text-center">
                <div class="text-2xl font-extrabold" style="color:var(--dark);">${s.value}</div>
                <div class="text-xs" style="color:var(--gray);">${s.label}</div>
            </div>
        `).join("");
    }

    function renderTestimonials(items) {
        return items.map(t => `
            <div class="glass-card p-8 flex flex-col gap-4">
                <div class="flex gap-1" style="color:#F6AD55;">
                    ${Array(t.rating).fill('<i class="fas fa-star"></i>').join('')}
                </div>
                <p class="text-sm leading-relaxed" style="color:var(--gray);">"${t.text}\"</p>
                <div>
                    <div class="font-semibold text-sm" style="color:var(--dark);">${t.name}</div>
                    <div class="text-xs" style="color:var(--gray);">${t.role}</div>
                </div>
            </div>
        `).join("");
    }

    function renderFAQs(items) {
        return items.map((f, idx) => `
            <div class="faq-item" data-index="${idx}">
                <div class="faq-question">
                    ${f.q}
                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="faq-answer">${f.a}</div>
            </div>
        `).join("");
    }

    function renderFooterCols(cols) {
        return cols.map(c => `
            <div>
                <h4 class="font-semibold text-sm mb-4" style="color:var(--dark);">${c.title}</h4>
                <ul class="space-y-2">
                    ${c.links.map(l => `<li><a href="#" class="text-sm" style="color:var(--gray);">${l}</a></li>`).join('')}
                </ul>
            </div>
        `).join("");
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

    const footerHtmlArray = footerCols.map(c => `
        <div>
            <h4 class="font-semibold text-sm mb-4" style="color:var(--dark);">${c.title}</h4>
            <ul class="space-y-2">
                ${c.links.map(l => `<li><a href="#" class="text-sm" style="color:var(--gray);">${l}</a></li>`).join('')}
            </ul>
        </div>
    `);
    
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
                <style>{`
        :root {
            --primary: #F03164;
            --primary-light: #F86E8F;
            --primary-dark: #C81E4E;
            --dark: #2D3748;
            --dark-light: #4A5568;
            --gray: #718096;
            --light: #F7FAFC;
            --bg-gradient-1: #FDF2F8;
            --bg-gradient-2: #EDF2F7;
            --bg-gradient-3: #E9E4F0;
            --shadow-soft: 0 8px 32px rgba(0,0,0,0.06);
            --shadow-glass: 0 8px 40px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-1), var(--bg-gradient-2), var(--bg-gradient-3));
            color: var(--dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .glass {
            background: rgba(255,255,255,0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: var(--shadow-glass);
        }
        .glass-card {
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: var(--shadow-soft);
            transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
            border-radius: 32px;
        }
        .glass-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 48px rgba(240,49,100,0.12);
            border-color: rgba(240,49,100,0.15);
            background: rgba(255,255,255,0.75);
        }
        .glass-card-lg {
            background: rgba(255,255,255,0.5);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: var(--shadow-glass);
            border-radius: 40px;
            transition: all 0.4s ease;
        }
        .glass-card-lg:hover {
            box-shadow: 0 20px 60px rgba(240,49,100,0.10);
        }
        .glass-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(240,49,100,0.12), rgba(240,49,100,0.04));
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.4rem;
            border: 1px solid rgba(240,49,100,0.1);
            transition: all 0.3s ease;
        }
        .glass-icon-lg {
            width: 72px;
            height: 72px;
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(240,49,100,0.15), rgba(240,49,100,0.04));
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.8rem;
            border: 1px solid rgba(240,49,100,0.1);
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 14px 32px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(240,49,100,0.25);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(240,49,100,0.35);
        }
        .btn-outline {
            background: transparent;
            color: var(--dark);
            padding: 14px 32px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 0.95rem;
            border: 1.5px solid rgba(45,55,72,0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(240,49,100,0.04);
            transform: translateY(-2px);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            border-radius: 40px;
            background: rgba(240,49,100,0.08);
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            border: 1px solid rgba(240,49,100,0.1);
        }
        .section-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.03em;
            color: var(--dark);
        }
        .section-subtitle {
            font-size: 1.1rem;
            color: var(--gray);
            max-width: 600px;
            margin: 16px auto 0;
            line-height: 1.7;
        }
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .step-connector {
            width: 2px;
            height: 60px;
            background: linear-gradient(to bottom, var(--primary), rgba(240,49,100,0.05));
            margin: 0 auto;
        }
        .map-placeholder {
            background: linear-gradient(135deg, #e2e8f0, #edf2f7, #e9e4f0);
            border-radius: 28px;
            position: relative;
            overflow: hidden;
        }
        .map-placeholder::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 40%, rgba(240,49,100,0.03), transparent 60%),
                        radial-gradient(circle at 70% 60%, rgba(99,102,241,0.03), transparent 60%);
        }
        .pricing-card {
            border-radius: 36px;
            transition: all 0.4s ease;
            background: rgba(255,255,255,0.5);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.4);
        }
        .pricing-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 60px rgba(240,49,100,0.10);
        }
        .pricing-card.featured {
            border: 1.5px solid rgba(240,49,100,0.2);
            box-shadow: 0 8px 40px rgba(240,49,100,0.08);
        }
        .faq-item {
            border-bottom: 1px solid rgba(45,55,72,0.06);
            padding: 24px 0;
            cursor: pointer;
        }
        .faq-item:last-child { border-bottom: none; }
        .faq-question {
            font-weight: 600;
            font-size: 1.05rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--dark);
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease;
            opacity: 0;
            color: var(--gray);
            font-size: 0.95rem;
            line-height: 1.7;
        }
        .faq-item.active .faq-answer {
            max-height: 200px;
            opacity: 1;
            padding-top: 16px;
        }
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
            color: var(--primary);
        }
        .faq-icon {
            transition: transform 0.3s ease;
            font-size: 1.1rem;
            color: var(--gray);
        }
        @media (max-width: 768px) {
            .section-title { font-size: 2rem; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        .float-anim { animation: float 6s ease-in-out infinite; }
        .float-anim-delay { animation: float 6s ease-in-out 2s infinite; }
        .float-anim-delay-2 { animation: float 6s ease-in-out 4s infinite; }
    `}</style>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
            </Head>
            <div ref={containerRef} dangerouslySetInnerHTML={{ __html: "\n\n<header class=\"fixed top-0 left-0 w-full z-50 glass\" style=\"border-bottom: 1px solid rgba(255,255,255,0.3);\">\n    <div class=\"max-w-7xl mx-auto px-6 lg:px-8 h-16 flex items-center justify-between\">\n        <div class=\"flex items-center gap-2\">\n            <svg width=\"36\" height=\"36\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"var(--primary)\" stroke-width=\"2.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"12\" cy=\"12\" r=\"2.5\"></circle><line x1=\"12\" y1=\"9.5\" x2=\"12\" y2=\"4.5\"></line><circle cx=\"12\" cy=\"3.5\" r=\"1\"></circle><line x1=\"9.5\" y1=\"12\" x2=\"4.5\" y2=\"12\"></line><circle cx=\"3.5\" cy=\"12\" r=\"1\"></circle><line x1=\"14.5\" y1=\"12\" x2=\"19.5\" y2=\"12\"></line><circle cx=\"20.5\" cy=\"12\" r=\"1\"></circle><line x1=\"10.2\" y1=\"13.8\" x2=\"6.8\" y2=\"18.2\"></line><circle cx=\"5.5\" cy=\"19.5\" r=\"1\"></circle><line x1=\"13.8\" y1=\"13.8\" x2=\"17.2\" y2=\"18.2\"></line><circle cx=\"18.5\" cy=\"19.5\" r=\"1\"></circle></svg>\n            <span class=\"font-bold text-lg text-dark\" style=\"color:var(--dark);\">Global Chain</span>\n        </div>\n        <nav class=\"hidden md:flex items-center gap-8\">\n            <ul id=\"nav-list\" class=\"flex items-center gap-8 list-none\"></ul>\n        </nav>\n        <div class=\"flex items-center gap-3\">\n            <a href=\"#\" class=\"hidden sm:inline text-sm font-medium\" style=\"color:var(--dark);\">Sign In</a>\n            <button class=\"btn-primary text-sm py-2.5 px-5\">Get Started <i class=\"fas fa-arrow-right text-xs\"></i></button>\n        </div>\n    </div>\n</header>\n\n<main>\n\n    <!-- HERO -->\n    <section class=\"pt-32 pb-24 lg:pb-32 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center\">\n            <div>\n                <div class=\"badge mb-6\"><i class=\"fas fa-brain\"></i> AI-Powered Global Trade Intelligence</div>\n                <h1 class=\"text-5xl lg:text-7xl font-extrabold leading-tight tracking-tight mb-6\" style=\"color:var(--dark);\">\n                    Make Smarter <span style=\"color:var(--primary);\">Import & Export</span> Decisions<br>\n                    Across The Globe.\n                </h1>\n                <p class=\"text-lg mb-8\" style=\"color:var(--gray);max-width:540px;line-height:1.8;\">\n                    Combine real-time vessel tracking, country intelligence, commodity analytics, and an \n                    <strong style=\"color:var(--dark);\">AI decision engine</strong> to optimize every trade route.\n                </p>\n                <div class=\"flex flex-wrap gap-4 mb-8\">\n                    <button id=\"btn-hero-dashboard\" class=\"btn-primary\"><i class=\"fas fa-chart-line\"></i> Go to Dashboard</button>\n                    <button id=\"btn-hero-tracking\" class=\"btn-outline\"><i class=\"fas fa-map-marked-alt\"></i> Live Tracking Map</button>\n                </div>\n                <div class=\"flex flex-wrap gap-5 text-sm\" style=\"color:var(--gray);\">\n                    <span><i class=\"fas fa-credit-card mr-1.5\" style=\"color:var(--primary);\"></i> No Credit Card</span>\n                    <span><i class=\"fas fa-calendar-alt mr-1.5\" style=\"color:var(--primary);\"></i> 14-Day Free Trial</span>\n                    <span><i class=\"fas fa-shield-alt mr-1.5\" style=\"color:var(--primary);\"></i> Enterprise Ready</span>\n                    <span><i class=\"fas fa-lock mr-1.5\" style=\"color:var(--primary);\"></i> ISO Security</span>\n                </div>\n            </div>\n            <div class=\"relative\">\n                <!-- Dashboard Mockup -->\n                <div class=\"glass-card-lg p-6 lg:p-8 relative z-10\">\n                    <div class=\"grid grid-cols-12 gap-3\">\n                        <div class=\"col-span-12 flex items-center justify-between mb-3\">\n                            <div class=\"flex gap-2 text-xs font-semibold\" style=\"color:var(--gray);\">\n                                <span style=\"color:var(--primary);\"><i class=\"fas fa-circle\"></i> Live</span>\n                                <span>Global Trade Dashboard</span>\n                            </div>\n                            <div class=\"flex gap-1\">\n                                <span style=\"width:8px;height:8px;border-radius:50%;background:#48BB78;\"></span>\n                                <span style=\"width:8px;height:8px;border-radius:50%;background:#F6AD55;\"></span>\n                                <span style=\"width:8px;height:8px;border-radius:50%;background:#FC8181;\"></span>\n                            </div>\n                        </div>\n                        <!-- Map area -->\n                        <div class=\"col-span-12 lg:col-span-8 map-placeholder\" style=\"height:200px;border-radius:20px;\">\n                            <div class=\"absolute inset-0 flex items-center justify-center\">\n                                <div style=\"width:80%;height:80%;position:relative;\">\n                                    <i class=\"fas fa-globe-americas text-6xl\" style=\"color:rgba(45,55,72,0.08);\"></i>\n                                    <span style=\"position:absolute;top:20%;left:30%;width:10px;height:10px;border-radius:50%;background:var(--primary);box-shadow:0 0 20px rgba(240,49,100,0.5);\"></span>\n                                    <span style=\"position:absolute;top:50%;left:60%;width:8px;height:8px;border-radius:50%;background:#48BB78;box-shadow:0 0 16px rgba(72,187,120,0.5);\"></span>\n                                    <span style=\"position:absolute;bottom:30%;left:45%;width:6px;height:6px;border-radius:50%;background:#F6AD55;box-shadow:0 0 12px rgba(246,173,85,0.5);\"></span>\n                                    <span style=\"position:absolute;top:60%;left:20%;width:7px;height:7px;border-radius:50%;background:#FC8181;box-shadow:0 0 14px rgba(252,129,129,0.5);\"></span>\n                                </div>\n                            </div>\n                        </div>\n                        <div class=\"col-span-12 lg:col-span-4 flex flex-col gap-2\">\n                            <div class=\"glass p-3 rounded-2xl text-xs\" style=\"background:rgba(255,255,255,0.5);backdrop-filter:blur(8px);\">\n                                <span style=\"color:var(--gray);\">Revenue</span>\n                                <div class=\"font-bold text-lg\" style=\"color:var(--dark);\">$2.4B <span style=\"color:#48BB78;font-size:0.7rem;\">+12.3%</span></div>\n                                <div style=\"height:3px;background:#e2e8f0;border-radius:4px;margin-top:4px;\"><div style=\"width:73%;height:100%;background:linear-gradient(90deg,var(--primary),#48BB78);border-radius:4px;\"></div></div>\n                            </div>\n                            <div class=\"glass p-3 rounded-2xl text-xs\" style=\"background:rgba(255,255,255,0.5);backdrop-filter:blur(8px);\">\n                                <span style=\"color:var(--gray);\">Risk Index</span>\n                                <div class=\"font-bold text-lg\" style=\"color:var(--dark);\">72 <span style=\"color:var(--gray);font-size:0.7rem;\">/100</span></div>\n                                <div style=\"height:3px;background:#e2e8f0;border-radius:4px;margin-top:4px;\"><div style=\"width:72%;height:100%;background:linear-gradient(90deg,#F6AD55,var(--primary));border-radius:4px;\"></div></div>\n                            </div>\n                        </div>\n                        <!-- Bottom KPI row -->\n                        <div class=\"col-span-6 lg:col-span-3 glass p-3 rounded-2xl text-xs\">\n                            <span style=\"color:var(--gray);\">Vessels Tracked</span>\n                            <div class=\"font-bold text-base mt-1\" style=\"color:var(--dark);\">12,847</div>\n                        </div>\n                        <div class=\"col-span-6 lg:col-span-3 glass p-3 rounded-2xl text-xs\">\n                            <span style=\"color:var(--gray);\">Commodities</span>\n                            <div class=\"font-bold text-base mt-1\" style=\"color:var(--dark);\">1,423</div>\n                        </div>\n                        <div class=\"col-span-6 lg:col-span-3 glass p-3 rounded-2xl text-xs\">\n                            <span style=\"color:var(--gray);\">Countries</span>\n                            <div class=\"font-bold text-base mt-1\" style=\"color:var(--dark);\">198</div>\n                        </div>\n                        <div class=\"col-span-6 lg:col-span-3 glass p-3 rounded-2xl text-xs\">\n                            <span style=\"color:var(--gray);\">AI Rec.</span>\n                            <div class=\"font-bold text-base mt-1\" style=\"color:var(--primary);\">Active</div>\n                        </div>\n                    </div>\n                </div>\n                <!-- Floating glass card -->\n                <div class=\"glass-card p-4 absolute -bottom-4 -left-4 lg:-left-8 lg:-bottom-8 z-20 float-anim\" style=\"width:180px;\">\n                    <div class=\"flex items-center gap-3\">\n                        <div style=\"width:40px;height:40px;border-radius:14px;background:rgba(240,49,100,0.1);display:flex;align-items:center;justify-content:center;color:var(--primary);\"><i class=\"fas fa-ship\"></i></div>\n                        <div class=\"text-xs\">\n                            <div class=\"font-semibold\" style=\"color:var(--dark);\">Vessel ETA</div>\n                            <span style=\"color:var(--gray);\">Singapore · 2 days</span>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"glass-card p-4 absolute -top-4 -right-4 lg:-top-8 lg:-right-8 z-20 float-anim-delay\" style=\"width:180px;\">\n                    <div class=\"flex items-center gap-3\">\n                        <div style=\"width:40px;height:40px;border-radius:14px;background:rgba(72,187,120,0.1);display:flex;align-items:center;justify-content:center;color:#48BB78;\"><i class=\"fas fa-chart-line\"></i></div>\n                        <div class=\"text-xs\">\n                            <div class=\"font-semibold\" style=\"color:var(--dark);\">AI Prediction</div>\n                            <span style=\"color:var(--gray);\">Profit +8.4%</span>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </section>\n\n    <!-- TRUSTED BY -->\n    <section class=\"py-16 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto\">\n            <p class=\"text-center text-sm uppercase tracking-widest mb-10\" style=\"color:var(--gray);\">Trusted by the world's leading logistics enterprises</p>\n            <div id=\"trusted-logos\" class=\"flex flex-wrap justify-center items-center gap-10 lg:gap-16 opacity-60 grayscale\"></div>\n        </div>\n    </section>\n\n    <!-- FEATURES -->\n    <section class=\"py-20 lg:py-28 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto\">\n            <div class=\"text-center mb-16\">\n                <span id=\"features\" style=\"scroll-margin-top: 100px; display: inline-block;\" class=\"badge mb-4\"><i class=\"fas fa-bolt\"></i> Platform Features</span>\n                <h2 class=\"section-title\">Everything you need to dominate global trade</h2>\n                <p class=\"section-subtitle\">From real-time tracking to AI-powered recommendations, Global Chain puts the world's trade data at your fingertips.</p>\n            </div>\n            <div id=\"feature-grid\" class=\"grid sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6\"></div>\n        </div>\n    </section>\n\n    <!-- HOW IT WORKS -->\n    <section class=\"py-20 lg:py-28 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto\">\n            <div class=\"text-center mb-16\">\n                <span class=\"badge mb-4\"><i class=\"fas fa-layer-group\"></i> How It Works</span>\n                <h2 class=\"section-title\">From shipment to insight in four steps</h2>\n                <p class=\"section-subtitle\">A streamlined workflow designed for enterprise efficiency.</p>\n            </div>\n            <div id=\"steps-container\" class=\"grid sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12\"></div>\n        </div>\n    </section>\n\n    <!-- PLATFORM MODULES -->\n    <section class=\"py-20 lg:py-28 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto\">\n            <div class=\"text-center mb-16\">\n                <span id=\"modules\" style=\"scroll-margin-top: 100px; display: inline-block;\" class=\"badge mb-4\"><i class=\"fas fa-cubes\"></i> Platform Modules</span>\n                <h2 class=\"section-title\">Modular intelligence for every trade need</h2>\n                <p class=\"section-subtitle\">Each module is a powerful engine, seamlessly integrated into your workflow.</p>\n            </div>\n            <div id=\"modules-grid\" class=\"grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-8 gap-4 lg:gap-5\"></div>\n        </div>\n    </section>\n\n    <!-- AI SECTION -->\n    <section class=\"py-20 lg:py-28 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto\">\n            <div class=\"grid lg:grid-cols-2 gap-16 items-center\">\n                <div>\n                    <span class=\"badge mb-4\"><i class=\"fas fa-microchip\"></i> AI Intelligence Engine</span>\n                    <h2 class=\"section-title mb-6\">Your personal <span class=\"text-gradient\">trade strategist</span> powered by AI</h2>\n                    <p class=\"mb-6\" style=\"color:var(--gray);line-height:1.8;\">\n                        Our AI analyzes millions of data points — commodity prices, country risk, weather, currency fluctuations, political stability, port congestion, demand, and supply — then generates actionable recommendations in seconds.\n                    </p>\n                    <ul id=\"ai-input-list\" class=\"space-y-3 mb-8\"></ul>\n                    <div class=\"glass-card p-6 flex items-center gap-4\">\n                        <div style=\"width:48px;height:48px;border-radius:16px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:white;\"><i class=\"fas fa-wand-magic-sparkles\"></i></div>\n                        <div>\n                            <div class=\"font-semibold text-sm\" style=\"color:var(--dark);\">AI Generated Recommendation</div>\n                            <span style=\"color:var(--gray);font-size:0.85rem;\">Export electronics to Vietnam · Profit prediction: +14.2%</span>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"grid grid-cols-2 gap-4\">\n                    <div id=\"ai-output-cards\" class=\"space-y-4\"></div>\n                    <div id=\"ai-output-cards-2\" class=\"space-y-4 mt-8\"></div>\n                </div>\n            </div>\n        </div>\n    </section>\n\n    <!-- GLOBAL MAP + STATS -->\n    <section class=\"py-20 lg:py-28 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto\">\n            <div class=\"glass-card-lg p-8 lg:p-12\">\n                <div class=\"grid lg:grid-cols-2 gap-10 items-center\">\n                    <div>\n                        <span class=\"badge mb-4\"><i class=\"fas fa-globe\"></i> Global Intelligence</span>\n                        <h2 class=\"text-3xl lg:text-4xl font-extrabold tracking-tight mb-4\" style=\"color:var(--dark);\">Live global trade <span class=\"text-gradient\">at a glance</span></h2>\n                        <p style=\"color:var(--gray);line-height:1.8;\">Track vessels, monitor port congestion, and analyze trade flows in real time. Our interactive map updates every 30 seconds.</p>\n                        <div id=\"global-stats\" class=\"grid grid-cols-2 gap-4 mt-8\"></div>\n                    </div>\n                    <div class=\"map-placeholder\" style=\"height:320px;border-radius:28px;\">\n                        <div class=\"absolute inset-0 flex items-center justify-center\">\n                            <div style=\"text-align:center;position:relative;\">\n                                <i class=\"fas fa-map-marked-alt text-7xl\" style=\"color:rgba(45,55,72,0.06);\"></i>\n                                <span style=\"position:absolute;top:20%;left:30%;width:12px;height:12px;border-radius:50%;background:var(--primary);box-shadow:0 0 30px rgba(240,49,100,0.4);\"></span>\n                                <span style=\"position:absolute;top:45%;left:60%;width:10px;height:10px;border-radius:50%;background:#48BB78;box-shadow:0 0 20px rgba(72,187,120,0.4);\"></span>\n                                <span style=\"position:absolute;bottom:30%;left:50%;width:8px;height:8px;border-radius:50%;background:#F6AD55;box-shadow:0 0 16px rgba(246,173,85,0.4);\"></span>\n                                <span style=\"position:absolute;top:65%;left:25%;width:9px;height:9px;border-radius:50%;background:#FC8181;box-shadow:0 0 18px rgba(252,129,129,0.4);\"></span>\n                                <div class=\"mt-4 font-semibold text-sm\" style=\"color:var(--gray);\">Live vessels being tracked</div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </section>\n\n    <!-- TESTIMONIALS -->\n    <section class=\"py-20 lg:py-28 px-6 lg:px-8\">\n        <div class=\"max-w-7xl mx-auto\">\n            <div class=\"text-center mb-16\">\n                <span class=\"badge mb-4\"><i class=\"fas fa-quote-left\"></i> Testimonials</span>\n                <h2 class=\"section-title\">Trusted by trade leaders worldwide</h2>\n            </div>\n            <div id=\"testimonial-grid\" class=\"grid md:grid-cols-3 gap-6 lg:gap-8\"></div>\n        </div>\n    </section>\n\n    <!-- FAQ -->\n    <section class=\"py-20 lg:py-28 px-6 lg:px-8\">\n        <div class=\"max-w-4xl mx-auto\">\n            <div class=\"text-center mb-16\">\n                <span id=\"faq\" style=\"scroll-margin-top: 100px; display: inline-block;\" class=\"badge mb-4\"><i class=\"fas fa-question-circle\"></i> FAQ</span>\n                <h2 class=\"section-title\">Frequently asked questions</h2>\n            </div>\n            <div id=\"faq-container\"></div>\n        </div>\n    </section>\n</main>\n\n<footer class=\"glass py-16 px-6 lg:px-8\" style=\"border-top:1px solid rgba(255,255,255,0.3);\">\n    <div class=\"max-w-7xl mx-auto\">\n        <div class=\"grid sm:grid-cols-2 lg:grid-cols-5 gap-10 mb-12\">\n            <div class=\"lg:col-span-2\">\n                <div class=\"flex items-center gap-2 mb-4\">\n                    <svg width=\"36\" height=\"36\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"var(--primary)\" stroke-width=\"2.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"12\" cy=\"12\" r=\"2.5\"></circle><line x1=\"12\" y1=\"9.5\" x2=\"12\" y2=\"4.5\"></line><circle cx=\"12\" cy=\"3.5\" r=\"1\"></circle><line x1=\"9.5\" y1=\"12\" x2=\"4.5\" y2=\"12\"></line><circle cx=\"3.5\" cy=\"12\" r=\"1\"></circle><line x1=\"14.5\" y1=\"12\" x2=\"19.5\" y2=\"12\"></line><circle cx=\"20.5\" cy=\"12\" r=\"1\"></circle><line x1=\"10.2\" y1=\"13.8\" x2=\"6.8\" y2=\"18.2\"></line><circle cx=\"5.5\" cy=\"19.5\" r=\"1\"></circle><line x1=\"13.8\" y1=\"13.8\" x2=\"17.2\" y2=\"18.2\"></line><circle cx=\"18.5\" cy=\"19.5\" r=\"1\"></circle></svg>\n                    <span class=\"font-bold text-lg\" style=\"color:var(--dark);\">Global Chain</span>\n                </div>\n                <p style=\"color:var(--gray);font-size:0.9rem;max-width:300px;\">The world's leading AI-powered global trade intelligence platform. Empowering enterprises with real-time data, predictive analytics, and actionable insights.</p>\n            </div>\n            <div id=\"footer-col-1\"></div>\n            <div id=\"footer-col-2\"></div>\n            <div id=\"footer-col-3\"></div>\n        </div>\n        <div class=\"flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t\" style=\"border-color:rgba(45,55,72,0.06);\">\n            <span style=\"color:var(--gray);font-size:0.8rem;\">&copy; 2025 Global Chain. All rights reserved.</span>\n            <div class=\"flex gap-4\">\n                <a href=\"#\" class=\"text-sm\" style=\"color:var(--gray);\"><i class=\"fab fa-twitter\"></i></a>\n                <a href=\"#\" class=\"text-sm\" style=\"color:var(--gray);\"><i class=\"fab fa-linkedin-in\"></i></a>\n                <a href=\"#\" class=\"text-sm\" style=\"color:var(--gray);\"><i class=\"fab fa-github\"></i></a>\n            </div>\n        </div>\n    </div>\n</footer>\n\n\n" }} />
        </>
    );
}

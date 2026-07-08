document.addEventListener('DOMContentLoaded', function() {
    // 1. Number Animations using CountUp.js
    const countOptions = {
        useEasing: true,
        useGrouping: true,
        separator: ',',
        decimal: '.',
        duration: 2.5
    };

    if (document.getElementById('kpi-shipments')) {
        let count1 = new countUp.CountUp('kpi-shipments', 12450, countOptions);
        if (!count1.error) count1.start();
    }
    
    if (document.getElementById('kpi-transit')) {
        let count2 = new countUp.CountUp('kpi-transit', 8230, countOptions);
        if (!count2.error) count2.start();
    }
    
    if (document.getElementById('kpi-delayed')) {
        let count3 = new countUp.CountUp('kpi-delayed', 142, countOptions);
        if (!count3.error) count3.start();
    }
    
    if (document.getElementById('kpi-revenue')) {
        let options = {...countOptions, prefix: '$', suffix: 'M', decimalPlaces: 1};
        let count4 = new countUp.CountUp('kpi-revenue', 4.2, options);
        if (!count4.error) count4.start();
    }

    // 2. Global Fleet Live Tracking Map
    if(document.getElementById('global-map')) {
        window.globalMap = L.map('global-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([20, 10], 3);
        
        // Clean Minimal Light Theme (CartoDB Positron)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(window.globalMap);

        L.control.zoom({ position: 'topright' }).addTo(window.globalMap);

        // Define ships with routes
        const shipsData = [
            { id: 1, lat: 34.05, lng: -118.24, name: "MSC Oliver", status: "transit", dest: "Shanghai", risk: "Low", eta: "2 Days" },
            { id: 2, lat: 31.23, lng: 121.47, name: "Maersk Mc-Kinney", status: "delayed", dest: "Los Angeles", risk: "High", eta: "Unknown" },
            { id: 3, lat: 1.35, lng: 103.81, name: "CMA CGM Bougainville", status: "transit", dest: "Rotterdam", risk: "Low", eta: "5 Days" },
            { id: 4, lat: 51.50, lng: -0.12, name: "Ever Given", status: "transit", dest: "New York", risk: "Low", eta: "1 Day" },
            { id: 5, lat: -33.86, lng: 151.20, name: "COSCO Shipping", status: "transit", dest: "Tokyo", risk: "Medium", eta: "12 Days" },
            { id: 6, lat: 25.20, lng: 55.27, name: "Hapag-Lloyd Express", status: "transit", dest: "Singapore", risk: "Low", eta: "3 Days" },
            { id: 7, lat: 29.97, lng: 32.55, name: "OOCL Hong Kong", status: "delayed", dest: "London", risk: "High", eta: "Delayed" }
        ];

        window.shipMarkers = {};

        shipsData.forEach(ship => {
            const shipIcon = L.divIcon({
                html: `<div class="custom-ship-marker ${ship.status}" style="width: 16px; height: 16px;"></div>`,
                className: '',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            const popupContent = `
                <div style="font-family: 'Inter', sans-serif; min-width: 180px; background: rgba(255,255,255,0.9); padding: 5px;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: #1F2937; margin-bottom: 5px;">${ship.name}</div>
                    <div style="font-size: 0.85rem; color: #6B7280; margin-bottom: 10px;">Destination: <b style="color:#1F2937;">${ship.dest}</b></div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #eee; padding-top: 8px;">
                        <span style="font-size: 0.8rem; font-weight:600; color: ${ship.status==='delayed' ? '#EF4444' : '#22C55E'}">${ship.status.toUpperCase()}</span>
                        <span style="font-size: 0.8rem; font-weight:600; color: #5DAEFF;">ETA: ${ship.eta}</span>
                    </div>
                </div>
            `;

            const marker = L.marker([ship.lat, ship.lng], {icon: shipIcon})
             .bindPopup(popupContent)
             .addTo(window.globalMap);
             
            window.shipMarkers[ship.id] = marker;
        });

        // Ship animation
        setInterval(() => {
            shipsData.forEach(ship => {
                if (ship.status !== 'delayed') {
                    ship.lng += (Math.random() * 0.5 - 0.2); 
                    ship.lat += (Math.random() * 0.2 - 0.1);
                    
                    const marker = window.shipMarkers[ship.id];
                    if(marker) {
                        marker.setLatLng([ship.lat, ship.lng]);
                    }
                }
            });
        }, 2000);
    }
    
    // 3. Chart.js Minimalist Settings & Initialization
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.weight = "400";
    Chart.defaults.color = "#9CA3AF";
    Chart.defaults.plugins.tooltip.backgroundColor = "rgba(255, 255, 255, 0.9)";
    Chart.defaults.plugins.tooltip.titleColor = "#374151";
    Chart.defaults.plugins.tooltip.bodyColor = "#374151";
    Chart.defaults.plugins.tooltip.borderColor = "rgba(0,0,0,0.05)";
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.titleFont = { size: 13, family: "'Inter', sans-serif", weight: '500' };
    Chart.defaults.plugins.tooltip.bodyFont = { size: 12, family: "'Inter', sans-serif" };

    const ctxTrend = document.getElementById('shipmentTrendChart');
    if(ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Imports',
                        data: [120, 190, 150, 220, 180, 250],
                        borderColor: '#F03164',
                        backgroundColor: 'rgba(240, 49, 100, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#F03164',
                        pointBorderWidth: 1.5,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Exports',
                        data: [90, 130, 110, 170, 140, 200],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#10B981',
                        pointBorderWidth: 1.5,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, font: {size: 11} } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: 'rgba(0,0,0,0.03)' }, border: {display: false}, ticks: {font: {size: 11}} },
                    x: { grid: { display: false }, border: {display: false}, ticks: {font: {size: 11}} }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
            }
        });
    }

    const ctxRisk = document.getElementById('riskChart');
    if(ctxRisk) {
        new Chart(ctxRisk, {
            type: 'doughnut',
            data: {
                labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: ['rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(240, 49, 100, 0.8)'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '85%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) { return ' ' + context.label + ': ' + context.parsed + '%'; }
                        }
                    }
                }
            }
        });
    }
    
    const ctxRevenue = document.getElementById('revenueChart');
    if(ctxRevenue) {
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue ($K)',
                    data: [45, 60, 55, 75, 65, 80, 95],
                    backgroundColor: 'rgba(244, 114, 182, 0.6)',
                    hoverBackgroundColor: 'rgba(244, 114, 182, 0.9)',
                    borderRadius: 4,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: 'rgba(0,0,0,0.03)' }, border: {display: false}, ticks: {font: {size: 11}} },
                    x: { grid: { display: false }, border: {display: false}, ticks: {font: {size: 11}} }
                }
            }
        });
    }
});

// Function to focus map from clicking an alert
window.focusMapAlert = function(lat, lng) {
    if(window.globalMap) {
        window.globalMap.flyTo([lat, lng], 6, {
            animate: true,
            duration: 1.5
        });
    }
};

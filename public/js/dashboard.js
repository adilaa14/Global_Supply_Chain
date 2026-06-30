document.addEventListener('DOMContentLoaded', function() {
    // Map Initialization
    if(document.getElementById('global-map')) {
        const map = L.map('global-map').setView([20, 0], 2);
        
        // Minimalist light theme map
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        const ships = [
            {lat: 34.05, lng: -118.24, name: "MSC Oliver", status: "In Transit", risk: "Low"},
            {lat: 35.67, lng: 139.65, name: "Maersk Mc-Kinney", status: "Docked", risk: "Low"},
            {lat: 1.35, lng: 103.81, name: "CMA CGM Bougainville", status: "In Transit", risk: "Medium"},
            {lat: 51.50, lng: -0.12, name: "Ever Given", status: "Delayed", risk: "High"},
            {lat: -33.86, lng: 151.20, name: "COSCO Shipping", status: "In Transit", risk: "Low"},
            {lat: 25.20, lng: 55.27, name: "Hapag-Lloyd Express", status: "In Transit", risk: "Low"}
        ];

        ships.forEach(ship => {
            let color = 'var(--primary)';
            if(ship.status === 'Delayed' || ship.risk === 'High') color = 'var(--danger)';
            else if(ship.risk === 'Medium') color = 'var(--warning)';

            const shipIcon = L.divIcon({
                html: `<div style="background: white; border-radius: 50%; padding: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05); display: inline-flex;"><span class="material-symbols-outlined" style="color: ${color}; font-size: 18px; font-weight: 300;">directions_boat</span></div>`,
                className: 'custom-div-icon',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            L.marker([ship.lat, ship.lng], {icon: shipIcon})
             .bindPopup(`
                <div style="font-family: 'Inter', sans-serif;">
                    <b style="color: var(--secondary); font-size: 1rem; font-weight: 500;">${ship.name}</b><br>
                    <span style="color: var(--text-muted); font-size: 0.8rem;">Status: <span style="color: ${color}">${ship.status}</span></span>
                </div>
             `)
             .addTo(map);
        });
    }

    // Chart.js Minimalist Settings
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
    Chart.defaults.plugins.tooltip.boxPadding = 4;
    Chart.defaults.plugins.tooltip.titleFont = { size: 13, family: "'Inter', sans-serif", weight: '500' };
    Chart.defaults.plugins.tooltip.bodyFont = { size: 12, family: "'Inter', sans-serif" };

    // Shipment Trend Chart (Minimalist Line)
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

    // Risk Trend Chart (Minimalist Doughnut)
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
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Revenue Chart (Minimalist Bar)
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
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4], color: 'rgba(0,0,0,0.03)' }, border: {display: false}, ticks: {font: {size: 11}} },
                    x: { grid: { display: false }, border: {display: false}, ticks: {font: {size: 11}} }
                }
            }
        });
    }
});

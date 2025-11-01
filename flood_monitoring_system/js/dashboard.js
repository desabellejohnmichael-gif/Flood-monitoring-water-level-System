// Initialize charts and gauges
let waterLevelChart;
let waterLevelGauge;
let selectedTimeRange = '7d'; // Default time range

// Initialize the charts when the page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    updateData();
    // Update data every 5 seconds
    setInterval(updateData, 5000);
    
    // Initialize time range buttons
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedTimeRange = this.textContent.toLowerCase();
            updateData();
        });
    });

    // Initialize export button
    document.querySelector('.card-header .btn-outline-primary').addEventListener('click', exportData);
});

function initializeCharts() {
    // Initialize Water Level Gauge
    const gaugeCtx = document.getElementById('waterLevelGauge').getContext('2d');
    waterLevelGauge = new Chart(gaugeCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [0, 100],
                backgroundColor: ['#29b6f6', 'rgba(194, 224, 255, 0.08)']
            }]
        },
        options: {
            circumference: 180,
            rotation: -90,
            cutout: '85%',
            plugins: {
                legend: {
                    display: false
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });

    // Initialize Water Level History Chart
    const chartCtx = document.getElementById('waterLevelChart').getContext('2d');
    waterLevelChart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Water Level',
                data: [],
                borderColor: '#29b6f6',
                backgroundColor: 'rgba(41, 182, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(194, 224, 255, 0.1)',
                        borderColor: 'rgba(194, 224, 255, 0.1)'
                    },
                    ticks: {
                        color: '#b2bac2'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(194, 224, 255, 0.1)',
                        borderColor: 'rgba(194, 224, 255, 0.1)'
                    },
                    ticks: {
                        color: '#b2bac2',
                        maxRotation: 0
                    }
                }
            }
        }
    });
}

function updateData() {
    fetch(`api/get_latest_data.php?timeRange=${selectedTimeRange}`)
        .then(response => response.json())
        .then(data => {
            // Update gauge
            waterLevelGauge.data.datasets[0].data = [data.current.water_level, 100 - data.current.water_level];
            waterLevelGauge.update();

            // Update status display
            const statusElement = document.getElementById('currentStatus');
            statusElement.textContent = `${data.current.status.toUpperCase()}`;
            statusElement.className = `status-${data.current.status.toLowerCase()}`;

            // Update last update time
            document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();

            // Update quick stats
            updateQuickStats(data);

            // Update history chart
            waterLevelChart.data.labels = data.history.map(reading => new Date(reading.timestamp).toLocaleTimeString());
            waterLevelChart.data.datasets[0].data = data.history.map(reading => reading.water_level);
            waterLevelChart.update();

            // Update readings table
            const tbody = document.querySelector('#readingsTable tbody');
            tbody.innerHTML = '';
            data.history.forEach(reading => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${new Date(reading.timestamp).toLocaleString()}</td>
                    <td>${reading.water_level}%</td>
                    <td>${reading.temperature}°C</td>
                    <td>${reading.humidity}%</td>
                    <td>${reading.rainfall}mm</td>
                    <td><span class="badge bg-${getStatusColor(reading.status)}">${reading.status}</span></td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}

function updateQuickStats(data) {
    const stats = calculateStats(data.history);
    document.querySelector('.card:last-child .card-body').innerHTML = `
        <div class="d-flex justify-content-between mb-3">
            <span class="text-secondary">24h Peak</span>
            <span class="fw-bold">${stats.peak}%</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
            <span class="text-secondary">Average Level</span>
            <span class="fw-bold">${stats.average}%</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-secondary">Alert Status</span>
            <span class="badge bg-${getStatusColor(data.current.status)}">${data.current.status}</span>
        </div>
    `;
}

function calculateStats(history) {
    if (!history.length) return { peak: 0, average: 0 };
    
    const levels = history.map(h => h.water_level);
    return {
        peak: Math.max(...levels).toFixed(1),
        average: (levels.reduce((a, b) => a + b, 0) / levels.length).toFixed(1)
    };
}

function exportData() {
    fetch(`api/get_latest_data.php?timeRange=${selectedTimeRange}`)
        .then(response => response.json())
        .then(data => {
            const csvContent = generateCSV(data.history);
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `water_level_data_${selectedTimeRange}_${new Date().toISOString().slice(0,10)}.csv`;
            link.click();
        })
        .catch(error => console.error('Error exporting data:', error));
}

function generateCSV(data) {
    const headers = ['Timestamp', 'Water Level (%)', 'Temperature (°C)', 'Humidity (%)', 'Rainfall (mm)', 'Status'];
    const rows = data.map(reading => [
        new Date(reading.timestamp).toLocaleString(),
        reading.water_level,
        reading.temperature,
        reading.humidity,
        reading.rainfall,
        reading.status
    ]);
    
    return [headers, ...rows]
        .map(row => row.join(','))
        .join('\n');
}

function getStatusColor(status) {
    switch(status.toLowerCase()) {
        case 'normal': return 'success';
        case 'warning': return 'warning';
        case 'danger': return 'danger';
        default: return 'secondary';
    }
}
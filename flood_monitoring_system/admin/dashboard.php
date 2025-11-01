<?php
session_start();
// Simple admin dashboard - requires login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Water Level System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="../index.php"><i class="bi bi-water me-2"></i>Water Level System</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Current Water Level</h6>
                    <h2 id="cardWaterLevel">--</h2>
                    <small id="cardLastUpdate" class="text-secondary">Last update: --</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Water Status</h6>
                    <div id="cardStatus"><span class="badge bg-secondary">--</span></div>
                    <small id="cardStatusDetail" class="text-secondary d-block">--</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Rate of Change</h6>
                    <h3 id="cardRate">--</h3>
                    <small class="text-secondary">(% per min)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Alerts</h6>
                    <div id="cardAlerts">0</div>
                    <small class="text-secondary">Recent alerts</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Water Level History</h5>
                </div>
                <div class="card-body">
                    <canvas id="adminWaterChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Readings</h5>
                </div>
                <div class="card-body">
                    <ul id="recentReadings" class="list-unstyled mb-0"></ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function fetchStats() {
    try {
        const res = await fetch('../api/get_latest_data.php');
        const data = await res.json();
        if (!data.success) {
            console.error('API error', data.message);
            return;
        }

        const current = data.current;
        const history = data.history || [];
        const rate = data.rate_per_min;

        if (current) {
            document.getElementById('cardWaterLevel').textContent = current.water_level + '%';
            document.getElementById('cardLastUpdate').textContent = 'Last update: ' + (current.created_at || ' --');
            // status badge
            const statusEl = document.getElementById('cardStatus');
            let status = current.status || 'unknown';
            let badgeClass = 'bg-secondary';
            if (status === 'normal') badgeClass = 'bg-success';
            if (status === 'warning') badgeClass = 'bg-warning text-dark';
            if (status === 'danger') badgeClass = 'bg-danger';
            statusEl.innerHTML = '<span class="badge ' + badgeClass + '">' + status.toUpperCase() + '</span>';
            document.getElementById('cardStatusDetail').textContent = 'Location: ' + (current.location || 'N/A');
        }

        if (rate !== null && rate !== undefined) {
            document.getElementById('cardRate').textContent = parseFloat(rate).toFixed(2);
        } else {
            document.getElementById('cardRate').textContent = '--';
        }

        // Recent readings list
        const list = document.getElementById('recentReadings');
        list.innerHTML = '';
        history.slice().reverse().slice(0,10).forEach(r => {
            const li = document.createElement('li');
            li.innerHTML = '<strong>' + (new Date(r.created_at)).toLocaleString() + '</strong><br>' + r.water_level + '% — ' + (r.status || 'N/A');
            li.className = 'mb-2';
            list.appendChild(li);
        });

        // Chart
        if (window.adminChart) {
            const labels = history.map(h => new Date(h.created_at).toLocaleTimeString());
            const values = history.map(h => parseFloat(h.water_level));
            window.adminChart.data.labels = labels;
            window.adminChart.data.datasets[0].data = values;
            window.adminChart.update();
        }

    } catch (err) {
        console.error(err);
    }
}

function initChart() {
    const ctx = document.getElementById('adminWaterChart').getContext('2d');
    window.adminChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Water level',
                data: [],
                borderColor: '#29b6f6',
                backgroundColor: 'rgba(41,182,246,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

initChart();
fetchStats();
setInterval(fetchStats, 5000);
</script>
</body>
</html>
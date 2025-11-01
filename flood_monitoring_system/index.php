<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Level System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-water me-2"></i>
                Water Level System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-bell me-1"></i> Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php"><i class="bi bi-shield-lock me-1"></i> Admin Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-primary mb-0" style="color: var(--accent-color) !important;">Dashboard Overview</h2>
                <p class="text-secondary">Real-time monitoring and analytics</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-graph-up me-2"></i>Water Level Trends</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary">24h</button>
                            <button class="btn btn-sm btn-outline-primary active">7d</button>
                            <button class="btn btn-sm btn-outline-primary">30d</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="waterLevelChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-water me-2"></i>Current Level</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="waterLevelGauge"></canvas>
                        <div id="statusDisplay" class="text-center mt-3">
                            <h4 id="currentStatus" class="mb-2">Loading...</h4>
                            <p class="text-secondary mb-0">Last updated: <span id="lastUpdate">--:--</span></p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-info-circle me-2"></i>Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">24h Peak</span>
                            <span class="fw-bold">3.2m</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">Average Level</span>
                            <span class="fw-bold">2.1m</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Alert Status</span>
                            <span class="badge bg-success">Normal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-table me-2"></i>Recent Readings</h5>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i>Export Data
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="readingsTable">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Water Level</th>
                                        <th>Temperature</th>
                                        <th>Humidity</th>
                                        <th>Rainfall</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
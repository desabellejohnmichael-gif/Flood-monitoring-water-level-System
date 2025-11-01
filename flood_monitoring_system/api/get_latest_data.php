<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

try {
    // Check if sensor_data table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'sensor_data'")->fetchAll();
    if (empty($tableCheck)) {
        echo json_encode(["success" => false, "message" => "No sensor_data table found", "history" => [], "current" => null]);
        exit;
    }

    // Get latest reading
    $stmt = $pdo->query("SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1");
    $current = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get history (last 50)
    $histStmt = $pdo->query("SELECT * FROM sensor_data ORDER BY id DESC LIMIT 50");
    $historyRaw = $histStmt->fetchAll(PDO::FETCH_ASSOC);

    // Reverse history so oldest first
    $history = array_reverse($historyRaw);

    // Compute rate of change (using last two readings)
    $rate = null; // units: percentage points per minute
    if (count($history) >= 2) {
        $last = $history[count($history) - 1];
        $prev = $history[count($history) - 2];
        if (!empty($last['water_level']) && !empty($prev['water_level']) && !empty($last['created_at']) && !empty($prev['created_at'])) {
            $t1 = strtotime($last['created_at']);
            $t0 = strtotime($prev['created_at']);
            $dt = $t1 - $t0; // seconds
            if ($dt > 0) {
                $dlevel = floatval($last['water_level']) - floatval($prev['water_level']);
                // per minute
                $rate = ($dlevel / $dt) * 60.0;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'current' => $current ?: null,
        'history' => $history,
        'rate_per_min' => $rate
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
?>
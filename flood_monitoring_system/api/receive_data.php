<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['water_level'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO sensor_data (water_level, temperature, humidity, rainfall, location, status) VALUES (?, ?, ?, ?, ?, ?)");
            
            // Calculate status based on water level
            $status = 'normal';
            if ($data['water_level'] > 70) {
                $status = 'danger';
            } elseif ($data['water_level'] > 50) {
                $status = 'warning';
            }
            
            $stmt->execute([
                $data['water_level'],
                $data['temperature'] ?? null,
                $data['humidity'] ?? null,
                $data['rainfall'] ?? null,
                $data['location'] ?? null,
                $status
            ]);
            
            // Create alert if status is warning or danger
            if ($status !== 'normal') {
                $sensorDataId = $pdo->lastInsertId();
                $message = "Water level is at {$data['water_level']}% - {$status} level";
                
                $alertStmt = $pdo->prepare("INSERT INTO alerts (sensor_data_id, alert_type, message) VALUES (?, ?, ?)");
                $alertStmt->execute([$sensorDataId, $status, $message]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Data recorded successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
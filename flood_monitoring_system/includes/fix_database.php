<?php
require_once 'config.php';

try {
    // Check if the users table exists, if not create it
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Check if password column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'password'");
    if ($stmt->rowCount() == 0) {
        // Add password column if it doesn't exist
        $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL AFTER username");
        echo "Password column added successfully!<br>";
    }

    // Check if admin user exists with proper password
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetchColumn() > 0;

    if (!$adminExists) {
        // Create default admin account
        $defaultPassword = 'admin123';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['admin', $hashedPassword, 'admin@waterlevel.com']);
        
        echo "Default admin account created successfully!<br>";
    } else {
        // Update admin password if it exists but password is empty
        $defaultPassword = 'admin123';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin' AND (password IS NULL OR password = '')");
        $stmt->execute([$hashedPassword]);
        
        echo "Admin password updated successfully!<br>";
    }

    echo "<br>You can now login with:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";

    // Display table structure
    echo "<br>Current table structure:<br>";
    $stmt = $pdo->query("DESCRIBE users");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
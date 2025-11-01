<?php
require_once 'config.php';

try {
    // Add email column if it doesn't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(255) UNIQUE");
    
    // Update the admin user with an email
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE username = ? AND email IS NULL");
    $stmt->execute(['admin@waterlevel.com', 'admin']);
    
    echo "Database updated successfully!<br>";
    echo "Admin email added: admin@waterlevel.com<br>";
    
    // Show current admin user details
    $stmt = $pdo->query("SELECT username, email, role FROM users WHERE username = 'admin'");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<br>Current admin account details:<br>";
        echo "Username: " . $admin['username'] . "<br>";
        echo "Email: " . $admin['email'] . "<br>";
        echo "Role: " . $admin['role'] . "<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
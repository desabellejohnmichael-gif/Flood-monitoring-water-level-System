<?php
require_once 'config.php';

try {
    // Check table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    if (empty($tables)) {
        echo "users table does not exist\n";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => 'admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "No admin user found.\n";
        exit;
    }

    echo "Admin row:\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Username: '" . $user['username'] . "'\n";
    echo "Role: " . $user['role'] . "\n";
    echo "Email: " . ($user['email'] ?? '(null)') . "\n";

    // Show password column presence and value length
    if (isset($user['password'])) {
        $hash = $user['password'];
        echo "Password hash length: " . strlen($hash) . "\n";
        echo "Password hash (first 60 chars): " . substr($hash,0,60) . "\n";

        // test verification using expected default password
        $testPass = 'admin123';
        $verified = password_verify($testPass, $hash) ? 'true' : 'false';
        echo "password_verify('admin123', hash): " . $verified . "\n";

        // detect common issues
        if (trim($hash) !== $hash) {
            echo "Warning: hash contains leading/trailing whitespace.\n";
        }
    } else {
        echo "No 'password' column present in row.\n";
    }

    // Count admin users
    $count = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
    echo "Total admin accounts: " . $count . "\n";

} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}
?>
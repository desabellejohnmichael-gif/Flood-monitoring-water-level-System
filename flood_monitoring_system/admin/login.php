<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        // Prepare the query (check username first)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = "User not found";
        } else if ($user['role'] !== 'admin') {
            $error = "This account does not have admin privileges";
        } else {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Invalid password";
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Flood Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center" style="min-height: 100vh; align-items: center;">
            <div class="col-md-6 col-lg-4">
                <div class="text-center mb-4">
                    <h2 class="text-primary" style="color: var(--accent-color) !important;">
                        <i class="bi bi-water me-2"></i>
                        Water Level System
                    </h2>
                    <p class="text-secondary">Admin Access Portal</p>
                </div>
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4" style="color: var(--text-primary);">Login</h4>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: rgba(194, 224, 255, 0.02); border-color: var(--border-color);">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: rgba(194, 224, 255, 0.02); border-color: var(--border-color);">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                        </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 mt-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>
                        <div class="text-center mt-4">
                            <p class="text-secondary mb-0">Need an admin account?</p>
                            <a href="register.php" class="text-decoration-none">
                                <i class="bi bi-person-plus me-1"></i>Create Account
                            </a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="../index.php" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var passwordInput = document.getElementById('password');
        var icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
    </script>
</body>
</html>
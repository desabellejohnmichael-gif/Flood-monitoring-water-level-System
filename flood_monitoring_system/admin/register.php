<?php
session_start();
require_once '../includes/config.php';

// Allow open registration (no admin privilege required)
$registrationAllowed = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = $_POST['email'] ?? '';

    $errors = [];

    // Validate input
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Username already exists";
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Email already registered";
    }

    // If no errors, create the account
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
            $stmt->execute([$username, $hashedPassword, $email]);
            
            $success = "Admin account created successfully";
            header("refresh:2;url=login.php"); // Redirect to login page after 2 seconds
        } catch (PDOException $e) {
            $errors[] = "Error creating account: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account - Water Level System</title>
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
                    <p class="text-secondary">Create Admin Account</p>
                </div>
                
                <?php if (!$registrationAllowed): ?>
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-shield-lock fs-1 text-warning mb-3"></i>
                        <h5 class="text-primary mb-3">Access Restricted</h5>
                        <p class="text-secondary">Only existing administrators can create new admin accounts.</p>
                        <a href="login.php" class="btn btn-primary mt-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4" style="color: var(--text-primary);">Create New Admin</h4>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: rgba(194, 224, 255, 0.02); border-color: var(--border-color);">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: rgba(194, 224, 255, 0.02); border-color: var(--border-color);">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: rgba(194, 224, 255, 0.02); border-color: var(--border-color);">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword" tabindex="-1">
                                        <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- admin_key removed to allow open registration -->
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="login.php" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-2"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');
        const toggleConfirmPasswordIcon = document.getElementById('toggleConfirmPasswordIcon');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('bi-eye');
            togglePasswordIcon.classList.toggle('bi-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            toggleConfirmPasswordIcon.classList.toggle('bi-eye');
            toggleConfirmPasswordIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
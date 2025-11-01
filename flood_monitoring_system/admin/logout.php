<?php
session_start();
// Destroy the session and redirect to login
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], isset($params['httponly']) ? $params['httponly'] : false
    );
}
session_destroy();
header('Location: login.php');
exit;
?>
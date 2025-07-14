<?php
require_once '../includes/config.php';
require_once '../includes/security.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log logout event if user was logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $admin_id = $_SESSION['admin_id'] ?? 0;
    $ip = getUserIP();
    
    // Log successful logout
    logSecurityEvent('admin_logout', $admin_id, $ip);
    
    // Update last logout in database if we have database connection
    try {
        require_once '../includes/database.php';
        $db = new Database();
        
        if ($admin_id > 0) {
            $db->update('users', [
                'last_logout' => date('Y-m-d H:i:s')
            ], ['id' => $admin_id]);
        }
    } catch (Exception $e) {
        // Silent fail - logout should continue even if DB update fails
        error_log('Logout DB update failed: ' . $e->getMessage());
    }
}

// Destroy all session data
session_unset();
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Clear any other admin cookies
$admin_cookies = ['admin_remember', 'admin_preferences', 'admin_theme'];
foreach ($admin_cookies as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, '', time() - 3600, '/');
    }
}

// Redirect to login page with logout message
header('Location: login.php?logout=1');
exit;
?>
<?php
/**
 * Casino Portfolio Website Configuration
 * Geliştirici: BERAT K
 * Main configuration file for the casino streaming portfolio site
 */

// Prevent direct access
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', dirname(__DIR__));
}

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Site configuration
define('SITE_URL', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('SITE_PATH', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
define('SITE_NAME', 'Casino Yayıncısı - BERAT K');
define('SITE_VERSION', '1.0.0');

// Database configuration
define('DB_PATH', SITE_ROOT . '/database/database.sqlite');
define('DB_TYPE', 'sqlite');

// Security settings
define('SECURITY_SALT', 'c4s1n0_b3r4t_k_s4lt_2024');
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 minutes

// File upload settings
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'avi']);
define('UPLOAD_PATH', SITE_ROOT . '/uploads/');

// Pagination settings
define('POSTS_PER_PAGE', 12);
define('ADMIN_POSTS_PER_PAGE', 20);

// Admin settings
define('ADMIN_PATH', '/admin');
define('ADMIN_EMAIL', 'admin@casinoportfolio.com');

// Social media settings
$social_media = [
    'instagram' => 'https://instagram.com/beratk_casino',
    'telegram' => 'https://t.me/beratk_casino',
    'youtube' => 'https://youtube.com/c/beratkasino',
    'twitter' => 'https://twitter.com/beratk_casino'
];

// Theme settings
$theme_colors = [
    'primary' => '#021526',
    'secondary' => '#6f42c1',
    'accent' => '#e91e63',
    'dark' => '#1a1a1a',
    'light' => '#ffffff'
];

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 hour

// SEO settings
define('SEO_TITLE_SEPARATOR', ' | ');
define('SEO_MAX_TITLE_LENGTH', 60);
define('SEO_MAX_DESCRIPTION_LENGTH', 160);

// Email settings
define('MAIL_FROM', 'noreply@casinoportfolio.com');
define('MAIL_FROM_NAME', 'Casino Yayıncısı - BERAT K');
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');

// Timezone
date_default_timezone_set('Europe/Istanbul');

// Start session with security settings (only for web requests)
if (session_status() === PHP_SESSION_NONE && php_sapi_name() !== 'cli') {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.use_strict_mode', 1);
    session_start();
    
    // CSRF Protection
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Include required files
require_once SITE_ROOT . '/includes/database.php';
require_once SITE_ROOT . '/includes/functions.php';
require_once SITE_ROOT . '/includes/security.php';

// Initialize database connection
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Veritabanı bağlantısı başarısız. Lütfen daha sonra tekrar deneyin.");
}

// Load site settings from database
$site_settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE 1");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    error_log("Failed to load site settings: " . $e->getMessage());
}

// Helper function to get site settings
function get_setting($key, $default = '') {
    global $site_settings;
    return isset($site_settings[$key]) ? $site_settings[$key] : $default;
}

// Helper function to get site URL
function site_url($path = '') {
    return SITE_URL . SITE_PATH . '/' . ltrim($path, '/');
}

// Helper function to get asset URL
function asset_url($path = '') {
    return site_url('assets/' . ltrim($path, '/'));
}

// Helper function to get upload URL
function upload_url($path = '') {
    return site_url('uploads/' . ltrim($path, '/'));
}

// Auto-create database if not exists
if (!file_exists(DB_PATH)) {
    try {
        // Create database directory if it doesn't exist
        $db_dir = dirname(DB_PATH);
        if (!is_dir($db_dir)) {
            mkdir($db_dir, 0755, true);
        }
        
        // Create database and tables
        $db = new Database();
        $pdo = $db->getConnection();
        
        // Execute schema
        $schema = file_get_contents(SITE_ROOT . '/database/schema.sql');
        $pdo->exec($schema);
        
        // Execute sample data
        $sample_data = file_get_contents(SITE_ROOT . '/database/sample-data.sql');
        $pdo->exec($sample_data);
        
        error_log("Database created successfully with sample data");
    } catch (Exception $e) {
        error_log("Failed to create database: " . $e->getMessage());
    }
}
?>
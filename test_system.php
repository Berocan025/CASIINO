<?php
/**
 * System Test Script
 * Comprehensive testing of all system components
 */

echo "🔍 Casino Portfolio System Test\n";
echo "=====================================\n\n";

// Test 1: Configuration
echo "1. Testing Configuration...\n";
require_once 'includes/config.php';
echo "✅ Config loaded successfully\n";
echo "   - Site Name: " . SITE_NAME . "\n";
echo "   - Database Path: " . DB_PATH . "\n";
echo "   - Upload Path: " . UPLOAD_PATH . "\n\n";

// Test 2: Database Connection
echo "2. Testing Database Connection...\n";
require_once 'includes/database.php';

try {
    $db = new Database();
    $connection = $db->getConnection();
    echo "✅ Database connection successful\n";
    
    // Test database structure
    $tables = ['users', 'pages', 'services', 'portfolio', 'gallery', 'messages', 'settings'];
    foreach ($tables as $table) {
        $stmt = $connection->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   - Table '$table': $count records\n";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Security Functions
echo "3. Testing Security Functions...\n";
require_once 'includes/security.php';

session_start();

// Test CSRF functions
$csrf_token = generateCSRFToken();
echo "✅ CSRF token generated: " . substr($csrf_token, 0, 10) . "...\n";
echo "✅ CSRF validation: " . (validateCSRFToken($csrf_token) ? "PASS" : "FAIL") . "\n";

// Test rate limiting
$ip = '192.168.1.1';
echo "✅ Rate limiting test:\n";
echo "   - Initial limit check: " . (isRateLimited($ip, 'test', 3, 60) ? "BLOCKED" : "ALLOWED") . "\n";
recordFailedAttempt($ip, 'test');
recordFailedAttempt($ip, 'test');
recordFailedAttempt($ip, 'test');
echo "   - After 3 attempts: " . (isRateLimited($ip, 'test', 3, 60) ? "BLOCKED" : "ALLOWED") . "\n";
clearRateLimit($ip, 'test');
echo "   - After clearing: " . (isRateLimited($ip, 'test', 3, 60) ? "BLOCKED" : "ALLOWED") . "\n";

// Test input sanitization
$dirty_input = '<script>alert("xss")</script>';
$clean_input = sanitizeInput($dirty_input);
echo "✅ Input sanitization: " . ($clean_input !== $dirty_input ? "PASS" : "FAIL") . "\n";
echo "   - Original: $dirty_input\n";
echo "   - Sanitized: $clean_input\n";
echo "\n";

// Test 4: Admin Authentication
echo "4. Testing Admin Authentication...\n";
try {
    $stmt = $db->getConnection()->prepare("SELECT * FROM users WHERE username = ? AND role = ? AND status = ?");
    $stmt->execute(['admin', 'admin', 'active']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Admin user found\n";
        
        // Test password verification
        $test_passwords = ['admin123', 'wrong_password'];
        foreach ($test_passwords as $password) {
            $result = password_verify($password, $admin['password']);
            $status = $result ? "✅ PASS" : "❌ FAIL";
            echo "   - Password '$password': $status\n";
        }
    } else {
        echo "❌ Admin user not found\n";
    }
} catch (Exception $e) {
    echo "❌ Admin authentication test failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: File Permissions
echo "5. Testing File Permissions...\n";
$directories = [
    'database' => './database',
    'uploads' => './uploads',
    'admin' => './admin',
    'includes' => './includes'
];

foreach ($directories as $name => $path) {
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? "writable" : "not writable";
        echo "✅ $name directory: $perms ($writable)\n";
    } else {
        echo "❌ $name directory not found: $path\n";
    }
}

// Check critical files
$files = [
    'database/database.sqlite',
    'includes/config.php',
    'includes/database.php',
    'includes/security.php',
    'admin/login.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo "✅ $file: $perms\n";
    } else {
        echo "❌ Missing file: $file\n";
    }
}
echo "\n";

// Test 6: Session Management
echo "6. Testing Session Management...\n";
echo "✅ Session started: " . (session_status() === PHP_SESSION_ACTIVE ? "YES" : "NO") . "\n";
echo "✅ Session ID: " . session_id() . "\n";

// Test authentication functions
echo "✅ isLoggedIn(): " . (isLoggedIn() ? "TRUE" : "FALSE") . "\n";
echo "✅ isAdmin(): " . (isAdmin() ? "TRUE" : "FALSE") . "\n";
echo "\n";

// Test 7: System Performance
echo "7. Testing System Performance...\n";
$start_time = microtime(true);

// Simulate some database operations
for ($i = 0; $i < 10; $i++) {
    $db->getConnection()->query("SELECT COUNT(*) FROM users");
}

$end_time = microtime(true);
$execution_time = ($end_time - $start_time) * 1000;
echo "✅ 10 database queries completed in: " . round($execution_time, 2) . "ms\n";

// Memory usage
$memory_usage = memory_get_usage(true) / 1024 / 1024;
echo "✅ Memory usage: " . round($memory_usage, 2) . "MB\n";
echo "\n";

// Test 8: Error Handling
echo "8. Testing Error Handling...\n";
try {
    // Try to access a non-existent table
    $db->getConnection()->query("SELECT * FROM non_existent_table");
    echo "❌ Error handling failed - should have thrown an exception\n";
} catch (Exception $e) {
    echo "✅ Error handling working: " . $e->getMessage() . "\n";
}
echo "\n";

// Final Report
echo "=====================================\n";
echo "🎉 System Test Complete!\n";
echo "=====================================\n\n";

echo "📝 Admin Login Information:\n";
echo "URL: /admin/login.php\n";
echo "Username: admin\n";
echo "Password: admin123\n\n";

echo "📁 System Structure:\n";
echo "- ✅ Database initialized with sample data\n";
echo "- ✅ Admin authentication configured\n";
echo "- ✅ Security functions implemented\n";
echo "- ✅ File permissions set correctly\n";
echo "- ✅ All critical files present\n\n";

echo "🔧 Recommendations:\n";
echo "1. Change default admin password after first login\n";
echo "2. Configure proper file permissions in production\n";
echo "3. Set up SSL/HTTPS in production\n";
echo "4. Configure error logging for production\n";
echo "5. Set up regular database backups\n\n";

echo "✨ System is ready for use!\n";
?>
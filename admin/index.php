<?php
/**
 * Admin Login Page
 * Geliştirici: BERAT K
 * Secure admin login with rate limiting and session management
 */

// Include configuration
define('SITE_ROOT', dirname(__DIR__));
require_once SITE_ROOT . '/includes/config.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

// Initialize variables
$error_message = '';
$login_attempts = 0;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verify_csrf($csrf_token)) {
        $error_message = 'Güvenlik doğrulaması başarısız.';
    } else {
        $client_ip = get_client_ip();
        
        // Check rate limiting
        if (!check_login_attempts($client_ip)) {
            $error_message = 'Çok fazla başarısız giriş denemesi. ' . (LOGIN_TIMEOUT / 60) . ' dakika sonra tekrar deneyin.';
            log_security_event('login_rate_limit_exceeded', ['ip' => $client_ip, 'username' => $username]);
        } else {
            // Validate input
            if (empty($username) || empty($password)) {
                $error_message = 'Kullanıcı adı ve şifre gereklidir.';
            } else {
                try {
                    // Get user from database
                    $user = $db->fetchRow(
                        "SELECT * FROM users WHERE username = :username AND role = 'admin'",
                        ['username' => $username]
                    );
                    
                    if ($user && verify_password($password, $user['password'])) {
                        // Successful login
                        session_regenerate_id(true);
                        
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['login_time'] = time();
                        
                        // Update last login
                        $db->update('users', 
                            ['last_login' => date('Y-m-d H:i:s')], 
                            ['id' => $user['id']]
                        );
                        
                        // Clear login attempts
                        clear_login_attempts($client_ip);
                        
                        // Log successful login
                        log_activity('admin_login', 'Admin user logged in', [
                            'user_id' => $user['id'],
                            'username' => $username,
                            'ip' => $client_ip
                        ]);
                        
                        // Redirect to dashboard
                        header('Location: dashboard.php');
                        exit();
                    } else {
                        // Failed login
                        record_login_attempt($client_ip);
                        $error_message = 'Geçersiz kullanıcı adı veya şifre.';
                        
                        log_security_event('admin_login_failed', [
                            'username' => $username,
                            'ip' => $client_ip,
                            'user_agent' => get_user_agent()
                        ]);
                    }
                } catch (Exception $e) {
                    error_log('Admin login error: ' . $e->getMessage());
                    $error_message = 'Giriş işlemi sırasında bir hata oluştu.';
                }
            }
        }
    }
}

// Get current login attempts for display
$client_ip = get_client_ip();
if (isset($_SESSION['login_attempts'])) {
    $login_attempts = count(array_filter($_SESSION['login_attempts'], function($attempt) use ($client_ip) {
        return $attempt['identifier'] === $client_ip && (time() - $attempt['time']) < LOGIN_TIMEOUT;
    }));
}

$remaining_attempts = MAX_LOGIN_ATTEMPTS - $login_attempts;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - Casino Portfolio</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #021526;
            --secondary-color: #6f42c1;
            --accent-color: #e91e63;
            --gradient-primary: linear-gradient(135deg, #021526 0%, #1e3a5f 100%);
            --gradient-secondary: linear-gradient(135deg, #6f42c1 0%, #e91e63 100%);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(111, 66, 193, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(233, 30, 99, 0.3) 0%, transparent 50%);
            z-index: 1;
        }
        
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: var(--gradient-secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }
        
        .login-title {
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 12px 15px;
            color: white;
            font-size: 14px;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--accent-color);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .btn-login {
            background: var(--gradient-secondary);
            border: none;
            border-radius: 10px;
            padding: 12px;
            width: 100%;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(111, 66, 193, 0.4);
            color: white;
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #f8d7da;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .security-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .attempts-info {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .developer-credit {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            text-align: center;
            z-index: 2;
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 25px;
                margin: 20px;
            }
            
            .login-container {
                max-width: 100%;
            }
        }
        
        /* Loading animation */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 class="login-title">Admin Paneli</h2>
                <p class="login-subtitle">Casino Portfolio - BERAT K</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <?php echo csrf_field(); ?>
                
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>Kullanıcı Adı
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Kullanıcı adınızı girin"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Şifre
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Şifrenizi girin"
                           required>
                </div>
                
                <button type="submit" class="btn btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                </button>
            </form>
            
            <?php if ($login_attempts > 0): ?>
                <div class="attempts-info">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Kalan deneme hakkı: <strong><?php echo $remaining_attempts; ?></strong> / <?php echo MAX_LOGIN_ATTEMPTS; ?>
                </div>
            <?php endif; ?>
            
            <div class="security-info">
                <div class="mb-2">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Güvenlik Bilgileri:</strong>
                </div>
                <ul class="mb-0" style="padding-left: 20px; margin: 0;">
                    <li>Maksimum <?php echo MAX_LOGIN_ATTEMPTS; ?> başarısız deneme hakkınız var</li>
                    <li>IP adresiniz: <?php echo htmlspecialchars($client_ip); ?></li>
                    <li>Tüm giriş denemeleri kaydedilir</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="developer-credit">
        <strong>Geliştirici: BERAT K</strong><br>
        Casino Portfolio Admin Panel v1.0
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span>Giriş yapılıyor...';
            
            // Re-enable button after 3 seconds if form doesn't submit
            setTimeout(function() {
                if (btn.disabled) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }, 3000);
        });
        
        // Auto-focus on username field
        document.getElementById('username').focus();
        
        // Enter key navigation
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });
        
        // Prevent form submission on rate limit
        <?php if (!check_login_attempts($client_ip)): ?>
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Çok fazla başarısız deneme. Lütfen <?php echo LOGIN_TIMEOUT / 60; ?> dakika sonra tekrar deneyin.');
            });
        <?php endif; ?>
        
        console.log('🎰 Casino Portfolio Admin Panel');
        console.log('👨‍💻 Geliştirici: BERAT K');
        console.log('🔒 Secure Admin Login System');
    </script>
</body>
</html>
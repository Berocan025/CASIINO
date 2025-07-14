<?php
/**
 * Security Functions
 * Geliştirici: BERAT K
 * XSS, CSRF and other security protection functions
 */

/**
 * Generate CSRF token
 * @return string
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token input field
 * @return string
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Sanitize input data for XSS protection
 * @param mixed $data
 * @return mixed
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    if (is_string($data)) {
        // Remove null bytes
        $data = str_replace(chr(0), '', $data);
        
        // Convert special characters to HTML entities
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Trim whitespace
        $data = trim($data);
    }
    
    return $data;
}

/**
 * Clean HTML content - allow only safe tags
 * @param string $html
 * @return string
 */
function clean_html($html) {
    $allowed_tags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img>';
    
    // Remove dangerous attributes
    $html = preg_replace('/(onload|onerror|onclick|onmouseover|onmouseout|onfocus|onblur|onchange|onsubmit)="[^"]*"/i', '', $html);
    $html = preg_replace('/(javascript:|data:|vbscript:)/i', '', $html);
    
    return strip_tags($html, $allowed_tags);
}

/**
 * Validate email address
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 * @param string $url
 * @return bool
 */
function validate_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Generate secure random password
 * @param int $length
 * @return string
 */
function generate_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    $chars_length = strlen($chars);
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $chars_length - 1)];
    }
    
    return $password;
}

/**
 * Hash password securely
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Check if user is admin
 * @return bool
 */
function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

/**
 * Require login or redirect
 * @param string $redirect_url
 */
function require_login($redirect_url = '/admin/') {
    if (!is_logged_in()) {
        header('Location: ' . $redirect_url);
        exit();
    }
}

/**
 * Require admin access or redirect
 * @param string $redirect_url
 */
function require_admin($redirect_url = '/admin/') {
    if (!is_admin()) {
        header('Location: ' . $redirect_url);
        exit();
    }
}

/**
 * Login rate limiting
 * @param string $identifier (IP or username)
 * @return bool
 */
function check_login_attempts($identifier) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    $current_time = time();
    $attempts = &$_SESSION['login_attempts'];
    
    // Clean old attempts (older than LOGIN_TIMEOUT)
    foreach ($attempts as $key => $attempt) {
        if ($current_time - $attempt['time'] > LOGIN_TIMEOUT) {
            unset($attempts[$key]);
        }
    }
    
    // Count current attempts for this identifier
    $count = 0;
    foreach ($attempts as $attempt) {
        if ($attempt['identifier'] === $identifier) {
            $count++;
        }
    }
    
    return $count < MAX_LOGIN_ATTEMPTS;
}

/**
 * Record failed login attempt
 * @param string $identifier
 */
function record_login_attempt($identifier) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    $_SESSION['login_attempts'][] = [
        'identifier' => $identifier,
        'time' => time()
    ];
}

/**
 * Clear login attempts for identifier
 * @param string $identifier
 */
function clear_login_attempts($identifier) {
    if (!isset($_SESSION['login_attempts'])) {
        return;
    }
    
    $attempts = &$_SESSION['login_attempts'];
    foreach ($attempts as $key => $attempt) {
        if ($attempt['identifier'] === $identifier) {
            unset($attempts[$key]);
        }
    }
}

/**
 * Get client IP address
 * @return string
 */
function get_client_ip() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Get user agent
 * @return string
 */
function get_user_agent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

/**
 * Validate file upload
 * @param array $file $_FILES array element
 * @return array [success => bool, error => string, file_info => array]
 */
function validate_file_upload($file) {
    $result = ['success' => false, 'error' => '', 'file_info' => []];
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $result['error'] = 'Dosya yüklenmedi.';
        return $result;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Dosya yükleme hatası: ' . $file['error'];
        return $result;
    }
    
    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        $result['error'] = 'Dosya boyutu çok büyük. Maksimum: ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . 'MB';
        return $result;
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check allowed file types
    if (!in_array($file_extension, UPLOAD_ALLOWED_TYPES)) {
        $result['error'] = 'İzin verilmeyen dosya türü. İzin verilen türler: ' . implode(', ', UPLOAD_ALLOWED_TYPES);
        return $result;
    }
    
    // Check if file is actually an image (for image uploads)
    $image_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($file_extension, $image_types)) {
        $image_info = getimagesize($file['tmp_name']);
        if ($image_info === false) {
            $result['error'] = 'Geçersiz resim dosyası.';
            return $result;
        }
        $result['file_info']['image_info'] = $image_info;
    }
    
    // Generate secure filename
    $secure_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $result['file_info']['original_name'] = $file['name'];
    $result['file_info']['secure_name'] = $secure_filename;
    $result['file_info']['extension'] = $file_extension;
    $result['file_info']['size'] = $file['size'];
    $result['file_info']['mime_type'] = $file['type'];
    
    $result['success'] = true;
    return $result;
}

/**
 * Secure file upload
 * @param array $file $_FILES array element
 * @param string $upload_dir Upload directory
 * @return array [success => bool, error => string, file_path => string]
 */
function secure_file_upload($file, $upload_dir) {
    $result = ['success' => false, 'error' => '', 'file_path' => ''];
    
    // Validate file
    $validation = validate_file_upload($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $result['error'] = 'Upload dizini oluşturulamadı.';
            return $result;
        }
    }
    
    // Move uploaded file
    $file_path = $upload_dir . '/' . $validation['file_info']['secure_name'];
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $result['success'] = true;
        $result['file_path'] = $file_path;
        $result['file_info'] = $validation['file_info'];
    } else {
        $result['error'] = 'Dosya taşınamadı.';
    }
    
    return $result;
}

/**
 * Generate slug from text
 * @param string $text
 * @return string
 */
function generate_slug($text) {
    // Convert to lowercase
    $text = mb_strtolower($text, 'UTF-8');
    
    // Turkish character replacements
    $turkish_chars = [
        'ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
        'Ç' => 'c', 'Ğ' => 'g', 'İ' => 'i', 'Ö' => 'o', 'Ş' => 's', 'Ü' => 'u'
    ];
    $text = strtr($text, $turkish_chars);
    
    // Remove special characters
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Replace spaces and multiple hyphens with single hyphen
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Trim hyphens from start and end
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Log security event
 * @param string $event
 * @param array $data
 */
function log_security_event($event, $data = []) {
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'ip' => get_client_ip(),
        'user_agent' => get_user_agent(),
        'user_id' => $_SESSION['user_id'] ?? 'guest',
        'data' => $data
    ];
    
    error_log("SECURITY: " . json_encode($log_data));
}
?>
<?php
/**
 * Contact Form API
 * Geliştirici: BERAT K
 * Handles contact form submissions with validation and email sending
 */

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include configuration
define('SITE_ROOT', dirname(__DIR__));
require_once SITE_ROOT . '/includes/config.php';

// Response function
function sendResponse($success, $message, $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Sadece POST istekleri kabul edilir.');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
    sendResponse(false, 'Güvenlik doğrulaması başarısız.');
}

// Rate limiting - simple IP based
$client_ip = get_client_ip();
$rate_limit_key = 'contact_form_' . md5($client_ip);

if (!isset($_SESSION[$rate_limit_key])) {
    $_SESSION[$rate_limit_key] = [];
}

// Clean old attempts (older than 1 hour)
$current_time = time();
$_SESSION[$rate_limit_key] = array_filter($_SESSION[$rate_limit_key], function($timestamp) use ($current_time) {
    return ($current_time - $timestamp) < 3600;
});

// Check rate limit (max 5 submissions per hour)
if (count($_SESSION[$rate_limit_key]) >= 5) {
    sendResponse(false, 'Çok fazla form gönderimi. Lütfen bir saat sonra tekrar deneyin.');
}

// Get and sanitize form data
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$subject = sanitize_input($_POST['subject'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'İsim alanı en az 2 karakter olmalıdır.';
}

if (empty($email) || !validate_email($email)) {
    $errors[] = 'Geçerli bir e-mail adresi girin.';
}

if (empty($subject) || strlen($subject) < 5) {
    $errors[] = 'Konu alanı en az 5 karakter olmalıdır.';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Mesaj alanı en az 10 karakter olmalıdır.';
}

// Phone validation (optional)
if (!empty($phone)) {
    $phone = preg_replace('/[^\d+\-\(\)\s]/', '', $phone);
    if (strlen($phone) < 10) {
        $errors[] = 'Telefon numarası geçersiz.';
    }
}

// Check for spam patterns
$spam_patterns = [
    '/\b(viagra|casino|poker|loan|debt|credit|sex|porn)\b/i',
    '/\b(click here|visit now|buy now|free money)\b/i',
    '/https?:\/\/[^\s]+\.(tk|ml|ga|cf)/i', // Suspicious domains
    '/\b\d{4}[\s\-]?\d{4}[\s\-]?\d{4}[\s\-]?\d{4}\b/', // Credit card patterns
];

$combined_text = $name . ' ' . $email . ' ' . $subject . ' ' . $message;
foreach ($spam_patterns as $pattern) {
    if (preg_match($pattern, $combined_text)) {
        log_security_event('spam_detected', [
            'ip' => $client_ip,
            'content' => substr($combined_text, 0, 200)
        ]);
        sendResponse(false, 'Mesajınız spam olarak algılandı.');
    }
}

// Return errors if any
if (!empty($errors)) {
    sendResponse(false, implode(' ', $errors));
}

try {
    // Record rate limit attempt
    $_SESSION[$rate_limit_key][] = $current_time;
    
    // Prepare data for database
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'status' => 'unread',
        'ip_address' => $client_ip,
        'user_agent' => get_user_agent(),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Save to database
    $message_id = $db->insert('messages', $data);
    
    if (!$message_id) {
        throw new Exception('Mesaj veritabanına kaydedilemedi.');
    }
    
    // Prepare email content
    $email_subject = 'Yeni İletişim Formu Mesajı - ' . $subject;
    $email_body = generateEmailTemplate($data);
    
    // Send email notification to admin
    $admin_email = get_setting('contact_email', ADMIN_EMAIL);
    
    $email_headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
        'X-Mailer: PHP/' . phpversion(),
        'X-Priority: 1',
        'X-MSMail-Priority: High'
    ];
    
    $mail_sent = mail($admin_email, $email_subject, $email_body, implode("\r\n", $email_headers));
    
    // Send auto-reply to user
    $auto_reply_subject = 'Mesajınız Alındı - ' . get_setting('site_title', SITE_NAME);
    $auto_reply_body = generateAutoReplyTemplate($data);
    
    $auto_reply_headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
        'Reply-To: ' . MAIL_FROM,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    mail($email, $auto_reply_subject, $auto_reply_body, implode("\r\n", $auto_reply_headers));
    
    // Log successful submission
    log_activity('contact_form_submitted', 'New contact form submission', [
        'message_id' => $message_id,
        'name' => $name,
        'email' => $email,
        'subject' => $subject
    ]);
    
    sendResponse(true, 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.', [
        'message_id' => $message_id
    ]);
    
} catch (Exception $e) {
    error_log('Contact form error: ' . $e->getMessage());
    sendResponse(false, 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.');
}

/**
 * Generate email template for admin notification
 */
function generateEmailTemplate($data) {
    $template = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Yeni İletişim Formu Mesajı</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #021526; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #021526; }
            .value { margin-top: 5px; padding: 8px; background: white; border-radius: 4px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>🎰 Yeni İletişim Formu Mesajı</h2>
                <p>Casino Portfolio Website - BERAT K</p>
            </div>
            
            <div class="content">
                <div class="field">
                    <div class="label">👤 Ad Soyad:</div>
                    <div class="value">' . htmlspecialchars($data['name']) . '</div>
                </div>
                
                <div class="field">
                    <div class="label">📧 E-mail:</div>
                    <div class="value"><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></div>
                </div>
                
                <div class="field">
                    <div class="label">📞 Telefon:</div>
                    <div class="value">' . htmlspecialchars($data['phone'] ?: 'Belirtilmemiş') . '</div>
                </div>
                
                <div class="field">
                    <div class="label">📋 Konu:</div>
                    <div class="value">' . htmlspecialchars($data['subject']) . '</div>
                </div>
                
                <div class="field">
                    <div class="label">💬 Mesaj:</div>
                    <div class="value">' . nl2br(htmlspecialchars($data['message'])) . '</div>
                </div>
                
                <hr style="margin: 20px 0;">
                
                <div class="field">
                    <div class="label">🌐 IP Adresi:</div>
                    <div class="value">' . htmlspecialchars($data['ip_address']) . '</div>
                </div>
                
                <div class="field">
                    <div class="label">🕒 Gönderim Zamanı:</div>
                    <div class="value">' . date('d.m.Y H:i:s', strtotime($data['created_at'])) . '</div>
                </div>
            </div>
            
            <div class="footer">
                <p>Bu mesaj otomatik olarak oluşturulmuştur.</p>
                <p><strong>Geliştirici: BERAT K</strong></p>
            </div>
        </div>
    </body>
    </html>';
    
    return $template;
}

/**
 * Generate auto-reply template for user
 */
function generateAutoReplyTemplate($data) {
    $template = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Mesajınız Alındı</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #021526 0%, #6f42c1 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #ddd; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            .btn { display: inline-block; background: linear-gradient(135deg, #6f42c1 0%, #e91e63 100%); color: white; padding: 12px 25px; text-decoration: none; border-radius: 25px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>🎰 Mesajınız Alındı!</h2>
                <p>Teşekkür ederiz, ' . htmlspecialchars($data['name']) . '</p>
            </div>
            
            <div class="content">
                <p>Merhaba <strong>' . htmlspecialchars($data['name']) . '</strong>,</p>
                
                <p>İletişim formumuz üzerinden gönderdiğiniz mesajı aldık. En kısa sürede size dönüş yapacağız.</p>
                
                <h4>📋 Mesaj Özeti:</h4>
                <ul>
                    <li><strong>Konu:</strong> ' . htmlspecialchars($data['subject']) . '</li>
                    <li><strong>Gönderim Zamanı:</strong> ' . date('d.m.Y H:i', strtotime($data['created_at'])) . '</li>
                </ul>
                
                <p>Acil durumlar için bize aşağıdaki kanallardan ulaşabilirsiniz:</p>
                
                <ul>
                    <li>📧 E-mail: ' . get_setting('contact_email') . '</li>
                    <li>📞 Telefon: ' . get_setting('contact_phone') . '</li>
                    <li>💬 WhatsApp: <a href="https://wa.me/' . str_replace(['+', ' ', '(', ')', '-'], '', get_setting('contact_phone', '')) . '">Direkt Mesaj</a></li>
                </ul>
                
                <div style="text-align: center;">
                    <a href="' . site_url() . '" class="btn">🏠 Ana Sayfaya Dön</a>
                </div>
                
                <p>Saygılarımla,<br>
                <strong>BERAT K</strong><br>
                <em>Casino Yayıncısı & Dijital Pazarlama Uzmanı</em></p>
            </div>
            
            <div class="footer">
                <p>Bu mesaj otomatik olarak gönderilmiştir.</p>
                <p><strong>© 2024 Casino Yayıncısı - BERAT K</strong></p>
            </div>
        </div>
    </body>
    </html>';
    
    return $template;
}
?>
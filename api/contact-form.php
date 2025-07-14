<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Start session for CSRF token validation
session_start();

$response = [
    'success' => false,
    'message' => 'Bir hata oluştu.',
    'errors' => []
];

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Sadece POST istekleri kabul edilir.');
    }

    // Get JSON input or form data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    // Initialize database
    $db = new Database();

    // Rate limiting check
    $ip = getUserIP();
    if (isRateLimited($ip, 'contact_form', 5, 300)) { // 5 attempts in 5 minutes
        throw new Exception('Çok fazla form gönderimi. 5 dakika bekleyin.');
    }

    // CSRF token validation
    $csrf_token = $input['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf_token)) {
        throw new Exception('Güvenlik hatası. Sayfayı yenileyin.');
    }

    // Input validation and sanitization
    $name = sanitizeInput($input['name'] ?? '');
    $email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = sanitizeInput($input['phone'] ?? '');
    $subject = sanitizeInput($input['subject'] ?? '');
    $message = sanitizeInput($input['message'] ?? '');
    $service = sanitizeInput($input['service'] ?? '');
    $package = sanitizeInput($input['package'] ?? '');

    // Validation errors
    $errors = [];

    if (empty($name) || strlen($name) < 2) {
        $errors['name'] = 'Ad soyad en az 2 karakter olmalıdır.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Geçerli bir e-posta adresi giriniz.';
    }

    if (!empty($phone) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $phone)) {
        $errors['phone'] = 'Geçerli bir telefon numarası giriniz.';
    }

    if (empty($subject) || strlen($subject) < 5) {
        $errors['subject'] = 'Konu en az 5 karakter olmalıdır.';
    }

    if (empty($message) || strlen($message) < 10) {
        $errors['message'] = 'Mesaj en az 10 karakter olmalıdır.';
    }

    // Spam detection
    $spam_keywords = ['viagra', 'casino spam', 'free money', 'click here', 'urgent', 'winner'];
    $message_lower = strtolower($message . ' ' . $subject);
    foreach ($spam_keywords as $keyword) {
        if (strpos($message_lower, $keyword) !== false) {
            $errors['message'] = 'Mesajınız spam olarak algılandı.';
            break;
        }
    }

    // Check for duplicate submissions
    $recent_message = $db->query(
        "SELECT id FROM messages WHERE email = ? AND created_at > datetime('now', '-10 minutes')",
        [$email]
    )->fetch();

    if ($recent_message) {
        $errors['general'] = 'Son 10 dakika içinde mesaj gönderdiniz. Lütfen bekleyin.';
    }

    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Form verilerinde hatalar var.';
        echo json_encode($response);
        exit;
    }

    // Prepare message data
    $messageData = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'service' => $service,
        'package' => $package,
        'ip_address' => $ip,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'status' => 'unread',
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Save to database
    $messageId = $db->insert('messages', $messageData);

    if (!$messageId) {
        throw new Exception('Mesaj kaydedilirken hata oluştu.');
    }

    // Send notification email to admin
    $adminEmail = getSiteSetting('admin_email', 'admin@yoursite.com');
    $siteName = getSiteSetting('site_name', 'Casino Yayıncısı - BERAT K');

    $emailSubject = "Yeni İletişim Mesajı: " . $subject;
    $emailBody = generateContactEmailTemplate([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'service' => $service,
        'package' => $package,
        'date' => date('d.m.Y H:i:s'),
        'ip' => $ip
    ]);

    $emailHeaders = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $siteName . ' <noreply@yoursite.com>',
        'Reply-To: ' . $name . ' <' . $email . '>',
        'X-Mailer: PHP/' . phpversion()
    ];

    $emailSent = mail($adminEmail, $emailSubject, $emailBody, implode("\r\n", $emailHeaders));

    // Send auto-reply to user
    $autoReplySubject = "Mesajınızı Aldık - " . $siteName;
    $autoReplyBody = generateAutoReplyTemplate([
        'name' => $name,
        'subject' => $subject,
        'site_name' => $siteName,
        'message_id' => $messageId
    ]);

    $autoReplyHeaders = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $siteName . ' <noreply@yoursite.com>',
        'X-Mailer: PHP/' . phpversion()
    ];

    mail($email, $autoReplySubject, $autoReplyBody, implode("\r\n", $autoReplyHeaders));

    // Record successful attempt (clear rate limiting)
    clearRateLimit($ip, 'contact_form');

    // Log successful contact
    logSecurityEvent('contact_form_success', 0, $ip, [
        'name' => $name,
        'email' => $email,
        'subject' => $subject
    ]);

    $response['success'] = true;
    $response['message'] = 'Mesajınız başarıyla gönderildi. En kısa sürede dönüş yapacağız.';
    $response['data'] = [
        'message_id' => $messageId,
        'email_sent' => $emailSent
    ];

} catch (Exception $e) {
    // Record failed attempt
    recordFailedAttempt($ip ?? getUserIP(), 'contact_form');
    
    // Log error
    logSecurityEvent('contact_form_error', 0, $ip ?? getUserIP(), [
        'error' => $e->getMessage(),
        'input' => $input ?? []
    ]);

    $response['message'] = $e->getMessage();
    
    // Don't expose detailed errors in production
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        $response['debug'] = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Helper functions
function generateContactEmailTemplate($data) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Yeni İletişim Mesajı</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #6f42c1, #e91e63); color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; }
            .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .info-table td { padding: 10px; border-bottom: 1px solid #ddd; }
            .info-table .label { font-weight: bold; background: #e9ecef; width: 150px; }
            .message-content { background: white; padding: 15px; border-left: 4px solid #6f42c1; margin: 15px 0; }
            .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>🎰 Yeni İletişim Mesajı</h2>
                <p>Casino Yayıncısı - BERAT K</p>
            </div>
            
            <div class="content">
                <h3>Mesaj Detayları</h3>
                
                <table class="info-table">
                    <tr>
                        <td class="label">Ad Soyad:</td>
                        <td>' . htmlspecialchars($data['name']) . '</td>
                    </tr>
                    <tr>
                        <td class="label">E-posta:</td>
                        <td><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></td>
                    </tr>
                    <tr>
                        <td class="label">Telefon:</td>
                        <td>' . htmlspecialchars($data['phone']) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Konu:</td>
                        <td>' . htmlspecialchars($data['subject']) . '</td>
                    </tr>
                    ' . ($data['service'] ? '<tr><td class="label">Hizmet:</td><td>' . htmlspecialchars($data['service']) . '</td></tr>' : '') . '
                    ' . ($data['package'] ? '<tr><td class="label">Paket:</td><td>' . htmlspecialchars($data['package']) . '</td></tr>' : '') . '
                    <tr>
                        <td class="label">Tarih:</td>
                        <td>' . $data['date'] . '</td>
                    </tr>
                    <tr>
                        <td class="label">IP Adresi:</td>
                        <td>' . $data['ip'] . '</td>
                    </tr>
                </table>
                
                <div class="message-content">
                    <h4>Mesaj İçeriği:</h4>
                    <p>' . nl2br(htmlspecialchars($data['message'])) . '</p>
                </div>
                
                <p><strong>Admin panelinden mesajı yanıtlayabilirsiniz.</strong></p>
            </div>
            
            <div class="footer">
                <p>Bu e-posta otomatik olarak oluşturulmuştur.<br>
                Casino Yayıncısı - BERAT K | © ' . date('Y') . '</p>
            </div>
        </div>
    </body>
    </html>';
}

function generateAutoReplyTemplate($data) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Mesajınızı Aldık</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #6f42c1, #e91e63); color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; }
            .highlight { background: #e7f3ff; padding: 15px; border-left: 4px solid #0066cc; margin: 15px 0; }
            .social-links { text-align: center; margin: 20px 0; }
            .social-links a { display: inline-block; margin: 0 10px; color: #6f42c1; text-decoration: none; }
            .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>🎰 Mesajınızı Aldık!</h2>
                <p>' . htmlspecialchars($data['site_name']) . '</p>
            </div>
            
            <div class="content">
                <h3>Merhaba ' . htmlspecialchars($data['name']) . ',</h3>
                
                <p>Mesajınız için teşekkür ederiz. "<strong>' . htmlspecialchars($data['subject']) . '</strong>" konulu mesajınızı aldık ve en kısa sürede size dönüş yapacağız.</p>
                
                <div class="highlight">
                    <strong>Mesaj Referans No:</strong> #' . $data['message_id'] . '<br>
                    <strong>Alınma Tarihi:</strong> ' . date('d.m.Y H:i:s') . '
                </div>
                
                <h4>Bize Ulaşabileceğiniz Diğer Kanallar:</h4>
                <ul>
                    <li>📞 <strong>Telefon:</strong> +90 555 555 55 55</li>
                    <li>📧 <strong>E-posta:</strong> info@yoursite.com</li>
                    <li>💬 <strong>WhatsApp:</strong> +90 555 555 55 55</li>
                    <li>📱 <strong>Telegram:</strong> @beratk_casino</li>
                </ul>
                
                <div class="social-links">
                    <a href="https://youtube.com/@beratk" target="_blank">📺 YouTube</a>
                    <a href="https://twitch.tv/beratk" target="_blank">🎮 Twitch</a>
                    <a href="https://instagram.com/beratk_casino" target="_blank">📸 Instagram</a>
                    <a href="https://t.me/beratk_casino" target="_blank">💬 Telegram</a>
                </div>
                
                <p>Casino dünyasında 5+ yıllık deneyimimizle size en iyi hizmeti sunmaya devam ediyoruz. Yayınlarımızı takip etmeyi unutmayın!</p>
                
                <p>Saygılarımızla,<br>
                <strong>BERAT K</strong><br>
                <em>Casino Yayıncısı & Dijital Pazarlama Uzmanı</em></p>
            </div>
            
            <div class="footer">
                <p>Bu e-posta otomatik olarak oluşturulmuştur.<br>
                Lütfen bu e-postayı yanıtlamayın.<br>
                ' . htmlspecialchars($data['site_name']) . ' | © ' . date('Y') . '</p>
            </div>
        </div>
    </body>
    </html>';
}
?>
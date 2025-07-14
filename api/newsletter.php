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
    'data' => []
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
    if (isRateLimited($ip, 'newsletter', 3, 600)) { // 3 attempts in 10 minutes
        throw new Exception('Çok fazla abonelik denemesi. 10 dakika bekleyin.');
    }

    // Input validation and sanitization
    $email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $name = sanitizeInput($input['name'] ?? '');
    $source = sanitizeInput($input['source'] ?? 'website');

    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Geçerli bir e-posta adresi giriniz.');
    }

    // Check if email already exists
    $existingSubscriber = $db->query(
        "SELECT id, status FROM newsletter_subscribers WHERE email = ?",
        [$email]
    )->fetch();

    if ($existingSubscriber) {
        if ($existingSubscriber['status'] === 'active') {
            throw new Exception('Bu e-posta adresi zaten kayıtlı.');
        } else if ($existingSubscriber['status'] === 'unsubscribed') {
            // Reactivate subscription
            $updateData = [
                'status' => 'active',
                'updated_at' => date('Y-m-d H:i:s'),
                'resubscribed_at' => date('Y-m-d H:i:s')
            ];
            
            $updated = $db->update('newsletter_subscribers', $updateData, ['id' => $existingSubscriber['id']]);
            
            if ($updated) {
                $response['success'] = true;
                $response['message'] = 'Newsletter aboneliğiniz yeniden aktifleştirildi!';
                $response['data'] = ['subscriber_id' => $existingSubscriber['id']];
            } else {
                throw new Exception('Abonelik güncellenirken hata oluştu.');
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Generate confirmation token
    $confirmationToken = bin2hex(random_bytes(32));
    
    // Prepare subscriber data
    $subscriberData = [
        'email' => $email,
        'name' => $name,
        'source' => $source,
        'status' => 'pending', // Will be activated after email confirmation
        'confirmation_token' => $confirmationToken,
        'ip_address' => $ip,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Save to database
    $subscriberId = $db->insert('newsletter_subscribers', $subscriberData);

    if (!$subscriberId) {
        throw new Exception('Abonelik kaydedilirken hata oluştu.');
    }

    // Send confirmation email
    $siteName = getSiteSetting('site_name', 'Casino Yayıncısı - BERAT K');
    $siteUrl = getSiteSetting('site_url', 'https://yoursite.com');
    $confirmationUrl = $siteUrl . '/newsletter-confirm.php?token=' . $confirmationToken;

    $emailSubject = "Newsletter Aboneliğinizi Onaylayın - " . $siteName;
    $emailBody = generateConfirmationEmailTemplate([
        'name' => $name ?: 'Değerli Kullanıcı',
        'email' => $email,
        'confirmation_url' => $confirmationUrl,
        'site_name' => $siteName,
        'site_url' => $siteUrl
    ]);

    $emailHeaders = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $siteName . ' <noreply@yoursite.com>',
        'Reply-To: ' . $siteName . ' <info@yoursite.com>',
        'X-Mailer: PHP/' . phpversion()
    ];

    $emailSent = mail($email, $emailSubject, $emailBody, implode("\r\n", $emailHeaders));

    // Send notification to admin
    $adminEmail = getSiteSetting('admin_email', 'admin@yoursite.com');
    if ($adminEmail) {
        $adminSubject = "Yeni Newsletter Aboneliği - " . $siteName;
        $adminBody = generateAdminNotificationTemplate([
            'email' => $email,
            'name' => $name,
            'source' => $source,
            'ip' => $ip,
            'date' => date('d.m.Y H:i:s'),
            'site_name' => $siteName
        ]);

        mail($adminEmail, $adminSubject, $adminBody, implode("\r\n", $emailHeaders));
    }

    // Record successful attempt (clear rate limiting)
    clearRateLimit($ip, 'newsletter');

    // Log successful subscription
    logSecurityEvent('newsletter_subscribe', $subscriberId, $ip, [
        'email' => $email,
        'source' => $source
    ]);

    $response['success'] = true;
    $response['message'] = 'Başarıyla abone oldunuz! Lütfen e-postanızı kontrol edip aboneliğinizi onaylayın.';
    $response['data'] = [
        'subscriber_id' => $subscriberId,
        'email_sent' => $emailSent,
        'confirmation_required' => true
    ];

} catch (Exception $e) {
    // Record failed attempt
    recordFailedAttempt($ip ?? getUserIP(), 'newsletter');
    
    // Log error
    logSecurityEvent('newsletter_error', 0, $ip ?? getUserIP(), [
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
function generateConfirmationEmailTemplate($data) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Newsletter Abonelik Onayı</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #6f42c1, #e91e63); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; background: linear-gradient(135deg, #6f42c1, #e91e63); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; margin: 20px 0; font-weight: bold; }
            .button:hover { opacity: 0.9; }
            .highlight { background: #e7f3ff; padding: 15px; border-left: 4px solid #0066cc; margin: 20px 0; }
            .benefits { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .benefits ul { list-style: none; padding: 0; }
            .benefits li { padding: 8px 0; position: relative; padding-left: 25px; }
            .benefits li:before { content: "✓"; position: absolute; left: 0; color: #28a745; font-weight: bold; }
            .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }
            .social-links { text-align: center; margin: 20px 0; }
            .social-links a { display: inline-block; margin: 0 10px; color: #6f42c1; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🎰 Newsletter Abonelik Onayı</h1>
                <p>' . htmlspecialchars($data['site_name']) . '</p>
            </div>
            
            <div class="content">
                <h3>Merhaba ' . htmlspecialchars($data['name']) . ',</h3>
                
                <p>Newsletter aboneliğiniz için teşekkür ederiz! Aboneliğinizi tamamlamak için aşağıdaki butona tıklayın:</p>
                
                <div style="text-align: center;">
                    <a href="' . $data['confirmation_url'] . '" class="button">
                        📧 Aboneliğimi Onayla
                    </a>
                </div>
                
                <div class="highlight">
                    <strong>Not:</strong> Bu onay linkini 24 saat içinde kullanmanız gerekmektedir.
                </div>
                
                <div class="benefits">
                    <h4>📢 Newsletter ile Neler Alacaksınız?</h4>
                    <ul>
                        <li>🎮 En güncel casino oyun stratejileri</li>
                        <li>🏆 Özel bonus fırsatları ve kampanyalar</li>
                        <li>📺 Canlı yayın duyuruları ve programlar</li>
                        <li>💡 Kazanç artırıcı ipuçları ve taktikler</li>
                        <li>🎲 Yeni oyun incelemeleri ve önerileri</li>
                        <li>🎁 Sadece abonelere özel içerikler</li>
                    </ul>
                </div>
                
                <div class="social-links">
                    <h4>Bizi Sosyal Medyada Takip Edin:</h4>
                    <a href="https://youtube.com/@beratk" target="_blank">📺 YouTube</a>
                    <a href="https://twitch.tv/beratk" target="_blank">🎮 Twitch</a>
                    <a href="https://instagram.com/beratk_casino" target="_blank">📸 Instagram</a>
                    <a href="https://t.me/beratk_casino" target="_blank">💬 Telegram</a>
                </div>
                
                <p>Casino dünyasında 5+ yıllık deneyimimizle en kaliteli içerikleri sizlerle paylaşmaya devam ediyoruz.</p>
                
                <p>Saygılarımızla,<br>
                <strong>BERAT K</strong><br>
                <em>Casino Yayıncısı & Dijital Pazarlama Uzmanı</em></p>
                
                <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
                
                <p style="font-size: 12px; color: #666;">
                    Eğer bu butona tıklayamıyorsanız, aşağıdaki linki kopyalayıp tarayıcınıza yapıştırın:<br>
                    <a href="' . $data['confirmation_url'] . '">' . $data['confirmation_url'] . '</a>
                </p>
            </div>
            
            <div class="footer">
                <p>Bu e-posta ' . htmlspecialchars($data['email']) . ' adresine gönderilmiştir.<br>
                Eğer siz bu aboneliği yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.<br>
                ' . htmlspecialchars($data['site_name']) . ' | © ' . date('Y') . '</p>
            </div>
        </div>
    </body>
    </html>';
}

function generateAdminNotificationTemplate($data) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Yeni Newsletter Aboneliği</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #6f42c1, #e91e63); color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 20px; border: 1px solid #ddd; }
            .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .info-table td { padding: 10px; border-bottom: 1px solid #ddd; }
            .info-table .label { font-weight: bold; background: #e9ecef; width: 150px; }
            .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>📧 Yeni Newsletter Aboneliği</h2>
                <p>' . htmlspecialchars($data['site_name']) . '</p>
            </div>
            
            <div class="content">
                <h3>Yeni Abone Bilgileri</h3>
                
                <table class="info-table">
                    <tr>
                        <td class="label">E-posta:</td>
                        <td>' . htmlspecialchars($data['email']) . '</td>
                    </tr>
                    <tr>
                        <td class="label">Ad:</td>
                        <td>' . htmlspecialchars($data['name'] ?: 'Belirtilmemiş') . '</td>
                    </tr>
                    <tr>
                        <td class="label">Kaynak:</td>
                        <td>' . htmlspecialchars($data['source']) . '</td>
                    </tr>
                    <tr>
                        <td class="label">IP Adresi:</td>
                        <td>' . $data['ip'] . '</td>
                    </tr>
                    <tr>
                        <td class="label">Tarih:</td>
                        <td>' . $data['date'] . '</td>
                    </tr>
                </table>
                
                <p><strong>Admin panelinden aboneyi yönetebilirsiniz.</strong></p>
            </div>
            
            <div class="footer">
                <p>Bu e-posta otomatik olarak oluşturulmuştur.<br>
                ' . htmlspecialchars($data['site_name']) . ' | © ' . date('Y') . '</p>
            </div>
        </div>
    </body>
    </html>';
}
?>
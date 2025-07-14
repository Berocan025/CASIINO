<?php
/**
 * Newsletter Unsubscribe Page
 * Geliştirici: BERAT K
 * GDPR compliant unsubscribe functionality
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/security.php';
require_once 'includes/functions.php';

// Initialize database
$db = new Database();

$success = false;
$message = '';
$email = '';

// Handle unsubscribe request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $reason = sanitizeInput($_POST['reason'] ?? '');
    $feedback = sanitizeInput($_POST['feedback'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Geçerli bir e-posta adresi giriniz.';
    } else {
        // Find subscriber
        $subscriber = $db->query(
            "SELECT * FROM newsletter_subscribers WHERE email = ? AND status = 'active'",
            [$email]
        )->fetch();
        
        if ($subscriber) {
            // Update subscriber status
            $updateResult = $db->update(
                'newsletter_subscribers',
                [
                    'status' => 'unsubscribed',
                    'unsubscribed_at' => date('Y-m-d H:i:s'),
                    'unsubscribe_reason' => $reason,
                    'unsubscribe_feedback' => $feedback,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                ['id' => $subscriber['id']]
            );
            
            if ($updateResult) {
                $success = true;
                $message = 'Newsletter aboneliğiniz başarıyla iptal edildi.';
                
                // Log unsubscribe
                logSecurityEvent('newsletter_unsubscribed', $subscriber['id'], getUserIP(), [
                    'email' => $email,
                    'reason' => $reason
                ]);
                
                // Send goodbye email
                sendGoodbyeEmail($subscriber, $reason);
                
            } else {
                $message = 'Abonelik iptal edilirken bir hata oluştu.';
            }
        } else {
            $message = 'Bu e-posta adresi ile aktif bir abonelik bulunamadı.';
        }
    }
} else {
    // Get email from URL parameter if provided
    $email = filter_var($_GET['email'] ?? '', FILTER_SANITIZE_EMAIL);
}

$pageTitle = "Newsletter Abonelik İptali";
include 'includes/header.php';
?>

<main id="main-content">
    <!-- Hero Section -->
    <section class="hero-section py-5" style="background: linear-gradient(135deg, #021526 0%, #6f42c1 100%); min-height: 50vh;">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8 text-center text-white">
                    <div class="unsubscribe-content">
                        <?php if ($success): ?>
                            <div class="success-animation mb-4">
                                <i class="fas fa-check-circle fa-4x text-success"></i>
                            </div>
                            <h1 class="hero-title mb-4">✅ Abonelik İptal Edildi</h1>
                            <p class="hero-description"><?php echo htmlspecialchars($message); ?></p>
                        <?php else: ?>
                            <div class="unsubscribe-icon mb-4">
                                <i class="fas fa-envelope-open-text fa-4x text-warning"></i>
                            </div>
                            <h1 class="hero-title mb-4">📧 Newsletter Abonelik İptali</h1>
                            <p class="hero-description">Aboneliğinizi iptal etmek istediğinizden emin misiniz?</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($success): ?>
    <!-- Success Section -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-4">😢 Ayrıldığınız için üzgünüz</h3>
                            <p class="text-muted mb-4">
                                Newsletter aboneliğiniz başarıyla iptal edildi. Artık size e-posta göndermeyeceğiz.
                                İsterseniz istediğiniz zaman tekrar abone olabilirsiniz.
                            </p>
                            
                            <div class="row g-4 mt-4">
                                <div class="col-md-6">
                                    <div class="alternative-card p-4">
                                        <h5>🎮 Sosyal Medyada Takip Edin</h5>
                                        <p class="small text-muted">E-posta almak istemiyorsanız sosyal medya hesaplarımızdan takip edebilirsiniz.</p>
                                        <div class="social-links mt-3">
                                            <a href="https://youtube.com/@beratk" target="_blank" class="btn btn-sm btn-danger me-2">
                                                <i class="fab fa-youtube"></i> YouTube
                                            </a>
                                            <a href="https://twitch.tv/beratk" target="_blank" class="btn btn-sm btn-primary me-2">
                                                <i class="fab fa-twitch"></i> Twitch
                                            </a>
                                            <a href="https://instagram.com/beratk_casino" target="_blank" class="btn btn-sm btn-gradient-primary me-2">
                                                <i class="fab fa-instagram"></i> Instagram
                                            </a>
                                            <a href="https://t.me/beratk_casino" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fab fa-telegram"></i> Telegram
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="alternative-card p-4">
                                        <h5>📞 Doğrudan İletişim</h5>
                                        <p class="small text-muted">Özel sorularınız için doğrudan bizimle iletişime geçebilirsiniz.</p>
                                        <div class="contact-options mt-3">
                                            <a href="/pages/contact.php" class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-envelope"></i> İletişim
                                            </a>
                                            <a href="https://wa.me/905555555555" target="_blank" class="btn btn-sm btn-success">
                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-5">
                                <a href="/" class="btn btn-primary btn-lg">
                                    <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php else: ?>
    <!-- Unsubscribe Form -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <?php if ($message): ?>
                            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST" id="unsubscribeForm">
                                <div class="mb-4">
                                    <label for="email" class="form-label">E-posta Adresiniz *</label>
                                    <input type="email" class="form-control form-control-lg" name="email" id="email" 
                                           value="<?php echo htmlspecialchars($email); ?>" required>
                                    <div class="form-text">Abonelikten çıkarmak istediğiniz e-posta adresini girin</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="reason" class="form-label">Neden abonelikten çıkıyorsunuz? (Opsiyonel)</label>
                                    <select class="form-select" name="reason" id="reason">
                                        <option value="">Neden seçin...</option>
                                        <option value="too_many_emails">Çok fazla e-posta alıyorum</option>
                                        <option value="not_relevant">İçerik ilgimi çekmiyor</option>
                                        <option value="never_signed_up">Hiç abone olmadım</option>
                                        <option value="changed_mind">Fikrimi değiştirdim</option>
                                        <option value="privacy_concerns">Gizlilik endişeleri</option>
                                        <option value="other">Diğer</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="feedback" class="form-label">Geri Bildirim (Opsiyonel)</label>
                                    <textarea class="form-control" name="feedback" id="feedback" rows="3" 
                                              placeholder="Nasıl daha iyi olabiliriz? Önerilerinizi bekliyoruz..."></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirm" required>
                                        <label class="form-check-label" for="confirm">
                                            Newsletter aboneliğimi iptal etmek istediğimi onaylıyorum
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-unlink me-2"></i>Aboneliği İptal Et
                                    </button>
                                    <a href="/" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Vazgeç
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alternative Options -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="mb-4">🤔 Abonelikten çıkmadan önce...</h3>
                    <p class="text-muted mb-4">
                        Belki sadece e-posta sıklığını değiştirmek istersiniz? Ya da belirli konularda e-posta almak istersiniz?
                    </p>
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="option-card p-4 border rounded">
                                <i class="fas fa-cog fa-2x text-primary mb-3"></i>
                                <h5>E-posta Ayarları</h5>
                                <p class="small text-muted">E-posta sıklığını ve içerik türünü özelleştirin</p>
                                <a href="/pages/contact.php" class="btn btn-sm btn-outline-primary">Ayarları Değiştir</a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="option-card p-4 border rounded">
                                <i class="fas fa-pause fa-2x text-warning mb-3"></i>
                                <h5>Geçici Durdur</h5>
                                <p class="small text-muted">Bir süre e-posta almayı durdurun</p>
                                <a href="/pages/contact.php" class="btn btn-sm btn-outline-warning">Geçici Durdur</a>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="option-card p-4 border rounded">
                                <i class="fab fa-telegram fa-2x text-info mb-3"></i>
                                <h5>Telegram'a Geç</h5>
                                <p class="small text-muted">E-posta yerine Telegram bildirimleri alın</p>
                                <a href="https://t.me/beratk_casino" target="_blank" class="btn btn-sm btn-outline-info">Telegram'a Katıl</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<style>
.unsubscribe-content {
    animation: slideInUp 1s ease-out;
}

.success-animation {
    animation: fadeInBounce 1s ease-out;
}

@keyframes fadeInBounce {
    0% {
        opacity: 0;
        transform: scale(0.3) translateY(-50px);
    }
    50% {
        opacity: 1;
        transform: scale(1.1);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alternative-card {
    background: #f8f9fa;
    border-radius: 15px;
    height: 100%;
    transition: all 0.3s ease;
}

.alternative-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.option-card {
    transition: all 0.3s ease;
    height: 100%;
}

.option-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.social-links a {
    border-radius: 20px;
    transition: all 0.3s ease;
}

.social-links a:hover {
    transform: scale(1.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('unsubscribeForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkbox = document.getElementById('confirm');
            if (!checkbox.checked) {
                e.preventDefault();
                alert('Lütfen abonelik iptalini onaylayın.');
                return false;
            }
            
            // Show confirmation dialog
            const confirmed = confirm('Newsletter aboneliğinizi iptal etmek istediğinizden emin misiniz? Bu işlem geri alınamaz.');
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Reason field change handler
    const reasonField = document.getElementById('reason');
    const feedbackField = document.getElementById('feedback');
    
    if (reasonField && feedbackField) {
        reasonField.addEventListener('change', function() {
            if (this.value === 'other') {
                feedbackField.setAttribute('placeholder', 'Lütfen nedeninizi açıklayın...');
                feedbackField.focus();
            } else {
                feedbackField.setAttribute('placeholder', 'Nasıl daha iyi olabiliriz? Önerilerinizi bekliyoruz...');
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>

<?php
// Helper function to send goodbye email
function sendGoodbyeEmail($subscriber, $reason) {
    $siteName = getSiteSetting('site_name', 'Casino Yayıncısı - BERAT K');
    $siteUrl = getSiteSetting('site_url', 'https://yoursite.com');
    
    $emailSubject = "Üzgünüz ki ayrılıyorsunuz - " . $siteName;
    $emailBody = generateGoodbyeEmailTemplate([
        'name' => $subscriber['name'] ?: 'Değerli Kullanıcı',
        'email' => $subscriber['email'],
        'reason' => $reason,
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
    
    return mail($subscriber['email'], $emailSubject, $emailBody, implode("\r\n", $emailHeaders));
}

function generateGoodbyeEmailTemplate($data) {
    $reasonTexts = [
        'too_many_emails' => 'Çok fazla e-posta aldığınızı',
        'not_relevant' => 'İçeriklerimizin ilginizi çekmediğini',
        'never_signed_up' => 'Abone olmadığınızı',
        'changed_mind' => 'Fikrinizi değiştirdiğinizi',
        'privacy_concerns' => 'Gizlilik endişeleriniz olduğunu',
        'other' => 'Farklı nedenleriniz olduğunu'
    ];
    
    $reasonText = $reasonTexts[$data['reason']] ?? '';
    
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Ayrıldığınız için üzgünüz</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #6f42c1, #e91e63); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 10px 10px; }
            .goodbye-message { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .social-links { text-align: center; margin: 20px 0; }
            .social-links a { display: inline-block; margin: 0 10px; color: #6f42c1; text-decoration: none; }
            .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }
            .resubscribe { background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>😢 Ayrıldığınız için üzgünüz</h1>
                <p>' . htmlspecialchars($data['site_name']) . '</p>
            </div>
            
            <div class="content">
                <div class="goodbye-message">
                    <h3>Merhaba ' . htmlspecialchars($data['name']) . ',</h3>
                    
                    <p>Newsletter aboneliğinizi iptal ettiğiniz için üzgünüz. ' . ($reasonText ? 'Belirttiğiniz nedeni anlıyoruz: ' . $reasonText . '.' : '') . '</p>
                    
                    <p>Her ne kadar e-posta listesi ile ayrılsak da, sosyal medya hesaplarımızdan bizi takip etmeye devam edebilirsiniz. Burada günlük içerikler, canlı yayın duyuruları ve eğlenceli içerikler paylaşıyoruz.</p>
                </div>
                
                <div class="social-links">
                    <h4>Sosyal Medyada Görüşmek Üzere:</h4>
                    <a href="https://youtube.com/@beratk" target="_blank">📺 YouTube</a>
                    <a href="https://twitch.tv/beratk" target="_blank">🎮 Twitch</a>
                    <a href="https://instagram.com/beratk_casino" target="_blank">📸 Instagram</a>
                    <a href="https://t.me/beratk_casino" target="_blank">💬 Telegram</a>
                </div>
                
                <div class="resubscribe">
                    <h4>💭 Fikrinizi Değiştirirseniz</h4>
                    <p>İstediğiniz zaman tekrar abone olabilirsiniz. Size her zaman kapımız açık!</p>
                    <p><a href="' . $data['site_url'] . '">Tekrar abone olmak için tıklayın</a></p>
                </div>
                
                <p style="text-align: center;">
                    Casino dünyasında başarılar dileriz!<br><br>
                    <strong>BERAT K</strong><br>
                    <em>Casino Yayıncısı & Dijital Pazarlama Uzmanı</em>
                </p>
            </div>
            
            <div class="footer">
                <p>Bu e-posta ' . htmlspecialchars($data['email']) . ' adresine gönderilmiştir.<br>
                Artık size e-posta göndermeyeceğiz.<br>
                ' . htmlspecialchars($data['site_name']) . ' | © ' . date('Y') . '</p>
            </div>
        </div>
    </body>
    </html>';
}
?>
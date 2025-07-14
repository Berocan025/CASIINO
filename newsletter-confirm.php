<?php
/**
 * Newsletter Confirmation Page
 * Geliştirici: BERAT K
 * Email verification for newsletter subscriptions
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/security.php';
require_once 'includes/functions.php';

// Initialize database
$db = new Database();

$success = false;
$message = '';
$subscriberData = null;

// Check if token is provided
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = sanitizeInput($_GET['token']);
    
    // Find subscriber with this token
    $subscriber = $db->query(
        "SELECT * FROM newsletter_subscribers WHERE confirmation_token = ? AND status = 'pending'",
        [$token]
    )->fetch();
    
    if ($subscriber) {
        // Check if token is not expired (24 hours)
        $tokenAge = strtotime('now') - strtotime($subscriber['created_at']);
        if ($tokenAge <= 86400) { // 24 hours = 86400 seconds
            
            // Activate subscription
            $updateResult = $db->update(
                'newsletter_subscribers',
                [
                    'status' => 'active',
                    'confirmed_at' => date('Y-m-d H:i:s'),
                    'confirmation_token' => null,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                ['id' => $subscriber['id']]
            );
            
            if ($updateResult) {
                $success = true;
                $message = 'Newsletter aboneliğiniz başarıyla onaylandı!';
                $subscriberData = $subscriber;
                
                // Log successful confirmation
                logSecurityEvent('newsletter_confirmed', $subscriber['id'], getUserIP(), [
                    'email' => $subscriber['email']
                ]);
                
                // Send welcome email
                sendWelcomeEmail($subscriber);
                
            } else {
                $message = 'Abonelik onaylanırken bir hata oluştu. Lütfen tekrar deneyin.';
            }
        } else {
            $message = 'Onay linki süresi dolmuş. Lütfen tekrar abone olmayı deneyin.';
            
            // Delete expired token
            $db->delete('newsletter_subscribers', ['id' => $subscriber['id']]);
        }
    } else {
        $message = 'Geçersiz onay linki. Link daha önce kullanılmış olabilir.';
    }
} else {
    $message = 'Onay linki eksik. Lütfen e-postanızdaki linki kullanın.';
}

$pageTitle = "Newsletter Onayı";
include 'includes/header.php';
?>

<main id="main-content">
    <!-- Hero Section -->
    <section class="hero-section py-5" style="background: linear-gradient(135deg, #021526 0%, #6f42c1 100%); min-height: 60vh;">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-60">
                <div class="col-lg-8 text-center text-white">
                    <div class="confirmation-content">
                        <?php if ($success): ?>
                            <div class="success-animation mb-4">
                                <i class="fas fa-check-circle fa-5x text-success"></i>
                            </div>
                            <h1 class="hero-title mb-4">🎉 Hoş Geldiniz!</h1>
                            <p class="hero-description mb-4"><?php echo htmlspecialchars($message); ?></p>
                            <p class="lead">Artık en güncel casino içeriklerimizi ve özel fırsatlarımızı e-posta adresinize göndereceğiz.</p>
                        <?php else: ?>
                            <div class="error-animation mb-4">
                                <i class="fas fa-exclamation-triangle fa-5x text-warning"></i>
                            </div>
                            <h1 class="hero-title mb-4">⚠️ Onay Sorunu</h1>
                            <p class="hero-description mb-4"><?php echo htmlspecialchars($message); ?></p>
                        <?php endif; ?>
                        
                        <div class="hero-buttons mt-4">
                            <a href="/" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                            </a>
                            <?php if (!$success): ?>
                            <a href="/pages/contact.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-envelope me-2"></i>İletişime Geç
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($success): ?>
    <!-- Welcome Section -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center mb-5">
                                <h2 class="section-title">🎰 Casino Dünyasına Hoş Geldiniz!</h2>
                                <p class="section-description">Şimdi ne olacak? İşte sizin için hazırladıklarımız:</p>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="feature-card text-center p-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-envelope-open-text fa-3x text-primary"></i>
                                        </div>
                                        <h4>Haftalık Newsletter</h4>
                                        <p class="text-muted">Her hafta en güncel casino stratejileri, oyun incelemeleri ve özel bonusları e-postanızda bulacaksınız.</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="feature-card text-center p-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-gift fa-3x text-success"></i>
                                        </div>
                                        <h4>Özel Fırsatlar</h4>
                                        <p class="text-muted">Sadece abonelerimize özel bonus kodları, kampanyalar ve casino sitelerinde özel avantajlar.</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="feature-card text-center p-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-video fa-3x text-danger"></i>
                                        </div>
                                        <h4>Canlı Yayın Duyuruları</h4>
                                        <p class="text-muted">YouTube ve Twitch'teki canlı yayınlarımızdan önce özel bildirimler alacaksınız.</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="feature-card text-center p-4">
                                        <div class="feature-icon mb-3">
                                            <i class="fas fa-chart-line fa-3x text-info"></i>
                                        </div>
                                        <h4>VIP İçerikler</h4>
                                        <p class="text-muted">Kazanç stratejileri, risk yönetimi ve sadece abonelere özel eğitim içerikleri.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="mb-4">📱 Bizi Sosyal Medyada Takip Edin</h3>
                    <p class="text-muted mb-4">Daha fazla içerik ve anlık güncellemeler için sosyal medya hesaplarımızı takip etmeyi unutmayın!</p>
                    
                    <div class="social-links">
                        <a href="https://youtube.com/@beratk" target="_blank" class="btn btn-danger btn-lg me-3 mb-3">
                            <i class="fab fa-youtube me-2"></i>YouTube
                        </a>
                        <a href="https://twitch.tv/beratk" target="_blank" class="btn btn-primary btn-lg me-3 mb-3" style="background-color: #9146ff;">
                            <i class="fab fa-twitch me-2"></i>Twitch
                        </a>
                        <a href="https://instagram.com/beratk_casino" target="_blank" class="btn btn-gradient-primary btn-lg me-3 mb-3">
                            <i class="fab fa-instagram me-2"></i>Instagram
                        </a>
                        <a href="https://t.me/beratk_casino" target="_blank" class="btn btn-info btn-lg mb-3">
                            <i class="fab fa-telegram me-2"></i>Telegram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Newsletter Section (if not confirmed) -->
    <?php if (!$success): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h3>📧 Tekrar Abone Ol</h3>
                                <p class="text-muted">Eğer onay linkiniz çalışmıyorsa, tekrar abone olabilirsiniz.</p>
                            </div>
                            
                            <form id="newsletterForm" class="newsletter-form">
                                <div class="input-group mb-3">
                                    <input type="email" class="form-control form-control-lg" name="email" placeholder="E-posta adresiniz" required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Kişisel verileriniz gizlilik politikamız kapsamında korunmaktadır.</small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<style>
.min-vh-60 {
    min-height: 60vh;
}

.success-animation, .error-animation {
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

.feature-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.feature-icon {
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1);
}

.social-links a {
    transition: all 0.3s ease;
    border-radius: 25px;
}

.social-links a:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.confirmation-content {
    animation: slideInUp 1s ease-out;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Newsletter form handling
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[name="email"]').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (!validateEmail(email)) {
                showAlert('warning', 'Geçerli bir e-posta adresi giriniz.');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            try {
                const response = await fetch('/api/newsletter.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Yeni onay e-postası gönderildi! Lütfen e-postanızı kontrol edin.');
                    this.reset();
                } else {
                    showAlert('danger', result.message);
                }
            } catch (error) {
                showAlert('danger', 'Bir hata oluştu. Lütfen tekrar deneyin.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            }
        });
    }
    
    // Success animation
    <?php if ($success): ?>
    setTimeout(() => {
        const successIcon = document.querySelector('.success-animation i');
        if (successIcon) {
            successIcon.style.animation = 'pulse 2s infinite';
        }
    }, 1000);
    <?php endif; ?>
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function showAlert(type, message, duration = 5000) {
    const alertContainer = getOrCreateAlertContainer();
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, duration);
}

function getOrCreateAlertContainer() {
    let container = document.getElementById('alert-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'alert-container';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    return container;
}
</script>

<?php include 'includes/footer.php'; ?>

<?php
// Helper function to send welcome email
function sendWelcomeEmail($subscriber) {
    $siteName = getSiteSetting('site_name', 'Casino Yayıncısı - BERAT K');
    $siteUrl = getSiteSetting('site_url', 'https://yoursite.com');
    
    $emailSubject = "Hoş Geldiniz! - " . $siteName;
    $emailBody = generateWelcomeEmailTemplate([
        'name' => $subscriber['name'] ?: 'Değerli Abone',
        'email' => $subscriber['email'],
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

function generateWelcomeEmailTemplate($data) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Hoş Geldiniz!</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #6f42c1, #e91e63); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border: 1px solid #ddd; border-radius: 0 0 10px 10px; }
            .welcome-message { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .benefits { background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .benefits ul { list-style: none; padding: 0; }
            .benefits li { padding: 8px 0; position: relative; padding-left: 25px; }
            .benefits li:before { content: "🎯"; position: absolute; left: 0; }
            .social-links { text-align: center; margin: 20px 0; }
            .social-links a { display: inline-block; margin: 0 10px; color: #6f42c1; text-decoration: none; }
            .footer { text-align: center; color: #6c757d; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🎉 Hoş Geldiniz!</h1>
                <p>' . htmlspecialchars($data['site_name']) . '</p>
            </div>
            
            <div class="content">
                <div class="welcome-message">
                    <h3>Merhaba ' . htmlspecialchars($data['name']) . ',</h3>
                    
                    <p>Newsletter aboneliğinizi onayladığınız için teşekkür ederiz! Artık casino dünyasındaki en güncel gelişmeleri, özel stratejileri ve sadece abonelerimize özel fırsatları ilk siz öğreneceksiniz.</p>
                </div>
                
                <div class="benefits">
                    <h4>📬 Bundan Sonra Neler Alacaksınız?</h4>
                    <ul>
                        <li>Haftalık casino stratejileri ve ipuçları</li>
                        <li>Özel bonus kodları ve kampanyalar</li>
                        <li>Canlı yayın duyuruları (YouTube & Twitch)</li>
                        <li>Yeni oyun incelemeleri ve önerileri</li>
                        <li>Risk yönetimi ve bankroll stratejileri</li>
                        <li>VIP webinarlar ve eğitim içerikleri</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <p><strong>5+ yıllık casino deneyimimizle birlikte başarıya giden yolda sizin yanınızdayız!</strong></p>
                </div>
                
                <div class="social-links">
                    <h4>Sosyal Medyada Bizi Takip Edin:</h4>
                    <a href="https://youtube.com/@beratk" target="_blank">📺 YouTube</a>
                    <a href="https://twitch.tv/beratk" target="_blank">🎮 Twitch</a>
                    <a href="https://instagram.com/beratk_casino" target="_blank">📸 Instagram</a>
                    <a href="https://t.me/beratk_casino" target="_blank">💬 Telegram</a>
                </div>
                
                <p style="text-align: center;">
                    <strong>BERAT K</strong><br>
                    <em>Casino Yayıncısı & Dijital Pazarlama Uzmanı</em>
                </p>
            </div>
            
            <div class="footer">
                <p>Bu e-posta ' . htmlspecialchars($data['email']) . ' adresine gönderilmiştir.<br>
                Abonelikten çıkmak için <a href="' . $data['site_url'] . '/unsubscribe.php">buraya tıklayın</a>.<br>
                ' . htmlspecialchars($data['site_name']) . ' | © ' . date('Y') . '</p>
            </div>
        </div>
    </body>
    </html>';
}
?>
<?php
/**
 * Admin Settings
 * Geliştirici: BERAT K
 * Site configuration and settings management
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Check admin authentication
if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login.php');
}

// Initialize database
$db = new Database();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Güvenlik hatası. Sayfayı yenileyin.';
    } else {
        try {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'update_general':
                    $success = updateGeneralSettings($_POST);
                    $message = $success ? 'Genel ayarlar güncellendi.' : 'Ayarlar güncellenirken hata oluştu.';
                    break;
                    
                case 'update_social':
                    $success = updateSocialSettings($_POST);
                    $message = $success ? 'Sosyal medya ayarları güncellendi.' : 'Ayarlar güncellenirken hata oluştu.';
                    break;
                    
                case 'update_email':
                    $success = updateEmailSettings($_POST);
                    $message = $success ? 'E-posta ayarları güncellendi.' : 'Ayarlar güncellenirken hata oluştu.';
                    break;
                    
                case 'update_seo':
                    $success = updateSEOSettings($_POST);
                    $message = $success ? 'SEO ayarları güncellendi.' : 'Ayarlar güncellenirken hata oluştu.';
                    break;
                    
                case 'backup_database':
                    $success = backupDatabase();
                    $message = $success ? 'Veritabanı yedeği oluşturuldu.' : 'Yedek oluşturulurken hata oluştu.';
                    break;
                    
                case 'clear_cache':
                    $success = clearCache();
                    $message = $success ? 'Önbellek temizlendi.' : 'Önbellek temizlenirken hata oluştu.';
                    break;
                    
                default:
                    $error = 'Geçersiz işlem.';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get current settings
$settings = [];
$settingsData = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
foreach ($settingsData as $setting) {
    $settings[$setting['setting_key']] = $setting['setting_value'];
}

$pageTitle = "Ayarlar";
include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">⚙️ Ayarlar</h1>
            <p class="text-muted">Site yapılandırması ve genel ayarlar</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-warning" onclick="backupDatabase()">
                <i class="fas fa-download me-2"></i>Yedek Al
            </button>
            <button class="btn btn-outline-info" onclick="clearCache()">
                <i class="fas fa-broom me-2"></i>Önbellek Temizle
            </button>
        </div>
    </div>

    <?php if (isset($message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Settings Tabs -->
    <div class="row">
        <div class="col-md-3">
            <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#general" role="tab">
                    <i class="fas fa-cog me-2"></i>Genel Ayarlar
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#social" role="tab">
                    <i class="fas fa-share-alt me-2"></i>Sosyal Medya
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#email" role="tab">
                    <i class="fas fa-envelope me-2"></i>E-posta Ayarları
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#seo" role="tab">
                    <i class="fas fa-search me-2"></i>SEO Ayarları
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security" role="tab">
                    <i class="fas fa-shield-alt me-2"></i>Güvenlik
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#maintenance" role="tab">
                    <i class="fas fa-tools me-2"></i>Bakım
                </button>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="tab-content">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Genel Ayarlar</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="update_general">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Site Adı</label>
                                            <input type="text" class="form-control" name="site_name" id="site_name" 
                                                   value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Casino Yayıncısı - BERAT K'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_url" class="form-label">Site URL</label>
                                            <input type="url" class="form-control" name="site_url" id="site_url" 
                                                   value="<?php echo htmlspecialchars($settings['site_url'] ?? 'https://yoursite.com'); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="site_description" class="form-label">Site Açıklaması</label>
                                    <textarea class="form-control" name="site_description" id="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? 'Profesyonel casino yayıncısı ve dijital pazarlama uzmanı'); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="admin_email" class="form-label">Admin E-posta</label>
                                            <input type="email" class="form-control" name="admin_email" id="admin_email" 
                                                   value="<?php echo htmlspecialchars($settings['admin_email'] ?? 'admin@yoursite.com'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_phone" class="form-label">İletişim Telefonu</label>
                                            <input type="text" class="form-control" name="contact_phone" id="contact_phone" 
                                                   value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '+90 555 555 55 55'); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Saat Dilimi</label>
                                            <select class="form-select" name="timezone" id="timezone">
                                                <option value="Europe/Istanbul" <?php echo ($settings['timezone'] ?? '') === 'Europe/Istanbul' ? 'selected' : ''; ?>>İstanbul</option>
                                                <option value="UTC" <?php echo ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="language" class="form-label">Dil</label>
                                            <select class="form-select" name="language" id="language">
                                                <option value="tr" <?php echo ($settings['language'] ?? '') === 'tr' ? 'selected' : ''; ?>>Türkçe</option>
                                                <option value="en" <?php echo ($settings['language'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" 
                                               value="1" <?php echo ($settings['maintenance_mode'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="maintenance_mode">
                                            Bakım Modu
                                        </label>
                                        <div class="form-text">Aktif olduğunda site ziyaretçilere kapatılır</div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Kaydet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Social Media Settings -->
                <div class="tab-pane fade" id="social">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sosyal Medya Ayarları</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="update_social">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="youtube_url" class="form-label">
                                                <i class="fab fa-youtube text-danger me-2"></i>YouTube
                                            </label>
                                            <input type="url" class="form-control" name="youtube_url" id="youtube_url" 
                                                   value="<?php echo htmlspecialchars($settings['youtube_url'] ?? 'https://youtube.com/@beratk'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="twitch_url" class="form-label">
                                                <i class="fab fa-twitch text-purple me-2"></i>Twitch
                                            </label>
                                            <input type="url" class="form-control" name="twitch_url" id="twitch_url" 
                                                   value="<?php echo htmlspecialchars($settings['twitch_url'] ?? 'https://twitch.tv/beratk'); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="instagram_url" class="form-label">
                                                <i class="fab fa-instagram text-primary me-2"></i>Instagram
                                            </label>
                                            <input type="url" class="form-control" name="instagram_url" id="instagram_url" 
                                                   value="<?php echo htmlspecialchars($settings['instagram_url'] ?? 'https://instagram.com/beratk_casino'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telegram_url" class="form-label">
                                                <i class="fab fa-telegram text-info me-2"></i>Telegram
                                            </label>
                                            <input type="url" class="form-control" name="telegram_url" id="telegram_url" 
                                                   value="<?php echo htmlspecialchars($settings['telegram_url'] ?? 'https://t.me/beratk_casino'); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="twitter_url" class="form-label">
                                                <i class="fab fa-twitter text-info me-2"></i>Twitter
                                            </label>
                                            <input type="url" class="form-control" name="twitter_url" id="twitter_url" 
                                                   value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="whatsapp_number" class="form-label">
                                                <i class="fab fa-whatsapp text-success me-2"></i>WhatsApp
                                            </label>
                                            <input type="text" class="form-control" name="whatsapp_number" id="whatsapp_number" 
                                                   value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? '+905555555555'); ?>"
                                                   placeholder="+905555555555">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Kaydet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="tab-pane fade" id="email">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">E-posta Ayarları</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="update_email">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_host" class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" name="smtp_host" id="smtp_host" 
                                                   value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>"
                                                   placeholder="mail.yoursite.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_port" class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" name="smtp_port" id="smtp_port" 
                                                   value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_username" class="form-label">SMTP Kullanıcı Adı</label>
                                            <input type="text" class="form-control" name="smtp_username" id="smtp_username" 
                                                   value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_password" class="form-label">SMTP Şifre</label>
                                            <input type="password" class="form-control" name="smtp_password" id="smtp_password" 
                                                   value="<?php echo htmlspecialchars($settings['smtp_password'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_from_name" class="form-label">Gönderen Adı</label>
                                            <input type="text" class="form-control" name="mail_from_name" id="mail_from_name" 
                                                   value="<?php echo htmlspecialchars($settings['mail_from_name'] ?? 'Casino Yayıncısı - BERAT K'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_from_email" class="form-label">Gönderen E-posta</label>
                                            <input type="email" class="form-control" name="mail_from_email" id="mail_from_email" 
                                                   value="<?php echo htmlspecialchars($settings['mail_from_email'] ?? 'noreply@yoursite.com'); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="smtp_auth" id="smtp_auth" 
                                               value="1" <?php echo ($settings['smtp_auth'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="smtp_auth">
                                            SMTP Authentication
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="smtp_secure" id="smtp_secure" 
                                               value="1" <?php echo ($settings['smtp_secure'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="smtp_secure">
                                            SMTP Secure (TLS/SSL)
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Kaydet
                                </button>
                                <button type="button" class="btn btn-outline-info ms-2" onclick="testEmail()">
                                    <i class="fas fa-paper-plane me-2"></i>Test E-postası Gönder
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="tab-pane fade" id="seo">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">SEO Ayarları</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="update_seo">
                                
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" name="meta_title" id="meta_title" 
                                           value="<?php echo htmlspecialchars($settings['meta_title'] ?? 'Casino Yayıncısı - BERAT K | Profesyonel Casino Streamer'); ?>"
                                           maxlength="60">
                                    <div class="form-text">Maksimum 60 karakter</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" name="meta_description" id="meta_description" rows="3" maxlength="160"><?php echo htmlspecialchars($settings['meta_description'] ?? '5+ yıllık deneyimli casino yayıncısı. YouTube, Twitch canlı yayınları, dijital pazarlama hizmetleri ve casino stratejileri.'); ?></textarea>
                                    <div class="form-text">Maksimum 160 karakter</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" 
                                           value="<?php echo htmlspecialchars($settings['meta_keywords'] ?? 'casino, yayıncı, twitch, youtube, stramer, berat k, dijital pazarlama, casino stratejileri'); ?>"
                                           placeholder="virgül ile ayırın">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="google_analytics" class="form-label">Google Analytics ID</label>
                                            <input type="text" class="form-control" name="google_analytics" id="google_analytics" 
                                                   value="<?php echo htmlspecialchars($settings['google_analytics'] ?? ''); ?>"
                                                   placeholder="G-XXXXXXXXXX">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="google_search_console" class="form-label">Google Search Console</label>
                                            <input type="text" class="form-control" name="google_search_console" id="google_search_console" 
                                                   value="<?php echo htmlspecialchars($settings['google_search_console'] ?? ''); ?>"
                                                   placeholder="Verification code">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="robots_txt" class="form-label">Robots.txt İçeriği</label>
                                    <textarea class="form-control" name="robots_txt" id="robots_txt" rows="5"><?php echo htmlspecialchars($settings['robots_txt'] ?? "User-agent: *\nAllow: /\nSitemap: " . ($settings['site_url'] ?? 'https://yoursite.com') . "/sitemap.xml"); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Kaydet
                                </button>
                                <button type="button" class="btn btn-outline-info ms-2" onclick="generateSitemap()">
                                    <i class="fas fa-sitemap me-2"></i>Sitemap Oluştur
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="tab-pane fade" id="security">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Güvenlik Ayarları</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Güvenlik Durumu</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>CSRF Protection: Aktif</li>
                                                <li><i class="fas fa-check text-success me-2"></i>XSS Protection: Aktif</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Rate Limiting: Aktif</li>
                                                <li><i class="fas fa-check text-success me-2"></i>SQL Injection Protection: Aktif</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Sistem Bilgileri</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
                                                <li><strong>SQLite Version:</strong> <?php echo SQLite3::version()['versionString']; ?></li>
                                                <li><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></li>
                                                <li><strong>Upload Max Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h6>Güvenlik Logları</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tarih</th>
                                                <th>Olay</th>
                                                <th>IP</th>
                                                <th>Detay</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $securityLogs = $db->query(
                                                "SELECT * FROM security_logs ORDER BY created_at DESC LIMIT 10"
                                            )->fetchAll();
                                            
                                            foreach ($securityLogs as $log): ?>
                                            <tr>
                                                <td><?php echo formatDateTime($log['created_at']); ?></td>
                                                <td><?php echo htmlspecialchars($log['event_type']); ?></td>
                                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($log['details'], 0, 50)) . '...'; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance -->
                <div class="tab-pane fade" id="maintenance">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Bakım İşlemleri</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-database fa-3x text-primary mb-3"></i>
                                            <h6>Veritabanı Yedeği</h6>
                                            <p class="text-muted">Veritabanının tam yedeğini al</p>
                                            <button class="btn btn-primary" onclick="backupDatabase()">
                                                <i class="fas fa-download me-2"></i>Yedek Al
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-broom fa-3x text-warning mb-3"></i>
                                            <h6>Önbellek Temizle</h6>
                                            <p class="text-muted">Tüm önbellek dosyalarını temizle</p>
                                            <button class="btn btn-warning" onclick="clearCache()">
                                                <i class="fas fa-broom me-2"></i>Temizle
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                                            <h6>İstatistikleri Güncelle</h6>
                                            <p class="text-muted">Site istatistiklerini yeniden hesapla</p>
                                            <button class="btn btn-info" onclick="updateStats()">
                                                <i class="fas fa-sync me-2"></i>Güncelle
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-sitemap fa-3x text-success mb-3"></i>
                                            <h6>Sitemap Oluştur</h6>
                                            <p class="text-muted">SEO için sitemap.xml oluştur</p>
                                            <button class="btn btn-success" onclick="generateSitemap()">
                                                <i class="fas fa-sitemap me-2"></i>Oluştur
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function backupDatabase() {
    if (confirm('Veritabanı yedeği oluşturulsun mu?')) {
        window.location.href = '?action=backup_database';
    }
}

function clearCache() {
    if (confirm('Önbellek temizlensin mi?')) {
        window.location.href = '?action=clear_cache';
    }
}

function updateStats() {
    showAlert('info', 'İstatistikler güncelleniyor...');
    // AJAX call to update stats
}

function generateSitemap() {
    showAlert('info', 'Sitemap oluşturuluyor...');
    // AJAX call to generate sitemap
}

function testEmail() {
    if (confirm('Test e-postası gönderilsin mi?')) {
        // AJAX call to send test email
        showAlert('info', 'Test e-postası gönderiliyor...');
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>

<?php
// Settings update functions
function updateGeneralSettings($data) {
    global $db;
    
    $settings = [
        'site_name' => sanitizeInput($data['site_name']),
        'site_url' => sanitizeInput($data['site_url']),
        'site_description' => sanitizeInput($data['site_description']),
        'admin_email' => sanitizeInput($data['admin_email']),
        'contact_phone' => sanitizeInput($data['contact_phone']),
        'timezone' => sanitizeInput($data['timezone']),
        'language' => sanitizeInput($data['language']),
        'maintenance_mode' => isset($data['maintenance_mode']) ? '1' : '0'
    ];
    
    return updateSettings($settings);
}

function updateSocialSettings($data) {
    global $db;
    
    $settings = [
        'youtube_url' => sanitizeInput($data['youtube_url']),
        'twitch_url' => sanitizeInput($data['twitch_url']),
        'instagram_url' => sanitizeInput($data['instagram_url']),
        'telegram_url' => sanitizeInput($data['telegram_url']),
        'twitter_url' => sanitizeInput($data['twitter_url']),
        'whatsapp_number' => sanitizeInput($data['whatsapp_number'])
    ];
    
    return updateSettings($settings);
}

function updateEmailSettings($data) {
    global $db;
    
    $settings = [
        'smtp_host' => sanitizeInput($data['smtp_host']),
        'smtp_port' => sanitizeInput($data['smtp_port']),
        'smtp_username' => sanitizeInput($data['smtp_username']),
        'smtp_password' => sanitizeInput($data['smtp_password']),
        'mail_from_name' => sanitizeInput($data['mail_from_name']),
        'mail_from_email' => sanitizeInput($data['mail_from_email']),
        'smtp_auth' => isset($data['smtp_auth']) ? '1' : '0',
        'smtp_secure' => isset($data['smtp_secure']) ? '1' : '0'
    ];
    
    return updateSettings($settings);
}

function updateSEOSettings($data) {
    global $db;
    
    $settings = [
        'meta_title' => sanitizeInput($data['meta_title']),
        'meta_description' => sanitizeInput($data['meta_description']),
        'meta_keywords' => sanitizeInput($data['meta_keywords']),
        'google_analytics' => sanitizeInput($data['google_analytics']),
        'google_search_console' => sanitizeInput($data['google_search_console']),
        'robots_txt' => sanitizeInput($data['robots_txt'])
    ];
    
    return updateSettings($settings);
}

function updateSettings($settings) {
    global $db;
    
    $db->beginTransaction();
    
    try {
        foreach ($settings as $key => $value) {
            $existing = $db->query("SELECT id FROM settings WHERE setting_key = ?", [$key])->fetch();
            
            if ($existing) {
                $db->update('settings', ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], ['setting_key' => $key]);
            } else {
                $db->insert('settings', [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

function backupDatabase() {
    try {
        $backupDir = '../backups/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $source = '../database/casino_portfolio.db';
        $destination = $backupDir . $filename;
        
        if (copy($source, $destination)) {
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function clearCache() {
    try {
        $cacheDir = '../cache/';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
<?php
/**
 * Contact Page
 * Geliştirici: BERAT K
 * Contact page with form, map and contact information
 */

// Include configuration
define('SITE_ROOT', dirname(__DIR__));
require_once SITE_ROOT . '/includes/config.php';

// Set page variables
$page_title = 'İletişim';
$page_description = 'Casino yayıncısı BERAT K ile iletişime geçin. Profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri için hemen iletişime geçin.';
$page_keywords = 'iletişim, casino yayıncısı, dijital pazarlama, BERAT K, contact';
$body_class = 'contact-page';

// Include header
include SITE_ROOT . '/includes/header.php';
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section py-4">
    <div class="container">
        <?php 
        echo get_breadcrumb([
            ['title' => 'Ana Sayfa', 'url' => site_url()],
            ['title' => 'İletişim', 'url' => '']
        ]);
        ?>
    </div>
</section>

<!-- Contact Hero Section -->
<section class="contact-hero py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <span class="section-subtitle">İletişim</span>
                <h1 class="section-title">Benimle İletişime Geçin</h1>
                <p class="section-description">
                    Casino yayıncılığı ve dijital pazarlama hizmetleri için profesyonel destek. 
                    Projeleriniz için ücretsiz danışmanlık alın.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information Cards -->
<section class="contact-info-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="contact-info-card text-center">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4>Telefon</h4>
                    <p><?php echo get_setting('contact_phone', '+90 555 123 45 67'); ?></p>
                    <a href="tel:<?php echo str_replace([' ', '(', ')', '-'], '', get_setting('contact_phone', '')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-phone"></i> Ara
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="contact-info-card text-center">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4>E-mail</h4>
                    <p><?php echo get_setting('contact_email', 'info@casinoportfolio.com'); ?></p>
                    <a href="mailto:<?php echo get_setting('contact_email', 'info@casinoportfolio.com'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-envelope"></i> Mail Gönder
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="contact-info-card text-center">
                    <div class="info-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h4>WhatsApp</h4>
                    <p>7/24 Hızlı Destek</p>
                    <a href="https://wa.me/<?php echo str_replace(['+', ' ', '(', ')', '-'], '', get_setting('contact_phone', '')); ?>?text=Merhaba, casino yayıncılığı hizmetleri hakkında bilgi almak istiyorum." 
                       target="_blank" class="btn btn-success">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact-form-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="contact-form-wrapper bg-glass p-4 p-md-5 rounded">
                    <div class="text-center mb-4">
                        <h2 class="section-title">Mesaj Gönder</h2>
                        <p class="section-description">
                            Formu doldurarak bana ulaşabilirsiniz. En kısa sürede size dönüş yapacağım.
                        </p>
                    </div>
                    
                    <form id="contactForm" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Ad Soyad *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">Konu *</label>
                                <select class="form-control" id="subject" name="subject" required>
                                    <option value="">Konu Seçin</option>
                                    <option value="YouTube Canlı Yayınları">YouTube Canlı Yayınları</option>
                                    <option value="Telegram Kanal Yönetimi">Telegram Kanal Yönetimi</option>
                                    <option value="Sosyal Medya Yönetimi">Sosyal Medya Yönetimi</option>
                                    <option value="Meta Reklamları">Meta Reklamları</option>
                                    <option value="SMS ve Mail Kampanyaları">SMS ve Mail Kampanyaları</option>
                                    <option value="Panel Güçlendirme">Panel Güçlendirme</option>
                                    <option value="Genel Bilgi">Genel Bilgi</option>
                                    <option value="Fiyat Teklifi">Fiyat Teklifi</option>
                                    <option value="Diğer">Diğer</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Mesajınız *</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required 
                                      placeholder="Lütfen projeniz hakkında detaylı bilgi verin..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="privacy" required>
                                <label class="form-check-label" for="privacy">
                                    <a href="<?php echo site_url('pages/privacy.php'); ?>" target="_blank">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum. *
                                </label>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Mesaj Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Media & Quick Contact -->
<section class="quick-contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="quick-contact-card">
                    <h3><i class="fas fa-rocket"></i> Hızlı İletişim</h3>
                    <p>Acil projeler için hızlı iletişim kanallarımızı kullanabilirsiniz:</p>
                    
                    <div class="quick-contact-buttons">
                        <a href="tel:<?php echo str_replace([' ', '(', ')', '-'], '', get_setting('contact_phone', '')); ?>" 
                           class="btn btn-primary me-2 mb-2">
                            <i class="fas fa-phone"></i> Hemen Ara
                        </a>
                        
                        <a href="https://wa.me/<?php echo str_replace(['+', ' ', '(', ')', '-'], '', get_setting('contact_phone', '')); ?>?text=Acil proje için iletişime geçiyorum." 
                           target="_blank" class="btn btn-success me-2 mb-2">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        
                        <a href="<?php echo get_setting('social_telegram', '#'); ?>" 
                           target="_blank" class="btn btn-info mb-2">
                            <i class="fab fa-telegram"></i> Telegram
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="social-media-card">
                    <h3><i class="fas fa-share-alt"></i> Sosyal Medya</h3>
                    <p>Güncel çalışmalarımı ve başarılarımı takip edin:</p>
                    
                    <div class="social-media-links">
                        <?php $social_instagram = get_setting('social_instagram', ''); ?>
                        <?php if (!empty($social_instagram)): ?>
                            <a href="<?php echo $social_instagram; ?>" target="_blank" class="social-btn instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </a>
                        <?php endif; ?>
                        
                        <?php $social_youtube = get_setting('social_youtube', ''); ?>
                        <?php if (!empty($social_youtube)): ?>
                            <a href="<?php echo $social_youtube; ?>" target="_blank" class="social-btn youtube">
                                <i class="fab fa-youtube"></i> YouTube
                            </a>
                        <?php endif; ?>
                        
                        <?php $social_telegram = get_setting('social_telegram', ''); ?>
                        <?php if (!empty($social_telegram)): ?>
                            <a href="<?php echo $social_telegram; ?>" target="_blank" class="social-btn telegram">
                                <i class="fab fa-telegram"></i> Telegram
                            </a>
                        <?php endif; ?>
                        
                        <?php $social_twitter = get_setting('social_twitter', ''); ?>
                        <?php if (!empty($social_twitter)): ?>
                            <a href="<?php echo $social_twitter; ?>" target="_blank" class="social-btn twitter">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Working Hours & FAQ -->
<section class="additional-info-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="working-hours-card bg-glass p-4 rounded">
                    <h3><i class="fas fa-clock"></i> Çalışma Saatleri</h3>
                    <div class="hours-list">
                        <div class="hour-item">
                            <span class="day">Pazartesi - Cuma</span>
                            <span class="time">09:00 - 18:00</span>
                        </div>
                        <div class="hour-item">
                            <span class="day">Cumartesi</span>
                            <span class="time">10:00 - 16:00</span>
                        </div>
                        <div class="hour-item">
                            <span class="day">Pazar</span>
                            <span class="time">Kapalı</span>
                        </div>
                    </div>
                    
                    <div class="emergency-contact mt-3">
                        <p><strong>Acil Durumlar:</strong> WhatsApp üzerinden 7/24 ulaşabilirsiniz.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="faq-card bg-glass p-4 rounded">
                    <h3><i class="fas fa-question-circle"></i> Sık Sorulan Sorular</h3>
                    
                    <div class="faq-item">
                        <h5>Projem için fiyat teklifi nasıl alabilirim?</h5>
                        <p>İletişim formunu doldurarak veya WhatsApp'tan direkt ulaşarak proje detaylarınızı paylaşın.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h5>Canlı yayın hizmeti nasıl çalışır?</h5>
                        <p>Haftada 3 adet profesyonel canlı yayın gerçekleştiriyorum. İçerik planlaması birlikte yapılır.</p>
                    </div>
                    
                    <div class="faq-item">
                        <h5>Telegram kanal yönetimi neler içerir?</h5>
                        <p>Günlük içerik paylaşımı, üye artırma stratejileri ve engagement yükseltme çalışmaları.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Optional) -->
<section class="map-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="map-wrapper text-center">
                    <h3>Lokasyon</h3>
                    <p><?php echo get_setting('contact_address', 'İstanbul, Türkiye'); ?></p>
                    
                    <!-- Placeholder for map - you can integrate Google Maps or another map service -->
                    <div class="map-placeholder bg-glass p-5 rounded">
                        <i class="fas fa-map-marker-alt fa-3x mb-3 text-primary"></i>
                        <h4>İstanbul, Türkiye</h4>
                        <p>Detaylı adres bilgisi için lütfen iletişime geçin.</p>
                        <a href="https://www.google.com/maps/search/İstanbul,+Türkiye" target="_blank" class="btn btn-primary">
                            <i class="fas fa-map"></i> Haritada Gör
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Additional CSS for contact page
$additional_head = '
<style>
.contact-info-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 30px;
    transition: all 0.3s ease;
    height: 100%;
}

.contact-info-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(111, 66, 193, 0.4);
}

.info-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #6f42c1 0%, #e91e63 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.8rem;
    color: white;
}

.contact-form-wrapper {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 10px;
    padding: 12px 15px;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.2);
    border-color: #e91e63;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.form-label {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    margin-bottom: 8px;
}

.quick-contact-card,
.social-media-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 30px;
    height: 100%;
}

.social-btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 5px;
    border-radius: 25px;
    text-decoration: none;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.social-btn.instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); }
.social-btn.youtube { background: #ff0000; }
.social-btn.telegram { background: #0088cc; }
.social-btn.twitter { background: #1da1f2; }

.social-btn:hover {
    transform: translateY(-2px);
    color: white;
}

.hours-list {
    margin: 20px 0;
}

.hour-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.faq-item {
    margin-bottom: 20px;
}

.faq-item h5 {
    color: #e91e63;
    margin-bottom: 8px;
}

.map-placeholder {
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

@media (max-width: 768px) {
    .contact-info-card {
        margin-bottom: 20px;
    }
    
    .quick-contact-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>
';

// Include footer
include SITE_ROOT . '/includes/footer.php';
?>
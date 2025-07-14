<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once '../includes/security.php';

$db = new Database();

// Get page content
$page = $db->find('pages', ['slug' => 'services']);
if (!$page) {
    $page = [
        'title' => 'Hizmetler - BERAT K',
        'content' => 'Profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri...',
        'meta_description' => 'BERAT K tarafından sunulan profesyonel casino yayıncılığı, Telegram yönetimi, Meta Ads ve dijital pazarlama hizmetleri.',
        'meta_keywords' => 'casino yayıncılığı, telegram yönetimi, meta ads, dijital pazarlama, youtube streaming'
    ];
}

// Get all active services
$services = $db->findAll('services', ['status' => 'active'], 'order_position ASC');

$pageTitle = $page['title'];
$metaDescription = $page['meta_description'];
$metaKeywords = $page['meta_keywords'];

include '../includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section services-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">
                        <span class="text-gradient">Profesyonel Hizmetler</span><br>
                        Casino & Dijital Pazarlama
                    </h1>
                    <p class="hero-description">
                        Casino sektöründe 5+ yıllık deneyimimle, yayıncılıktan dijital pazarlamaya 
                        kadar kapsamlı hizmetler sunuyorum. Başarılı projelerin arkasındaki güç.
                    </p>
                    <div class="hero-badges">
                        <span class="badge-item">5+ Yıl Deneyim</span>
                        <span class="badge-item">100+ Başarılı Proje</span>
                        <span class="badge-item">%300+ ROI</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <?php foreach ($services as $index => $service): ?>
                <div class="col-lg-6 mb-5">
                    <div class="service-detail-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="service-header">
                            <div class="service-icon-large">
                                <i class="<?= htmlspecialchars($service['icon']) ?>"></i>
                            </div>
                            <div class="service-title-area">
                                <h3><?= htmlspecialchars($service['title']) ?></h3>
                                <p class="service-subtitle"><?= htmlspecialchars($service['description']) ?></p>
                            </div>
                        </div>
                        
                        <div class="service-content">
                            <div class="service-description">
                                <?= nl2br(htmlspecialchars($service['content'])) ?>
                            </div>
                            
                            <div class="service-features-list">
                                <h5>Özellikler:</h5>
                                <ul>
                                    <?php 
                                    $features = explode("\n", $service['features']);
                                    foreach ($features as $feature): 
                                        if (trim($feature)):
                                    ?>
                                    <li><i class="fas fa-check"></i> <?= htmlspecialchars(trim($feature)) ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </div>
                            
                            <div class="service-pricing">
                                <div class="price-tag">
                                    <?php if ($service['price_type'] === 'fixed'): ?>
                                        <span class="price"><?= number_format($service['price']) ?> ₺</span>
                                        <span class="price-period">Sabit Fiyat</span>
                                    <?php elseif ($service['price_type'] === 'monthly'): ?>
                                        <span class="price"><?= number_format($service['price']) ?> ₺</span>
                                        <span class="price-period">/Aylık</span>
                                    <?php else: ?>
                                        <span class="price">Özel Fiyat</span>
                                        <span class="price-period">Görüşmeye Göre</span>
                                    <?php endif; ?>
                                </div>
                                <a href="../pages/contact.php?service=<?= urlencode($service['title']) ?>" class="btn btn-primary">
                                    Teklif Al
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Service Process -->
    <section class="section-padding bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Çalışma Sürecim</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="step-number">01</div>
                        <div class="step-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4>İlk Görüşme</h4>
                        <p>İhtiyaçlarınızı anlıyor, hedeflerinizi belirliyoruz. Detaylı analiz yaparak en uygun stratejiyi planlıyoruz.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="step-number">02</div>
                        <div class="step-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Strateji Geliştirme</h4>
                        <p>Sektör analizi ve rakip araştırması yaparak size özel dijital pazarlama stratejisi geliştiriyorum.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="step-number">03</div>
                        <div class="step-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4>Uygulama</h4>
                        <p>Belirlenen stratejiyi titizlikle uyguluyorum. Her adımda şeffaf raporlama ile süreci takip edebilirsiniz.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="process-step">
                        <div class="step-number">04</div>
                        <div class="step-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h4>Sonuçlar</h4>
                        <p>Elde edilen sonuçları analiz ediyor, optimizasyonlar yaparak sürekli gelişim sağlıyorum.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Başarı Hikayeleri</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="success-card">
                        <div class="success-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="success-stats">
                            <h3>%400</h3>
                            <p>Trafik Artışı</p>
                        </div>
                        <div class="success-description">
                            <h5>Casino Sitesi Optimizasyonu</h5>
                            <p>6 aylık çalışma sonucunda müşterimin casino sitesinin organik trafiği %400 arttı.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="success-card">
                        <div class="success-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="success-stats">
                            <h3>15K+</h3>
                            <p>Yeni Üye</p>
                        </div>
                        <div class="success-description">
                            <h5>Telegram Kanalı Büyütme</h5>
                            <p>3 aylık kampanya ile Telegram kanalına 15.000'den fazla aktif üye kazandırdım.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="success-card">
                        <div class="success-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="success-stats">
                            <h3>%350</h3>
                            <p>ROI Artışı</p>
                        </div>
                        <div class="success-description">
                            <h5>Meta Ads Kampanyası</h5>
                            <p>Facebook ve Instagram reklamları ile müşteri ROI'sini %350 artırdım.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Packages -->
    <section class="section-padding bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Hizmet Paketleri</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="package-card">
                        <div class="package-header">
                            <h4>Başlangıç Paketi</h4>
                            <div class="package-price">
                                <span class="price">2.500 ₺</span>
                                <span class="period">/Aylık</span>
                            </div>
                        </div>
                        <div class="package-features">
                            <ul>
                                <li><i class="fas fa-check"></i> YouTube yayın danışmanlığı</li>
                                <li><i class="fas fa-check"></i> Temel sosyal medya yönetimi</li>
                                <li><i class="fas fa-check"></i> Haftalık raporlama</li>
                                <li><i class="fas fa-check"></i> E-mail desteği</li>
                                <li><i class="fas fa-times"></i> Meta Ads yönetimi</li>
                                <li><i class="fas fa-times"></i> Telegram optimizasyonu</li>
                            </ul>
                        </div>
                        <div class="package-footer">
                            <a href="../pages/contact.php?package=starter" class="btn btn-outline-primary w-100">
                                Paketi Seç
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="package-card featured">
                        <div class="package-badge">En Popüler</div>
                        <div class="package-header">
                            <h4>Profesyonel Paket</h4>
                            <div class="package-price">
                                <span class="price">5.000 ₺</span>
                                <span class="period">/Aylık</span>
                            </div>
                        </div>
                        <div class="package-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Tüm yayın platformları yönetimi</li>
                                <li><i class="fas fa-check"></i> Profesyonel sosyal medya</li>
                                <li><i class="fas fa-check"></i> Meta Ads kampanya yönetimi</li>
                                <li><i class="fas fa-check"></i> Telegram kanalı optimizasyonu</li>
                                <li><i class="fas fa-check"></i> Günlük raporlama</li>
                                <li><i class="fas fa-check"></i> 7/24 WhatsApp desteği</li>
                            </ul>
                        </div>
                        <div class="package-footer">
                            <a href="../pages/contact.php?package=professional" class="btn btn-primary w-100">
                                Paketi Seç
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="package-card">
                        <div class="package-header">
                            <h4>Kurumsal Paket</h4>
                            <div class="package-price">
                                <span class="price">10.000 ₺</span>
                                <span class="period">/Aylık</span>
                            </div>
                        </div>
                        <div class="package-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Tüm profesyonel hizmetler</li>
                                <li><i class="fas fa-check"></i> Özel strateji geliştirme</li>
                                <li><i class="fas fa-check"></i> Panel optimizasyonu</li>
                                <li><i class="fas fa-check"></i> SMS/Email kampanyaları</li>
                                <li><i class="fas fa-check"></i> Özel danışmanlık</li>
                                <li><i class="fas fa-check"></i> Öncelikli destek</li>
                            </ul>
                        </div>
                        <div class="package-footer">
                            <a href="../pages/contact.php?package=enterprise" class="btn btn-outline-primary w-100">
                                Paketi Seç
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Sıkça Sorulan Sorular</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Hizmetleriniz hangi casino sitelerine uyumlu?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Tüm popüler casino platformları ile çalışabiliyorum. Betboo, Bets10, Sekabet, Youwin gibi büyük sitelerden küçük ölçekli casino sitelerine kadar geniş yelpazede deneyimim bulunuyor.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Sonuçları ne kadar sürede görmeye başlarım?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    İlk sonuçlar genellikle 2-4 hafta içinde görülmeye başlar. Ancak kalıcı ve büyük değişiklikler 2-3 aylık süreçte ortaya çıkar. Her hizmet türüne göre süreler değişebilir.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Ödeme koşullarınız nasıl?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Aylık paketlerde ödeme peşin olarak ayın başında alınır. Proje bazlı işlerde %50 peşin, %50 teslim şeklinde çalışıyorum. Tüm ödemeler fatura ile yapılır.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Raporlama nasıl yapılıyor?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Haftalık ve aylık detaylı raporlar sunuyorum. Raporlarda trafik analizi, dönüşüm oranları, ROI hesaplamaları ve öneriler yer alır. İstediğiniz zaman güncel verilere erişebilirsiniz.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Sözleşme süresi ne kadar?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Minimum 3 aylık çalışma süresi öneriyorum çünkü dijital pazarlamada sonuçlar zaman içinde ortaya çıkar. Ancak özel durumlar için esnek çözümler sunabilirim.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="section-padding bg-gradient-primary">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="text-white mb-4">Hangi Hizmete İhtiyacınız Var?</h2>
                    <p class="text-white-75 mb-4">
                        Size en uygun hizmeti belirlemek için ücretsiz konsültasyon yapabiliriz. 
                        Hedeflerinizi konuşup en iyi stratejiyi birlikte belirleyelim.
                    </p>
                    <div class="cta-buttons">
                        <a href="../pages/contact.php" class="btn btn-white btn-lg me-3">
                            <i class="fas fa-calendar me-2"></i>
                            Ücretsiz Konsültasyon
                        </a>
                        <a href="https://wa.me/905555555555" class="btn btn-outline-white btn-lg" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>
                            WhatsApp'tan Yaz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// AOS Animation Library initialization
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation for success stories
    const observerOptions = {
        threshold: 0.5
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('[data-count]');
                counters.forEach(counter => {
                    const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                    animateCounter(counter, target);
                });
            }
        });
    }, observerOptions);
    
    const successSection = document.querySelector('.success-card');
    if (successSection) {
        observer.observe(successSection.parentElement);
    }
});

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        if (element.textContent.includes('%')) {
            element.textContent = Math.floor(current) + '%';
        } else if (element.textContent.includes('K')) {
            element.textContent = Math.floor(current / 1000) + 'K+';
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 20);
}

// Service card hover effects
document.querySelectorAll('.service-detail-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
</script>

<?php include '../includes/footer.php'; ?>
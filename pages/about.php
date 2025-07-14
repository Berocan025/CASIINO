<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once '../includes/security.php';

$db = new Database();

// Get page content from database
$page = $db->find('pages', ['slug' => 'about']);
if (!$page) {
    $page = [
        'title' => 'Hakkımda - BERAT K',
        'content' => 'BERAT K hakkında bilgi...',
        'meta_description' => 'Casino yayıncısı BERAT K hakkında detaylı bilgi. Deneyim, başarılar ve profesyonel hizmetler.',
        'meta_keywords' => 'berat k, casino yayıncısı, twitch, youtube, casino streamer'
    ];
}

// Get services for showcase
$services = $db->findAll('services', ['status' => 'active'], 'order_position ASC');

// Get statistics
$stats = $db->findAll('statistics', ['status' => 'active'], 'order_position ASC');

$pageTitle = $page['title'];
$metaDescription = $page['meta_description'];
$metaKeywords = $page['meta_keywords'];

include '../includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section about-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            <span class="text-gradient">BERAT K</span><br>
                            Casino Yayıncısı & Dijital Pazarlama Uzmanı
                        </h1>
                        <p class="hero-description">
                            5+ yıllık deneyimimle casino dünyasında profesyonel yayıncılık ve dijital pazarlama hizmetleri sunuyorum. Türkiye'nin en güvenilir casino yayıncılarından biri olarak sizlere en iyi hizmeti vermeyi hedefliyorum.
                        </p>
                        <div class="hero-stats">
                            <?php foreach ($stats as $stat): ?>
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?= $stat['number'] ?>">0</div>
                                <div class="stat-label"><?= htmlspecialchars($stat['label']) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <div class="profile-card">
                            <div class="profile-image">
                                <img src="../assets/img/berat-k-profile.jpg" alt="BERAT K - Casino Yayıncısı" class="img-fluid">
                                <div class="profile-overlay"></div>
                            </div>
                            <div class="profile-info">
                                <h3>BERAT K</h3>
                                <p>Profesyonel Casino Yayıncısı</p>
                                <div class="social-links">
                                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-twitch"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-telegram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="content-card">
                        <h2 class="section-title">Benim Hikayem</h2>
                        <div class="content-text">
                            <?= nl2br(htmlspecialchars($page['content'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Timeline -->
    <section class="section-padding bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Deneyim & Başarılar</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-date">2019</div>
                            <div class="timeline-content">
                                <h4>Casino Yayıncılığına Başlangıç</h4>
                                <p>YouTube ve Twitch platformlarında casino oyunları yayıncılığına başladım. İlk ayda 1000 takipçiye ulaştım.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-date">2020</div>
                            <div class="timeline-content">
                                <h4>Profesyonel Yayıncı</h4>
                                <p>Büyük casino markalarıyla sponsorluk anlaşmaları imzaladım. Aylık 100K+ izlenme sayısına ulaştım.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-date">2021</div>
                            <div class="timeline-content">
                                <h4>Dijital Pazarlama Ajansı</h4>
                                <p>Casino sitelerine özel dijital pazarlama hizmetleri vermeye başladım. İlk yıl 50+ başarılı proje tamamladım.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-date">2022</div>
                            <div class="timeline-content">
                                <h4>Telegram Kanalı Yönetimi</h4>
                                <p>Casino bonusları ve stratejiler üzerine Telegram kanalımı açtım. 6 ay içinde 10K+ üye topladım.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-date">2023</div>
                            <div class="timeline-content">
                                <h4>Meta Ads Uzmanlığı</h4>
                                <p>Facebook ve Instagram reklam kampanyalarında uzmanlaştım. Müşterilerime %300+ ROI sağladım.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-date">2024</div>
                            <div class="timeline-content">
                                <h4>Tam Hizmet Ajansı</h4>
                                <p>Casino sektöründe tam hizmet dijital pazarlama ajansı haline geldim. 100+ aktif müşterim bulunuyor.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Uzmanlık Alanlarım</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="skill-card">
                        <div class="skill-header">
                            <i class="fab fa-youtube skill-icon"></i>
                            <h4>YouTube Yayıncılığı</h4>
                        </div>
                        <div class="skill-progress">
                            <div class="progress-bar" data-percent="95"></div>
                            <span class="progress-text">95%</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="skill-card">
                        <div class="skill-header">
                            <i class="fab fa-twitch skill-icon"></i>
                            <h4>Twitch Streaming</h4>
                        </div>
                        <div class="skill-progress">
                            <div class="progress-bar" data-percent="90"></div>
                            <span class="progress-text">90%</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="skill-card">
                        <div class="skill-header">
                            <i class="fab fa-telegram skill-icon"></i>
                            <h4>Telegram Yönetimi</h4>
                        </div>
                        <div class="skill-progress">
                            <div class="progress-bar" data-percent="88"></div>
                            <span class="progress-text">88%</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="skill-card">
                        <div class="skill-header">
                            <i class="fab fa-facebook skill-icon"></i>
                            <h4>Meta Ads</h4>
                        </div>
                        <div class="skill-progress">
                            <div class="progress-bar" data-percent="92"></div>
                            <span class="progress-text">92%</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="skill-card">
                        <div class="skill-header">
                            <i class="fas fa-chart-line skill-icon"></i>
                            <h4>Dijital Pazarlama</h4>
                        </div>
                        <div class="skill-progress">
                            <div class="progress-bar" data-percent="94"></div>
                            <span class="progress-text">94%</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="skill-card">
                        <div class="skill-header">
                            <i class="fas fa-cogs skill-icon"></i>
                            <h4>Panel Optimizasyonu</h4>
                        </div>
                        <div class="skill-progress">
                            <div class="progress-bar" data-percent="89"></div>
                            <span class="progress-text">89%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Preview -->
    <section class="section-padding bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Sunduğum Hizmetler</h2>
                </div>
            </div>
            <div class="row">
                <?php foreach ($services as $index => $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="<?= htmlspecialchars($service['icon']) ?>"></i>
                        </div>
                        <h4><?= htmlspecialchars($service['title']) ?></h4>
                        <p><?= htmlspecialchars($service['description']) ?></p>
                        <div class="service-features">
                            <?php 
                            $features = explode("\n", $service['features']);
                            foreach (array_slice($features, 0, 3) as $feature): 
                            ?>
                            <span class="feature-tag"><?= htmlspecialchars(trim($feature)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <a href="../pages/services.php" class="btn btn-primary btn-lg">
                        Tüm Hizmetleri Görüntüle
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Müşteri Yorumları</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"BERAT K ile çalıştığımızdan beri casino sitemizin trafiği %400 arttı. Profesyonel yaklaşımı ve sonuç odaklı çalışması bizi çok memnun etti."</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h5>Ahmet Y.</h5>
                                <span>Casino Site Sahibi</span>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"YouTube yayınlarında verdiği casino tavsiyeleri sayesinde büyük kazançlar elde ettim. Güvenilir ve dürüst bir yayıncı."</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h5>Mehmet K.</h5>
                                <span>Takipçi</span>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"Meta Ads kampanyalarımızı yönetmeye başladığından beri ROI'miz %300 arttı. Kesinlikle tavsiye ediyorum."</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h5>Fatma S.</h5>
                                <span>Pazarlama Müdürü</span>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
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
                    <h2 class="text-white mb-4">Benimle Çalışmaya Hazır mısınız?</h2>
                    <p class="text-white-75 mb-4">
                        Casino dünyasında başarılı olmak için profesyonel destek alın. 
                        Ücretsiz konsültasyon için hemen iletişime geçin.
                    </p>
                    <div class="cta-buttons">
                        <a href="../pages/contact.php" class="btn btn-white btn-lg me-3">
                            <i class="fas fa-phone me-2"></i>
                            İletişime Geç
                        </a>
                        <a href="https://wa.me/905555555555" class="btn btn-outline-white btn-lg" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Counter animation
function animateCounters() {
    const counters = document.querySelectorAll('[data-count]');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current).toLocaleString();
        }, 16);
    });
}

// Progress bars animation
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const percent = bar.getAttribute('data-percent');
        setTimeout(() => {
            bar.style.width = percent + '%';
        }, 500);
    });
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.3,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            if (entry.target.classList.contains('hero-stats')) {
                animateCounters();
            }
            if (entry.target.classList.contains('skills-section')) {
                animateProgressBars();
            }
            entry.target.classList.add('animate-in');
        }
    });
}, observerOptions);

// Observe elements
document.addEventListener('DOMContentLoaded', function() {
    const heroStats = document.querySelector('.hero-stats');
    const skillsSection = document.querySelector('.skills-section');
    
    if (heroStats) observer.observe(heroStats);
    if (skillsSection) observer.observe(skillsSection);
    
    // Timeline animation
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        observer.observe(item);
        item.style.animationDelay = (index * 0.2) + 's';
    });
});
</script>

<?php include '../includes/footer.php'; ?>
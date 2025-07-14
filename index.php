<?php
/**
 * Casino Portfolio Website - Homepage
 * Geliştirici: BERAT K
 * Main homepage displaying hero section, services, portfolio and statistics
 */

// Include configuration
define('SITE_ROOT', __DIR__);
require_once 'includes/config.php';

// Set page variables
$page_title = get_setting('site_title', 'Casino Yayıncısı - BERAT K');
$page_description = get_setting('site_description', 'Profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri');
$page_keywords = get_setting('site_keywords', 'casino, yayıncı, pazarlama, sosyal medya, telegram, youtube');
$body_class = 'homepage';

// Get homepage data
try {
    // Get slider items
    $slider_items = $db->fetchAll("SELECT * FROM slider WHERE status = 'active' ORDER BY sort_order ASC");
    
    // Get featured services
    $featured_services = $db->fetchAll("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");
    
    // Get featured portfolio items
    $featured_portfolio = $db->fetchAll("SELECT * FROM portfolio WHERE status = 'active' AND featured = 1 ORDER BY sort_order ASC LIMIT 8");
    
    // Get statistics
    $statistics = $db->fetchAll("SELECT * FROM statistics WHERE status = 'active' ORDER BY sort_order ASC");
    
    // Get latest gallery items
    $latest_gallery = $db->fetchAll("SELECT * FROM gallery WHERE status = 'active' ORDER BY created_at DESC LIMIT 6");
    
} catch (Exception $e) {
    error_log("Homepage data fetch error: " . $e->getMessage());
    $slider_items = [];
    $featured_services = [];
    $featured_portfolio = [];
    $statistics = [];
    $latest_gallery = [];
}

// Include header
include 'includes/header.php';
?>

<!-- Hero Section with Slider -->
<section class="hero-section">
    <?php if (!empty($slider_items)): ?>
        <div id="heroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                <?php foreach ($slider_items as $index => $slide): ?>
                    <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?php echo $index; ?>" 
                            <?php echo $index === 0 ? 'class="active"' : ''; ?>></button>
                <?php endforeach; ?>
            </div>
            
            <div class="carousel-inner">
                <?php foreach ($slider_items as $index => $slide): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="hero-bg" style="background-image: url('<?php echo upload_url($slide['image']); ?>');">
                            <div class="hero-overlay"></div>
                        </div>
                        <div class="container">
                            <div class="hero-content">
                                <h1 class="hero-title"><?php echo htmlspecialchars($slide['title']); ?></h1>
                                <p class="hero-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                <p class="hero-description"><?php echo htmlspecialchars($slide['description']); ?></p>
                                <?php if (!empty($slide['button_text']) && !empty($slide['button_url'])): ?>
                                    <a href="<?php echo $slide['button_url']; ?>" class="btn btn-primary btn-lg hero-btn">
                                        <?php echo htmlspecialchars($slide['button_text']); ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    <?php else: ?>
        <!-- Default Hero Content -->
        <div class="hero-bg default-hero">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title">Profesyonel Casino Yayıncısı</h1>
                    <p class="hero-subtitle">BERAT K</p>
                    <p class="hero-description">Deneyimli casino yayıncısı ile işinizi bir üst seviyeye taşıyın. YouTube, Telegram ve sosyal medya uzmanlığı.</p>
                    <a href="<?php echo site_url('pages/services.php'); ?>" class="btn btn-primary btn-lg hero-btn">
                        Hizmetleri İncele <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Statistics Section -->
<?php if (!empty($statistics)): ?>
<section class="statistics-section">
    <div class="container">
        <div class="row">
            <?php foreach ($statistics as $stat): ?>
                <div class="col-6 col-md-3 mb-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon">
                            <i class="<?php echo $stat['icon']; ?>"></i>
                        </div>
                        <div class="stat-number" data-count="<?php echo $stat['value']; ?>">0</div>
                        <div class="stat-label"><?php echo htmlspecialchars($stat['title']); ?></div>
                        <?php if (!empty($stat['description'])): ?>
                            <div class="stat-description"><?php echo htmlspecialchars($stat['description']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- About Section -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image">
                    <img src="<?php echo asset_url('img/about-me.jpg'); ?>" alt="BERAT K - Casino Yayıncısı" class="img-fluid rounded">
                    <div class="about-badge">
                        <i class="fas fa-play"></i>
                        <span>5+ Yıl Deneyim</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <span class="section-subtitle">Hakkımda</span>
                    <h2 class="section-title">Profesyonel Casino Yayıncısı</h2>
                    <p class="section-description">
                        5+ yıllık deneyimim ile casino dünyasında profesyonel yayıncılık hizmetleri sunuyorum. 
                        YouTube canlı yayınları, Telegram kanal yönetimi, sosyal medya pazarlaması ve Meta reklamları 
                        konularında uzmanlaşmış durumdayım.
                    </p>
                    <div class="about-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>250K+ Organik Follower Kazandırdım</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>50+ Başarılı Proje Tamamladım</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>1,200+ Saat Canlı Yayın Deneyimi</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>%98 Müşteri Memnuniyet Oranı</span>
                        </div>
                    </div>
                    <div class="about-actions mt-4">
                        <a href="<?php echo site_url('pages/about.php'); ?>" class="btn btn-primary">
                            Daha Fazla Bilgi <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="<?php echo site_url('pages/contact.php'); ?>" class="btn btn-outline-primary ms-3">
                            İletişime Geç <i class="fas fa-phone"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<?php if (!empty($featured_services)): ?>
<section class="services-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <span class="section-subtitle">Hizmetlerim</span>
                <h2 class="section-title">Profesyonel Casino Yayıncılığı Hizmetleri</h2>
                <p class="section-description">
                    Casino dünyasında başarılı olmak için ihtiyacınız olan tüm dijital pazarlama ve yayıncılık hizmetlerini sunuyorum.
                </p>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($featured_services as $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-card">
                        <?php if (!empty($service['image'])): ?>
                            <div class="service-image">
                                <img src="<?php echo upload_url($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="img-fluid">
                            </div>
                        <?php endif; ?>
                        
                        <div class="service-content">
                            <h4 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h4>
                            <p class="service-description"><?php echo htmlspecialchars($service['short_description']); ?></p>
                            
                            <?php if (!empty($service['features'])): ?>
                                <?php $features = json_decode($service['features'], true); ?>
                                <?php if (is_array($features)): ?>
                                    <ul class="service-features">
                                        <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                                            <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <div class="service-footer">
                                <?php if (!empty($service['price'])): ?>
                                    <div class="service-price"><?php echo htmlspecialchars($service['price']); ?></div>
                                <?php endif; ?>
                                <a href="<?php echo site_url('pages/service-detail.php?slug=' . $service['slug']); ?>" class="btn btn-primary">
                                    Detayları Gör <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo site_url('pages/services.php'); ?>" class="btn btn-outline-primary btn-lg">
                Tüm Hizmetleri Gör <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Portfolio Section -->
<?php if (!empty($featured_portfolio)): ?>
<section class="portfolio-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <span class="section-subtitle">Portföyüm</span>
                <h2 class="section-title">Başarılı Projelerim</h2>
                <p class="section-description">
                    Casino yayıncılığı ve dijital pazarlama alanında gerçekleştirdiğim başarılı projelerden örnekler.
                </p>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($featured_portfolio as $project): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="portfolio-card">
                        <div class="portfolio-image">
                            <?php if (!empty($project['image'])): ?>
                                <img src="<?php echo upload_url($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="img-fluid">
                            <?php else: ?>
                                <img src="<?php echo asset_url('img/portfolio-placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="img-fluid">
                            <?php endif; ?>
                            <div class="portfolio-overlay">
                                <div class="portfolio-actions">
                                    <a href="<?php echo site_url('pages/portfolio-detail.php?slug=' . $project['slug']); ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (!empty($project['project_url']) && $project['project_url'] !== '#'): ?>
                                        <a href="<?php echo $project['project_url']; ?>" target="_blank" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="portfolio-content">
                            <div class="portfolio-category"><?php echo htmlspecialchars($project['category']); ?></div>
                            <h5 class="portfolio-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <p class="portfolio-description"><?php echo truncate_text($project['description'], 80); ?></p>
                            <?php if (!empty($project['client_name'])): ?>
                                <div class="portfolio-client">Müşteri: <?php echo htmlspecialchars($project['client_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo site_url('pages/portfolio.php'); ?>" class="btn btn-outline-primary btn-lg">
                Tüm Portföyü Gör <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Section -->
<?php if (!empty($latest_gallery)): ?>
<section class="gallery-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <span class="section-subtitle">Galeri</span>
                <h2 class="section-title">Çalışma Anlarım</h2>
                <p class="section-description">
                    Canlı yayınlar, projeler ve günlük çalışmalarımdan kareler.
                </p>
            </div>
        </div>
        
        <div class="row">
            <?php foreach ($latest_gallery as $media): ?>
                <div class="col-lg-2 col-md-4 col-6 mb-3">
                    <div class="gallery-item">
                        <?php if ($media['type'] === 'video'): ?>
                            <div class="gallery-video">
                                <img src="<?php echo !empty($media['thumbnail']) ? upload_url($media['thumbnail']) : asset_url('img/video-placeholder.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($media['title']); ?>" class="img-fluid">
                                <div class="video-overlay">
                                    <i class="fas fa-play"></i>
                                </div>
                                <a href="<?php echo $media['file_path']; ?>" target="_blank" class="gallery-link"></a>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo upload_url($media['file_path']); ?>" data-lightbox="gallery" 
                               data-title="<?php echo htmlspecialchars($media['title']); ?>" class="gallery-link">
                                <img src="<?php echo upload_url($media['file_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($media['title']); ?>" class="img-fluid">
                                <div class="gallery-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?php echo site_url('pages/gallery.php'); ?>" class="btn btn-outline-primary btn-lg">
                Tüm Galeriyi Gör <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact CTA Section -->
<section class="contact-cta py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="cta-title">Projenizi Konuşalım</h2>
                <p class="cta-description">
                    Casino yayıncılığı ve dijital pazarlama ihtiyaçlarınız için benimle iletişime geçin. 
                    Ücretsiz danışmanlık için hemen arayın!
                </p>
                <div class="cta-actions">
                    <a href="<?php echo site_url('pages/contact.php'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-phone"></i> İletişime Geç
                    </a>
                    <a href="https://wa.me/<?php echo str_replace(['+', ' ', '(', ')', '-'], '', get_setting('contact_phone', '')); ?>" 
                       target="_blank" class="btn btn-success btn-lg ms-3">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Additional JavaScript for homepage
$additional_js = '
<script>
// Counter animation for statistics
function animateCounters() {
    const counters = document.querySelectorAll(".stat-number");
    counters.forEach(counter => {
        const target = counter.getAttribute("data-count");
        const count = parseInt(target.replace(/[^0-9]/g, ""));
        const suffix = target.replace(/[0-9]/g, "");
        let current = 0;
        const increment = count / 100;
        const timer = setInterval(() => {
            current += increment;
            if (current >= count) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current) + suffix;
            }
        }, 20);
    });
}

// Trigger counter animation when section is visible
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            observer.unobserve(entry.target);
        }
    });
});

const statsSection = document.querySelector(".statistics-section");
if (statsSection) {
    observer.observe(statsSection);
}
</script>
';

// Include footer
include 'includes/footer.php';
?>
<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once '../includes/security.php';

$db = new Database();

// Get page content
$page = $db->find('pages', ['slug' => 'portfolio']);
if (!$page) {
    $page = [
        'title' => 'Portföy - BERAT K',
        'content' => 'Başarılı casino projeleri ve dijital pazarlama kampanyaları...',
        'meta_description' => 'BERAT K\'nin başarılı casino projeleri, yayıncılık deneyimleri ve dijital pazarlama kampanyalarını keşfedin.',
        'meta_keywords' => 'casino portföy, başarılı projeler, yayıncılık, dijital pazarlama kampanyaları'
    ];
}

// Get portfolio items
$portfolio = $db->findAll('portfolio', ['status' => 'active'], 'order_position ASC');

// Get portfolio categories for filtering
$categories = $db->query("SELECT DISTINCT category FROM portfolio WHERE status = 'active' ORDER BY category")->fetchAll();

$pageTitle = $page['title'];
$metaDescription = $page['meta_description'];
$metaKeywords = $page['meta_keywords'];

include '../includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section portfolio-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">
                        <span class="text-gradient">Başarı Portföyü</span><br>
                        Gerçekleşen Projeler
                    </h1>
                    <p class="hero-description">
                        5+ yıllık casino yayıncılığı kariyerimde gerçekleştirdiğim başarılı projeler, 
                        dijital pazarlama kampanyaları ve elde ettiğim sonuçlar.
                    </p>
                    <div class="hero-stats-grid">
                        <div class="stat-box">
                            <h3>100+</h3>
                            <p>Başarılı Proje</p>
                        </div>
                        <div class="stat-box">
                            <h3>50M+</h3>
                            <p>Toplam İzlenme</p>
                        </div>
                        <div class="stat-box">
                            <h3>%400</h3>
                            <p>Ortalama ROI</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Filter -->
    <section class="section-padding-sm bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="portfolio-filter text-center">
                        <button class="filter-btn active" data-filter="*">Tümü</button>
                        <?php foreach ($categories as $category): ?>
                        <button class="filter-btn" data-filter=".<?= strtolower(str_replace(' ', '-', $category['category'])) ?>">
                            <?= htmlspecialchars($category['category']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Grid -->
    <section class="section-padding">
        <div class="container">
            <div class="portfolio-grid">
                <?php foreach ($portfolio as $item): ?>
                <div class="portfolio-item <?= strtolower(str_replace(' ', '-', $item['category'])) ?>">
                    <div class="portfolio-card">
                        <div class="portfolio-image">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="img-fluid">
                            <div class="portfolio-overlay">
                                <div class="portfolio-overlay-content">
                                    <h4><?= htmlspecialchars($item['title']) ?></h4>
                                    <p><?= htmlspecialchars($item['description']) ?></p>
                                    <div class="portfolio-buttons">
                                        <button class="btn btn-primary btn-sm portfolio-modal-btn" 
                                                data-title="<?= htmlspecialchars($item['title']) ?>"
                                                data-description="<?= htmlspecialchars($item['description']) ?>"
                                                data-content="<?= htmlspecialchars($item['content']) ?>"
                                                data-image="<?= htmlspecialchars($item['image']) ?>"
                                                data-category="<?= htmlspecialchars($item['category']) ?>"
                                                data-client="<?= htmlspecialchars($item['client']) ?>"
                                                data-date="<?= htmlspecialchars($item['project_date']) ?>"
                                                data-url="<?= htmlspecialchars($item['project_url']) ?>">
                                            <i class="fas fa-eye"></i> Detaylar
                                        </button>
                                        <?php if ($item['project_url']): ?>
                                        <a href="<?= htmlspecialchars($item['project_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-external-link-alt"></i> Ziyaret Et
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="portfolio-info">
                            <div class="portfolio-meta">
                                <span class="category"><?= htmlspecialchars($item['category']) ?></span>
                                <span class="date"><?= date('Y', strtotime($item['project_date'])) ?></span>
                            </div>
                            <h4><?= htmlspecialchars($item['title']) ?></h4>
                            <p><?= htmlspecialchars($item['description']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Achievement Section -->
    <section class="section-padding bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Öne Çıkan Başarılar</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <div class="achievement-content">
                            <h4>YouTube Kanalı Büyütme</h4>
                            <div class="achievement-stats">
                                <div class="stat">
                                    <span class="number">250K+</span>
                                    <span class="label">Abone</span>
                                </div>
                                <div class="stat">
                                    <span class="number">15M+</span>
                                    <span class="label">Toplam İzlenme</span>
                                </div>
                            </div>
                            <p>Sıfırdan başladığım YouTube kanalımı 2 yılda 250K+ aboneye ulaştırdım. Casino içeriklerimle toplam 15 milyondan fazla izlenme elde ettim.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fab fa-twitch"></i>
                        </div>
                        <div class="achievement-content">
                            <h4>Twitch Canlı Yayıncılığı</h4>
                            <div class="achievement-stats">
                                <div class="stat">
                                    <span class="number">50K+</span>
                                    <span class="label">Takipçi</span>
                                </div>
                                <div class="stat">
                                    <span class="number">2500+</span>
                                    <span class="label">Ortalama İzleyici</span>
                                </div>
                            </div>
                            <p>Twitch'te düzenli casino yayınları yaparak 50K+ takipçi topladım. Canlı yayınlarımda ortalama 2500+ izleyici ile etkileşim kuruyorum.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <div class="achievement-content">
                            <h4>Telegram Kanalı Yönetimi</h4>
                            <div class="achievement-stats">
                                <div class="stat">
                                    <span class="number">25K+</span>
                                    <span class="label">Üye</span>
                                </div>
                                <div class="stat">
                                    <span class="number">%85</span>
                                    <span class="label">Etkileşim Oranı</span>
                                </div>
                            </div>
                            <p>Casino bonusları ve stratejiler paylaştığım Telegram kanalımda 25K+ aktif üye bulunuyor. %85 etkileşim oranı ile sektörde öncü konumdayım.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="achievement-content">
                            <h4>Dijital Pazarlama Başarıları</h4>
                            <div class="achievement-stats">
                                <div class="stat">
                                    <span class="number">100+</span>
                                    <span class="label">Proje</span>
                                </div>
                                <div class="stat">
                                    <span class="number">%400</span>
                                    <span class="label">Ortalama ROI</span>
                                </div>
                            </div>
                            <p>Casino sektöründe 100'den fazla dijital pazarlama projesi yönettim. Müşterilerime ortalama %400 ROI sağladım.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Client Testimonials -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Müşteri Geri Bildirimleri</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card-detailed">
                        <div class="testimonial-header">
                            <div class="client-avatar">
                                <img src="../assets/img/client-1.jpg" alt="Müşteri 1" class="img-fluid">
                            </div>
                            <div class="client-info">
                                <h5>Ahmet Yılmaz</h5>
                                <span>Casino Site Sahibi</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-content">
                            <p>"BERAT K ile çalışmaya başladıktan sonra sitemizin trafiği inanılmaz arttı. YouTube yayınlarında sitemi tanıtması sonucu üye sayımız 3 kata çıktı. Kesinlikle tavsiye ediyorum."</p>
                        </div>
                        <div class="testimonial-project">
                            <strong>Proje:</strong> Casino Sitesi Tanıtımı<br>
                            <strong>Sonuç:</strong> %300 trafik artışı
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card-detailed">
                        <div class="testimonial-header">
                            <div class="client-avatar">
                                <img src="../assets/img/client-2.jpg" alt="Müşteri 2" class="img-fluid">
                            </div>
                            <div class="client-info">
                                <h5>Fatma Kaya</h5>
                                <span>Pazarlama Müdürü</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-content">
                            <p>"Meta Ads kampanyalarımızı yönetmeye başladıktan sonra ROI'miz çok arttı. Profesyonel yaklaşımı ve detaylı raporlaması sayesinde her adımı takip edebildik."</p>
                        </div>
                        <div class="testimonial-project">
                            <strong>Proje:</strong> Facebook & Instagram Reklamları<br>
                            <strong>Sonuç:</strong> %350 ROI artışı
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="testimonial-card-detailed">
                        <div class="testimonial-header">
                            <div class="client-avatar">
                                <img src="../assets/img/client-3.jpg" alt="Müşteri 3" class="img-fluid">
                            </div>
                            <div class="client-info">
                                <h5>Mehmet Demir</h5>
                                <span>Casino Operasyon Müdürü</span>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-content">
                            <p>"Telegram kanalımızı büyütmek için BERAT K ile çalıştık. 6 ayda 5K'dan 20K'ya çıkardığımız üye sayısı ile hedeflerimizi aştık. Çok başarılı bir iş birliği oldu."</p>
                        </div>
                        <div class="testimonial-project">
                            <strong>Proje:</strong> Telegram Kanalı Büyütme<br>
                            <strong>Sonuç:</strong> %400 üye artışı
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
                    <h2 class="text-white mb-4">Siz de Başarı Hikayemizin Parçası Olun</h2>
                    <p class="text-white-75 mb-4">
                        Casino sektöründeki deneyimimle projenizi bir sonraki seviyeye taşıyalım. 
                        Başarılı projelerimize bir yenisini daha ekleyelim.
                    </p>
                    <div class="cta-buttons">
                        <a href="../pages/contact.php" class="btn btn-white btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>
                            Projeye Başla
                        </a>
                        <a href="../pages/services.php" class="btn btn-outline-white btn-lg">
                            <i class="fas fa-list me-2"></i>
                            Hizmetleri İncele
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Portfolio Modal -->
<div class="modal fade" id="portfolioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="portfolioModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="portfolioModalImage" src="" alt="" class="img-fluid rounded">
                    </div>
                    <div class="col-md-6">
                        <div class="portfolio-modal-info">
                            <div class="project-meta">
                                <span class="badge bg-primary" id="portfolioModalCategory"></span>
                                <span class="text-muted ms-2" id="portfolioModalDate"></span>
                            </div>
                            <h6 class="mt-3">Müşteri:</h6>
                            <p id="portfolioModalClient"></p>
                            <h6>Açıklama:</h6>
                            <p id="portfolioModalDescription"></p>
                            <h6>Proje Detayları:</h6>
                            <div id="portfolioModalContent"></div>
                            <div class="mt-3">
                                <a id="portfolioModalLink" href="#" target="_blank" class="btn btn-primary" style="display: none;">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Projeyi Ziyaret Et
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Portfolio filtering
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            portfolioItems.forEach(item => {
                if (filter === '*' || item.classList.contains(filter.substring(1))) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.5s ease';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Portfolio modal
    const modalBtns = document.querySelectorAll('.portfolio-modal-btn');
    const modal = new bootstrap.Modal(document.getElementById('portfolioModal'));
    
    modalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const description = this.getAttribute('data-description');
            const content = this.getAttribute('data-content');
            const image = this.getAttribute('data-image');
            const category = this.getAttribute('data-category');
            const client = this.getAttribute('data-client');
            const date = this.getAttribute('data-date');
            const url = this.getAttribute('data-url');
            
            document.getElementById('portfolioModalTitle').textContent = title;
            document.getElementById('portfolioModalDescription').textContent = description;
            document.getElementById('portfolioModalContent').innerHTML = content.replace(/\n/g, '<br>');
            document.getElementById('portfolioModalImage').src = image;
            document.getElementById('portfolioModalImage').alt = title;
            document.getElementById('portfolioModalCategory').textContent = category;
            document.getElementById('portfolioModalClient').textContent = client;
            document.getElementById('portfolioModalDate').textContent = new Date(date).getFullYear();
            
            const linkBtn = document.getElementById('portfolioModalLink');
            if (url) {
                linkBtn.href = url;
                linkBtn.style.display = 'inline-block';
            } else {
                linkBtn.style.display = 'none';
            }
            
            modal.show();
        });
    });
    
    // Counter animation for achievement stats
    const observerOptions = {
        threshold: 0.5
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numbers = entry.target.querySelectorAll('.number');
                numbers.forEach(number => {
                    animateNumber(number);
                });
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.achievement-stats').forEach(stats => {
        observer.observe(stats);
    });
});

function animateNumber(element) {
    const text = element.textContent;
    const target = parseInt(text.replace(/[^\d]/g, ''));
    let current = 0;
    const increment = target / 100;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        if (text.includes('K')) {
            element.textContent = Math.floor(current / 1000) + 'K+';
        } else if (text.includes('M')) {
            element.textContent = Math.floor(current / 1000000) + 'M+';
        } else if (text.includes('%')) {
            element.textContent = Math.floor(current) + '%';
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 20);
}

// Portfolio card hover effects
document.querySelectorAll('.portfolio-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.querySelector('.portfolio-overlay').style.opacity = '1';
    });
    
    card.addEventListener('mouseleave', function() {
        this.querySelector('.portfolio-overlay').style.opacity = '0';
    });
});
</script>

<?php include '../includes/footer.php'; ?>
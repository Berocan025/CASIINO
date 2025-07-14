<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once '../includes/security.php';

$db = new Database();

// Get page content
$page = $db->find('pages', ['slug' => 'gallery']);
if (!$page) {
    $page = [
        'title' => 'Galeri - BERAT K',
        'content' => 'Casino yayınları, etkinlikler ve özel anlardan fotoğraflar...',
        'meta_description' => 'BERAT K\'nin casino yayınları, etkinlikleri ve özel anlarından fotoğraf ve video galerisi.',
        'meta_keywords' => 'casino galeri, yayın fotoğrafları, casino etkinlikleri, video galeri'
    ];
}

// Get gallery items
$galleryItems = $db->findAll('gallery', ['status' => 'active'], 'order_position ASC');

// Get gallery categories
$categories = $db->query("SELECT DISTINCT category FROM gallery WHERE status = 'active' ORDER BY category")->fetchAll();

$pageTitle = $page['title'];
$metaDescription = $page['meta_description'];
$metaKeywords = $page['meta_keywords'];

include '../includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section gallery-hero">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">
                        <span class="text-gradient">Galeri</span><br>
                        Özel Anlar & Yayınlar
                    </h1>
                    <p class="hero-description">
                        Casino yayıncılığı kariyerimden öne çıkan anlar, canlı yayın kareleri, 
                        etkinlikler ve başarılı projelerden görüntüler.
                    </p>
                    <div class="hero-gallery-stats">
                        <div class="gallery-stat">
                            <i class="fas fa-images"></i>
                            <span>500+ Fotoğraf</span>
                        </div>
                        <div class="gallery-stat">
                            <i class="fas fa-video"></i>
                            <span>100+ Video</span>
                        </div>
                        <div class="gallery-stat">
                            <i class="fas fa-calendar"></i>
                            <span>5+ Yıl Arşiv</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Filter -->
    <section class="section-padding-sm bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="gallery-filter text-center">
                        <button class="filter-btn active" data-filter="*">Tümü</button>
                        <button class="filter-btn" data-filter=".photos">Fotoğraflar</button>
                        <button class="filter-btn" data-filter=".videos">Videolar</button>
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

    <!-- Gallery Grid -->
    <section class="section-padding">
        <div class="container">
            <div class="gallery-grid">
                <?php foreach ($galleryItems as $item): ?>
                <div class="gallery-item <?= $item['type'] === 'video' ? 'videos' : 'photos' ?> <?= strtolower(str_replace(' ', '-', $item['category'])) ?>" 
                     data-title="<?= htmlspecialchars($item['title']) ?>"
                     data-description="<?= htmlspecialchars($item['description']) ?>"
                     data-date="<?= htmlspecialchars($item['created_at']) ?>">
                    
                    <?php if ($item['type'] === 'video'): ?>
                    <!-- Video Item -->
                    <div class="gallery-video-card">
                        <div class="video-thumbnail">
                            <img src="<?= htmlspecialchars($item['thumbnail']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="img-fluid">
                            <div class="video-overlay">
                                <div class="play-button" data-video-url="<?= htmlspecialchars($item['file_path']) ?>">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="video-duration">
                                    <?= htmlspecialchars($item['duration'] ?? '0:00') ?>
                                </div>
                            </div>
                        </div>
                        <div class="video-info">
                            <h5><?= htmlspecialchars($item['title']) ?></h5>
                            <p><?= htmlspecialchars($item['description']) ?></p>
                            <div class="video-meta">
                                <span class="category"><?= htmlspecialchars($item['category']) ?></span>
                                <span class="date"><?= date('d.m.Y', strtotime($item['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php else: ?>
                    <!-- Photo Item -->
                    <div class="gallery-photo-card">
                        <div class="photo-image">
                            <img src="<?= htmlspecialchars($item['file_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="img-fluid">
                            <div class="photo-overlay">
                                <div class="photo-overlay-content">
                                    <h5><?= htmlspecialchars($item['title']) ?></h5>
                                    <p><?= htmlspecialchars($item['description']) ?></p>
                                    <button class="btn btn-light btn-sm photo-zoom-btn">
                                        <i class="fas fa-search-plus"></i> Büyüt
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="photo-info">
                            <div class="photo-meta">
                                <span class="category"><?= htmlspecialchars($item['category']) ?></span>
                                <span class="date"><?= date('d.m.Y', strtotime($item['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Load More Button -->
            <div class="row">
                <div class="col-12 text-center mt-5">
                    <button class="btn btn-primary btn-lg" id="loadMoreBtn" style="display: none;">
                        <i class="fas fa-plus me-2"></i>
                        Daha Fazla Yükle
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Streams Section -->
    <section class="section-padding bg-dark-secondary">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Öne Çıkan Yayınlar</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="featured-stream-card">
                        <div class="stream-thumbnail">
                            <img src="../assets/img/stream-1.jpg" alt="En Büyük Kazanç" class="img-fluid">
                            <div class="stream-badge">
                                <i class="fas fa-trophy"></i>
                                Rekor Kazanç
                            </div>
                            <div class="stream-play">
                                <i class="fab fa-youtube"></i>
                            </div>
                        </div>
                        <div class="stream-info">
                            <h4>En Büyük Kazanç Anı</h4>
                            <p>Sweet Bonanza'da yakaladığım x1000 multiplikör ile elde ettiğim 50.000₺ kazanç anı. Tarihi bir yayın!</p>
                            <div class="stream-stats">
                                <span><i class="fas fa-eye"></i> 2.5M İzlenme</span>
                                <span><i class="fas fa-heart"></i> 45K Beğeni</span>
                            </div>
                            <a href="https://youtube.com/watch?v=example1" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fab fa-youtube me-2"></i>
                                İzle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="featured-stream-card">
                        <div class="stream-thumbnail">
                            <img src="../assets/img/stream-2.jpg" alt="Canlı Turnuva" class="img-fluid">
                            <div class="stream-badge">
                                <i class="fas fa-medal"></i>
                                Turnuva
                            </div>
                            <div class="stream-play">
                                <i class="fab fa-twitch"></i>
                            </div>
                        </div>
                        <div class="stream-info">
                            <h4>Canlı Turnuva Finali</h4>
                            <p>Pragma Play turnuvasının final maçında birinci olduğum heyecanlı anlar. 25.000₺ ödül!</p>
                            <div class="stream-stats">
                                <span><i class="fas fa-eye"></i> 1.8M İzlenme</span>
                                <span><i class="fas fa-heart"></i> 38K Beğeni</span>
                            </div>
                            <a href="https://twitch.tv/videos/example2" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fab fa-twitch me-2"></i>
                                İzle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="featured-stream-card">
                        <div class="stream-thumbnail">
                            <img src="../assets/img/stream-3.jpg" alt="Özel Etkinlik" class="img-fluid">
                            <div class="stream-badge">
                                <i class="fas fa-star"></i>
                                Özel Etkinlik
                            </div>
                            <div class="stream-play">
                                <i class="fab fa-youtube"></i>
                            </div>
                        </div>
                        <div class="stream-info">
                            <h4>1000 Abone Özel Yayını</h4>
                            <p>1000 aboneye özel olarak düzenlediğim 24 saatlik maraton yayın. Unutulmaz anlar!</p>
                            <div class="stream-stats">
                                <span><i class="fas fa-eye"></i> 3.2M İzlenme</span>
                                <span><i class="fas fa-heart"></i> 52K Beğeni</span>
                            </div>
                            <a href="https://youtube.com/watch?v=example3" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fab fa-youtube me-2"></i>
                                İzle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Gallery -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Sosyal Medya Galerimiz</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="social-gallery-card">
                        <div class="social-platform youtube">
                            <i class="fab fa-youtube"></i>
                            <span>YouTube</span>
                        </div>
                        <div class="social-content">
                            <img src="../assets/img/youtube-gallery.jpg" alt="YouTube İçerikleri" class="img-fluid">
                        </div>
                        <div class="social-stats">
                            <div class="stat">
                                <span class="number">250K+</span>
                                <span class="label">Abone</span>
                            </div>
                            <div class="stat">
                                <span class="number">15M+</span>
                                <span class="label">İzlenme</span>
                            </div>
                        </div>
                        <a href="https://youtube.com/@beratk" target="_blank" class="social-link">
                            Kanalı Ziyaret Et
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="social-gallery-card">
                        <div class="social-platform twitch">
                            <i class="fab fa-twitch"></i>
                            <span>Twitch</span>
                        </div>
                        <div class="social-content">
                            <img src="../assets/img/twitch-gallery.jpg" alt="Twitch Yayınları" class="img-fluid">
                        </div>
                        <div class="social-stats">
                            <div class="stat">
                                <span class="number">50K+</span>
                                <span class="label">Takipçi</span>
                            </div>
                            <div class="stat">
                                <span class="number">2.5K</span>
                                <span class="label">Ortalama İzleyici</span>
                            </div>
                        </div>
                        <a href="https://twitch.tv/beratk" target="_blank" class="social-link">
                            Kanalı Ziyaret Et
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="social-gallery-card">
                        <div class="social-platform instagram">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </div>
                        <div class="social-content">
                            <img src="../assets/img/instagram-gallery.jpg" alt="Instagram Paylaşımları" class="img-fluid">
                        </div>
                        <div class="social-stats">
                            <div class="stat">
                                <span class="number">75K+</span>
                                <span class="label">Takipçi</span>
                            </div>
                            <div class="stat">
                                <span class="number">15K</span>
                                <span class="label">Ortalama Beğeni</span>
                            </div>
                        </div>
                        <a href="https://instagram.com/beratk_casino" target="_blank" class="social-link">
                            Profili Ziyaret Et
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="social-gallery-card">
                        <div class="social-platform telegram">
                            <i class="fab fa-telegram"></i>
                            <span>Telegram</span>
                        </div>
                        <div class="social-content">
                            <img src="../assets/img/telegram-gallery.jpg" alt="Telegram Kanalı" class="img-fluid">
                        </div>
                        <div class="social-stats">
                            <div class="stat">
                                <span class="number">25K+</span>
                                <span class="label">Üye</span>
                            </div>
                            <div class="stat">
                                <span class="number">%85</span>
                                <span class="label">Etkileşim</span>
                            </div>
                        </div>
                        <a href="https://t.me/beratk_casino" target="_blank" class="social-link">
                            Kanala Katıl
                            <i class="fas fa-external-link-alt"></i>
                        </a>
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
                    <h2 class="text-white mb-4">Siz de Galerimizde Yer Alın</h2>
                    <p class="text-white-75 mb-4">
                        Benimle çalıştığınızda başarılı projeleriniz de bu galeride yerini alacak. 
                        Birlikte unutulmaz anlar yaşayalım!
                    </p>
                    <div class="cta-buttons">
                        <a href="../pages/contact.php" class="btn btn-white btn-lg me-3">
                            <i class="fas fa-handshake me-2"></i>
                            İşbirliği Yap
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

<!-- Photo Lightbox Modal -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
                <div class="modal-image-info mt-3 text-white">
                    <h5 id="modalImageTitle"></h5>
                    <p id="modalImageDescription"></p>
                    <small id="modalImageDate" class="text-white-50"></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalTitle">Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <video id="modalVideo" controls>
                        <source src="" type="video/mp4">
                        Tarayıcınız video oynatmayı desteklemiyor.
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gallery filtering
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            galleryItems.forEach(item => {
                if (filter === '*' || item.classList.contains(filter.substring(1))) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.5s ease';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Photo lightbox
    const photoModal = new bootstrap.Modal(document.getElementById('photoModal'));
    const photoZoomBtns = document.querySelectorAll('.photo-zoom-btn');
    
    photoZoomBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.gallery-item');
            const img = card.querySelector('img');
            const title = card.getAttribute('data-title');
            const description = card.getAttribute('data-description');
            const date = card.getAttribute('data-date');
            
            document.getElementById('modalImage').src = img.src;
            document.getElementById('modalImageTitle').textContent = title;
            document.getElementById('modalImageDescription').textContent = description;
            document.getElementById('modalImageDate').textContent = new Date(date).toLocaleDateString('tr-TR');
            
            photoModal.show();
        });
    });
    
    // Video modal
    const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
    const playBtns = document.querySelectorAll('.play-button');
    
    playBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const videoUrl = this.getAttribute('data-video-url');
            const card = this.closest('.gallery-item');
            const title = card.getAttribute('data-title');
            
            document.getElementById('modalVideo').src = videoUrl;
            document.getElementById('videoModalTitle').textContent = title;
            
            videoModal.show();
        });
    });
    
    // Pause video when modal closes
    document.getElementById('videoModal').addEventListener('hidden.bs.modal', function() {
        const video = document.getElementById('modalVideo');
        video.pause();
        video.currentTime = 0;
    });
    
    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Gallery hover effects
    document.querySelectorAll('.gallery-photo-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.querySelector('.photo-overlay').style.opacity = '1';
        });
        
        card.addEventListener('mouseleave', function() {
            this.querySelector('.photo-overlay').style.opacity = '0';
        });
    });
    
    // Social stats counter animation
    const socialCards = document.querySelectorAll('.social-gallery-card');
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
    
    socialCards.forEach(card => observer.observe(card));
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
</script>

<?php include '../includes/footer.php'; ?>
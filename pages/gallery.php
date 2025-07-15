<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();

// Get all active gallery items with error handling
try {
    $gallery = $db->findAll('gallery', ['status' => 'active'], 'sort_order ASC') ?? [];
} catch (Exception $e) {
    $gallery = [];
}

$pageTitle = 'Galeri - BERAT K Casino Yayıncısı';
$metaDescription = 'Canlı casino yayınlarımdan özel kareler, büyük kazançlar ve unutulmaz anlar.';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="casino galeri, canlı yayın, slot kazançları, casino anları">
    <meta name="author" content="BERAT K">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <!-- Enhanced Casino Theme -->
    <link href="../assets/css/casino-enhanced.css" rel="stylesheet">
    
    <style>
        /* GALLERY SPECIAL EFFECTS */
        .gallery-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #330000 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="2" height="2" fill="rgba(255,215,0,0.1)"/></svg>') repeat;
            background-size: 20px 20px;
            animation: sparkle 20s linear infinite;
        }

        @keyframes sparkle {
            0% { transform: translateY(0) translateX(0); }
            100% { transform: translateY(-100px) translateX(100px); }
        }

        .gallery-hero-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .gallery-hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            color: var(--casino-gold);
            text-shadow: 0 0 30px var(--casino-gold);
            margin-bottom: 1rem;
            animation: glow-pulse 2s ease-in-out infinite alternate;
        }

        .gallery-hero-subtitle {
            font-size: 1.5rem;
            color: var(--text-silver);
            margin-bottom: 2rem;
            animation: neon-flicker 3s ease-in-out infinite;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 2rem 0;
        }

        .gallery-card {
            background: linear-gradient(145deg, rgba(0,0,0,0.9) 0%, rgba(26,26,26,0.9) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid transparent;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            transition: all 0.5s ease;
            position: relative;
        }

        .gallery-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--casino-gold) 0%, var(--neon-pink) 50%, var(--neon-blue) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-card:hover::before {
            opacity: 0.1;
        }

        .gallery-card:hover {
            transform: translateY(-15px) scale(1.05);
            border-color: var(--casino-gold);
            box-shadow: 0 30px 60px rgba(255,215,0,0.3);
        }

        .gallery-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .gallery-card:hover .gallery-image {
            transform: scale(1.1);
            filter: brightness(1.2) contrast(1.2);
        }

        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,20,147,0.9) 0%, rgba(0,255,255,0.9) 100%);
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .gallery-card:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-overlay-content {
            text-align: center;
            color: white;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .gallery-card:hover .gallery-overlay-content {
            transform: translateY(0);
        }

        .gallery-overlay i {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .gallery-info {
            padding: 2rem;
            position: relative;
            z-index: 2;
        }

        .gallery-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--casino-gold);
        }

        .gallery-description {
            color: var(--text-silver);
            font-size: 1rem;
            line-height: 1.6;
        }

        .gallery-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,215,0,0.3);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--neon-pink);
            text-shadow: 0 0 10px var(--neon-pink);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-silver);
            margin-top: 0.5rem;
        }

        .gallery-filters {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
            gap: 1rem;
        }

        .filter-btn {
            padding: 0.8rem 2rem;
            background: linear-gradient(45deg, rgba(0,0,0,0.8) 0%, rgba(26,26,26,0.8) 100%);
            color: var(--text-silver);
            border: 2px solid transparent;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(45deg, var(--casino-gold) 0%, var(--neon-pink) 100%);
            color: var(--casino-black);
            box-shadow: 0 0 20px rgba(255,215,0,0.5);
            transform: translateY(-2px);
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--casino-gold) 0%, var(--neon-pink) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--casino-black);
            font-size: 1.5rem;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(255,215,0,0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255,215,0,0.5);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .gallery-hero-title {
                font-size: 2.5rem;
            }
            
            .gallery-hero-subtitle {
                font-size: 1.2rem;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
                padding: 1rem 0;
            }
            
            .gallery-filters {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .filter-btn {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></div>

    <!-- Navigation -->
    <nav class="casino-navbar" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 1rem 0; background: rgba(0,0,0,0.9); backdrop-filter: blur(10px);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="../index.php" class="casino-brand">BERAT K</a>
                <div class="d-flex gap-4">
                    <a href="../index.php" class="casino-nav-item">Ana Sayfa</a>
                    <a href="about.php" class="casino-nav-item">Hakkımda</a>
                    <a href="services.php" class="casino-nav-item">Hizmetler</a>
                    <a href="portfolio.php" class="casino-nav-item">Portfolyo</a>
                    <a href="gallery.php" class="casino-nav-item active">Galeri</a>
                    <a href="contact.php" class="casino-nav-item">İletişim</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gallery-hero">
        <div class="gallery-hero-content">
            <h1 class="gallery-hero-title" data-aos="fade-up">
                <i class="fas fa-camera"></i> GALERİ
            </h1>
            <p class="gallery-hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                Canlı Casino Yayınlarımdan Özel Kareler
            </p>
            <div class="gallery-stats" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($gallery); ?></div>
                    <div class="stat-label">Görsel</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100K+</div>
                    <div class="stat-label">Görüntüleme</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Kazanç Anı</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Content -->
    <section class="casino-section" style="padding: 5rem 0;">
        <div class="container">
            <!-- Filters -->
            <div class="gallery-filters" data-aos="fade-up">
                <div class="filter-btn active" data-filter="all">Tümü</div>
                <div class="filter-btn" data-filter="slot">Slot</div>
                <div class="filter-btn" data-filter="live">Canlı Casino</div>
                <div class="filter-btn" data-filter="bonus">Bonuslar</div>
                <div class="filter-btn" data-filter="win">Kazançlar</div>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-grid">
                <?php if (!empty($gallery)): ?>
                    <?php foreach ($gallery as $index => $item): ?>
                        <div class="gallery-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="position-relative">
                                <?php if (!empty($item['file_path'])): ?>
                                    <img src="../uploads/gallery/<?php echo htmlspecialchars($item['file_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title'] ?? 'Galeri Görseli'); ?>" 
                                         class="gallery-image">
                                <?php else: ?>
                                    <div class="gallery-image" style="background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink)); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x" style="color: var(--casino-black);"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="gallery-overlay">
                                    <div class="gallery-overlay-content">
                                        <i class="fas fa-search-plus"></i>
                                        <h4>Büyüt</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="gallery-info">
                                <h3 class="gallery-title"><?php echo htmlspecialchars($item['title'] ?? 'Casino Anısı'); ?></h3>
                                <p class="gallery-description"><?php echo htmlspecialchars($item['description'] ?? 'Unutulmaz casino anlarından biri...'); ?></p>
                                
                                <div class="gallery-stats">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo rand(1000, 9999); ?></div>
                                        <div class="stat-label">Beğeni</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo rand(100, 999); ?></div>
                                        <div class="stat-label">Yorum</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo rand(50, 500); ?></div>
                                        <div class="stat-label">Paylaşım</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center" style="grid-column: 1 / -1;">
                        <div class="casino-card" style="padding: 3rem;">
                            <i class="fas fa-images fa-4x" style="color: var(--casino-gold); margin-bottom: 1rem;"></i>
                            <h3 style="color: var(--casino-gold);">Henüz Galeri Görseli Yok</h3>
                            <p style="color: var(--text-silver);">Yakında harika casino anlarını burada görebileceksiniz!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Back to Top -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../assets/js/casino-enhanced.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true
        });

        // Initialize Particles
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: ['#FFD700', '#FF1493', '#00FFFF'] },
                shape: { type: 'circle' },
                opacity: { value: 0.5, random: true },
                size: { value: 3, random: true },
                line_linked: { enable: false },
                move: { enable: true, speed: 1, direction: 'none', random: true, straight: false, out_mode: 'out' }
            }
        });

        // Gallery filters
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Filter logic would go here
                console.log('Filter:', this.dataset.filter);
            });
        });

        // Back to top
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Gallery card click effects
        document.querySelectorAll('.gallery-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.animation = 'card-flip 0.6s ease-in-out';
                setTimeout(() => {
                    this.style.animation = '';
                }, 600);
            });
        });
    </script>
</body>
</html>
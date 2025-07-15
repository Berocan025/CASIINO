<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();

try {
    $services = $db->findAll('services', ['status' => 'active'], 'sort_order ASC') ?? [];
    $portfolio = $db->findAll('portfolio', ['status' => 'active'], 'sort_order ASC LIMIT 6') ?? [];
    $gallery = $db->findAll('gallery', ['status' => 'active'], 'sort_order ASC LIMIT 8') ?? [];
    
    $settings = [];
    $settingsData = $db->findAll('settings', ['status' => 'active']) ?? [];
    foreach ($settingsData as $setting) {
        if (isset($setting['setting_key']) && isset($setting['setting_value'])) {
            $settings[$setting['setting_key']] = $setting['setting_value'];
        }
    }
} catch (Exception $e) {
    $services = [];
    $portfolio = [];
    $gallery = [];
    $settings = [];
}

$pageTitle = $settings['site_title'] ?? 'Casino Yayıncısı - BERAT K';
$metaDescription = $settings['site_description'] ?? 'Profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --casino-black: #000000;
            --casino-dark: #0a0a0a;
            --casino-gold: #FFD700;
            --casino-red: #FF0000;
            --casino-green: #00FF00;
            --casino-blue: #00BFFF;
            --casino-purple: #8A2BE2;
            --neon-pink: #FF1493;
            --neon-cyan: #00FFFF;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Rajdhani', sans-serif;
            background: var(--casino-black);
            color: white;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Navigation */
        .casino-navbar {
            background: rgba(0, 0, 0, 0.95) !important;
            backdrop-filter: blur(15px);
            border-bottom: 2px solid var(--casino-gold);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }
        
        .casino-brand {
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            font-size: 2rem;
            color: var(--casino-gold);
            text-decoration: none;
            text-shadow: 0 0 10px var(--casino-gold);
        }
        
        .casino-nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .casino-nav-link:hover,
        .casino-nav-link.active {
            background: rgba(255, 215, 0, 0.2);
            color: var(--casino-gold);
            text-shadow: 0 0 5px var(--casino-gold);
        }
        
        .mobile-nav-toggle {
            display: none;
            background: rgba(0, 0, 0, 0.8) !important;
            border: none;
            color: var(--casino-gold) !important;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }
        
        .nav-menu {
            display: flex;
            gap: 1rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        /* Hero Section */
        .casino-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--casino-black) 0%, var(--casino-dark) 50%, var(--casino-black) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            padding: 120px 15px 50px;
        }
        
        .hero-avatar {
            width: 150px;
            height: 150px;
            margin: 0 auto 2rem;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
            border: 3px solid var(--casino-gold);
        }
        
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .text-gradient {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            font-family: 'Orbitron', monospace;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            color: var(--casino-gold);
            margin-bottom: 2rem;
            text-shadow: 0 0 10px var(--casino-gold);
        }
        
        .casino-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 15px 30px;
            margin: 0.5rem;
            border: 2px solid var(--casino-gold);
            background: transparent;
            color: var(--casino-gold);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .casino-btn:hover {
            background: var(--casino-gold);
            color: var(--casino-black);
            box-shadow: 0 0 20px var(--casino-gold);
            transform: translateY(-2px);
        }
        
        .casino-btn-primary {
            background: var(--casino-gold);
            color: var(--casino-black);
        }
        
        .casino-btn-primary:hover {
            background: transparent;
            color: var(--casino-gold);
        }
        
        /* Stats */
        .casino-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .casino-stat {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 15px;
            border: 1px solid rgba(255, 215, 0, 0.3);
        }
        
        .casino-stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--casino-gold);
            display: block;
        }
        
        .casino-stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        /* Sections */
        .casino-section {
            padding: 5rem 0;
            background: var(--casino-dark);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            font-family: 'Orbitron', monospace;
        }
        
        .section-title p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
        }
        
        .casino-card {
            background: rgba(255, 215, 0, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 15px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .casino-card:hover {
            background: rgba(255, 215, 0, 0.1);
            border-color: var(--casino-gold);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }
        
        .casino-card h3 {
            color: var(--casino-gold);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .casino-card p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }
        
        .casino-card-icon {
            font-size: 3rem;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .mobile-nav-toggle {
                display: block !important;
            }
            
            .nav-menu {
                position: fixed;
                top: 80px;
                right: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: rgba(0, 0, 0, 0.98);
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                padding: 2rem 0;
                transition: right 0.3s ease;
                z-index: 1000;
                backdrop-filter: blur(15px);
                border-left: 2px solid var(--casino-gold);
            }
            
            .nav-menu.active {
                right: 0;
            }
            
            .casino-nav-link {
                font-size: 1.3rem;
                padding: 1.2rem 2rem;
                width: 90%;
                text-align: center;
                margin: 0.8rem 0;
                border: 2px solid rgba(255, 215, 0, 0.3);
                border-radius: 25px;
                background: rgba(255, 215, 0, 0.05);
            }
            
            .casino-nav-link:hover,
            .casino-nav-link.active {
                background: var(--casino-gold);
                color: var(--casino-black);
                transform: scale(1.05);
            }
            
            .casino-hero {
                padding: 120px 15px 50px !important;
            }
            
            .text-gradient {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .hero-avatar {
                width: 120px;
                height: 120px;
            }
            
            .casino-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .casino-stat-number {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .text-gradient {
                font-size: 1.5rem;
            }
            
            .casino-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="casino-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="index.php" class="casino-brand">🎰 BERAT K</a>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-nav-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Navigation Menu -->
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="casino-nav-link active">Ana Sayfa</a></li>
                    <li><a href="pages/about.php" class="casino-nav-link">Hakkımda</a></li>
                    <li><a href="pages/services.php" class="casino-nav-link">Hizmetler</a></li>
                    <li><a href="pages/portfolio.php" class="casino-nav-link">Portfolyo</a></li>
                    <li><a href="pages/gallery.php" class="casino-nav-link">Galeri</a></li>
                    <li><a href="pages/contact.php" class="casino-nav-link">İletişim</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="casino-hero">
        <div class="container">
            <div class="casino-hero-content">
                <div class="hero-avatar">
                    <img src="assets/images/avatar.svg" alt="BERAT K" class="avatar-img">
                </div>
                
                <h1 class="text-gradient">BERAT K</h1>
                <h2 class="hero-subtitle">🎰 CASINO YAYINCISI 🎰</h2>
                
                <p class="lead text-white-50 mb-4">
                    Profesyonel Casino Streaming & Dijital Pazarlama Uzmanı
                </p>
                
                <div class="casino-stats">
                    <div class="casino-stat">
                        <span class="casino-stat-number">100K+</span>
                        <span class="casino-stat-label">Takipçi</span>
                    </div>
                    <div class="casino-stat">
                        <span class="casino-stat-number">2000+</span>
                        <span class="casino-stat-label">Canlı Yayın</span>
                    </div>
                    <div class="casino-stat">
                        <span class="casino-stat-number">5+</span>
                        <span class="casino-stat-label">Yıl Deneyim</span>
                    </div>
                    <div class="casino-stat">
                        <span class="casino-stat-number">50+</span>
                        <span class="casino-stat-label">Başarılı Proje</span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="pages/services.php" class="casino-btn casino-btn-primary">
                        <i class="fas fa-rocket"></i>
                        Hizmetlerimi Keşfet
                    </a>
                    <a href="pages/contact.php" class="casino-btn">
                        <i class="fas fa-envelope"></i>
                        İletişime Geç
                    </a>
                </div>
                
                <div class="mt-4">
                    <a href="https://twitch.tv/beratk" class="casino-btn" target="_blank">
                        <i class="fab fa-twitch"></i> Twitch
                    </a>
                    <a href="https://youtube.com/@beratk" class="casino-btn" target="_blank">
                        <i class="fab fa-youtube"></i> YouTube
                    </a>
                    <a href="https://instagram.com/beratk" class="casino-btn" target="_blank">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <?php if (!empty($services)): ?>
    <section class="casino-section">
        <div class="container">
            <div class="section-title">
                <h2>🎯 Hizmetlerim</h2>
                <p>Profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri</p>
            </div>
            
            <div class="row">
                <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="casino-card">
                        <div class="casino-card-icon">
                            <i class="fas fa-dice"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p><?php echo htmlspecialchars($service['short_description'] ?? $service['description']); ?></p>
                        <?php if ($service['price']): ?>
                            <div class="text-warning h5 mt-3">
                                <i class="fas fa-coins"></i> <?php echo htmlspecialchars($service['price']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Portfolio Section -->
    <?php if (!empty($portfolio)): ?>
    <section class="casino-section" style="background: var(--casino-black);">
        <div class="container">
            <div class="section-title">
                <h2>🏆 Portfolyo</h2>
                <p>Başarılı projelerim ve çalışmalarım</p>
            </div>
            
            <div class="row">
                <?php foreach ($portfolio as $item): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="casino-card">
                        <?php if ($item['image']): ?>
                            <img src="uploads/portfolio/<?php echo htmlspecialchars($item['image']); ?>" 
                                 class="w-100 mb-3" style="height: 200px; object-fit: cover; border-radius: 10px;">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <?php if ($item['project_url']): ?>
                            <a href="<?php echo htmlspecialchars($item['project_url']); ?>" class="casino-btn mt-3" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Projeyi Gör
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Gallery Section -->
    <?php if (!empty($gallery)): ?>
    <section class="casino-section">
        <div class="container">
            <div class="section-title">
                <h2>📸 Galeri</h2>
                <p>En iyi anlarım ve çekimlerim</p>
            </div>
            
            <div class="row">
                <?php foreach ($gallery as $item): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="casino-card p-0">
                        <img src="uploads/gallery/<?php echo htmlspecialchars($item['file_path']); ?>" 
                             class="w-100" style="height: 200px; object-fit: cover; border-radius: 15px;"
                             alt="<?php echo htmlspecialchars($item['alt_text'] ?: $item['title']); ?>">
                        <div class="p-3">
                            <h5 class="text-warning"><?php echo htmlspecialchars($item['title']); ?></h5>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="casino-section" style="background: var(--casino-black); border-top: 2px solid var(--casino-gold);">
        <div class="container">
            <div class="text-center">
                <h3 class="text-warning mb-3">🎰 BERAT K</h3>
                <p class="text-white-50 mb-4">Profesyonel Casino Yayıncısı & Dijital Pazarlama Uzmanı</p>
                
                <div class="mb-4">
                    <a href="https://twitch.tv/beratk" class="casino-btn me-2" target="_blank">
                        <i class="fab fa-twitch"></i> Twitch
                    </a>
                    <a href="https://youtube.com/@beratk" class="casino-btn me-2" target="_blank">
                        <i class="fab fa-youtube"></i> YouTube
                    </a>
                    <a href="https://instagram.com/beratk" class="casino-btn" target="_blank">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                </div>
                
                <hr class="my-4" style="border-color: var(--casino-gold);">
                
                <div class="row">
                    <div class="col-md-6 text-md-start text-center">
                        <p class="mb-0 text-white-50">&copy; 2024 BERAT K. Tüm hakları saklıdır.</p>
                    </div>
                    <div class="col-md-6 text-md-end text-center">
                        <a href="pages/contact.php" class="text-warning text-decoration-none">İletişim</a> |
                        <a href="pages/about.php" class="text-warning text-decoration-none">Hakkımda</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const navMenu = document.getElementById('navMenu');
            const toggleIcon = document.querySelector('.mobile-nav-toggle i');
            
            navMenu.classList.toggle('active');
            
            if (navMenu.classList.contains('active')) {
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-times');
                document.body.style.overflow = 'hidden';
            } else {
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
                document.body.style.overflow = 'auto';
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            const navMenu = document.getElementById('navMenu');
            const toggleButton = document.querySelector('.mobile-nav-toggle');
            
            if (!navMenu.contains(e.target) && !toggleButton.contains(e.target)) {
                navMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
                document.querySelector('.mobile-nav-toggle i').classList.remove('fa-times');
                document.querySelector('.mobile-nav-toggle i').classList.add('fa-bars');
            }
        });
        
        // Close mobile menu when clicking on nav links
        document.querySelectorAll('.casino-nav-link').forEach(link => {
            link.addEventListener('click', function() {
                const navMenu = document.getElementById('navMenu');
                navMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
                document.querySelector('.mobile-nav-toggle i').classList.remove('fa-times');
                document.querySelector('.mobile-nav-toggle i').classList.add('fa-bars');
            });
        });
    </script>
</body>
</html>
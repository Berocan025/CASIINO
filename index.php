<?php
/**
 * Casino Portfolio Website - Homepage
 * Geliştirici: BERAT K
 * Professional casino streaming and digital marketing services
 */

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();

// Get data
$services = $db->findAll('services', ['status' => 'active'], 'order_position ASC');
$portfolio = $db->findAll('portfolio', ['status' => 'active'], 'order_position ASC LIMIT 6');
$gallery = $db->findAll('gallery', ['status' => 'active'], 'order_position ASC LIMIT 8');
$settings = [];
$settingsData = $db->findAll('settings', ['status' => 'active']);
foreach ($settingsData as $setting) {
    $settings[$setting['key']] = $setting['value'];
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
    <meta name="keywords" content="casino yayıncısı, twitch streamer, youtube casino, online casino, slot oyunları, canlı casino">
    <meta name="author" content="BERAT K">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0f0f23;
            --secondary-color: #1a1a2e;
            --accent-color: #e94560;
            --gold-color: #ffd700;
            --text-light: #f5f5f5;
            --text-muted: #b0b0b0;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-accent: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-gold: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            --shadow-light: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 8px 25px rgba(0, 0, 0, 0.15);
            --shadow-heavy: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-color);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .navbar.scrolled {
            background: rgba(15, 15, 35, 0.98);
            box-shadow: var(--shadow-medium);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-light);
            font-weight: 500;
            margin: 0 10px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-gold);
            transition: width 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 100%;
        }
        
        /* Hero Section */
        .hero-section {
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="casino-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.05"/><rect x="5" y="5" width="2" height="2" fill="gold" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23casino-pattern)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-weight: 400;
        }
        
        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
            padding: 13px 28px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-outline:hover {
            background: var(--accent-color);
            color: white;
            transform: translateY(-3px);
        }
        
        /* Sections */
        .section {
            padding: 100px 0;
            position: relative;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Services */
        .service-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--accent-color);
        }
        
        .service-icon {
            font-size: 3rem;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .service-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-light);
        }
        
        .service-description {
            color: var(--text-muted);
            line-height: 1.6;
        }
        
        /* Portfolio */
        .portfolio-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        
        .portfolio-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-heavy);
        }
        
        .portfolio-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .portfolio-item:hover .portfolio-image {
            transform: scale(1.05);
        }
        
        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            display: flex;
            align-items: flex-end;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .portfolio-item:hover .portfolio-overlay {
            opacity: 1;
        }
        
        .portfolio-info h4 {
            color: white;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .portfolio-info p {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Gallery */
        .gallery-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-heavy);
        }
        
        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        /* Contact Section */
        .contact-section {
            background: var(--secondary-color);
        }
        
        .contact-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-heavy);
        }
        
        .contact-icon {
            font-size: 2.5rem;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .contact-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }
        
        .contact-info {
            color: var(--text-muted);
        }
        
        .contact-info a {
            color: var(--accent-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .contact-info a:hover {
            color: var(--gold-color);
        }
        
        /* Footer */
        .footer {
            background: var(--primary-color);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px 0 20px;
        }
        
        .footer-content {
            text-align: center;
        }
        
        .footer-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .footer-text {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: var(--accent-color);
            color: white;
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-stats {
                gap: 1rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .service-card {
                margin-bottom: 30px;
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--primary-color);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fas fa-dice me-2"></i>
                BERAT K
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Hizmetler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#portfolio">Portföy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#gallery">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">İletişim</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-up">
                        <h1 class="hero-title">
                            Profesyonel<br>
                            <span style="background: var(--gradient-accent); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Casino Yayıncısı</span>
                        </h1>
                        <p class="hero-subtitle">
                            5+ yıllık deneyimimle casino oyunları, slot makineleri ve canlı casino yayınlarında uzmanlaşmış profesyonel bir yayıncıyım. Twitch, YouTube ve diğer platformlarda binlerce takipçiye ulaşıyorum.
                        </p>
                        
                        <div class="hero-stats">
                            <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                                <span class="stat-number">50K+</span>
                                <span class="stat-label">Takipçi</span>
                            </div>
                            <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                                <span class="stat-number">1000+</span>
                                <span class="stat-label">Canlı Yayın</span>
                            </div>
                            <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                                <span class="stat-number">5+</span>
                                <span class="stat-label">Yıl Deneyim</span>
                            </div>
                        </div>
                        
                        <div class="hero-buttons">
                            <a href="#services" class="btn-primary" data-aos="fade-up" data-aos-delay="400">
                                <i class="fas fa-play"></i>
                                Hizmetlerim
                            </a>
                            <a href="#contact" class="btn-outline" data-aos="fade-up" data-aos-delay="500">
                                <i class="fas fa-envelope"></i>
                                İletişime Geç
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="600">
                    <div class="hero-image text-center">
                        <i class="fas fa-dice" style="font-size: 20rem; background: var(--gradient-gold); -webkit-background-clip: text; -webkit-text-fill-color: transparent; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Hizmetlerim</h2>
                <p>Casino dünyasında sunduğum profesyonel hizmetler ile başarıya ulaşın</p>
            </div>
            
            <div class="row">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="service-card">
                                <div class="service-icon">
                                    <i class="<?php echo htmlspecialchars($service['icon'] ?? 'fas fa-cog'); ?>"></i>
                                </div>
                                <h3 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                                <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Henüz hizmet eklenmemiş.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="section" style="background: var(--secondary-color);">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Portföyüm</h2>
                <p>Gerçekleştirdiğim projeler ve başarı hikayelerim</p>
            </div>
            
            <div class="row">
                <?php if (!empty($portfolio)): ?>
                    <?php foreach ($portfolio as $index => $item): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="portfolio-item">
                                <?php if ($item['image']): ?>
                                    <img src="uploads/portfolio/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                         class="portfolio-image">
                                <?php else: ?>
                                    <div class="portfolio-image" style="background: var(--gradient-primary); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x" style="color: rgba(255,255,255,0.3);"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="portfolio-overlay">
                                    <div class="portfolio-info">
                                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <?php if ($item['description']): ?>
                                            <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>...</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Henüz portföy projesi eklenmemiş.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Galeri</h2>
                <p>Canlı yayınlarımdan ve casino deneyimlerimden kareler</p>
            </div>
            
            <div class="row">
                <?php if (!empty($gallery)): ?>
                    <?php foreach ($gallery as $index => $item): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="gallery-item">
                                <img src="uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                     class="gallery-image">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Henüz galeri görseli eklenmemiş.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>İletişim</h2>
                <p>Benimle iletişime geçin ve birlikte çalışalım</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fab fa-twitch"></i>
                        </div>
                        <h3 class="contact-title">Twitch</h3>
                        <div class="contact-info">
                            <a href="https://twitch.tv/beratk" target="_blank">twitch.tv/beratk</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h3 class="contact-title">YouTube</h3>
                        <div class="contact-info">
                            <a href="https://youtube.com/@beratk" target="_blank">youtube.com/@beratk</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h3 class="contact-title">Telegram</h3>
                        <div class="contact-info">
                            <a href="https://t.me/beratk" target="_blank">@beratk</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">BERAT K</div>
                <p class="footer-text">
                    Profesyonel casino yayıncısı ve dijital pazarlama uzmanı
                </p>
                
                <div class="social-links">
                    <a href="https://twitch.tv/beratk" class="social-link" target="_blank">
                        <i class="fab fa-twitch"></i>
                    </a>
                    <a href="https://youtube.com/@beratk" class="social-link" target="_blank">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://t.me/beratk" class="social-link" target="_blank">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="https://instagram.com/beratk" class="social-link" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 BERAT K. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Active nav link
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
        
        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current) + counter.textContent.replace(/[0-9]/g, '').replace(/[0-9]/g, '');
                }, 20);
            });
        }
        
        // Trigger counter animation when hero section is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(document.querySelector('.hero-section'));
    </script>
</body>
</html>
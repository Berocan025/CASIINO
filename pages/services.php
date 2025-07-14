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
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($metaKeywords); ?>">
    <meta name="author" content="BERAT K">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:type" content="website">
    
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
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-light);
            font-weight: 500;
            margin: 0 10px;
            position: relative;
            transition: all 0.3s ease;
            text-decoration: none;
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
            padding: 120px 0 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="services-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.05"/><rect x="5" y="5" width="2" height="2" fill="gold" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23services-pattern)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3rem;
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
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .hero-badges {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .badge-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Services Section */
        .services-section {
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
        
        .service-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-accent);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .service-card:hover::before {
            transform: scaleX(1);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--accent-color);
        }
        
        .service-icon {
            font-size: 3.5rem;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            display: block;
        }
        
        .service-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-light);
        }
        
        .service-description {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .service-features {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
            text-align: left;
        }
        
        .service-features li {
            padding: 5px 0;
            color: var(--text-muted);
            position: relative;
            padding-left: 25px;
        }
        
        .service-features li i {
            position: absolute;
            left: 0;
            top: 8px;
            color: var(--accent-color);
        }
        
        .service-action {
            margin-top: auto;
        }
        
        .btn-service {
            background: var(--gradient-primary);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-service:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            color: white;
        }
        
        /* Process Section */
        .process-section {
            padding: 100px 0;
            background: var(--secondary-color);
        }
        
        .process-step {
            text-align: center;
            position: relative;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-gold);
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .step-icon {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }
        
        .process-step h4 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-light);
        }
        
        .process-step p {
            color: var(--text-muted);
            line-height: 1.6;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            text-align: center;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .cta-description {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-cta {
            background: var(--gradient-accent);
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
            color: white;
        }
        
        .btn-outline-cta {
            background: transparent;
            border: 2px solid var(--gold-color);
            color: var(--gold-color);
            padding: 13px 28px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-outline-cta:hover {
            background: var(--gold-color);
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        /* Footer */
        .footer {
            background: var(--primary-color);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px 0 20px;
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
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-badges {
                gap: 1rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .service-card {
                margin-bottom: 30px;
            }
            
            .cta-title {
                font-size: 2rem;
            }
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
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-dice me-2"></i>
                BERAT K
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="services.php">Hizmetler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Portföy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gallery.php">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">İletişim</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1 class="hero-title">Profesyonel Hizmetler</h1>
                <p class="hero-subtitle">
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
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Hizmetlerim</h2>
                <p>Casino dünyasında başarılı olmak için ihtiyacınız olan tüm profesyonel hizmetler</p>
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
                                
                                <div class="service-action">
                                    <a href="contact.php?service=<?php echo urlencode($service['title']); ?>" class="btn-service">
                                        <i class="fas fa-arrow-right"></i>
                                        Teklif Al
                                    </a>
                                </div>
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

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Çalışma Sürecim</h2>
                <p>Profesyonel hizmet anlayışımla her adımda yanınızda</p>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="process-step">
                        <div class="step-number">01</div>
                        <div class="step-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h4>İlk Görüşme</h4>
                        <p>İhtiyaçlarınızı anlıyor, hedeflerinizi belirliyoruz. Detaylı analiz yaparak en uygun stratejiyi planlıyoruz.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="process-step">
                        <div class="step-number">02</div>
                        <div class="step-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Strateji Geliştirme</h4>
                        <p>Sektör analizi ve rakip araştırması yaparak size özel dijital pazarlama stratejisi geliştiriyorum.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="process-step">
                        <div class="step-number">03</div>
                        <div class="step-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4>Uygulama</h4>
                        <p>Belirlenen stratejiyi profesyonel ekibimle birlikte hayata geçiriyoruz. Her aşamada raporlama yapıyoruz.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="process-step">
                        <div class="step-number">04</div>
                        <div class="step-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h4>Sonuç & Optimizasyon</h4>
                        <p>Sonuçları analiz ediyoruz, performansı optimize ediyoruz ve sürekli iyileştirmeler yapıyoruz.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2 class="cta-title">Projenizi Konuşalım</h2>
                <p class="cta-description">
                    Casino yayıncılığı ve dijital pazarlama ihtiyaçlarınız için benimle iletişime geçin. 
                    Ücretsiz danışmanlık için hemen arayın!
                </p>
                
                <div class="cta-buttons">
                    <a href="contact.php" class="btn-cta">
                        <i class="fas fa-envelope"></i>
                        İletişime Geç
                    </a>
                    <a href="https://wa.me/905555555555" target="_blank" class="btn-outline-cta">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-brand">BERAT K</div>
            <p class="footer-text">
                Profesyonel casino yayıncısı ve dijital pazarlama uzmanı
            </p>
            
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
    </script>
</body>
</html>
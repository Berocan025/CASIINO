<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();

// Get all active services
try {
    $services = $db->findAll('services', ['status' => 'active'], 'sort_order ASC') ?? [];
} catch (Exception $e) {
    $services = [];
}

$pageTitle = 'Hizmetler - BERAT K Casino Yayıncısı';
$metaDescription = 'Profesyonel casino yayıncılığı, dijital pazarlama, Telegram kanal yönetimi ve daha fazlası.';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="casino yayıncılığı, dijital pazarlama, telegram kanal yönetimi, casino hizmetleri">
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
        /* SERVICES PAGE SPECIAL EFFECTS */
        .services-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #000000 0%, #1a0033 25%, #330000 50%, #001a33 75%, #000000 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .services-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="1" height="1" fill="rgba(255,215,0,0.3)"/><rect x="20" y="20" width="1" height="1" fill="rgba(255,20,147,0.3)"/><rect x="40" y="40" width="1" height="1" fill="rgba(0,255,255,0.3)"/><rect x="60" y="60" width="1" height="1" fill="rgba(138,43,226,0.3)"/><rect x="80" y="80" width="1" height="1" fill="rgba(255,215,0,0.3)"/></svg>') repeat;
            background-size: 50px 50px;
            animation: matrix-rain 10s linear infinite;
        }

        @keyframes matrix-rain {
            0% { transform: translateY(0); }
            100% { transform: translateY(-100px); }
        }

        .services-hero-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .services-hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-blue), var(--casino-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 50px rgba(255,215,0,0.5);
            margin-bottom: 1rem;
            animation: glow-pulse 3s ease-in-out infinite alternate;
        }

        .services-hero-subtitle {
            font-size: 1.5rem;
            color: var(--text-silver);
            margin-bottom: 2rem;
            text-shadow: 0 0 20px rgba(192,192,192,0.5);
        }

        .services-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 3rem;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(10px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255,215,0,0.3);
        }

        .stat-number {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: var(--casino-gold);
            text-shadow: 0 0 20px var(--casino-gold);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-silver);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .service-card {
            background: linear-gradient(145deg, rgba(0,0,0,0.95), rgba(26,26,26,0.95));
            backdrop-filter: blur(20px);
            border: 3px solid transparent;
            border-radius: 25px;
            padding: 3rem;
            text-align: center;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,215,0,0.1), transparent);
            animation: rotate-shine 4s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .service-card:hover::before {
            opacity: 1;
        }

        @keyframes rotate-shine {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .service-card:hover {
            transform: translateY(-20px) scale(1.05);
            border-color: var(--casino-gold);
            box-shadow: 0 30px 60px rgba(255,215,0,0.4);
        }

        .service-icon {
            font-size: 4rem;
            color: var(--casino-gold);
            margin-bottom: 2rem;
            animation: bounce 2s ease-in-out infinite;
            text-shadow: 0 0 30px var(--casino-gold);
        }

        .service-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            text-shadow: 0 0 20px var(--casino-gold);
        }

        .service-description {
            color: var(--text-silver);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .service-features {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .service-features li {
            color: var(--text-silver);
            margin-bottom: 0.8rem;
            position: relative;
            padding-left: 2rem;
        }

        .service-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--neon-green);
            font-weight: 900;
            text-shadow: 0 0 10px var(--neon-green);
        }

        .service-price {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--neon-pink);
            text-shadow: 0 0 20px var(--neon-pink);
            margin-bottom: 2rem;
        }

        .service-btn {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255,215,0,0.3);
            position: relative;
            overflow: hidden;
        }

        .service-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .service-btn:hover::before {
            left: 100%;
        }

        .service-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255,215,0,0.5);
        }

        .process-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            padding: 5rem 0;
            position: relative;
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .process-step {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .process-step::before {
            content: attr(data-step);
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            color: var(--casino-black);
            font-size: 1.2rem;
        }

        .process-step:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255,215,0,0.3);
        }

        .process-icon {
            font-size: 3rem;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }

        .process-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
        }

        .process-description {
            color: var(--text-silver);
            line-height: 1.6;
        }

        .testimonials-section {
            background: linear-gradient(135deg, #000000 0%, #2c0014 100%);
            padding: 5rem 0;
        }

        .testimonial-card {
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(20px);
            border: 2px solid var(--casino-gold);
            border-radius: 25px;
            padding: 2rem;
            margin: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255,215,0,0.3);
        }

        .testimonial-content {
            font-style: italic;
            color: var(--text-silver);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .testimonial-author {
            color: var(--casino-gold);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .testimonial-role {
            color: var(--neon-pink);
            font-size: 0.9rem;
        }

        .testimonial-rating {
            color: var(--casino-gold);
            font-size: 1.2rem;
            margin-top: 1rem;
        }

        .cta-section {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-blue));
            padding: 5rem 0;
            text-align: center;
        }

        .cta-content {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem;
            margin: 0 auto;
            max-width: 800px;
        }

        .cta-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            text-shadow: 0 0 30px var(--casino-gold);
        }

        .cta-description {
            color: var(--text-silver);
            font-size: 1.3rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }

        .cta-btn {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            padding: 1.5rem 3rem;
            border: none;
            border-radius: 50px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255,215,0,0.3);
            text-decoration: none;
            display: inline-block;
        }

        .cta-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(255,215,0,0.5);
            color: var(--casino-black);
        }

        .cta-btn-secondary {
            background: transparent;
            color: var(--casino-gold);
            border: 2px solid var(--casino-gold);
        }

        .cta-btn-secondary:hover {
            background: var(--casino-gold);
            color: var(--casino-black);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .services-hero-title {
                font-size: 2.5rem;
            }
            
            .services-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .service-card {
                padding: 2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
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
                    <a href="services.php" class="casino-nav-item active">Hizmetler</a>
                    <a href="portfolio.php" class="casino-nav-item">Portfolyo</a>
                    <a href="gallery.php" class="casino-nav-item">Galeri</a>
                    <a href="contact.php" class="casino-nav-item">İletişim</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="services-hero">
        <div class="container">
            <div class="services-hero-content">
                <h1 class="services-hero-title" data-aos="fade-up">
                    <i class="fas fa-briefcase"></i> HİZMETLER
                </h1>
                <p class="services-hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                    Profesyonel Casino Yayıncılığı ve Dijital Pazarlama Hizmetleri
                </p>
                <div class="services-stats" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-item">
                        <div class="stat-number">100+</div>
                        <div class="stat-label">Mutlu Müşteri</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Proje</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">5+</div>
                        <div class="stat-label">Yıl Deneyim</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="casino-section" style="padding: 5rem 0;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="casino-section-title" data-aos="fade-up">
                    <i class="fas fa-star"></i> PROFESYONEL HİZMETLER
                </h2>
                <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                    Casino sektöründe başarı için ihtiyacınız olan tüm hizmetler
                </p>
            </div>
            
            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                        <div class="service-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="service-icon">
                                <i class="fas fa-gamepad"></i>
                            </div>
                            <h3 class="service-title"><?php echo htmlspecialchars($service['title'] ?? 'Hizmet'); ?></h3>
                            <p class="service-description"><?php echo htmlspecialchars($service['short_description'] ?? 'Hizmet açıklaması'); ?></p>
                            
                            <ul class="service-features">
                                <li>Profesyonel yaklaşım</li>
                                <li>7/24 destek</li>
                                <li>Hızlı teslimat</li>
                                <li>Müşteri memnuniyeti garantisi</li>
                            </ul>
                            
                            <div class="service-price">
                                <?php echo htmlspecialchars($service['price'] ?? '₺999'); ?>
                            </div>
                            
                            <button class="service-btn" onclick="window.location.href='contact.php'">
                                <i class="fas fa-shopping-cart"></i> Hemen Başla
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default services if none in database -->
                    <div class="service-card" data-aos="fade-up">
                        <div class="service-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <h3 class="service-title">Canlı Casino Yayıncılığı</h3>
                        <p class="service-description">Profesyonel casino yayıncılığı hizmetleri ile markanıza değer katacak bir profesyonel.</p>
                        
                        <ul class="service-features">
                            <li>Düzenli canlı yayınlar</li>
                            <li>Profesyonel sunum</li>
                            <li>Etkileşimli izleyici deneyimi</li>
                            <li>Marka bilinirliği artırma</li>
                        </ul>
                        
                        <div class="service-price">₺2,999</div>
                        
                        <button class="service-btn" onclick="window.location.href='contact.php'">
                            <i class="fas fa-shopping-cart"></i> Hemen Başla
                        </button>
                    </div>
                    
                    <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="service-title">Dijital Pazarlama</h3>
                        <p class="service-description">Sosyal medya ve dijital platformlarda etkili pazarlama stratejileri.</p>
                        
                        <ul class="service-features">
                            <li>Sosyal medya yönetimi</li>
                            <li>İçerik üretimi</li>
                            <li>Reklam kampanyaları</li>
                            <li>Analiz ve raporlama</li>
                        </ul>
                        
                        <div class="service-price">₺1,999</div>
                        
                        <button class="service-btn" onclick="window.location.href='contact.php'">
                            <i class="fas fa-shopping-cart"></i> Hemen Başla
                        </button>
                    </div>
                    
                    <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h3 class="service-title">Telegram Kanal Yönetimi</h3>
                        <p class="service-description">Telegram kanalınızı büyütün ve etkili içerik yönetimi yapın.</p>
                        
                        <ul class="service-features">
                            <li>Kanal kurulumu</li>
                            <li>İçerik paylaşımı</li>
                            <li>Üye artırma</li>
                            <li>Etkileşim yönetimi</li>
                        </ul>
                        
                        <div class="service-price">₺999</div>
                        
                        <button class="service-btn" onclick="window.location.href='contact.php'">
                            <i class="fas fa-shopping-cart"></i> Hemen Başla
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="casino-section-title" data-aos="fade-up">
                    <i class="fas fa-cogs"></i> ÇALIŞMA SÜRECİM
                </h2>
                <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                    Profesyonel hizmet süreci adım adım
                </p>
            </div>
            
            <div class="process-steps">
                <div class="process-step" data-step="1" data-aos="fade-up">
                    <div class="process-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="process-title">İlk Görüşme</h3>
                    <p class="process-description">
                        İhtiyaçlarınızı dinler, hedeflerinizi belirler ve size özel çözümler sunarım.
                    </p>
                </div>
                
                <div class="process-step" data-step="2" data-aos="fade-up" data-aos-delay="100">
                    <div class="process-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="process-title">Strateji Geliştirme</h3>
                    <p class="process-description">
                        Detaylı analiz yaparak size özel strateji ve çalışma planı hazırlarım.
                    </p>
                </div>
                
                <div class="process-step" data-step="3" data-aos="fade-up" data-aos-delay="200">
                    <div class="process-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="process-title">Uygulama</h3>
                    <p class="process-description">
                        Planları profesyonel şekilde uygular ve sürekli optimizasyon yaparım.
                    </p>
                </div>
                
                <div class="process-step" data-step="4" data-aos="fade-up" data-aos-delay="300">
                    <div class="process-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="process-title">Analiz & Raporlama</h3>
                    <p class="process-description">
                        Sonuçları analiz eder, detaylı raporlar sunar ve iyileştirmeler yaparım.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="casino-section-title" data-aos="fade-up">
                    <i class="fas fa-quote-left"></i> MÜŞTERİ YORUMLARI
                </h2>
                <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                    Birlikte çalıştığımız müşterilerimizin değerli görüşleri
                </p>
            </div>
            
            <div class="row">
                <div class="col-lg-4" data-aos="fade-up">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "BERAT K ile çalışmak harika bir deneyimdi. Profesyonel yaklaşımı ve başarılı sonuçlar elde etmemizi sağladı."
                        </div>
                        <div class="testimonial-author">Ahmet Yılmaz</div>
                        <div class="testimonial-role">Casino Site Sahibi</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "Telegram kanalımızı büyütmede çok etkili oldu. Kısa sürede binlerce takipçiye ulaştık."
                        </div>
                        <div class="testimonial-author">Fatma Kaya</div>
                        <div class="testimonial-role">Pazarlama Uzmanı</div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "Casino yayıncılığı konusunda gerçekten uzman. Markanıza değer katacak bir profesyonel."
                        </div>
                        <div class="testimonial-author">Mehmet Öz</div>
                        <div class="testimonial-role">İş Ortağı</div>
                        <div class="testimonial-rating">
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
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2 class="cta-title">
                    <i class="fas fa-handshake"></i> HEMEN BAŞLAYALIM
                </h2>
                <p class="cta-description">
                    Profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri için 
                    benimle iletişime geçin. Ücretsiz konsültasyon için hemen arayın!
                </p>
                <div class="cta-buttons">
                    <a href="contact.php" class="cta-btn">
                        <i class="fas fa-envelope"></i> İletişime Geç
                    </a>
                    <a href="tel:+905555555555" class="cta-btn cta-btn-secondary">
                        <i class="fas fa-phone"></i> Hemen Ara
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Top -->
    <a href="#" class="back-to-top" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--casino-black); font-size: 1.5rem; text-decoration: none; box-shadow: 0 10px 30px rgba(255,215,0,0.3); transition: all 0.3s ease; z-index: 1000;">
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
                number: { value: 120, density: { enable: true, value_area: 800 } },
                color: { value: ['#FFD700', '#FF1493', '#00FFFF', '#8A2BE2', '#FF8C00'] },
                shape: { type: 'circle' },
                opacity: { value: 0.7, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: true, distance: 100, color: '#FFD700', opacity: 0.3, width: 1 },
                move: { enable: true, speed: 3, direction: 'none', random: true, straight: false, out_mode: 'out' }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'grab' },
                    onclick: { enable: true, mode: 'repulse' }
                }
            }
        });

        // Back to top
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Service card animations
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-20px) scale(1.05)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Stat counter animation
        function animateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const text = stat.textContent;
                const number = parseInt(text.replace(/\D/g, ''));
                const suffix = text.replace(/\d/g, '');
                
                let current = 0;
                const increment = number / 100;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= number) {
                        current = number;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(current) + suffix;
                }, 30);
            });
        }

        // Trigger animations when in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('services-stats')) {
                        animateStats();
                    }
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(document.querySelector('.services-stats'));
    </script>
</body>
</html>
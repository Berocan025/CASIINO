<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();

// Get all active portfolio items
try {
    $portfolio = $db->findAll('portfolio', ['status' => 'active'], 'sort_order ASC') ?? [];
} catch (Exception $e) {
    $portfolio = [];
}

$pageTitle = 'Portfolyo - BERAT K Casino Yayıncısı';
$metaDescription = 'Başarılı casino yayıncılığı projelerim, kazançlarım ve iş ortaklıklarım.';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="casino portfolyo, başarılı projeler, casino yayıncılığı, kazançlar">
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
        /* PORTFOLIO PAGE SPECIAL EFFECTS */
        .portfolio-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #000000 0%, #330033 25%, #660000 50%, #003366 75%, #000000 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .portfolio-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><circle cx="50" cy="50" r="2" fill="rgba(255,215,0,0.3)"/><circle cx="150" cy="50" r="1" fill="rgba(255,20,147,0.3)"/><circle cx="50" cy="150" r="1.5" fill="rgba(0,255,255,0.3)"/><circle cx="150" cy="150" r="1" fill="rgba(138,43,226,0.3)"/><circle cx="100" cy="100" r="2" fill="rgba(255,215,0,0.3)"/></svg>') repeat;
            background-size: 200px 200px;
            animation: constellation 20s linear infinite;
        }

        @keyframes constellation {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
            100% { transform: rotate(360deg) scale(1); }
        }

        .portfolio-hero-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .portfolio-hero-title {
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

        .portfolio-hero-subtitle {
            font-size: 1.5rem;
            color: var(--text-silver);
            margin-bottom: 2rem;
            text-shadow: 0 0 20px rgba(192,192,192,0.5);
        }

        .portfolio-filter {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 3rem 0;
        }

        .filter-btn {
            padding: 1rem 2rem;
            background: linear-gradient(45deg, rgba(0,0,0,0.8), rgba(26,26,26,0.8));
            color: var(--text-silver);
            border: 2px solid transparent;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            border-color: var(--casino-gold);
            box-shadow: 0 0 20px rgba(255,215,0,0.5);
            transform: translateY(-2px);
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .portfolio-item {
            background: linear-gradient(145deg, rgba(0,0,0,0.9), rgba(26,26,26,0.9));
            backdrop-filter: blur(20px);
            border: 2px solid transparent;
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.5s ease;
            position: relative;
            cursor: pointer;
        }

        .portfolio-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,215,0,0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .portfolio-item:hover::before {
            opacity: 1;
        }

        .portfolio-item:hover {
            transform: translateY(-15px) scale(1.03);
            border-color: var(--casino-gold);
            box-shadow: 0 30px 60px rgba(255,215,0,0.3);
        }

        .portfolio-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .portfolio-item:hover .portfolio-image {
            transform: scale(1.1);
            filter: brightness(1.2) saturate(1.2);
        }

        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 300px;
            background: linear-gradient(45deg, rgba(255,215,0,0.9), rgba(255,20,147,0.9));
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .portfolio-item:hover .portfolio-overlay {
            opacity: 1;
        }

        .portfolio-overlay-content {
            text-align: center;
            color: var(--casino-black);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .portfolio-item:hover .portfolio-overlay-content {
            transform: translateY(0);
        }

        .portfolio-overlay-content i {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: bounce 2s ease-in-out infinite;
        }

        .portfolio-overlay-content h4 {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .portfolio-content {
            padding: 2rem;
            position: relative;
            z-index: 3;
        }

        .portfolio-category {
            display: inline-block;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .portfolio-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--casino-gold);
        }

        .portfolio-description {
            color: var(--text-silver);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .portfolio-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: rgba(0,0,0,0.5);
            border-radius: 15px;
            border: 1px solid var(--casino-gold);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255,215,0,0.3);
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

        .portfolio-technologies {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .tech-tag {
            background: rgba(0,0,0,0.7);
            color: var(--casino-gold);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            border: 1px solid var(--casino-gold);
            transition: all 0.3s ease;
        }

        .tech-tag:hover {
            background: var(--casino-gold);
            color: var(--casino-black);
        }

        .portfolio-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .portfolio-btn {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .portfolio-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255,215,0,0.3);
            color: var(--casino-black);
        }

        .portfolio-btn-secondary {
            background: transparent;
            color: var(--casino-gold);
            border: 2px solid var(--casino-gold);
        }

        .portfolio-btn-secondary:hover {
            background: var(--casino-gold);
            color: var(--casino-black);
        }

        .achievements-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            padding: 5rem 0;
            position: relative;
        }

        .achievement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .achievement-card {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .achievement-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, var(--casino-gold), var(--neon-pink), var(--neon-blue), var(--casino-gold));
            animation: rotate 4s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .achievement-card:hover::before {
            opacity: 0.1;
        }

        .achievement-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(255,215,0,0.3);
        }

        .achievement-icon {
            font-size: 3rem;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }

        .achievement-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
        }

        .achievement-description {
            color: var(--text-silver);
            line-height: 1.6;
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

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .portfolio-hero-title {
                font-size: 2.5rem;
            }
            
            .portfolio-filter {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .portfolio-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .portfolio-item {
                min-height: auto;
            }
            
            .portfolio-stats {
                grid-template-columns: 1fr;
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
                    <a href="portfolio.php" class="casino-nav-item active">Portfolyo</a>
                    <a href="gallery.php" class="casino-nav-item">Galeri</a>
                    <a href="contact.php" class="casino-nav-item">İletişim</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="portfolio-hero">
        <div class="container">
            <div class="portfolio-hero-content">
                <h1 class="portfolio-hero-title" data-aos="fade-up">
                    <i class="fas fa-trophy"></i> PORTFOLYO
                </h1>
                <p class="portfolio-hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                    Başarılı Projeler ve Kazançlar
                </p>
                
                <!-- Portfolio Filter -->
                <div class="portfolio-filter" data-aos="fade-up" data-aos-delay="400">
                    <div class="filter-btn active" data-filter="all">Tümü</div>
                    <div class="filter-btn" data-filter="streaming">Yayıncılık</div>
                    <div class="filter-btn" data-filter="marketing">Pazarlama</div>
                    <div class="filter-btn" data-filter="telegram">Telegram</div>
                    <div class="filter-btn" data-filter="partnership">Ortaklıklar</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Items -->
    <section class="casino-section" style="padding: 5rem 0;">
        <div class="container">
            <div class="portfolio-grid">
                <?php if (!empty($portfolio)): ?>
                    <?php foreach ($portfolio as $index => $item): ?>
                        <div class="portfolio-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="position-relative">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="../uploads/portfolio/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title'] ?? 'Proje'); ?>" 
                                         class="portfolio-image">
                                <?php else: ?>
                                    <div class="portfolio-image" style="background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink)); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-project-diagram fa-4x" style="color: var(--casino-black);"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="portfolio-overlay">
                                    <div class="portfolio-overlay-content">
                                        <i class="fas fa-eye"></i>
                                        <h4>Detayları Gör</h4>
                                        <p>Projeyi İncele</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="portfolio-content">
                                <div class="portfolio-category"><?php echo htmlspecialchars($item['category'] ?? 'Proje'); ?></div>
                                <h3 class="portfolio-title"><?php echo htmlspecialchars($item['title'] ?? 'Proje Başlığı'); ?></h3>
                                <p class="portfolio-description"><?php echo htmlspecialchars($item['description'] ?? 'Proje açıklaması'); ?></p>
                                
                                <div class="portfolio-stats">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo rand(1000, 9999); ?></div>
                                        <div class="stat-label">Görüntüleme</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo rand(100, 999); ?></div>
                                        <div class="stat-label">Beğeni</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo rand(50, 500); ?></div>
                                        <div class="stat-label">Paylaşım</div>
                                    </div>
                                </div>
                                
                                <div class="portfolio-technologies">
                                    <span class="tech-tag">Casino</span>
                                    <span class="tech-tag">Yayıncılık</span>
                                    <span class="tech-tag">Pazarlama</span>
                                </div>
                                
                                <div class="portfolio-actions">
                                    <a href="<?php echo htmlspecialchars($item['project_url'] ?? '#'); ?>" class="portfolio-btn" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Görüntüle
                                    </a>
                                    <a href="contact.php" class="portfolio-btn portfolio-btn-secondary">
                                        <i class="fas fa-envelope"></i> İletişim
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default portfolio items if none in database -->
                    <div class="portfolio-item" data-aos="fade-up">
                        <div class="position-relative">
                            <div class="portfolio-image" style="background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink)); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-video fa-4x" style="color: var(--casino-black);"></i>
                            </div>
                            <div class="portfolio-overlay">
                                <div class="portfolio-overlay-content">
                                    <i class="fas fa-eye"></i>
                                    <h4>Detayları Gör</h4>
                                    <p>Projeyi İncele</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="portfolio-content">
                            <div class="portfolio-category">Yayıncılık</div>
                            <h3 class="portfolio-title">Twitch Casino Yayını</h3>
                            <p class="portfolio-description">Profesyonel casino yayıncılığı ile büyük bir izleyici kitlesi oluşturdum.</p>
                            
                            <div class="portfolio-stats">
                                <div class="stat-item">
                                    <div class="stat-number">50K</div>
                                    <div class="stat-label">Takipçi</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">500K</div>
                                    <div class="stat-label">Görüntüleme</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">100</div>
                                    <div class="stat-label">Yayın</div>
                                </div>
                            </div>
                            
                            <div class="portfolio-technologies">
                                <span class="tech-tag">Twitch</span>
                                <span class="tech-tag">Casino</span>
                                <span class="tech-tag">Streaming</span>
                            </div>
                            
                            <div class="portfolio-actions">
                                <a href="#" class="portfolio-btn">
                                    <i class="fas fa-external-link-alt"></i> Görüntüle
                                </a>
                                <a href="contact.php" class="portfolio-btn portfolio-btn-secondary">
                                    <i class="fas fa-envelope"></i> İletişim
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="portfolio-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="position-relative">
                            <div class="portfolio-image" style="background: linear-gradient(45deg, var(--neon-blue), var(--neon-pink)); display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-telegram fa-4x" style="color: var(--casino-black);"></i>
                            </div>
                            <div class="portfolio-overlay">
                                <div class="portfolio-overlay-content">
                                    <i class="fas fa-eye"></i>
                                    <h4>Detayları Gör</h4>
                                    <p>Projeyi İncele</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="portfolio-content">
                            <div class="portfolio-category">Telegram</div>
                            <h3 class="portfolio-title">Telegram Kanal Yönetimi</h3>
                            <p class="portfolio-description">10,000+ üyeli casino telegram kanalını başarıyla yönetiyorum.</p>
                            
                            <div class="portfolio-stats">
                                <div class="stat-item">
                                    <div class="stat-number">10K</div>
                                    <div class="stat-label">Üye</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">95%</div>
                                    <div class="stat-label">Aktiflik</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">200</div>
                                    <div class="stat-label">Günlük Mesaj</div>
                                </div>
                            </div>
                            
                            <div class="portfolio-technologies">
                                <span class="tech-tag">Telegram</span>
                                <span class="tech-tag">Yönetim</span>
                                <span class="tech-tag">İçerik</span>
                            </div>
                            
                            <div class="portfolio-actions">
                                <a href="#" class="portfolio-btn">
                                    <i class="fas fa-external-link-alt"></i> Görüntüle
                                </a>
                                <a href="contact.php" class="portfolio-btn portfolio-btn-secondary">
                                    <i class="fas fa-envelope"></i> İletişim
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="portfolio-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="position-relative">
                            <div class="portfolio-image" style="background: linear-gradient(45deg, var(--neon-green), var(--casino-gold)); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line fa-4x" style="color: var(--casino-black);"></i>
                            </div>
                            <div class="portfolio-overlay">
                                <div class="portfolio-overlay-content">
                                    <i class="fas fa-eye"></i>
                                    <h4>Detayları Gör</h4>
                                    <p>Projeyi İncele</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="portfolio-content">
                            <div class="portfolio-category">Pazarlama</div>
                            <h3 class="portfolio-title">Dijital Pazarlama Kampanyası</h3>
                            <p class="portfolio-description">Casino sitesi için %300 ROI sağlayan başarılı pazarlama kampanyası.</p>
                            
                            <div class="portfolio-stats">
                                <div class="stat-item">
                                    <div class="stat-number">300%</div>
                                    <div class="stat-label">ROI</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">50K</div>
                                    <div class="stat-label">Tıklama</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">30</div>
                                    <div class="stat-label">Gün</div>
                                </div>
                            </div>
                            
                            <div class="portfolio-technologies">
                                <span class="tech-tag">Meta Ads</span>
                                <span class="tech-tag">Google Ads</span>
                                <span class="tech-tag">Analytics</span>
                            </div>
                            
                            <div class="portfolio-actions">
                                <a href="#" class="portfolio-btn">
                                    <i class="fas fa-external-link-alt"></i> Görüntüle
                                </a>
                                <a href="contact.php" class="portfolio-btn portfolio-btn-secondary">
                                    <i class="fas fa-envelope"></i> İletişim
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section class="achievements-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="casino-section-title" data-aos="fade-up">
                    <i class="fas fa-medal"></i> BAŞARILAR
                </h2>
                <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                    Casino yayıncılığı alanında elde ettiğim önemli başarılar
                </p>
            </div>
            
            <div class="achievement-grid">
                <div class="achievement-card" data-aos="fade-up">
                    <div class="achievement-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="achievement-title">En İyi Yayıncı</h3>
                    <p class="achievement-description">
                        2023 yılında casino yayıncılığı kategorisinde en başarılı yayıncı ödülü.
                    </p>
                </div>
                
                <div class="achievement-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="achievement-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="achievement-title">Altın Ortak</h3>
                    <p class="achievement-description">
                        Önde gelen casino sitelerinin altın seviye ortağı statüsü.
                    </p>
                </div>
                
                <div class="achievement-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="achievement-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="achievement-title">5 Yıl Deneyim</h3>
                    <p class="achievement-description">
                        Casino yayıncılığı alanında 5 yıllık kesintisiz deneyim.
                    </p>
                </div>
                
                <div class="achievement-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="achievement-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="achievement-title">50K+ Takipçi</h3>
                    <p class="achievement-description">
                        Tüm platformlarda toplam 50,000+ aktif takipçi kitlesi.
                    </p>
                </div>
                
                <div class="achievement-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="achievement-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="achievement-title">1M+ Görüntüleme</h3>
                    <p class="achievement-description">
                        Yayınlarımda toplam 1 milyon+ görüntüleme sayısı.
                    </p>
                </div>
                
                <div class="achievement-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="achievement-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="achievement-title">100+ İş Ortağı</h3>
                    <p class="achievement-description">
                        Sektörde 100'den fazla başarılı iş ortaklığı.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2 class="cta-title">
                    <i class="fas fa-rocket"></i> BİRLİKTE BAŞARALIM
                </h2>
                <p class="cta-description">
                    Benimle çalışarak casino dünyasında büyük başarılar elde edebilirsiniz. 
                    Profesyonel hizmetlerim için hemen iletişime geçin.
                </p>
                <a href="contact.php" class="cta-btn">
                    <i class="fas fa-envelope"></i> Proje Başlat
                </a>
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
                number: { value: 100, density: { enable: true, value_area: 800 } },
                color: { value: ['#FFD700', '#FF1493', '#00FFFF', '#8A2BE2', '#FF8C00', '#00FF41'] },
                shape: { type: 'circle' },
                opacity: { value: 0.8, random: true },
                size: { value: 3, random: true },
                line_linked: { enable: true, distance: 120, color: '#FFD700', opacity: 0.4, width: 1 },
                move: { enable: true, speed: 4, direction: 'none', random: true, straight: false, out_mode: 'out' }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'bubble' },
                    onclick: { enable: true, mode: 'push' }
                }
            }
        });

        // Portfolio filters
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

        // Portfolio item click effects
        document.querySelectorAll('.portfolio-item').forEach(item => {
            item.addEventListener('click', function() {
                this.style.animation = 'card-flip 0.6s ease-in-out';
                setTimeout(() => {
                    this.style.animation = '';
                }, 600);
            });
        });

        // Achievement cards hover effects
        document.querySelectorAll('.achievement-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.05)';
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
                const increment = number / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= number) {
                        current = number;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(current) + suffix;
                }, 40);
            });
        }

        // Trigger animations when in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('portfolio-stats')) {
                        animateStats();
                    }
                    observer.unobserve(entry.target);
                }
            });
        });

        document.querySelectorAll('.portfolio-stats').forEach(stats => {
            observer.observe(stats);
        });
    </script>
</body>
</html>
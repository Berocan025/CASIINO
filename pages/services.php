<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();

// Get all active services with error handling
try {
    $services = $db->findAll('services', ['status' => 'active'], 'order_position ASC') ?? [];
} catch (Exception $e) {
    $services = [];
}

$pageTitle = 'Hizmetler - BERAT K Casino Yayıncısı';
$metaDescription = 'Profesyonel casino yayıncılığı, Telegram yönetimi, Meta Ads ve dijital pazarlama hizmetleri.';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="casino yayıncılığı, telegram yönetimi, meta ads, dijital pazarlama, youtube streaming">
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
    
    <style>
        :root {
            --casino-black: #0a0a0a;
            --casino-dark: #1a1a1a;
            --casino-gold: #FFD700;
            --casino-red: #DC143C;
            --neon-pink: #FF1493;
            --neon-cyan: #00FFFF;
            --text-light: #FFFFFF;
            --text-silver: #C0C0C0;
            --shadow-neon: 0 0 20px;
            --shadow-heavy: 0 15px 35px rgba(0, 0, 0, 0.8);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Rajdhani', sans-serif;
            background: var(--casino-black);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Particles Background */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
        }
        
        /* Casino Navigation */
        .casino-navbar {
            background: linear-gradient(135deg, var(--casino-black) 0%, var(--casino-dark) 100%);
            backdrop-filter: blur(15px);
            border-bottom: 2px solid var(--casino-gold);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-heavy);
        }
        
        .casino-brand {
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            font-size: 2rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            text-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        .casino-nav-link {
            color: var(--text-silver);
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0 15px;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .casino-nav-link:hover,
        .casino-nav-link.active {
            color: var(--casino-gold);
            text-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        /* Hero Section */
        .casino-hero {
            padding: 120px 0 80px;
            background: linear-gradient(135deg, var(--casino-black) 0%, var(--casino-dark) 100%);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .casino-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, var(--casino-red) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, var(--neon-cyan) 0%, transparent 50%);
            opacity: 0.1;
        }
        
        .casino-hero-content {
            position: relative;
            z-index: 2;
        }
        
        .casino-hero-title {
            font-family: 'Orbitron', monospace;
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        .casino-hero-subtitle {
            font-size: 1.3rem;
            color: var(--text-silver);
            margin-bottom: 2rem;
        }
        
        /* Services Section */
        .casino-services-section {
            padding: 120px 0;
            position: relative;
        }
        
        .casino-section-title {
            text-align: center;
            margin-bottom: 80px;
        }
        
        .casino-section-title h2 {
            font-family: 'Orbitron', monospace;
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        .casino-section-title p {
            font-size: 1.2rem;
            color: var(--text-silver);
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Service Cards */
        .casino-service-card {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(220, 20, 60, 0.1));
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .casino-service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .casino-service-card:hover::before {
            opacity: 1;
        }
        
        .casino-service-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--neon-pink);
        }
        
        .casino-service-icon {
            font-size: 5rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            display: block;
        }
        
        .casino-service-title {
            font-family: 'Orbitron', monospace;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--casino-gold);
            text-transform: uppercase;
        }
        
        .casino-service-description {
            color: var(--text-silver);
            line-height: 1.8;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        .casino-service-btn {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        .casino-service-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
            color: var(--casino-black);
        }
        
        /* Footer */
        .casino-footer {
            background: var(--casino-black);
            border-top: 2px solid var(--casino-gold);
            padding: 60px 0 30px;
            text-align: center;
        }
        
        .casino-footer-brand {
            font-family: 'Orbitron', monospace;
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .casino-footer-text {
            color: var(--text-silver);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .casino-footer-bottom {
            border-top: 1px solid var(--casino-gold);
            padding-top: 30px;
            color: var(--text-silver);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .casino-hero-title {
                font-size: 2.5rem;
            }
            
            .casino-service-card {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js"></div>

    <!-- Casino Navigation -->
    <nav class="casino-navbar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="../index.php" class="casino-brand">🎰 BERAT K</a>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <a href="../index.php" class="casino-nav-link">Ana Sayfa</a>
                        <a href="services.php" class="casino-nav-link active">Hizmetler</a>
                        <a href="portfolio.php" class="casino-nav-link">Portföy</a>
                        <a href="gallery.php" class="casino-nav-link">Galeri</a>
                        <a href="contact.php" class="casino-nav-link">İletişim</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="casino-hero">
        <div class="container">
            <div class="casino-hero-content" data-aos="fade-up">
                <h1 class="casino-hero-title">🎲 HİZMETLER 🎲</h1>
                <p class="casino-hero-subtitle">
                    Casino dünyasında profesyonel hizmetler ile büyük kazançlar elde edin
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="casino-services-section">
        <div class="container">
            <div class="casino-section-title" data-aos="fade-up">
                <h2>PROFESYONEL HİZMETLER</h2>
                <p>Casino yayıncılığından dijital pazarlamaya kadar tüm ihtiyaçlarınız için</p>
            </div>
            
            <div class="row">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                        <div class="col-lg-4 col-md-6 mb-5" data-aos="fade-up" data-aos-delay="<?php echo $index * 200; ?>">
                            <div class="casino-service-card">
                                <div class="casino-service-icon">
                                    <i class="<?php echo htmlspecialchars($service['icon'] ?? 'fas fa-dice'); ?>"></i>
                                </div>
                                <h3 class="casino-service-title"><?php echo htmlspecialchars($service['title'] ?? ''); ?></h3>
                                <p class="casino-service-description"><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
                                <a href="contact.php?service=<?php echo urlencode($service['title'] ?? ''); ?>" class="casino-service-btn">
                                    🎰 TEKLİF AL
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p style="color: var(--text-silver); font-size: 1.5rem;">🎰 Henüz hizmet eklenmemiş 🎰</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="casino-footer">
        <div class="container">
            <div class="casino-footer-brand">🎰 BERAT K 🎰</div>
            <p class="casino-footer-text">
                Profesyonel Casino Yayıncısı & Dijital Pazarlama Uzmanı
            </p>
            
            <div class="casino-footer-bottom">
                <p>&copy; 2024 BERAT K - Casino Yayıncılığında Lider 🎲</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Particles.js Configuration
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 60,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: ['#FFD700', '#DC143C', '#FF1493', '#00FFFF']
                },
                shape: {
                    type: 'circle'
                },
                opacity: {
                    value: 0.5,
                    random: false
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#FFD700',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 6,
                    direction: 'none',
                    random: false,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: {
                        enable: true,
                        mode: 'repulse'
                    },
                    onclick: {
                        enable: true,
                        mode: 'push'
                    },
                    resize: true
                },
                modes: {
                    repulse: {
                        distance: 200,
                        duration: 0.4
                    },
                    push: {
                        particles_nb: 4
                    }
                }
            },
            retina_detect: true
        });
    </script>
</body>
</html>
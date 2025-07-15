<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once '../includes/page_functions.php';

$db = new Database();

$pageTitle = 'Hakkımda - BERAT K Casino Yayıncısı';
$metaDescription = 'Casino yayıncılığında lider BERAT K hakkında detaylı bilgiler, deneyimler ve başarılar.';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="berat k, casino yayıncısı, twitch streamer, casino uzmanı">
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
        /* Mobile Navigation Styles */
        .mobile-nav-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--casino-gold);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }
        
        .nav-menu {
            display: flex;
            gap: 1rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .casino-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--casino-gold);
            text-decoration: none;
            font-family: 'Orbitron', monospace;
        }
        
        .casino-nav-link {
            color: var(--text-light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .casino-nav-link:hover,
        .casino-nav-link.active {
            background: rgba(255, 215, 0, 0.1);
            color: var(--casino-gold);
        }
        
        @media (max-width: 768px) {
            .mobile-nav-toggle {
                display: block;
            }
            
            .nav-menu {
                position: fixed;
                top: 70px;
                right: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background: rgba(10, 10, 10, 0.98);
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                padding: 2rem 0;
                transition: right 0.3s ease;
                z-index: 1000;
                backdrop-filter: blur(10px);
            }
            
            .nav-menu.active {
                right: 0;
            }
            
            .casino-nav-link {
                font-size: 1.2rem;
                padding: 1rem 2rem;
                width: 90%;
                text-align: center;
                margin: 0.5rem 0;
                border: 1px solid rgba(255, 215, 0, 0.2);
            }
        }
        
        /* ABOUT PAGE SPECIAL EFFECTS */
        .about-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #000000 0%, #2c0014 50%, #1a0033 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon points="50,20 80,80 20,80" fill="rgba(255,215,0,0.05)"/></svg>') repeat;
            background-size: 100px 100px;
            animation: float 15s linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-200px) rotate(360deg); }
        }

        .about-profile {
            display: flex;
            align-items: center;
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-image {
            position: relative;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid var(--casino-gold);
            box-shadow: 0 0 50px rgba(255,215,0,0.5);
            animation: glow-pulse 3s ease-in-out infinite alternate;
        }

        .profile-image::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-blue), var(--casino-gold));
            border-radius: 50%;
            z-index: -1;
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-content {
            flex: 1;
            color: white;
        }

        .profile-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--casino-gold);
            text-shadow: 0 0 30px var(--casino-gold);
            margin-bottom: 1rem;
            animation: neon-flicker 2s ease-in-out infinite alternate;
        }

        .profile-subtitle {
            font-size: 1.5rem;
            color: var(--neon-pink);
            text-shadow: 0 0 20px var(--neon-pink);
            margin-bottom: 2rem;
            animation: glow-pulse 2s ease-in-out infinite alternate;
        }

        .profile-description {
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--text-silver);
            margin-bottom: 2rem;
        }

        .achievement-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(10px);
            border: 2px solid var(--casino-gold);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255,215,0,0.3);
            border-color: var(--neon-pink);
        }

        .stat-number {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
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

        .skills-section {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            padding: 5rem 0;
            position: relative;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .skill-card {
            background: linear-gradient(145deg, rgba(0,0,0,0.9), rgba(26,26,26,0.9));
            backdrop-filter: blur(20px);
            border: 2px solid transparent;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .skill-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-blue));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .skill-card:hover::before {
            opacity: 0.1;
        }

        .skill-card:hover {
            transform: translateY(-15px) scale(1.05);
            border-color: var(--casino-gold);
            box-shadow: 0 30px 60px rgba(255,215,0,0.4);
        }

        .skill-icon {
            font-size: 3rem;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            animation: bounce 2s ease-in-out infinite;
        }

        .skill-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px var(--casino-gold);
        }

        .skill-description {
            color: var(--text-silver);
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        .experience-timeline {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            padding: 5rem 0;
            position: relative;
        }

        .timeline {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--casino-gold), var(--neon-pink), var(--neon-blue));
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            margin: 3rem 0;
            display: flex;
            align-items: center;
        }

        .timeline-content {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 2rem;
            width: 45%;
            transition: all 0.3s ease;
        }

        .timeline-item:nth-child(even) .timeline-content {
            margin-left: auto;
        }

        .timeline-content:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 40px rgba(255,215,0,0.3);
        }

        .timeline-date {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            font-weight: 900;
            color: var(--neon-pink);
            text-shadow: 0 0 10px var(--neon-pink);
            margin-bottom: 1rem;
        }

        .timeline-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
        }

        .timeline-description {
            color: var(--text-silver);
            line-height: 1.6;
        }

        .timeline-icon {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--casino-black);
            z-index: 10;
            box-shadow: 0 0 20px rgba(255,215,0,0.5);
        }

        .contact-cta {
            background: linear-gradient(135deg, var(--casino-gold) 0%, var(--neon-pink) 100%);
            color: var(--casino-black);
            padding: 1.5rem 3rem;
            border: none;
            border-radius: 50px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255,215,0,0.3);
        }

        .contact-cta:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(255,215,0,0.5);
            animation: casino-pulse 0.5s ease-in-out;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .about-profile {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
            }
            
            .profile-image {
                width: 250px;
                height: 250px;
            }
            
            .profile-title {
                font-size: 2.5rem;
            }
            
            .achievement-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .timeline::before {
                left: 30px;
            }
            
            .timeline-content {
                width: calc(100% - 80px);
                margin-left: 80px !important;
            }
            
            .timeline-icon {
                left: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></div>

    <!-- Navigation -->
    <nav class="casino-navbar" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 1rem 0; background: rgba(10,10,10,0.95); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255, 215, 0, 0.2);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="../index.php" class="casino-brand">BERAT K</a>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-nav-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Navigation Menu -->
                <ul class="nav-menu" id="navMenu">
                    <li><a href="../index.php" class="casino-nav-link">Ana Sayfa</a></li>
                    <li><a href="about.php" class="casino-nav-link active">Hakkımda</a></li>
                    <li><a href="services.php" class="casino-nav-link">Hizmetler</a></li>
                    <li><a href="portfolio.php" class="casino-nav-link">Portfolyo</a></li>
                    <li><a href="gallery.php" class="casino-nav-link">Galeri</a></li>
                    <li><a href="contact.php" class="casino-nav-link">İletişim</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="about-profile" data-aos="fade-up">
                <div class="profile-image">
                    <img src="../assets/images/berat-k-profile.jpg" alt="BERAT K" onerror="this.src='data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 400 400\"><rect width=\"400\" height=\"400\" fill=\"%23FFD700\"/><text x=\"200\" y=\"200\" text-anchor=\"middle\" dy=\".3em\" font-size=\"100\" fill=\"%23000\">BK</text></svg>'">
                </div>
                <div class="profile-content">
                    <h1 class="profile-title"><?php echo getPageContent('page_title', 'BERAT K'); ?></h1>
                    <p class="profile-subtitle"><?php echo getPageContent('page_subtitle', 'Profesyonel Casino Yayıncısı'); ?></p>
                    <p class="profile-description">
                        <?php echo getPageContent('bio_content', '5 yıldan fazla casino yayıncılığı deneyimi ile binlerce takipçiye ulaşmış, alanında uzman bir profesyonel. Canlı casino yayınları, slot oyunları ve casino stratejileri konusunda lider isim.'); ?>
                    </p>
                    <div class="achievement-stats">
                        <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="stat-number">50K+</div>
                            <div class="stat-label">Takipçi</div>
                        </div>
                        <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                            <div class="stat-number">1M+</div>
                            <div class="stat-label">Görüntüleme</div>
                        </div>
                        <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                            <div class="stat-number">5+</div>
                            <div class="stat-label">Yıl Deneyim</div>
                        </div>
                        <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Canlı Yayın</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section class="skills-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="casino-section-title" data-aos="fade-up">
                    <i class="fas fa-star"></i> UZMANLIK ALANLARI
                </h2>
                <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                    Casino dünyasında deneyim kazandığım özel alanlar
                </p>
            </div>
            
            <div class="skills-grid">
                <div class="skill-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="skill-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <h3 class="skill-title">Canlı Casino</h3>
                    <p class="skill-description">
                        Blackjack, Rulet, Baccarat gibi canlı casino oyunlarında uzman seviyede deneyim.
                    </p>
                </div>
                
                <div class="skill-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="skill-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3 class="skill-title">Slot Oyunları</h3>
                    <p class="skill-description">
                        Tüm slot oyun çeşitlerinde stratejik oyun ve büyük kazanç deneyimi.
                    </p>
                </div>
                
                <div class="skill-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="skill-icon">
                        <i class="fas fa-broadcast-tower"></i>
                    </div>
                    <h3 class="skill-title">Canlı Yayın</h3>
                    <p class="skill-description">
                        Profesyonel yayıncılık teknikleri ve izleyici etkileşimi konusunda uzman.
                    </p>
                </div>
                
                <div class="skill-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="skill-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="skill-title">Strateji Geliştirme</h3>
                    <p class="skill-description">
                        Kazanç optimizasyonu ve risk yönetimi stratejileri geliştirme.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Experience Timeline -->
    <section class="experience-timeline">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="casino-section-title" data-aos="fade-up">
                    <i class="fas fa-history"></i> DENEYIM TARİHÇESİ
                </h2>
                <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                    Casino yayıncılığı yolculuğumdaki önemli dönüm noktaları
                </p>
            </div>
            
            <div class="timeline">
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-content">
                        <div class="timeline-date">2024</div>
                        <h3 class="timeline-title">Premium Casino Partner</h3>
                        <p class="timeline-description">
                            Önde gelen casino sitelerinin resmi partneri olarak özel bonuslar ve kampanyalar sunuyor.
                        </p>
                    </div>
                    <div class="timeline-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
                
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-content">
                        <div class="timeline-date">2023</div>
                        <h3 class="timeline-title">50K Takipçi Başarısı</h3>
                        <p class="timeline-description">
                            Sosyal medya platformlarında toplam 50,000 takipçiye ulaşarak büyük bir milestona imza attı.
                        </p>
                    </div>
                    <div class="timeline-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-content">
                        <div class="timeline-date">2022</div>
                        <h3 class="timeline-title">Büyük Kazanç Rekoru</h3>
                        <p class="timeline-description">
                            Canlı yayında gerçekleştirilen en büyük kazanç rekorunu kırarak izleyicileri şaşırttı.
                        </p>
                    </div>
                    <div class="timeline-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-content">
                        <div class="timeline-date">2021</div>
                        <h3 class="timeline-title">Profesyonel Yayıncılık</h3>
                        <p class="timeline-description">
                            Düzenli canlı yayınlara başlayarak profesyonel casino yayıncısı kimliğini oluşturdu.
                        </p>
                    </div>
                    <div class="timeline-icon">
                        <i class="fas fa-video"></i>
                    </div>
                </div>
                
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-content">
                        <div class="timeline-date">2019</div>
                        <h3 class="timeline-title">Casino Yolculuğu Başlangıcı</h3>
                        <p class="timeline-description">
                            Casino dünyasına ilk adımını atarak oyun stratejileri ve teknikleri öğrenmeye başladı.
                        </p>
                    </div>
                    <div class="timeline-icon">
                        <i class="fas fa-dice"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="casino-section text-center" style="padding: 5rem 0;">
        <div class="container">
            <h2 class="casino-section-title" data-aos="fade-up">
                <i class="fas fa-handshake"></i> İŞBİRLİĞİ YAPALIM
            </h2>
            <p class="text-silver mb-4" data-aos="fade-up" data-aos-delay="200">
                Profesyonel casino yayıncılığı hizmetleri için benimle iletişime geçin.
            </p>
            <button class="contact-cta" data-aos="fade-up" data-aos-delay="400" onclick="window.location.href='contact.php'">
                <i class="fas fa-envelope"></i> İletişime Geç
            </button>
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
                color: { value: ['#FFD700', '#FF1493', '#00FFFF', '#8A2BE2'] },
                shape: { type: 'circle' },
                opacity: { value: 0.6, random: true },
                size: { value: 4, random: true },
                line_linked: { enable: true, distance: 150, color: '#FFD700', opacity: 0.2, width: 1 },
                move: { enable: true, speed: 2, direction: 'none', random: true, straight: false, out_mode: 'out' }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'repulse' },
                    onclick: { enable: true, mode: 'push' }
                }
            }
        });

        // Mobile Navigation Toggle
        function toggleMobileMenu() {
            const navMenu = document.getElementById('navMenu');
            const toggleBtn = document.querySelector('.mobile-nav-toggle i');
            
            navMenu.classList.toggle('active');
            
            // Toggle hamburger/close icon
            if (navMenu.classList.contains('active')) {
                toggleBtn.classList.remove('fa-bars');
                toggleBtn.classList.add('fa-times');
            } else {
                toggleBtn.classList.remove('fa-times');
                toggleBtn.classList.add('fa-bars');
            }
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('navMenu');
            const toggleBtn = document.querySelector('.mobile-nav-toggle');
            
            if (!navMenu.contains(event.target) && !toggleBtn.contains(event.target)) {
                navMenu.classList.remove('active');
                document.querySelector('.mobile-nav-toggle i').classList.remove('fa-times');
                document.querySelector('.mobile-nav-toggle i').classList.add('fa-bars');
            }
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.casino-nav-link').forEach(link => {
            link.addEventListener('click', function() {
                const navMenu = document.getElementById('navMenu');
                navMenu.classList.remove('active');
                document.querySelector('.mobile-nav-toggle i').classList.remove('fa-times');
                document.querySelector('.mobile-nav-toggle i').classList.add('fa-bars');
            });
        });
        
        // Back to top
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
                }, 20);
            });
        }

        // Trigger stat animation when in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(document.querySelector('.achievement-stats'));
    </script>
</body>
</html>
<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();

// Get data with error handling
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
    
    <!-- Google Fonts - Orbitron for Casino Theme -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Enhanced Casino Theme CSS -->
    <link href="assets/css/casino-enhanced.css" rel="stylesheet">
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
            --casino-green: #228B22;
            --casino-blue: #4169E1;
            --casino-purple: #8A2BE2;
            --neon-pink: #FF1493;
            --neon-cyan: #00FFFF;
            --neon-lime: #32CD32;
            --text-light: #FFFFFF;
            --text-gold: #FFD700;
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
        
        .casino-navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--casino-gold), var(--neon-pink), var(--neon-cyan), var(--casino-gold), transparent);
            animation: neonFlow 3s ease-in-out infinite;
        }
        
        @keyframes neonFlow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
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
            position: relative;
        }
        
        .casino-brand::before {
            content: '🎰';
            position: absolute;
            left: -40px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            animation: spin 4s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }
        
        .casino-nav-link {
            color: var(--text-silver);
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0 15px;
            text-decoration: none;
            position: relative;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .casino-nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--casino-gold), var(--neon-pink));
            transition: width 0.3s ease;
        }
        
        .casino-nav-link:hover,
        .casino-nav-link.active {
            color: var(--casino-gold);
            text-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        .casino-nav-link:hover::after,
        .casino-nav-link.active::after {
            width: 100%;
        }
        
        /* Casino Hero Section */
        .casino-hero {
            height: 100vh;
            background: linear-gradient(135deg, var(--casino-black) 0%, var(--casino-dark) 50%, var(--casino-black) 100%);
            display: flex;
            align-items: center;
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
                radial-gradient(circle at 80% 20%, var(--casino-blue) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, var(--casino-purple) 0%, transparent 50%);
            opacity: 0.1;
            animation: colorShift 8s ease-in-out infinite;
        }
        
        @keyframes colorShift {
            0%, 100% { opacity: 0.1; }
            50% { opacity: 0.3; }
        }
        
        .casino-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        
        .casino-hero-title {
            font-family: 'Orbitron', monospace;
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan), var(--casino-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: var(--shadow-neon) var(--casino-gold);
            animation: textGlow 2s ease-in-out infinite alternate;
        }
        
        @keyframes textGlow {
            0% { filter: brightness(1); }
            100% { filter: brightness(1.2); }
        }
        
        .casino-hero-subtitle {
            font-size: 1.5rem;
            color: var(--text-silver);
            margin-bottom: 2rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .casino-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .casino-stat {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(220, 20, 60, 0.1));
            border: 2px solid var(--casino-gold);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        
        .casino-stat::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan), var(--casino-gold));
            border-radius: 15px;
            z-index: -1;
            animation: borderGlow 2s linear infinite;
        }
        
        @keyframes borderGlow {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }
        
        .casino-stat-number {
            font-family: 'Orbitron', monospace;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--casino-gold);
            display: block;
            text-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        .casino-stat-label {
            font-size: 1rem;
            color: var(--text-silver);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .casino-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        .casino-btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .casino-btn-primary {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            box-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        .casino-btn-secondary {
            background: transparent;
            color: var(--casino-gold);
            border: 2px solid var(--casino-gold);
        }
        
        .casino-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .casino-btn:hover::before {
            left: 100%;
        }
        
        .casino-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
        }
        
        /* Casino Sections */
        .casino-section {
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
        
        /* Casino Cards */
        .casino-card {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(220, 20, 60, 0.1));
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .casino-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--casino-gold), var(--neon-pink), var(--neon-cyan), var(--casino-gold));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .casino-card:hover::before {
            opacity: 1;
        }
        
        .casino-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--neon-pink);
        }
        
        .casino-card-icon {
            font-size: 4rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            display: block;
        }
        
        .casino-card-title {
            font-family: 'Orbitron', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--casino-gold);
            text-transform: uppercase;
        }
        
        .casino-card-description {
            color: var(--text-silver);
            line-height: 1.6;
            font-size: 1.1rem;
        }
        
        /* Portfolio Items */
        .casino-portfolio-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 2px solid var(--casino-gold);
        }
        
        .casino-portfolio-item:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-heavy);
        }
        
        .casino-portfolio-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .casino-portfolio-item:hover .casino-portfolio-image {
            transform: scale(1.1);
        }
        
        .casino-portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(255, 215, 0, 0.9) 0%, 
                rgba(220, 20, 60, 0.9) 50%, 
                rgba(255, 20, 147, 0.9) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .casino-portfolio-item:hover .casino-portfolio-overlay {
            opacity: 1;
        }
        
        .casino-portfolio-info {
            text-align: center;
            color: var(--casino-black);
        }
        
        .casino-portfolio-info h4 {
            font-family: 'Orbitron', monospace;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        /* Gallery */
        .casino-gallery-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid var(--casino-gold);
        }
        
        .casino-gallery-item:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-neon) var(--neon-pink);
        }
        
        .casino-gallery-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .casino-gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(255, 215, 0, 0.8), 
                rgba(255, 20, 147, 0.8));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .casino-gallery-item:hover .casino-gallery-overlay {
            opacity: 1;
        }
        
        .casino-gallery-overlay i {
            font-size: 3rem;
            color: var(--casino-black);
        }
        
        /* Contact Section */
        .casino-contact-section {
            background: linear-gradient(135deg, var(--casino-dark) 0%, var(--casino-black) 100%);
        }
        
        .casino-contact-card {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(220, 20, 60, 0.1));
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .casino-contact-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--neon-pink);
        }
        
        .casino-contact-icon {
            font-size: 3rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .casino-contact-title {
            font-family: 'Orbitron', monospace;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--casino-gold);
            text-transform: uppercase;
        }
        
        .casino-contact-info a {
            color: var(--text-silver);
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        
        .casino-contact-info a:hover {
            color: var(--casino-gold);
            text-shadow: var(--shadow-neon) var(--casino-gold);
        }
        
        /* Footer */
        .casino-footer {
            background: var(--casino-black);
            border-top: 2px solid var(--casino-gold);
            padding: 60px 0 30px;
            position: relative;
        }
        
        .casino-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--casino-gold), var(--neon-pink), var(--neon-cyan), var(--casino-gold), transparent);
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
            text-align: center;
        }
        
        .casino-footer-text {
            color: var(--text-silver);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .casino-social-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .casino-social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            border-radius: 50%;
            color: var(--casino-black);
            text-decoration: none;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .casino-social-link:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: var(--shadow-neon) var(--neon-pink);
        }
        
        .casino-footer-bottom {
            border-top: 1px solid var(--casino-gold);
            padding-top: 30px;
            text-align: center;
            color: var(--text-silver);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .casino-hero-title {
                font-size: 2.5rem;
            }
            
            .casino-hero-subtitle {
                font-size: 1.2rem;
            }
            
            .casino-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .casino-section-title h2 {
                font-size: 2rem;
            }
            
            .casino-card {
                margin-bottom: 30px;
            }
        }
        
        /* Loading Animation */
        .casino-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--casino-black);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        
        .casino-loading.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .casino-loader {
            font-size: 4rem;
            color: var(--casino-gold);
            animation: spin 2s linear infinite;
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="casino-loading" id="casinoLoading">
        <div class="casino-loader">
            <i class="fas fa-dice-d20"></i>
        </div>
    </div>

    <!-- Particles Background -->
    <div id="particles-js"></div>

    <!-- Casino Navigation -->
    <nav class="casino-navbar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="#home" class="casino-brand">BERAT K</a>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <a href="#home" class="casino-nav-link active">Ana Sayfa</a>
                        <a href="#services" class="casino-nav-link">Hizmetler</a>
                        <a href="#portfolio" class="casino-nav-link">Portföy</a>
                        <a href="#gallery" class="casino-nav-link">Galeri</a>
                        <a href="#contact" class="casino-nav-link">İletişim</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Casino Hero Section -->
    <section id="home" class="casino-hero">
        <div class="container">
            <div class="casino-hero-content" data-aos="fade-up">
                <h1 class="casino-hero-title">CASINO YAYINCISI</h1>
                <p class="casino-hero-subtitle">Profesyonel Casino Streaming & Dijital Pazarlama</p>
                
                <div class="casino-stats">
                    <div class="casino-stat" data-aos="fade-up" data-aos-delay="200">
                        <span class="casino-stat-number">100K+</span>
                        <span class="casino-stat-label">Takipçi</span>
                    </div>
                    <div class="casino-stat" data-aos="fade-up" data-aos-delay="400">
                        <span class="casino-stat-number">2000+</span>
                        <span class="casino-stat-label">Canlı Yayın</span>
                    </div>
                    <div class="casino-stat" data-aos="fade-up" data-aos-delay="600">
                        <span class="casino-stat-number">5+</span>
                        <span class="casino-stat-label">Yıl Deneyim</span>
                    </div>
                </div>
                
                <div class="casino-buttons">
                    <a href="#services" class="casino-btn casino-btn-primary" data-aos="fade-up" data-aos-delay="800">
                        <i class="fas fa-play me-2"></i>
                        Hizmetlerim
                    </a>
                    <a href="#contact" class="casino-btn casino-btn-secondary" data-aos="fade-up" data-aos-delay="1000">
                        <i class="fas fa-envelope me-2"></i>
                        İletişim
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="casino-section">
        <div class="container">
            <div class="casino-section-title" data-aos="fade-up">
                <h2>HİZMETLERİM</h2>
                <p>Casino dünyasında profesyonel hizmetler ile başarıya ulaşın</p>
            </div>
            
            <div class="row">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 200; ?>">
                            <div class="casino-card">
                                <div class="casino-card-icon">
                                    <i class="<?php echo htmlspecialchars($service['icon'] ?? 'fas fa-cog'); ?>"></i>
                                </div>
                                <h3 class="casino-card-title"><?php echo htmlspecialchars($service['title'] ?? ''); ?></h3>
                                <p class="casino-card-description"><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p style="color: var(--text-silver); font-size: 1.2rem;">Henüz hizmet eklenmemiş.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="casino-section" style="background: linear-gradient(135deg, var(--casino-dark) 0%, var(--casino-black) 100%);">
        <div class="container">
            <div class="casino-section-title" data-aos="fade-up">
                <h2>PORTFÖYÜM</h2>
                <p>Gerçekleştirdiğim başarılı projeler ve casino deneyimleri</p>
            </div>
            
            <div class="row">
                <?php if (!empty($portfolio)): ?>
                    <?php foreach ($portfolio as $index => $item): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 200; ?>">
                            <div class="casino-portfolio-item">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="uploads/portfolio/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" 
                                         class="casino-portfolio-image">
                                <?php else: ?>
                                    <div class="casino-portfolio-image" style="background: linear-gradient(135deg, var(--casino-gold), var(--neon-pink)); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x" style="color: var(--casino-black);"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="casino-portfolio-overlay">
                                    <div class="casino-portfolio-info">
                                        <h4><?php echo htmlspecialchars($item['title'] ?? ''); ?></h4>
                                        <p><?php echo htmlspecialchars(substr($item['description'] ?? '', 0, 100)); ?>...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p style="color: var(--text-silver); font-size: 1.2rem;">Henüz portföy projesi eklenmemiş.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="casino-section">
        <div class="container">
            <div class="casino-section-title" data-aos="fade-up">
                <h2>GALERİ</h2>
                <p>Canlı yayınlarımdan ve casino deneyimlerimden özel kareler</p>
            </div>
            
            <div class="row">
                <?php if (!empty($gallery)): ?>
                    <?php foreach ($gallery as $index => $item): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="casino-gallery-item">
                                <?php if (!empty($item['file_path'])): ?>
                                    <img src="uploads/gallery/<?php echo htmlspecialchars($item['file_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title'] ?? 'Galeri Görseli'); ?>" 
                                         class="casino-gallery-image">
                                <?php else: ?>
                                    <div class="casino-gallery-image" style="background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink)); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-2x" style="color: var(--casino-black);"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="casino-gallery-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p style="color: var(--text-silver); font-size: 1.2rem;">Henüz galeri görseli eklenmemiş.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="casino-section casino-contact-section">
        <div class="container">
            <div class="casino-section-title" data-aos="fade-up">
                <h2>İLETİŞİM</h2>
                <p>Benimle iletişime geçin ve birlikte büyük kazançlar elde edelim</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="casino-contact-card">
                        <div class="casino-contact-icon">
                            <i class="fab fa-twitch"></i>
                        </div>
                        <h3 class="casino-contact-title">Twitch</h3>
                        <div class="casino-contact-info">
                            <a href="https://twitch.tv/beratk" target="_blank">twitch.tv/beratk</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="casino-contact-card">
                        <div class="casino-contact-icon">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h3 class="casino-contact-title">YouTube</h3>
                        <div class="casino-contact-info">
                            <a href="https://youtube.com/@beratk" target="_blank">youtube.com/@beratk</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="casino-contact-card">
                        <div class="casino-contact-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h3 class="casino-contact-title">Telegram</h3>
                        <div class="casino-contact-info">
                            <a href="https://t.me/beratk" target="_blank">@beratk</a>
                        </div>
                    </div>
                </div>
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
            
            <div class="casino-social-links">
                <a href="https://twitch.tv/beratk" class="casino-social-link" target="_blank">
                    <i class="fab fa-twitch"></i>
                </a>
                <a href="https://youtube.com/@beratk" class="casino-social-link" target="_blank">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://t.me/beratk" class="casino-social-link" target="_blank">
                    <i class="fab fa-telegram"></i>
                </a>
                <a href="https://instagram.com/beratk" class="casino-social-link" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
            
            <div class="casino-footer-bottom">
                <p>&copy; 2024 BERAT K - Tüm hakları saklıdır. | 🎲 Casino Yayıncılığında Lider 🎲</p>
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
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: ['#FFD700', '#DC143C', '#FF1493', '#00FFFF']
                },
                shape: {
                    type: 'circle',
                    stroke: {
                        width: 0,
                        color: '#000000'
                    }
                },
                opacity: {
                    value: 0.5,
                    random: false,
                    anim: {
                        enable: false,
                        speed: 1,
                        opacity_min: 0.1,
                        sync: false
                    }
                },
                size: {
                    value: 3,
                    random: true,
                    anim: {
                        enable: false,
                        speed: 40,
                        size_min: 0.1,
                        sync: false
                    }
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
                    bounce: false,
                    attract: {
                        enable: false,
                        rotateX: 600,
                        rotateY: 1200
                    }
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
                    grab: {
                        distance: 400,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    bubble: {
                        distance: 400,
                        size: 40,
                        duration: 2,
                        opacity: 8,
                        speed: 3
                    },
                    repulse: {
                        distance: 200,
                        duration: 0.4
                    },
                    push: {
                        particles_nb: 4
                    },
                    remove: {
                        particles_nb: 2
                    }
                }
            },
            retina_detect: true
        });
        
        // Loading Screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('casinoLoading').classList.add('hidden');
            }, 1000);
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
            const navLinks = document.querySelectorAll('.casino-nav-link');
            
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
            const counters = document.querySelectorAll('.casino-stat-number');
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
                    
                    if (counter.textContent.includes('K')) {
                        counter.textContent = Math.floor(current / 1000) + 'K+';
                    } else {
                        counter.textContent = Math.floor(current) + '+';
                    }
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
        
        observer.observe(document.querySelector('.casino-hero'));
        
        // Casino sound effects (optional)
        function playSlotSound() {
            // You can add casino sound effects here
            console.log('🎰 Casino sound effect!');
        }
        
        // Add click sound to buttons
        document.querySelectorAll('.casino-btn').forEach(btn => {
            btn.addEventListener('click', playSlotSound);
        });
    </script>
    
    <!-- Enhanced Casino JavaScript -->
    <script src="assets/js/casino-enhanced.js"></script>
</body>
</html>
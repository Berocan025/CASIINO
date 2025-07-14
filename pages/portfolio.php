<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$portfolio = $db->findAll('portfolio', ['status' => 'active'], 'order_position ASC');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portföy - BERAT K Casino Yayıncısı</title>
    <meta name="description" content="BERAT K'nın casino yayıncılığı ve dijital pazarlama alanında gerçekleştirdiği başarılı projeler">
    
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
            text-decoration: none;
        }
        
        .navbar-nav .nav-link.active {
            color: var(--gold-color);
        }
        
        /* Hero Section */
        .hero-section {
            padding: 120px 0 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
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
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }
        
        /* Portfolio Section */
        .portfolio-section {
            padding: 100px 0;
        }
        
        .portfolio-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
        
        .portfolio-item:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
        }
        
        .portfolio-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .portfolio-item:hover .portfolio-image {
            transform: scale(1.05);
        }
        
        .portfolio-content {
            padding: 2rem;
        }
        
        .portfolio-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-light);
        }
        
        .portfolio-description {
            color: var(--text-muted);
            line-height: 1.6;
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
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../index.php">Ana Sayfa</a>
                <a class="nav-link" href="services.php">Hizmetler</a>
                <a class="nav-link active" href="portfolio.php">Portföy</a>
                <a class="nav-link" href="gallery.php">Galeri</a>
                <a class="nav-link" href="contact.php">İletişim</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1 class="hero-title">Portföyüm</h1>
                <p class="hero-subtitle">
                    Casino yayıncılığı ve dijital pazarlama alanında gerçekleştirdiğim başarılı projeler
                </p>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="portfolio-section">
        <div class="container">
            <div class="row">
                <?php if (!empty($portfolio)): ?>
                    <?php foreach ($portfolio as $index => $item): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            <div class="portfolio-item">
                                <?php if ($item['image']): ?>
                                    <img src="../uploads/portfolio/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                         class="portfolio-image">
                                <?php else: ?>
                                    <div class="portfolio-image" style="background: var(--gradient-primary); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x" style="color: rgba(255,255,255,0.3);"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="portfolio-content">
                                    <h3 class="portfolio-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p class="portfolio-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                </div>
                                
                                <div class="portfolio-overlay">
                                    <div class="portfolio-info">
                                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>...</p>
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
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>
</html>
<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $messageContent = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($messageContent)) {
        $message = 'Lütfen tüm alanları doldurun.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Geçerli bir e-posta adresi girin.';
        $messageType = 'error';
    } else {
        try {
            $result = $db->insert('messages', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $messageContent,
                'status' => 'unread',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($result) {
                $message = 'Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağım.';
                $messageType = 'success';
            } else {
                $message = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.';
            $messageType = 'error';
        }
    }
}

$pageTitle = 'İletişim - BERAT K Casino Yayıncısı';
$metaDescription = 'BERAT K ile iletişime geçin. Casino yayıncılığı ve dijital pazarlama hizmetleri için benimle iletişime geçin.';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="casino iletişim, berat k iletişim, casino yayıncısı iletişim">
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
        
        /* Contact Section */
        .casino-contact-section {
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
        
        /* Contact Cards */
        .casino-contact-card {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(220, 20, 60, 0.1));
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .casino-contact-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
            border-color: var(--neon-pink);
        }
        
        .casino-contact-icon {
            font-size: 3.5rem;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
        }
        
        .casino-contact-title {
            font-family: 'Orbitron', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--casino-gold);
            text-transform: uppercase;
        }
        
        .casino-contact-info {
            color: var(--text-silver);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        
        .casino-contact-link {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .casino-contact-link:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-neon) var(--casino-gold);
            color: var(--casino-black);
        }
        
        /* Contact Form */
        .casino-form-container {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(220, 20, 60, 0.1));
            backdrop-filter: blur(15px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 3rem;
            margin-top: 3rem;
        }
        
        .casino-form-title {
            font-family: 'Orbitron', monospace;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            color: var(--casino-gold);
            text-transform: uppercase;
        }
        
        .casino-form-group {
            margin-bottom: 1.5rem;
        }
        
        .casino-form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--casino-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .casino-form-input,
        .casino-form-textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--casino-gold);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .casino-form-input:focus,
        .casino-form-textarea:focus {
            outline: none;
            border-color: var(--neon-pink);
            box-shadow: var(--shadow-neon) var(--neon-pink);
        }
        
        .casino-form-textarea {
            resize: vertical;
            min-height: 150px;
        }
        
        .casino-form-btn {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            width: 100%;
        }
        
        .casino-form-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
        }
        
        /* Alert Messages */
        .casino-alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 600;
            text-align: center;
        }
        
        .casino-alert-success {
            background: rgba(34, 139, 34, 0.2);
            border: 2px solid #228B22;
            color: #32CD32;
        }
        
        .casino-alert-error {
            background: rgba(220, 20, 60, 0.2);
            border: 2px solid var(--casino-red);
            color: var(--neon-pink);
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
            
            .casino-contact-card {
                margin-bottom: 30px;
            }
            
            .casino-form-container {
                padding: 2rem;
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
                        <a href="services.php" class="casino-nav-link">Hizmetler</a>
                        <a href="portfolio.php" class="casino-nav-link">Portföy</a>
                        <a href="gallery.php" class="casino-nav-link">Galeri</a>
                        <a href="contact.php" class="casino-nav-link active">İletişim</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="casino-hero">
        <div class="container">
            <div class="casino-hero-content" data-aos="fade-up">
                <h1 class="casino-hero-title">💬 İLETİŞİM 💬</h1>
                <p class="casino-hero-subtitle">
                    Benimle iletişime geçin ve birlikte büyük kazançlar elde edelim
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="casino-contact-section">
        <div class="container">
            <div class="casino-section-title" data-aos="fade-up">
                <h2>İLETİŞİM KANALLARI</h2>
                <p>Casino dünyasında başarılı olmak için benimle iletişime geçin</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="casino-contact-card">
                        <div class="casino-contact-icon">
                            <i class="fab fa-twitch"></i>
                        </div>
                        <h3 class="casino-contact-title">Twitch</h3>
                        <p class="casino-contact-info">
                            Canlı casino yayınlarımı takip edin
                        </p>
                        <a href="https://twitch.tv/beratk" target="_blank" class="casino-contact-link">
                            🎮 TAKİP ET
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="casino-contact-card">
                        <div class="casino-contact-icon">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h3 class="casino-contact-title">YouTube</h3>
                        <p class="casino-contact-info">
                            Casino stratejileri ve kazanç videoları
                        </p>
                        <a href="https://youtube.com/@beratk" target="_blank" class="casino-contact-link">
                            📺 İZLE
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="casino-contact-card">
                        <div class="casino-contact-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h3 class="casino-contact-title">Telegram</h3>
                        <p class="casino-contact-info">
                            Özel casino ipuçları ve bonuslar
                        </p>
                        <a href="https://t.me/beratk" target="_blank" class="casino-contact-link">
                            💬 KATIL
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="casino-form-container" data-aos="fade-up" data-aos-delay="800">
                <h3 class="casino-form-title">🎰 MESAJ GÖNDER 🎰</h3>
                
                <?php if ($message): ?>
                    <div class="casino-alert casino-alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="casino-form-group">
                                <label for="name" class="casino-form-label">Ad Soyad</label>
                                <input type="text" id="name" name="name" class="casino-form-input" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="casino-form-group">
                                <label for="email" class="casino-form-label">E-posta</label>
                                <input type="email" id="email" name="email" class="casino-form-input" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="casino-form-group">
                        <label for="subject" class="casino-form-label">Konu</label>
                        <input type="text" id="subject" name="subject" class="casino-form-input" required>
                    </div>
                    
                    <div class="casino-form-group">
                        <label for="message" class="casino-form-label">Mesaj</label>
                        <textarea id="message" name="message" class="casino-form-textarea" required></textarea>
                    </div>
                    
                    <button type="submit" class="casino-form-btn">
                        🎰 MESAJ GÖNDER 🎰
                    </button>
                </form>
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
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !subject || !message) {
                e.preventDefault();
                alert('Lütfen tüm alanları doldurun.');
                return;
            }
            
            if (!email.includes('@') || !email.includes('.')) {
                e.preventDefault();
                alert('Geçerli bir e-posta adresi girin.');
                return;
            }
        });
    </script>
</body>
</html>
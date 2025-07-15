<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Lütfen tüm zorunlu alanları doldurun.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Geçerli bir e-posta adresi girin.';
    } else {
        // Save to database
        try {
            $data = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message,
                'status' => 'new',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('messages', $data);
            $success_message = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağım.';
            
            // Clear form data
            $name = $email = $phone = $subject = $message = '';
            
        } catch (Exception $e) {
            $error_message = 'Mesaj gönderilemedi. Lütfen daha sonra tekrar deneyin.';
        }
    }
}

$pageTitle = 'İletişim - BERAT K Casino Yayıncısı';
$metaDescription = 'Profesyonel casino yayıncılığı hizmetleri için benimle iletişime geçin. Hemen teklif alın!';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta name="keywords" content="casino yayıncısı iletişim, teklif al, casino hizmetleri, iş birliği">
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
        /* CONTACT PAGE SPECIAL EFFECTS */
        .contact-hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #000000 0%, #1a0033 20%, #330066 40%, #660033 60%, #330000 80%, #000000 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M20,50 Q50,20 80,50 Q50,80 20,50" fill="none" stroke="rgba(255,215,0,0.1)" stroke-width="1"/><path d="M50,20 Q80,50 50,80 Q20,50 50,20" fill="none" stroke="rgba(255,20,147,0.1)" stroke-width="1"/><circle cx="50" cy="50" r="3" fill="rgba(0,255,255,0.2)"/></svg>') repeat;
            background-size: 100px 100px;
            animation: wave-pattern 15s linear infinite;
        }

        @keyframes wave-pattern {
            0% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(-25px) translateY(25px); }
            50% { transform: translateX(50px) translateY(-50px); }
            75% { transform: translateX(-75px) translateY(75px); }
            100% { transform: translateX(0) translateY(0); }
        }

        .contact-hero-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .contact-hero-title {
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

        .contact-hero-subtitle {
            font-size: 1.5rem;
            color: var(--text-silver);
            margin-bottom: 2rem;
            text-shadow: 0 0 20px rgba(192,192,192,0.5);
        }

        .contact-info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .contact-info-card {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(20px);
            border: 2px solid var(--casino-gold);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .contact-info-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,215,0,0.1) 0%, transparent 70%);
            animation: pulse-glow 3s ease-in-out infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .contact-info-card:hover::before {
            opacity: 1;
        }

        .contact-info-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(255,215,0,0.3);
            border-color: var(--neon-pink);
        }

        .contact-info-icon {
            font-size: 3rem;
            color: var(--casino-gold);
            margin-bottom: 1rem;
            animation: bounce 2s ease-in-out infinite;
        }

        .contact-info-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--casino-gold);
            margin-bottom: 1rem;
        }

        .contact-info-text {
            color: var(--text-silver);
            line-height: 1.6;
        }

        .contact-form-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            padding: 5rem 0;
            position: relative;
        }

        .contact-form-container {
            max-width: 800px;
            margin: 0 auto;
            background: linear-gradient(145deg, rgba(0,0,0,0.95), rgba(26,26,26,0.95));
            backdrop-filter: blur(20px);
            border: 2px solid var(--casino-gold);
            border-radius: 30px;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .contact-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,215,0,0.05), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .contact-form {
            position: relative;
            z-index: 2;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            color: var(--casino-gold);
            margin-bottom: 0.5rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control {
            background: rgba(0,0,0,0.7);
            border: 2px solid var(--casino-gold);
            border-radius: 15px;
            padding: 1rem;
            color: var(--text-white);
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            background: rgba(0,0,0,0.9);
            border-color: var(--neon-pink);
            box-shadow: 0 0 20px rgba(255,20,147,0.3);
            color: var(--text-white);
        }

        .form-control::placeholder {
            color: var(--text-silver);
            opacity: 0.7;
        }

        .contact-submit-btn {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
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
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .contact-submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .contact-submit-btn:hover::before {
            left: 100%;
        }

        .contact-submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255,215,0,0.5);
        }

        .alert {
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            font-weight: 600;
            position: relative;
            z-index: 3;
        }

        .alert-success {
            background: linear-gradient(45deg, rgba(0,255,65,0.2), rgba(0,255,65,0.1));
            color: var(--neon-green);
            border: 2px solid var(--neon-green);
        }

        .alert-danger {
            background: linear-gradient(45deg, rgba(255,7,58,0.2), rgba(255,7,58,0.1));
            color: var(--neon-red);
            border: 2px solid var(--neon-red);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
            border-radius: 50%;
            font-size: 1.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255,215,0,0.3);
        }

        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 15px 40px rgba(255,215,0,0.5);
            color: var(--casino-black);
        }

        .availability-section {
            background: linear-gradient(135deg, #000000 0%, #2c0014 100%);
            padding: 5rem 0;
            text-align: center;
        }

        .availability-card {
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(20px);
            border: 2px solid var(--casino-gold);
            border-radius: 25px;
            padding: 3rem;
            margin: 0 auto;
            max-width: 600px;
            position: relative;
            overflow: hidden;
        }

        .availability-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,215,0,0.05), transparent);
            animation: rotate 8s linear infinite;
        }

        .availability-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--casino-gold);
            margin-bottom: 2rem;
            text-shadow: 0 0 30px var(--casino-gold);
        }

        .availability-time {
            font-size: 1.5rem;
            color: var(--neon-green);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .availability-status {
            display: inline-block;
            background: linear-gradient(45deg, var(--neon-green), var(--casino-gold));
            color: var(--casino-black);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: pulse 2s ease-in-out infinite;
        }

        .working-hours {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .working-hour {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0,0,0,0.7);
            border-radius: 15px;
            border: 1px solid var(--casino-gold);
        }

        .working-day {
            color: var(--casino-gold);
            font-weight: 600;
        }

        .working-time {
            color: var(--text-silver);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .contact-hero-title {
                font-size: 2.5rem;
            }
            
            .contact-info-cards {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .contact-form-container {
                padding: 2rem;
            }
            
            .social-links {
                gap: 1rem;
            }
            
            .working-hours {
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
                    <a href="portfolio.php" class="casino-nav-item">Portfolyo</a>
                    <a href="gallery.php" class="casino-nav-item">Galeri</a>
                    <a href="contact.php" class="casino-nav-item active">İletişim</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <div class="contact-hero-content">
                <h1 class="contact-hero-title" data-aos="fade-up">
                    <i class="fas fa-envelope"></i> İLETİŞİM
                </h1>
                <p class="contact-hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                    Profesyonel Casino Yayıncılığı İçin Benimle İletişime Geçin
                </p>
                
                <!-- Contact Info Cards -->
                <div class="contact-info-cards">
                    <div class="contact-info-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3 class="contact-info-title">Telefon</h3>
                        <p class="contact-info-text">
                            +90 555 123 4567<br>
                            7/24 Ulaşılabilir
                        </p>
                    </div>
                    
                    <div class="contact-info-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="contact-info-title">E-posta</h3>
                        <p class="contact-info-text">
                            info@beratk.com<br>
                            24 Saat İçinde Yanıt
                        </p>
                    </div>
                    
                    <div class="contact-info-card" data-aos="fade-up" data-aos-delay="500">
                        <div class="contact-info-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h3 class="contact-info-title">WhatsApp</h3>
                        <p class="contact-info-text">
                            +90 555 123 4567<br>
                            Anında Mesajlaşma
                        </p>
                    </div>
                    
                    <div class="contact-info-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="contact-info-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h3 class="contact-info-title">Telegram</h3>
                        <p class="contact-info-text">
                            @BeratKCasino<br>
                            Güvenli İletişim
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="casino-section-title" data-aos="fade-up">
                    <i class="fas fa-paper-plane"></i> MESAJ GÖNDERİN
                </h2>
                <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                    Hizmetlerimiz hakkında bilgi almak ve teklif istemek için formu doldurun
                </p>
            </div>
            
            <div class="contact-form-container" data-aos="fade-up" data-aos-delay="300">
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form class="contact-form" method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">
                                    <i class="fas fa-user"></i> Ad Soyad *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                       placeholder="Adınızı ve soyadınızı girin"
                                       required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="email">
                                    <i class="fas fa-envelope"></i> E-posta *
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       placeholder="E-posta adresinizi girin"
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="phone">
                                    <i class="fas fa-phone"></i> Telefon
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                                       placeholder="Telefon numaranızı girin">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="subject">
                                    <i class="fas fa-tag"></i> Konu *
                                </label>
                                <select class="form-control" id="subject" name="subject" required>
                                    <option value="">Konu seçin</option>
                                    <option value="Casino Yayıncılığı" <?php echo ($subject ?? '') === 'Casino Yayıncılığı' ? 'selected' : ''; ?>>Casino Yayıncılığı</option>
                                    <option value="Dijital Pazarlama" <?php echo ($subject ?? '') === 'Dijital Pazarlama' ? 'selected' : ''; ?>>Dijital Pazarlama</option>
                                    <option value="Telegram Yönetimi" <?php echo ($subject ?? '') === 'Telegram Yönetimi' ? 'selected' : ''; ?>>Telegram Yönetimi</option>
                                    <option value="İş Ortaklığı" <?php echo ($subject ?? '') === 'İş Ortaklığı' ? 'selected' : ''; ?>>İş Ortaklığı</option>
                                    <option value="Diğer" <?php echo ($subject ?? '') === 'Diğer' ? 'selected' : ''; ?>>Diğer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">
                            <i class="fas fa-comment"></i> Mesaj *
                        </label>
                        <textarea class="form-control" 
                                  id="message" 
                                  name="message" 
                                  rows="5" 
                                  placeholder="Mesajınızı detaylı bir şekilde yazın..."
                                  required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="contact-submit-btn">
                        <i class="fas fa-rocket"></i> Mesaj Gönder
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Availability Section -->
    <section class="availability-section">
        <div class="container">
            <div class="availability-card" data-aos="fade-up">
                <h2 class="availability-title">
                    <i class="fas fa-clock"></i> MÜSAİTLİK DURUMU
                </h2>
                <div class="availability-time">
                    Şu anda: <span id="currentTime"></span>
                </div>
                <div class="availability-status">
                    <i class="fas fa-circle" style="color: #00ff41;"></i> AKTİF
                </div>
                
                <div class="working-hours">
                    <div class="working-hour">
                        <span class="working-day">Pazartesi</span>
                        <span class="working-time">09:00 - 18:00</span>
                    </div>
                    <div class="working-hour">
                        <span class="working-day">Salı</span>
                        <span class="working-time">09:00 - 18:00</span>
                    </div>
                    <div class="working-hour">
                        <span class="working-day">Çarşamba</span>
                        <span class="working-time">09:00 - 18:00</span>
                    </div>
                    <div class="working-hour">
                        <span class="working-day">Perşembe</span>
                        <span class="working-time">09:00 - 18:00</span>
                    </div>
                    <div class="working-hour">
                        <span class="working-day">Cuma</span>
                        <span class="working-time">09:00 - 18:00</span>
                    </div>
                    <div class="working-hour">
                        <span class="working-day">Cumartesi</span>
                        <span class="working-time">10:00 - 16:00</span>
                    </div>
                    <div class="working-hour">
                        <span class="working-day">Pazar</span>
                        <span class="working-time">Kapalı</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Links -->
    <section class="casino-section text-center" style="padding: 5rem 0;">
        <div class="container">
            <h2 class="casino-section-title" data-aos="fade-up">
                <i class="fas fa-share-alt"></i> SOSYAL MEDYA
            </h2>
            <p class="text-silver" data-aos="fade-up" data-aos-delay="200">
                Beni sosyal medya hesaplarımdan takip edin
            </p>
            
            <div class="social-links" data-aos="fade-up" data-aos-delay="300">
                <a href="#" class="social-link" target="_blank" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="#" class="social-link" target="_blank" title="Twitch">
                    <i class="fab fa-twitch"></i>
                </a>
                <a href="#" class="social-link" target="_blank" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link" target="_blank" title="Telegram">
                    <i class="fab fa-telegram"></i>
                </a>
                <a href="#" class="social-link" target="_blank" title="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
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
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: ['#FFD700', '#FF1493', '#00FFFF', '#8A2BE2', '#FF8C00', '#00FF41'] },
                shape: { type: 'circle' },
                opacity: { value: 0.6, random: true },
                size: { value: 2, random: true },
                line_linked: { enable: true, distance: 150, color: '#FFD700', opacity: 0.3, width: 1 },
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

        // Back to top
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Update current time
        function updateCurrentTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('tr-TR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('currentTime').textContent = timeString;
        }

        // Update time every second
        setInterval(updateCurrentTime, 1000);
        updateCurrentTime();

        // Form validation and effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 10px 30px rgba(255,215,0,0.2)';
            });
            
            input.addEventListener('blur', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });

        // Contact form submission effects
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('.contact-submit-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';
            submitBtn.disabled = true;
        });

        // Contact info cards hover effects
        document.querySelectorAll('.contact-info-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.05)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Social links hover effects
        document.querySelectorAll('.social-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.1)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>
<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$db = new Database();
$message = '';
$messageType = '';

if ($_POST) {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $messageContent = sanitize_input($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $messageContent) {
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
                $message = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağım.';
                $messageType = 'success';
            } else {
                $message = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
            $messageType = 'error';
        }
    } else {
        $message = 'Lütfen tüm alanları doldurun.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim - BERAT K Casino Yayıncısı</title>
    <meta name="description" content="BERAT K ile iletişime geçin. Casino yayıncılığı ve dijital pazarlama hizmetleri için profesyonel destek.">
    
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
        
        /* Contact Section */
        .contact-section {
            padding: 100px 0;
        }
        
        .contact-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
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
        
        /* Contact Form */
        .contact-form {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-light);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        .btn-submit {
            background: var(--gradient-accent);
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            cursor: pointer;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-heavy);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #4fd69c;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            color: #ff6b7a;
            border: 1px solid rgba(220, 53, 69, 0.2);
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
            
            .contact-form {
                padding: 2rem;
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
                <a class="nav-link" href="portfolio.php">Portföy</a>
                <a class="nav-link" href="gallery.php">Galeri</a>
                <a class="nav-link active" href="contact.php">İletişim</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1 class="hero-title">İletişim</h1>
                <p class="hero-subtitle">
                    Benimle iletişime geçin ve birlikte çalışalım
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="contact-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="contact-icon">
                            <i class="fab fa-twitch"></i>
                        </div>
                        <h3 class="contact-title">Twitch</h3>
                        <div class="contact-info">
                            <a href="https://twitch.tv/beratk" target="_blank">twitch.tv/beratk</a>
                        </div>
                    </div>
                    
                    <div class="contact-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="contact-icon">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h3 class="contact-title">YouTube</h3>
                        <div class="contact-info">
                            <a href="https://youtube.com/@beratk" target="_blank">youtube.com/@beratk</a>
                        </div>
                    </div>
                    
                    <div class="contact-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="contact-icon">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <h3 class="contact-title">Telegram</h3>
                        <div class="contact-info">
                            <a href="https://t.me/beratk" target="_blank">@beratk</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="contact-form" data-aos="fade-up" data-aos-delay="400">
                        <h3 class="mb-4" style="color: var(--text-light);">Mesaj Gönder</h3>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Ad Soyad</label>
                                        <input type="text" id="name" name="name" class="form-control" 
                                               placeholder="Adınızı ve soyadınızı girin" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">E-posta</label>
                                        <input type="email" id="email" name="email" class="form-control" 
                                               placeholder="E-posta adresinizi girin" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject" class="form-label">Konu</label>
                                <input type="text" id="subject" name="subject" class="form-control" 
                                       placeholder="Mesajınızın konusunu girin" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message" class="form-label">Mesaj</label>
                                <textarea id="message" name="message" class="form-control" 
                                          placeholder="Mesajınızı buraya yazın..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-paper-plane me-2"></i>
                                Mesaj Gönder
                            </button>
                        </form>
                    </div>
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
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>
</html>
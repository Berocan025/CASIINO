<?php
/**
 * 404 Error Page
 * Geliştirici: BERAT K
 * Custom 404 page for casino portfolio website
 */

http_response_code(404);

require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = "Sayfa Bulunamadı - 404";
$metaDescription = "Aradığınız sayfa bulunamadı. Casino yayıncısı BERAT K'nın diğer sayfalarını keşfedin.";
$metaKeywords = "404, sayfa bulunamadı, casino yayıncısı, berat k";

include 'includes/header.php';
?>

<main class="main-content">
    <!-- 404 Hero Section -->
    <section class="error-page-hero">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-lg-8 text-center">
                    <!-- Casino Themed 404 -->
                    <div class="error-content">
                        <div class="error-number">
                            <span class="casino-chips">🎰</span>
                            <h1 class="display-1 fw-bold gradient-text">404</h1>
                            <span class="casino-chips">🎲</span>
                        </div>
                        
                        <div class="error-message mt-4">
                            <h2 class="h3 mb-3">Jackpot Kaçırdınız!</h2>
                            <p class="lead text-muted mb-4">
                                Aradığınız sayfa casino masalarında kaybolmuş gibi görünüyor. 
                                Ama endişe etmeyin, başka şanslar var!
                            </p>
                        </div>

                        <!-- Search Box -->
                        <div class="error-search mb-5">
                            <form class="search-form" onsubmit="return searchSite(event)">
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control" id="searchInput" 
                                           placeholder="Ne arıyordunuz? Burada arayın..." 
                                           autocomplete="off">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Quick Navigation -->
                        <div class="quick-nav">
                            <h4 class="mb-4">Popüler Sayfalar</h4>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="/" class="quick-nav-card">
                                        <div class="card h-100 text-center border-0 shadow-sm">
                                            <div class="card-body">
                                                <i class="fas fa-home fa-2x text-primary mb-3"></i>
                                                <h6 class="card-title">Ana Sayfa</h6>
                                                <p class="card-text small text-muted">Casino dünyama hoş geldiniz</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/about" class="quick-nav-card">
                                        <div class="card h-100 text-center border-0 shadow-sm">
                                            <div class="card-body">
                                                <i class="fas fa-user fa-2x text-success mb-3"></i>
                                                <h6 class="card-title">Hakkımda</h6>
                                                <p class="card-text small text-muted">5+ yıllık deneyimim</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/services" class="quick-nav-card">
                                        <div class="card h-100 text-center border-0 shadow-sm">
                                            <div class="card-body">
                                                <i class="fas fa-cogs fa-2x text-warning mb-3"></i>
                                                <h6 class="card-title">Hizmetler</h6>
                                                <p class="card-text small text-muted">Dijital pazarlama çözümleri</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/contact" class="quick-nav-card">
                                        <div class="card h-100 text-center border-0 shadow-sm">
                                            <div class="card-body">
                                                <i class="fas fa-envelope fa-2x text-info mb-3"></i>
                                                <h6 class="card-title">İletişim</h6>
                                                <p class="card-text small text-muted">Hemen ulaşın</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Links -->
                        <div class="error-social mt-5">
                            <h5 class="mb-3">Sosyal Medyada Takip Edin</h5>
                            <div class="social-links">
                                <a href="https://youtube.com/@beratk" class="btn btn-outline-danger me-2 mb-2" target="_blank">
                                    <i class="fab fa-youtube me-2"></i>YouTube
                                </a>
                                <a href="https://twitch.tv/beratk" class="btn btn-outline-primary me-2 mb-2" target="_blank">
                                    <i class="fab fa-twitch me-2"></i>Twitch
                                </a>
                                <a href="https://instagram.com/beratk_casino" class="btn btn-outline-info me-2 mb-2" target="_blank">
                                    <i class="fab fa-instagram me-2"></i>Instagram
                                </a>
                                <a href="https://t.me/beratk_casino" class="btn btn-outline-success mb-2" target="_blank">
                                    <i class="fab fa-telegram me-2"></i>Telegram
                                </a>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="error-actions mt-5">
                            <button class="btn btn-lg btn-primary me-3" onclick="window.history.back()">
                                <i class="fas fa-arrow-left me-2"></i>Geri Dön
                            </button>
                            <a href="/" class="btn btn-lg btn-outline-primary">
                                <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Content -->
    <section class="recent-content py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h3 class="mb-4">Son Eklenen İçerikler</h3>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-video fa-2x text-danger mb-3"></i>
                                    <h5>Canlı Yayınlar</h5>
                                    <p class="text-muted">YouTube ve Twitch'te düzenli casino yayınları</p>
                                    <a href="/portfolio" class="btn btn-sm btn-outline-primary">Detaylar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-chart-line fa-2x text-success mb-3"></i>
                                    <h5>Dijital Pazarlama</h5>
                                    <p class="text-muted">Meta reklam yönetimi ve SMS kampanyaları</p>
                                    <a href="/services" class="btn btn-sm btn-outline-primary">Detaylar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x text-info mb-3"></i>
                                    <h5>Topluluk</h5>
                                    <p class="text-muted">Casino tutkunları için özel içerikler</p>
                                    <a href="/about" class="btn btn-sm btn-outline-primary">Detaylar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.error-page-hero {
    background: linear-gradient(135deg, #021526 0%, #03346E 50%, #6EACDA 100%);
    position: relative;
    overflow: hidden;
}

.error-page-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="casino" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23casino)"/></svg>');
    animation: float 20s ease-in-out infinite;
}

.error-content {
    position: relative;
    z-index: 2;
    color: white;
}

.error-number {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.casino-chips {
    font-size: 4rem;
    animation: spin 3s linear infinite;
}

.gradient-text {
    background: linear-gradient(45deg, #E21D48, #E6DEDD, #E21D48);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientShift 3s ease-in-out infinite;
}

.quick-nav-card {
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease;
}

.quick-nav-card:hover {
    transform: translateY(-5px);
    color: inherit;
    text-decoration: none;
}

.quick-nav-card .card {
    transition: all 0.3s ease;
}

.quick-nav-card:hover .card {
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.search-form .form-control {
    border-radius: 50px 0 0 50px;
    border: 2px solid #E21D48;
    padding: 12px 20px;
}

.search-form .btn {
    border-radius: 0 50px 50px 0;
    border: 2px solid #E21D48;
    background: #E21D48;
    padding: 12px 20px;
}

.social-links .btn {
    border-radius: 50px;
    transition: all 0.3s ease;
}

.social-links .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@media (max-width: 768px) {
    .error-number {
        flex-direction: column;
        gap: 1rem;
    }
    
    .casino-chips {
        font-size: 3rem;
    }
    
    .display-1 {
        font-size: 4rem;
    }
}
</style>

<script>
// Search functionality
function searchSite(event) {
    event.preventDefault();
    const searchTerm = document.getElementById('searchInput').value.trim();
    
    if (searchTerm) {
        // Simple client-side search suggestions
        const suggestions = {
            'hakkımda': '/about',
            'about': '/about',
            'hizmet': '/services',
            'service': '/services',
            'portfoy': '/portfolio',
            'portfolio': '/portfolio',
            'iletişim': '/contact',
            'contact': '/contact',
            'youtube': 'https://youtube.com/@beratk',
            'twitch': 'https://twitch.tv/beratk',
            'casino': '/',
            'yayın': '/portfolio',
            'stream': '/portfolio'
        };
        
        const lowerTerm = searchTerm.toLowerCase();
        
        for (const [key, url] of Object.entries(suggestions)) {
            if (lowerTerm.includes(key)) {
                if (url.startsWith('http')) {
                    window.open(url, '_blank');
                } else {
                    window.location.href = url;
                }
                return false;
            }
        }
        
        // If no match found, go to homepage with search parameter
        window.location.href = '/?search=' + encodeURIComponent(searchTerm);
    }
    
    return false;
}

// Auto-focus search input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchInput').focus();
});

// Add some casino-style animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${Math.random() * 0.5}s`;
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.card').forEach((card) => {
        observer.observe(card);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
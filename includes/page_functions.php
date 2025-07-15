<?php
/**
 * Page Functions - Tüm sayfalarda kullanılacak ortak fonksiyonlar
 * Geliştirici: BERAT K
 */

// Get page content from database
function getPageContent($contentKey, $defaultValue = '') {
    global $db;
    
    if (!isset($db)) {
        return $defaultValue;
    }
    
    try {
        $content = $db->find('page_content', ['content_key' => $contentKey, 'is_active' => 1]);
        return $content ? $content['content_value'] : $defaultValue;
    } catch (Exception $e) {
        return $defaultValue;
    }
}

// Get all content for a specific page
function getPageContents($pageName) {
    global $db;
    
    if (!isset($db)) {
        return [];
    }
    
    try {
        $contents = $db->findAll('page_content', ['page_name' => $pageName, 'is_active' => 1]);
        $result = [];
        
        foreach ($contents as $content) {
            $result[$content['content_key']] = $content['content_value'];
        }
        
        return $result;
    } catch (Exception $e) {
        return [];
    }
}

// Render mobile navigation
function renderMobileNavigation($currentPage = '') {
    $navItems = [
        'index.php' => 'Ana Sayfa',
        'pages/about.php' => 'Hakkımda',
        'pages/services.php' => 'Hizmetler',
        'pages/portfolio.php' => 'Portfolyo',
        'pages/gallery.php' => 'Galeri',
        'pages/contact.php' => 'İletişim'
    ];
    
    $output = '<nav class="casino-navbar" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 1rem 0; background: rgba(0,0,0,0.95); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255, 215, 0, 0.2);">';
    $output .= '<div class="container">';
    $output .= '<div class="d-flex justify-content-between align-items-center">';
    $output .= '<a href="../index.php" class="casino-brand">BERAT K</a>';
    
    // Mobile Menu Toggle
    $output .= '<button class="mobile-nav-toggle" onclick="toggleMobileMenu()">';
    $output .= '<i class="fas fa-bars"></i>';
    $output .= '</button>';
    
    // Navigation Menu
    $output .= '<ul class="nav-menu" id="navMenu">';
    
    foreach ($navItems as $url => $title) {
        $activeClass = '';
        if ($currentPage === $url || basename($currentPage) === basename($url)) {
            $activeClass = ' active';
        }
        
        $output .= '<li><a href="' . $url . '" class="casino-nav-link' . $activeClass . '">' . $title . '</a></li>';
    }
    
    $output .= '</ul>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</nav>';
    
    return $output;
}

// Render mobile navigation CSS
function renderMobileNavigationCSS() {
    return '
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
            font-family: "Orbitron", monospace;
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
                background: rgba(0, 0, 0, 0.98);
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
    </style>';
}

// Render mobile navigation JavaScript
function renderMobileNavigationJS() {
    return '
    <script>
        // Mobile Navigation Toggle
        function toggleMobileMenu() {
            const navMenu = document.getElementById("navMenu");
            const toggleBtn = document.querySelector(".mobile-nav-toggle i");
            
            navMenu.classList.toggle("active");
            
            // Toggle hamburger/close icon
            if (navMenu.classList.contains("active")) {
                toggleBtn.classList.remove("fa-bars");
                toggleBtn.classList.add("fa-times");
            } else {
                toggleBtn.classList.remove("fa-times");
                toggleBtn.classList.add("fa-bars");
            }
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener("click", function(event) {
            const navMenu = document.getElementById("navMenu");
            const toggleBtn = document.querySelector(".mobile-nav-toggle");
            
            if (!navMenu.contains(event.target) && !toggleBtn.contains(event.target)) {
                navMenu.classList.remove("active");
                document.querySelector(".mobile-nav-toggle i").classList.remove("fa-times");
                document.querySelector(".mobile-nav-toggle i").classList.add("fa-bars");
            }
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll(".casino-nav-link").forEach(link => {
            link.addEventListener("click", function() {
                const navMenu = document.getElementById("navMenu");
                navMenu.classList.remove("active");
                document.querySelector(".mobile-nav-toggle i").classList.remove("fa-times");
                document.querySelector(".mobile-nav-toggle i").classList.add("fa-bars");
            });
        });
    </script>';
}

// Render dark theme CSS
function renderDarkThemeCSS() {
    return '
    <style>
        :root {
            --casino-black: #000000;
            --casino-dark: #0a0a0a;
            --casino-darker: #050505;
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
            --text-silver: #E0E0E0;
            --text-muted: #CCCCCC;
            --shadow-neon: 0 0 20px;
            --shadow-heavy: 0 15px 35px rgba(0, 0, 0, 0.9);
        }
        
        body {
            background: var(--casino-black);
            color: var(--text-light);
            font-family: "Rajdhani", sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-light);
            font-weight: 700;
        }
        
        p {
            color: var(--text-silver);
            line-height: 1.6;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .mb-5 {
            margin-bottom: 3rem;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            color: var(--casino-black);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.3);
        }
        
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--casino-gold), var(--neon-pink));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--casino-black);
            font-size: 1.5rem;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(255,215,0,0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(255,215,0,0.5);
        }
    </style>';
}
?>
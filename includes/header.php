<?php
/**
 * Site Header Template
 * Geliştirici: BERAT K
 * Main header template for the casino portfolio website
 */

// Prevent direct access
if (!defined('SITE_ROOT')) {
    require_once __DIR__ . '/../includes/config.php';
}

// Get page specific data
$page_title = $page_title ?? get_setting('site_title', SITE_NAME);
$page_description = $page_description ?? get_setting('site_description', '');
$page_keywords = $page_keywords ?? get_setting('site_keywords', '');
$current_page = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo get_page_title($page_title); ?></title>
    <meta name="description" content="<?php echo get_meta_description($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="BERAT K">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo get_page_title($page_title); ?>">
    <meta property="og:description" content="<?php echo get_meta_description($page_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo current_url(); ?>">
    <meta property="og:image" content="<?php echo asset_url('img/og-image.jpg'); ?>">
    <meta property="og:site_name" content="<?php echo get_setting('site_title', SITE_NAME); ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo get_page_title($page_title); ?>">
    <meta name="twitter:description" content="<?php echo get_meta_description($page_description); ?>">
    <meta name="twitter:image" content="<?php echo asset_url('img/og-image.jpg'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo asset_url(get_setting('favicon_url', 'img/favicon.ico')); ?>">
    <link rel="apple-touch-icon" href="<?php echo asset_url('img/apple-touch-icon.png'); ?>">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="<?php echo get_setting('theme_color', '#021526'); ?>">
    
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/responsive.css'); ?>">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="<?php echo asset_url('css/style.css'); ?>" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Additional Head Content -->
    <?php if (isset($additional_head)): ?>
        <?php echo $additional_head; ?>
    <?php endif; ?>
    
    <!-- Analytics Code -->
    <?php $analytics_code = get_setting('analytics_code', ''); ?>
    <?php if (!empty($analytics_code)): ?>
        <?php echo $analytics_code; ?>
    <?php endif; ?>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <!-- Loading Spinner -->
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner"></div>
    </div>
    
    <!-- Header Section -->
    <header class="header fixed-top">
        <!-- Top Bar -->
        <div class="top-bar d-none d-lg-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="contact-info">
                            <span><i class="fas fa-envelope"></i> <?php echo get_setting('contact_email', 'info@casinoportfolio.com'); ?></span>
                            <span><i class="fas fa-phone"></i> <?php echo get_setting('contact_phone', '+90 555 123 45 67'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="social-links text-end">
                            <?php $social_instagram = get_setting('social_instagram', ''); ?>
                            <?php if (!empty($social_instagram)): ?>
                                <a href="<?php echo $social_instagram; ?>" target="_blank" rel="noopener">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php $social_telegram = get_setting('social_telegram', ''); ?>
                            <?php if (!empty($social_telegram)): ?>
                                <a href="<?php echo $social_telegram; ?>" target="_blank" rel="noopener">
                                    <i class="fab fa-telegram"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php $social_youtube = get_setting('social_youtube', ''); ?>
                            <?php if (!empty($social_youtube)): ?>
                                <a href="<?php echo $social_youtube; ?>" target="_blank" rel="noopener">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php $social_twitter = get_setting('social_twitter', ''); ?>
                            <?php if (!empty($social_twitter)): ?>
                                <a href="<?php echo $social_twitter; ?>" target="_blank" rel="noopener">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark main-nav">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo site_url(); ?>">
                    <?php $logo_url = get_setting('logo_url', ''); ?>
                    <?php if (!empty($logo_url)): ?>
                        <img src="<?php echo asset_url($logo_url); ?>" alt="<?php echo get_setting('site_title', SITE_NAME); ?>" class="logo">
                    <?php else: ?>
                        <span class="logo-text">BERAT K</span>
                    <?php endif; ?>
                </a>
                
                <!-- Mobile Menu Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php 
                        $menu_items = get_menu_items(0);
                        foreach ($menu_items as $item): 
                            $is_active = strpos($current_page, $item['url']) !== false;
                        ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $is_active ? 'active' : ''; ?>" 
                                   href="<?php echo site_url($item['url']); ?>"
                                   <?php echo $item['target'] !== '_self' ? 'target="' . $item['target'] . '"' : ''; ?>>
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        
                        <!-- Contact Button -->
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary contact-btn" href="<?php echo site_url('pages/contact.php'); ?>">
                                <i class="fas fa-phone"></i> İletişim
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content Wrapper -->
    <main class="main-content">
        <!-- Flash Messages -->
        <div class="container">
            <?php echo display_flash_messages(); ?>
        </div>
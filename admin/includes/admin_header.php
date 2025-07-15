<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Check session timeout (4 hours)
$timeout = 4 * 60 * 60; // 4 hours in seconds
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

// Update last activity
$_SESSION['last_activity'] = time();

// Get current page name for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Get admin info
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - Casino Yayıncısı BERAT K</title>
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #021526;
            --secondary-color: #6f42c1;
            --accent-color: #e91e63;
            --dark-bg: #0a0a0a;
            --sidebar-bg: #1a1a1a;
            --card-bg: rgba(255, 255, 255, 0.05);
            --border-color: rgba(255, 255, 255, 0.1);
            --text-light: rgba(255, 255, 255, 0.8);
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            color: white;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
        }
        
        .sidebar-brand i {
            font-size: 2rem;
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand-text {
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .brand-text {
            opacity: 0;
            pointer-events: none;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }
        
        .nav-link.active {
            background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
            color: white;
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--accent-color);
        }
        
        .nav-icon {
            min-width: 20px;
            text-align: center;
        }
        
        .nav-text {
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .nav-text {
            opacity: 0;
            pointer-events: none;
        }
        
        /* Top Navigation */
        .topnav {
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            height: 70px;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 999;
            transition: left 0.3s ease;
        }
        
        .sidebar.collapsed + .topnav {
            left: 80px;
        }
        
        .topnav-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .topnav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .admin-dropdown {
            position: relative;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
        }
        
        .admin-profile:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .admin-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .admin-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .admin-name {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .admin-role {
            font-size: 0.75rem;
            color: var(--text-light);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--sidebar-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            min-width: 200px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            display: none;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }
        
        .dropdown-divider {
            height: 1px;
            background: var(--border-color);
            margin: 0.5rem 0;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s ease;
        }
        
        .sidebar.collapsed ~ .main-content {
            margin-left: 80px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .topnav {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .admin-info {
                display: none;
            }
        }
        
        /* Notifications */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .notification-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand">
                <i class="fas fa-dice"></i>
                <div class="brand-text">
                    <h5 class="mb-0">BERAT K</h5>
                    <small>Admin Panel</small>
                </div>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="pages.php" class="nav-link <?= $current_page === 'pages' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt nav-icon"></i>
                    <span class="nav-text">Sayfa Yönetimi</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="services.php" class="nav-link <?= $current_page === 'services' ? 'active' : '' ?>">
                    <i class="fas fa-cogs nav-icon"></i>
                    <span class="nav-text">Hizmetler</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="portfolio.php" class="nav-link <?= $current_page === 'portfolio' ? 'active' : '' ?>">
                    <i class="fas fa-briefcase nav-icon"></i>
                    <span class="nav-text">Portföy</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="gallery.php" class="nav-link <?= $current_page === 'gallery' ? 'active' : '' ?>">
                    <i class="fas fa-images nav-icon"></i>
                    <span class="nav-text">Galeri</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="messages.php" class="nav-link <?= $current_page === 'messages' ? 'active' : '' ?>">
                    <i class="fas fa-envelope nav-icon"></i>
                    <span class="nav-text">Mesajlar</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="settings.php" class="nav-link <?= $current_page === 'settings' ? 'active' : '' ?>">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="nav-text">Ayarlar</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="../index.php" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt nav-icon"></i>
                    <span class="nav-text">Siteyi Görüntüle</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Top Navigation -->
    <nav class="topnav">
        <div class="topnav-left">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title"><?= $pageTitle ?? 'Admin Panel' ?></h1>
        </div>
        
        <div class="topnav-right">
            <button class="notification-btn" onclick="showNotifications()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
            
            <div class="admin-dropdown">
                <a href="#" class="admin-profile" onclick="toggleDropdown(event)">
                    <div class="admin-avatar">
                        <?= strtoupper(substr($admin_username, 0, 1)) ?>
                    </div>
                    <div class="admin-info">
                        <div class="admin-name"><?= htmlspecialchars($admin_username) ?></div>
                        <div class="admin-role">Administrator</div>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </a>
                
                <div class="dropdown-menu" id="adminDropdown">
                    <a href="profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        Profil
                    </a>
                    <a href="settings.php" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        Ayarlar
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?')">
                        <i class="fas fa-sign-out-alt"></i>
                        Çıkış Yap
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">

<script>
// Sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
    
    // Save state
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}

// Restore sidebar state
document.addEventListener('DOMContentLoaded', function() {
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        document.getElementById('sidebar').classList.add('collapsed');
    }
});

// Admin dropdown toggle
function toggleDropdown(event) {
    event.preventDefault();
    const dropdown = document.getElementById('adminDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('adminDropdown');
    const profileBtn = document.querySelector('.admin-profile');
    
    if (!profileBtn.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Notifications
function showNotifications() {
    // This would typically show a notifications panel
    alert('Bildirimler özelliği yakında eklenecek!');
}

// Mobile sidebar toggle
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');
}

// Auto-logout warning
let logoutTimer;
const LOGOUT_TIME = 4 * 60 * 60 * 1000; // 4 hours

function resetLogoutTimer() {
    clearTimeout(logoutTimer);
    logoutTimer = setTimeout(function() {
        if (confirm('Oturumunuz sona ermek üzere. Devam etmek istiyor musunuz?')) {
            resetLogoutTimer();
        } else {
            window.location.href = 'logout.php';
        }
    }, LOGOUT_TIME - 300000); // Warn 5 minutes before logout
}

// Reset timer on user activity
document.addEventListener('mousedown', resetLogoutTimer);
document.addEventListener('keypress', resetLogoutTimer);
document.addEventListener('scroll', resetLogoutTimer);

// Initialize logout timer
resetLogoutTimer();

// Escape key handling
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        // Close any open modals or dropdowns
        document.getElementById('adminDropdown').classList.remove('show');
    }
});
</script>
<?php
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$pageTitle = 'Dashboard';
$db = new Database();

// Get dashboard statistics
$stats = [
    'total_messages' => $db->count('messages'),
    'unread_messages' => $db->count('messages', ['status' => 'unread']),
    'total_portfolio' => $db->count('portfolio', ['status' => 'active']),
    'total_gallery' => $db->count('gallery', ['status' => 'active']),
    'total_services' => $db->count('services', ['status' => 'active']),
];

// Get recent messages
$recent_messages = $db->query("
    SELECT * FROM messages 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();

// Get monthly message statistics for chart
$monthly_stats = $db->query("
    SELECT 
        strftime('%Y-%m', created_at) as month,
        COUNT(*) as count
    FROM messages 
    WHERE created_at >= date('now', '-12 months')
    GROUP BY strftime('%Y-%m', created_at)
    ORDER BY month ASC
")->fetchAll();

// Get portfolio category statistics
$portfolio_stats = $db->query("
    SELECT 
        category,
        COUNT(*) as count
    FROM portfolio 
    WHERE status = 'active'
    GROUP BY category
    ORDER BY count DESC
")->fetchAll();

// Get recent activity (simplified)
$recent_activity = [
    [
        'action' => 'Yeni mesaj',
        'description' => 'İletişim formundan yeni mesaj alındı',
        'time' => '2 dakika önce',
        'icon' => 'fas fa-envelope',
        'color' => 'info'
    ],
    [
        'action' => 'Portföy güncellendi',
        'description' => 'Casino Sitesi Tanıtımı projesi güncellendi',
        'time' => '1 saat önce',
        'icon' => 'fas fa-briefcase',
        'color' => 'success'
    ],
    [
        'action' => 'Yeni galeri öğesi',
        'description' => 'Video galeri kategorisine yeni içerik eklendi',
        'time' => '3 saat önce',
        'icon' => 'fas fa-images',
        'color' => 'warning'
    ]
];

include 'includes/admin_header.php';
?>

<style>
.dashboard-card {
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.stat-card {
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--text-light);
    font-weight: 500;
}

.chart-container {
    position: relative;
    height: 300px;
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 1.5rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 10px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.activity-icon.info {
    background: rgba(23, 162, 184, 0.2);
    color: var(--info-color);
}

.activity-icon.success {
    background: rgba(40, 167, 69, 0.2);
    color: var(--success-color);
}

.activity-icon.warning {
    background: rgba(255, 193, 7, 0.2);
    color: var(--warning-color);
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.activity-description {
    font-size: 0.85rem;
    color: var(--text-light);
}

.activity-time {
    font-size: 0.75rem;
    color: var(--text-light);
    opacity: 0.7;
}

.quick-action-btn {
    background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
    border: none;
    border-radius: 10px;
    color: white;
    padding: 1rem 1.5rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    width: 100%;
    margin-bottom: 1rem;
}

.quick-action-btn:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(111, 66, 193, 0.3);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.welcome-banner {
    background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" opacity="0.1"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>') no-repeat center;
    opacity: 0.1;
}

.welcome-title {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
}

.welcome-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
}

.message-preview {
    background: rgba(255, 255, 255, 0.02);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--secondary-color);
}

.message-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.message-sender {
    font-weight: 600;
    color: white;
}

.message-date {
    font-size: 0.8rem;
    color: var(--text-light);
}

.message-subject {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--accent-color);
}

.message-excerpt {
    font-size: 0.9rem;
    color: var(--text-light);
    line-height: 1.4;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.unread {
    background: rgba(233, 30, 99, 0.2);
    color: var(--accent-color);
}

.status-badge.read {
    background: rgba(40, 167, 69, 0.2);
    color: var(--success-color);
}
</style>

<div class="welcome-banner">
    <h1 class="welcome-title">Hoş Geldiniz, <?= htmlspecialchars($_SESSION['admin_username']) ?>! 👋</h1>
    <p class="welcome-subtitle">Casino portföy sitenizin admin paneline hoş geldiniz. Son durumu buradan takip edebilirsiniz.</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-number" data-count="<?= $stats['total_messages'] ?>">0</div>
            <div class="stat-label">Toplam Mesaj</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-envelope-open"></i>
            </div>
            <div class="stat-number" data-count="<?= $stats['unread_messages'] ?>">0</div>
            <div class="stat-label">Okunmamış Mesaj</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-number" data-count="<?= $stats['total_portfolio'] ?>">0</div>
            <div class="stat-label">Portföy Projesi</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-number" data-count="<?= $stats['total_gallery'] ?>">0</div>
            <div class="stat-label">Galeri Öğesi</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Charts Section -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <h3 class="section-title">
                <i class="fas fa-chart-area me-2"></i>
                Aylık Mesaj İstatistikleri
            </h3>
            <div class="chart-container">
                <canvas id="messagesChart"></canvas>
            </div>
        </div>
        
        <div class="dashboard-card">
            <h3 class="section-title">
                <i class="fas fa-clock me-2"></i>
                Son Aktiviteler
            </h3>
            <?php foreach ($recent_activity as $activity): ?>
            <div class="activity-item">
                <div class="activity-icon <?= $activity['color'] ?>">
                    <i class="<?= $activity['icon'] ?>"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title"><?= htmlspecialchars($activity['action']) ?></div>
                    <div class="activity-description"><?= htmlspecialchars($activity['description']) ?></div>
                </div>
                <div class="activity-time"><?= htmlspecialchars($activity['time']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="dashboard-card">
            <h3 class="section-title">
                <i class="fas fa-bolt me-2"></i>
                Hızlı İşlemler
            </h3>
            <a href="messages/index.php" class="quick-action-btn">
                <i class="fas fa-envelope"></i>
                <span>Mesajları Görüntüle</span>
            </a>
            <a href="portfolio/add.php" class="quick-action-btn">
                <i class="fas fa-plus"></i>
                <span>Yeni Proje Ekle</span>
            </a>
            <a href="gallery/add.php" class="quick-action-btn">
                <i class="fas fa-image"></i>
                <span>Galeri'ye Ekle</span>
            </a>
            <a href="services/index.php" class="quick-action-btn">
                <i class="fas fa-cogs"></i>
                <span>Hizmetleri Düzenle</span>
            </a>
            <a href="../index.php" class="quick-action-btn" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Siteyi Görüntüle</span>
            </a>
        </div>
        
        <!-- Recent Messages -->
        <div class="dashboard-card">
            <h3 class="section-title">
                <i class="fas fa-envelope me-2"></i>
                Son Mesajlar
            </h3>
            <?php if (empty($recent_messages)): ?>
            <p class="text-muted">Henüz mesaj bulunmuyor.</p>
            <?php else: ?>
                <?php foreach ($recent_messages as $message): ?>
                <div class="message-preview">
                    <div class="message-header">
                        <div class="message-sender"><?= htmlspecialchars($message['name']) ?></div>
                        <span class="status-badge <?= $message['status'] === 'unread' ? 'unread' : 'read' ?>">
                            <?= $message['status'] === 'unread' ? 'Okunmadı' : 'Okundu' ?>
                        </span>
                    </div>
                    <div class="message-subject"><?= htmlspecialchars($message['subject']) ?></div>
                    <div class="message-excerpt">
                        <?= htmlspecialchars(substr($message['message'], 0, 100)) ?><?= strlen($message['message']) > 100 ? '...' : '' ?>
                    </div>
                    <div class="message-date mt-2">
                        <i class="fas fa-clock me-1"></i>
                        <?= timeAgo($message['created_at']) ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <a href="messages/index.php" class="btn btn-outline-primary w-100 mt-2">
                    Tüm Mesajları Görüntüle
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Portfolio Categories Chart -->
        <div class="dashboard-card">
            <h3 class="section-title">
                <i class="fas fa-chart-pie me-2"></i>
                Portföy Kategorileri
            </h3>
            <div class="chart-container">
                <canvas id="portfolioChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    animateCounters();
});

// Counter animation
function animateCounters() {
    const counters = document.querySelectorAll('[data-count]');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    });
}

// Initialize charts
function initializeCharts() {
    // Messages Chart
    const messagesCtx = document.getElementById('messagesChart').getContext('2d');
    const messagesData = <?= json_encode($monthly_stats) ?>;
    
    new Chart(messagesCtx, {
        type: 'line',
        data: {
            labels: messagesData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('tr-TR', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Mesaj Sayısı',
                data: messagesData.map(item => item.count),
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#e91e63',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.6)'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.6)'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            }
        }
    });
    
    // Portfolio Categories Chart
    const portfolioCtx = document.getElementById('portfolioChart').getContext('2d');
    const portfolioData = <?= json_encode($portfolio_stats) ?>;
    
    new Chart(portfolioCtx, {
        type: 'doughnut',
        data: {
            labels: portfolioData.map(item => item.category),
            datasets: [{
                data: portfolioData.map(item => item.count),
                backgroundColor: [
                    '#6f42c1',
                    '#e91e63',
                    '#17a2b8',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderColor: 'rgba(255, 255, 255, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.8)',
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    // This would typically make an AJAX call to refresh data
    console.log('Dashboard data refresh (simulation)');
}, 5 * 60 * 1000);

// Welcome banner auto-hide after 10 seconds
setTimeout(function() {
    const banner = document.querySelector('.welcome-banner');
    if (banner) {
        banner.style.opacity = '0.8';
    }
}, 10000);
</script>

<?php include 'includes/admin_footer.php'; ?>
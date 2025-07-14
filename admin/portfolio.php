<?php
/**
 * Admin Portfolio Management
 * Geliştirici: BERAT K
 * Portfolio CRUD operations with image upload
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/security.php';
require_once '../includes/functions.php';

// Check admin authentication
if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login.php');
}

// Initialize database
$db = new Database();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'];
        $response = ['success' => false, 'message' => 'Geçersiz işlem'];
        
        // CSRF validation
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Güvenlik hatası');
        }
        
        switch ($action) {
            case 'save_portfolio':
                $response = savePortfolio($_POST, $_FILES);
                break;
                
            case 'delete_portfolio':
                $response = deletePortfolio($_POST['portfolio_id']);
                break;
                
            case 'toggle_status':
                $response = togglePortfolioStatus($_POST['portfolio_id']);
                break;
                
            case 'reorder_portfolio':
                $response = reorderPortfolio($_POST['portfolio']);
                break;
                
            case 'get_portfolio':
                $response = getPortfolio($_POST['portfolio_id']);
                break;
        }
        
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Get portfolio with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

$searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

// Build query
$whereClause = '';
$params = [];

if ($searchTerm) {
    $whereClause .= " WHERE (title LIKE ? OR description LIKE ?)";
    $params = ["%$searchTerm%", "%$searchTerm%"];
}

if ($statusFilter) {
    $whereClause .= $whereClause ? " AND status = ?" : " WHERE status = ?";
    $params[] = $statusFilter;
}

// Get total count
$totalPortfolio = $db->query("SELECT COUNT(*) as count FROM portfolio" . $whereClause, $params)->fetch()['count'];
$totalPaginationPages = ceil($totalPortfolio / $perPage);

// Get portfolio items
$portfolio = $db->query(
    "SELECT * FROM portfolio" . $whereClause . " ORDER BY order_position ASC, created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
)->fetchAll();

$pageTitle = "Portföy Yönetimi";

// Portfolio functions
function savePortfolio($data, $files) {
    global $db;
    
    $title = sanitizeInput($data['title'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $client = sanitizeInput($data['client'] ?? '');
    $project_url = sanitizeInput($data['project_url'] ?? '');
    $technologies = sanitizeInput($data['technologies'] ?? '');
    $status = sanitizeInput($data['status'] ?? 'active');
    $order_position = (int)($data['order_position'] ?? 0);
    $portfolio_id = (int)($data['portfolio_id'] ?? 0);
    
    if (empty($title)) {
        return ['success' => false, 'message' => 'Proje başlığı gereklidir'];
    }
    
    $portfolioData = [
        'title' => $title,
        'description' => $description,
        'client' => $client,
        'project_url' => $project_url,
        'technologies' => $technologies,
        'status' => $status,
        'order_position' => $order_position,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Handle image upload
    if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
        $imageUpload = uploadPortfolioImage($files['image']);
        if ($imageUpload['success']) {
            $portfolioData['image'] = $imageUpload['filename'];
            
            // Delete old image if updating
            if ($portfolio_id > 0) {
                $oldPortfolio = $db->find('portfolio', ['id' => $portfolio_id]);
                if ($oldPortfolio && $oldPortfolio['image']) {
                    $oldImagePath = '../uploads/portfolio/' . $oldPortfolio['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }
        } else {
            return ['success' => false, 'message' => $imageUpload['message']];
        }
    }
    
    if ($portfolio_id > 0) {
        // Update existing portfolio
        $result = $db->update('portfolio', $portfolioData, ['id' => $portfolio_id]);
        $message = $result ? 'Portföy projesi başarıyla güncellendi' : 'Proje güncellenirken hata oluştu';
    } else {
        // Create new portfolio
        $portfolioData['created_at'] = date('Y-m-d H:i:s');
        $result = $db->insert('portfolio', $portfolioData);
        $message = $result ? 'Yeni portföy projesi başarıyla eklendi' : 'Proje eklenirken hata oluştu';
    }
    
    return ['success' => (bool)$result, 'message' => $message];
}

function uploadPortfolioImage($file) {
    $uploadDir = '../uploads/portfolio/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Dosya boyutu 5MB\'dan büyük olamaz'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Sadece JPEG, PNG, GIF ve WebP formatları desteklenir'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'portfolio_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Dosya yüklenirken hata oluştu'];
    }
}

function deletePortfolio($portfolio_id) {
    global $db;
    
    if (!is_numeric($portfolio_id) || $portfolio_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz proje ID'];
    }
    
    // Get portfolio data to delete image
    $portfolio = $db->find('portfolio', ['id' => $portfolio_id]);
    if ($portfolio && $portfolio['image']) {
        $imagePath = '../uploads/portfolio/' . $portfolio['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $result = $db->delete('portfolio', ['id' => $portfolio_id]);
    $message = $result ? 'Portföy projesi başarıyla silindi' : 'Proje silinirken hata oluştu';
    
    return ['success' => (bool)$result, 'message' => $message];
}

function togglePortfolioStatus($portfolio_id) {
    global $db;
    
    if (!is_numeric($portfolio_id) || $portfolio_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz proje ID'];
    }
    
    $portfolio = $db->find('portfolio', ['id' => $portfolio_id]);
    if (!$portfolio) {
        return ['success' => false, 'message' => 'Proje bulunamadı'];
    }
    
    $newStatus = $portfolio['status'] === 'active' ? 'inactive' : 'active';
    $result = $db->update('portfolio', ['status' => $newStatus], ['id' => $portfolio_id]);
    
    $message = $result ? 'Proje durumu güncellendi' : 'Durum güncellenirken hata oluştu';
    
    return ['success' => (bool)$result, 'message' => $message];
}

function reorderPortfolio($portfolio) {
    global $db;
    
    if (!is_array($portfolio)) {
        return ['success' => false, 'message' => 'Geçersiz veri formatı'];
    }
    
    $db->beginTransaction();
    
    try {
        foreach ($portfolio as $index => $portfolio_id) {
            $db->update('portfolio', ['order_position' => $index + 1], ['id' => $portfolio_id]);
        }
        
        $db->commit();
        return ['success' => true, 'message' => 'Sıralama güncellendi'];
        
    } catch (Exception $e) {
        $db->rollback();
        return ['success' => false, 'message' => 'Sıralama güncellenirken hata oluştu'];
    }
}

function getPortfolio($portfolio_id) {
    global $db;
    
    if (!is_numeric($portfolio_id) || $portfolio_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz proje ID'];
    }
    
    $portfolio = $db->find('portfolio', ['id' => $portfolio_id]);
    if (!$portfolio) {
        return ['success' => false, 'message' => 'Proje bulunamadı'];
    }
    
    return ['success' => true, 'data' => $portfolio];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Casino Yayıncısı BERAT K</title>
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 10px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-2px);
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 500;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .search-box {
            background: white;
            border-radius: 25px;
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .search-box input {
            border: none;
            outline: none;
            background: transparent;
        }
        
        .search-box input:focus {
            box-shadow: none;
        }
        
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .portfolio-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .portfolio-item:hover {
            transform: translateY(-5px);
        }
        
        .portfolio-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }
        
        .portfolio-content {
            padding: 20px;
        }
        
        .portfolio-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .portfolio-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .portfolio-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .portfolio-actions {
            display: flex;
            gap: 5px;
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px 20px 0 0;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .image-preview {
            width: 100%;
            max-width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
            display: none;
        }
        
        .image-preview.show {
            display: block;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .upload-area:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }
        
        .upload-area.dragover {
            border-color: #667eea;
            background: #e3f2fd;
        }
        
        .tech-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .tech-tag {
            background: #e9ecef;
            color: #495057;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        
        .sortable-ghost {
            opacity: 0.4;
        }
        
        .sortable-chosen {
            transform: scale(1.05);
        }
        
        .drag-handle {
            cursor: move;
            color: #6c757d;
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.9);
            padding: 5px;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .portfolio-item:hover .drag-handle {
            opacity: 1;
        }
        
        .drag-handle:hover {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-dice me-2"></i>
                        Casino Admin
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link" href="pages.php">
                            <i class="fas fa-file-alt me-2"></i>
                            Sayfalar
                        </a>
                        <a class="nav-link" href="services.php">
                            <i class="fas fa-cogs me-2"></i>
                            Hizmetler
                        </a>
                        <a class="nav-link active" href="portfolio.php">
                            <i class="fas fa-briefcase me-2"></i>
                            Portföy
                        </a>
                        <a class="nav-link" href="gallery.php">
                            <i class="fas fa-images me-2"></i>
                            Galeri
                        </a>
                        <a class="nav-link" href="messages.php">
                            <i class="fas fa-envelope me-2"></i>
                            Mesajlar
                        </a>
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog me-2"></i>
                            Ayarlar
                        </a>
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Çıkış Yap
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content p-0">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="mb-0">
                                    <i class="fas fa-briefcase me-3"></i>
                                    <?php echo $pageTitle; ?>
                                </h1>
                                <p class="mb-0 opacity-75">Portföy projelerinizi yönetin ve düzenleyin</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-light btn-lg" onclick="showPortfolioModal()">
                                    <i class="fas fa-plus me-2"></i>
                                    Yeni Proje Ekle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="container-fluid">
                    <!-- Search and Filter -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="search-box">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Proje ara..." 
                                           id="searchInput" value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter" style="border-radius: 25px;">
                                <option value="">Tüm Durumlar</option>
                                <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Pasif</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Portfolio Grid -->
                    <div class="portfolio-grid" id="portfolioGrid">
                        <?php if (empty($portfolio)): ?>
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Henüz portföy projesi eklenmemiş</p>
                                <button class="btn btn-primary" onclick="showPortfolioModal()">
                                    <i class="fas fa-plus me-2"></i>
                                    İlk Projeyi Ekle
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($portfolio as $item): ?>
                                <div class="portfolio-item" data-id="<?php echo $item['id']; ?>">
                                    <div class="drag-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    
                                    <?php if ($item['image']): ?>
                                        <img src="../uploads/portfolio/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                             class="portfolio-image">
                                    <?php else: ?>
                                        <div class="portfolio-image d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="portfolio-content">
                                        <div class="portfolio-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                        
                                        <?php if ($item['description']): ?>
                                            <div class="portfolio-description"><?php echo htmlspecialchars($item['description']); ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if ($item['client']): ?>
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    <?php echo htmlspecialchars($item['client']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($item['technologies']): ?>
                                            <div class="tech-tags">
                                                <?php foreach (explode(',', $item['technologies']) as $tech): ?>
                                                    <span class="tech-tag"><?php echo htmlspecialchars(trim($tech)); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="portfolio-meta">
                                            <span class="status-badge status-<?php echo $item['status']; ?>">
                                                <?php echo $item['status'] === 'active' ? 'Aktif' : 'Pasif'; ?>
                                            </span>
                                            
                                            <div class="portfolio-actions">
                                                <?php if ($item['project_url']): ?>
                                                    <a href="<?php echo htmlspecialchars($item['project_url']); ?>" 
                                                       target="_blank" class="btn btn-sm btn-outline-info" title="Projeyi Görüntüle">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editPortfolio(<?php echo $item['id']; ?>)" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-outline-<?php echo $item['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                        onclick="toggleStatus(<?php echo $item['id']; ?>)"
                                                        title="<?php echo $item['status'] === 'active' ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                                    <i class="fas fa-<?php echo $item['status'] === 'active' ? 'eye-slash' : 'eye'; ?>"></i>
                                                </button>
                                                
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deletePortfolio(<?php echo $item['id']; ?>)" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPaginationPages > 1): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Sayfa navigasyonu">
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $totalPaginationPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($statusFilter); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Portfolio Modal -->
    <div class="modal fade" id="portfolioModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-briefcase me-2"></i>
                        <span id="modalTitle">Yeni Proje Ekle</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="portfolioForm" enctype="multipart/form-data">
                        <input type="hidden" id="portfolioId" name="portfolio_id" value="0">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="portfolioTitle" class="form-label">
                                        <i class="fas fa-heading me-2"></i>
                                        Proje Başlığı *
                                    </label>
                                    <input type="text" class="form-control" id="portfolioTitle" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="portfolioDescription" class="form-label">
                                        <i class="fas fa-align-left me-2"></i>
                                        Proje Açıklaması
                                    </label>
                                    <textarea class="form-control" id="portfolioDescription" name="description" rows="4"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="portfolioClient" class="form-label">
                                                <i class="fas fa-user me-2"></i>
                                                Müşteri/İşveren
                                            </label>
                                            <input type="text" class="form-control" id="portfolioClient" name="client">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="portfolioUrl" class="form-label">
                                                <i class="fas fa-link me-2"></i>
                                                Proje URL'si
                                            </label>
                                            <input type="url" class="form-control" id="portfolioUrl" name="project_url">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="portfolioTechnologies" class="form-label">
                                        <i class="fas fa-code me-2"></i>
                                        Kullanılan Teknolojiler
                                    </label>
                                    <input type="text" class="form-control" id="portfolioTechnologies" name="technologies" 
                                           placeholder="PHP, MySQL, JavaScript, Bootstrap (virgülle ayırın)">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="portfolioStatus" class="form-label">
                                        <i class="fas fa-toggle-on me-2"></i>
                                        Durum
                                    </label>
                                    <select class="form-select" id="portfolioStatus" name="status">
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Pasif</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="portfolioOrder" class="form-label">
                                        <i class="fas fa-sort me-2"></i>
                                        Sıra
                                    </label>
                                    <input type="number" class="form-control" id="portfolioOrder" name="order_position" min="0" value="0">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="portfolioImage" class="form-label">
                                        <i class="fas fa-image me-2"></i>
                                        Proje Görseli
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('portfolioImage').click()">
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p class="mb-0">Görsel yüklemek için tıklayın</p>
                                        <small class="text-muted">JPEG, PNG, GIF, WebP (Max: 5MB)</small>
                                    </div>
                                    <input type="file" class="form-control d-none" id="portfolioImage" name="image" 
                                           accept="image/jpeg,image/png,image/gif,image/webp">
                                    <img id="imagePreview" class="image-preview mt-2" alt="Önizleme">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        İptal
                    </button>
                    <button type="button" class="btn btn-primary" onclick="savePortfolio()">
                        <i class="fas fa-save me-2"></i>
                        Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <script>
        // Initialize page
        $(document).ready(function() {
            initializePage();
        });
        
        function initializePage() {
            // Initialize sortable
            if ($('.portfolio-item').length > 0) {
                new Sortable(document.getElementById('portfolioGrid'), {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    handle: '.drag-handle',
                    onEnd: function(evt) {
                        updateOrder();
                    }
                });
            }
            
            // Initialize search
            $('#searchInput').on('keyup', debounce(function() {
                filterPortfolio();
            }, 500));
            
            // Initialize status filter
            $('#statusFilter').on('change', function() {
                filterPortfolio();
            });
            
            // Initialize image upload
            $('#portfolioImage').on('change', function() {
                previewImage(this);
            });
            
            // Initialize drag and drop
            const uploadArea = $('.upload-area');
            uploadArea.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });
            
            uploadArea.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });
            
            uploadArea.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    $('#portfolioImage')[0].files = files;
                    previewImage($('#portfolioImage')[0]);
                }
            });
        }
        
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).addClass('show');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function showPortfolioModal(portfolioId = null) {
            if (portfolioId) {
                // Edit mode
                $('#modalTitle').text('Projeyi Düzenle');
                loadPortfolioData(portfolioId);
            } else {
                // Add mode
                $('#modalTitle').text('Yeni Proje Ekle');
                $('#portfolioForm')[0].reset();
                $('#portfolioId').val('0');
                $('#imagePreview').removeClass('show');
            }
            
            $('#portfolioModal').modal('show');
        }
        
        function loadPortfolioData(portfolioId) {
            $.post('portfolio.php', {
                action: 'get_portfolio',
                portfolio_id: portfolioId,
                csrf_token: $('input[name="csrf_token"]').val()
            }, function(response) {
                if (response.success) {
                    const portfolio = response.data;
                    $('#portfolioId').val(portfolio.id);
                    $('#portfolioTitle').val(portfolio.title);
                    $('#portfolioDescription').val(portfolio.description);
                    $('#portfolioClient').val(portfolio.client);
                    $('#portfolioUrl').val(portfolio.project_url);
                    $('#portfolioTechnologies').val(portfolio.technologies);
                    $('#portfolioStatus').val(portfolio.status);
                    $('#portfolioOrder').val(portfolio.order_position);
                    
                    if (portfolio.image) {
                        $('#imagePreview').attr('src', '../uploads/portfolio/' + portfolio.image).addClass('show');
                    } else {
                        $('#imagePreview').removeClass('show');
                    }
                }
            }, 'json');
        }
        
        function savePortfolio() {
            const formData = new FormData($('#portfolioForm')[0]);
            formData.append('action', 'save_portfolio');
            
            $.ajax({
                url: 'portfolio.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Başarılı!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Tamam'
                        }).then(() => {
                            $('#portfolioModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Hata!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'Tamam'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Hata!',
                        text: 'Bir hata oluştu. Lütfen tekrar deneyin.',
                        icon: 'error',
                        confirmButtonText: 'Tamam'
                    });
                }
            });
        }
        
        function editPortfolio(portfolioId) {
            showPortfolioModal(portfolioId);
        }
        
        function toggleStatus(portfolioId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Proje durumunu değiştirmek istediğinizden emin misiniz?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Evet, Değiştir',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('portfolio.php', {
                        action: 'toggle_status',
                        portfolio_id: portfolioId,
                        csrf_token: $('input[name="csrf_token"]').val()
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Hata!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'Tamam'
                            });
                        }
                    }, 'json');
                }
            });
        }
        
        function deletePortfolio(portfolioId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu projeyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('portfolio.php', {
                        action: 'delete_portfolio',
                        portfolio_id: portfolioId,
                        csrf_token: $('input[name="csrf_token"]').val()
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Silindi!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Hata!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'Tamam'
                            });
                        }
                    }, 'json');
                }
            });
        }
        
        function updateOrder() {
            const portfolioIds = [];
            $('.portfolio-item').each(function() {
                portfolioIds.push($(this).data('id'));
            });
            
            $.post('portfolio.php', {
                action: 'reorder_portfolio',
                portfolio: portfolioIds,
                csrf_token: $('input[name="csrf_token"]').val()
            }, function(response) {
                if (response.success) {
                    // Show success message briefly
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                }
            }, 'json');
        }
        
        function filterPortfolio() {
            const search = $('#searchInput').val();
            const status = $('#statusFilter').val();
            
            let url = 'portfolio.php?';
            if (search) url += 'search=' + encodeURIComponent(search) + '&';
            if (status) url += 'status=' + encodeURIComponent(status) + '&';
            
            window.location.href = url.slice(0, -1); // Remove last &
        }
        
        // Debounce function
        function debounce(func, wait, immediate) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }
    </script>
</body>
</html>
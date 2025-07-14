<?php
/**
 * Admin Services Management
 * Geliştirici: BERAT K
 * Services CRUD operations for content management
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
            case 'save_service':
                $response = saveService($_POST);
                break;
                
            case 'delete_service':
                $response = deleteService($_POST['service_id']);
                break;
                
            case 'toggle_status':
                $response = toggleServiceStatus($_POST['service_id']);
                break;
                
            case 'reorder_services':
                $response = reorderServices($_POST['services']);
                break;
        }
        
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Get services with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
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
$totalServices = $db->query("SELECT COUNT(*) as count FROM services" . $whereClause, $params)->fetch()['count'];
$totalPaginationPages = ceil($totalServices / $perPage);

// Get services
$services = $db->query(
    "SELECT * FROM services" . $whereClause . " ORDER BY order_position ASC, created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
)->fetchAll();

// Get service data for editing if ID provided
$editService = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editService = $db->query("SELECT * FROM services WHERE id = ?", [$_GET['edit']])->fetch();
}

$pageTitle = "Hizmet Yönetimi";

// Service functions
function saveService($data) {
    global $db;
    
    $title = sanitizeInput($data['title'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $icon = sanitizeInput($data['icon'] ?? '');
    $status = sanitizeInput($data['status'] ?? 'active');
    $order_position = (int)($data['order_position'] ?? 0);
    $service_id = (int)($data['service_id'] ?? 0);
    
    if (empty($title)) {
        return ['success' => false, 'message' => 'Hizmet başlığı gereklidir'];
    }
    
    if (empty($description)) {
        return ['success' => false, 'message' => 'Hizmet açıklaması gereklidir'];
    }
    
    $serviceData = [
        'title' => $title,
        'description' => $description,
        'icon' => $icon,
        'status' => $status,
        'order_position' => $order_position,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if ($service_id > 0) {
        // Update existing service
        $result = $db->update('services', $serviceData, ['id' => $service_id]);
        $message = $result ? 'Hizmet başarıyla güncellendi' : 'Hizmet güncellenirken hata oluştu';
    } else {
        // Create new service
        $serviceData['created_at'] = date('Y-m-d H:i:s');
        $result = $db->insert('services', $serviceData);
        $message = $result ? 'Yeni hizmet başarıyla eklendi' : 'Hizmet eklenirken hata oluştu';
    }
    
    return ['success' => (bool)$result, 'message' => $message];
}

function deleteService($service_id) {
    global $db;
    
    if (!is_numeric($service_id) || $service_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz hizmet ID'];
    }
    
    $result = $db->delete('services', ['id' => $service_id]);
    $message = $result ? 'Hizmet başarıyla silindi' : 'Hizmet silinirken hata oluştu';
    
    return ['success' => (bool)$result, 'message' => $message];
}

function toggleServiceStatus($service_id) {
    global $db;
    
    if (!is_numeric($service_id) || $service_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz hizmet ID'];
    }
    
    $service = $db->find('services', ['id' => $service_id]);
    if (!$service) {
        return ['success' => false, 'message' => 'Hizmet bulunamadı'];
    }
    
    $newStatus = $service['status'] === 'active' ? 'inactive' : 'active';
    $result = $db->update('services', ['status' => $newStatus], ['id' => $service_id]);
    
    $message = $result ? 'Hizmet durumu güncellendi' : 'Durum güncellenirken hata oluştu';
    
    return ['success' => (bool)$result, 'message' => $message];
}

function reorderServices($services) {
    global $db;
    
    if (!is_array($services)) {
        return ['success' => false, 'message' => 'Geçersiz veri formatı'];
    }
    
    $db->beginTransaction();
    
    try {
        foreach ($services as $index => $service_id) {
            $db->update('services', ['order_position' => $index + 1], ['id' => $service_id]);
        }
        
        $db->commit();
        return ['success' => true, 'message' => 'Sıralama güncellendi'];
        
    } catch (Exception $e) {
        $db->rollback();
        return ['success' => false, 'message' => 'Sıralama güncellenirken hata oluştu'];
    }
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
    <!-- Sortable -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
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
        
        .sortable-ghost {
            opacity: 0.4;
        }
        
        .sortable-chosen {
            background: #f8f9fa;
        }
        
        .drag-handle {
            cursor: move;
            color: #6c757d;
        }
        
        .drag-handle:hover {
            color: #495057;
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table th {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            font-weight: 500;
        }
        
        .table td {
            vertical-align: middle;
            border-color: #f1f3f4;
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
        
        .icon-preview {
            font-size: 2rem;
            color: #667eea;
            margin-right: 10px;
        }
        
        .icon-selector {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
        }
        
        .icon-option {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .icon-option:hover {
            background: #f8f9fa;
            transform: scale(1.1);
        }
        
        .icon-option.selected {
            background: #667eea;
            color: white;
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
                        <a class="nav-link active" href="services.php">
                            <i class="fas fa-cogs me-2"></i>
                            Hizmetler
                        </a>
                        <a class="nav-link" href="portfolio.php">
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
                                    <i class="fas fa-cogs me-3"></i>
                                    <?php echo $pageTitle; ?>
                                </h1>
                                <p class="mb-0 opacity-75">Hizmetlerinizi yönetin ve düzenleyin</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-light btn-lg" onclick="showServiceModal()">
                                    <i class="fas fa-plus me-2"></i>
                                    Yeni Hizmet Ekle
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
                                    <input type="text" class="form-control" placeholder="Hizmet ara..." 
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
                    
                    <!-- Services Table -->
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="servicesTable">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <i class="fas fa-arrows-alt-v"></i>
                                            </th>
                                            <th width="80">İkon</th>
                                            <th>Başlık</th>
                                            <th>Açıklama</th>
                                            <th width="100">Durum</th>
                                            <th width="120">Tarih</th>
                                            <th width="150">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody id="servicesList">
                                        <?php if (empty($services)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Henüz hizmet eklenmemiş</p>
                                                    <button class="btn btn-primary" onclick="showServiceModal()">
                                                        <i class="fas fa-plus me-2"></i>
                                                        İlk Hizmeti Ekle
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($services as $service): ?>
                                                <tr data-id="<?php echo $service['id']; ?>">
                                                    <td>
                                                        <i class="fas fa-grip-vertical drag-handle"></i>
                                                    </td>
                                                    <td>
                                                        <?php if ($service['icon']): ?>
                                                            <i class="<?php echo htmlspecialchars($service['icon']); ?> fa-2x text-primary"></i>
                                                        <?php else: ?>
                                                            <i class="fas fa-cog fa-2x text-muted"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($service['title']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0 text-muted">
                                                            <?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>
                                                            <?php if (strlen($service['description']) > 100): ?>...<?php endif; ?>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge status-<?php echo $service['status']; ?>">
                                                            <?php echo $service['status'] === 'active' ? 'Aktif' : 'Pasif'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo date('d.m.Y', strtotime($service['created_at'])); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary" 
                                                                    onclick="editService(<?php echo $service['id']; ?>)"
                                                                    title="Düzenle">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-outline-<?php echo $service['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                                    onclick="toggleStatus(<?php echo $service['id']; ?>)"
                                                                    title="<?php echo $service['status'] === 'active' ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                                                <i class="fas fa-<?php echo $service['status'] === 'active' ? 'eye-slash' : 'eye'; ?>"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger" 
                                                                    onclick="deleteService(<?php echo $service['id']; ?>)"
                                                                    title="Sil">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    
    <!-- Service Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-cogs me-2"></i>
                        <span id="modalTitle">Yeni Hizmet Ekle</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="serviceForm">
                        <input type="hidden" id="serviceId" name="service_id" value="0">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="serviceTitle" class="form-label">
                                        <i class="fas fa-heading me-2"></i>
                                        Hizmet Başlığı *
                                    </label>
                                    <input type="text" class="form-control" id="serviceTitle" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="serviceStatus" class="form-label">
                                        <i class="fas fa-toggle-on me-2"></i>
                                        Durum
                                    </label>
                                    <select class="form-select" id="serviceStatus" name="status">
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="serviceDescription" class="form-label">
                                <i class="fas fa-align-left me-2"></i>
                                Hizmet Açıklaması *
                            </label>
                            <textarea class="form-control" id="serviceDescription" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="serviceIcon" class="form-label">
                                        <i class="fas fa-icons me-2"></i>
                                        İkon Seçin
                                    </label>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="icon-preview" id="iconPreview">
                                            <i class="fas fa-cog"></i>
                                        </span>
                                        <input type="text" class="form-control" id="serviceIcon" name="icon" 
                                               placeholder="Örn: fas fa-cog" value="fas fa-cog">
                                    </div>
                                    <div class="icon-selector" id="iconSelector">
                                        <!-- İkonlar buraya JS ile yüklenecek -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="serviceOrder" class="form-label">
                                        <i class="fas fa-sort me-2"></i>
                                        Sıra
                                    </label>
                                    <input type="number" class="form-control" id="serviceOrder" name="order_position" min="0" value="0">
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
                    <button type="button" class="btn btn-primary" onclick="saveService()">
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
    
    <script>
        // Common icons for services
        const commonIcons = [
            'fas fa-cog', 'fas fa-star', 'fas fa-heart', 'fas fa-shield-alt',
            'fas fa-rocket', 'fas fa-trophy', 'fas fa-diamond', 'fas fa-crown',
            'fas fa-gift', 'fas fa-magic', 'fas fa-bolt', 'fas fa-fire',
            'fas fa-gem', 'fas fa-coins', 'fas fa-medal', 'fas fa-award',
            'fas fa-thumbs-up', 'fas fa-check-circle', 'fas fa-star-half-alt', 'fas fa-handshake'
        ];
        
        // Initialize page
        $(document).ready(function() {
            initializePage();
        });
        
        function initializePage() {
            // Initialize sortable
            if ($('#servicesList tr[data-id]').length > 0) {
                new Sortable(document.getElementById('servicesList'), {
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
                filterServices();
            }, 500));
            
            // Initialize status filter
            $('#statusFilter').on('change', function() {
                filterServices();
            });
            
            // Initialize icon selector
            loadIconSelector();
            
            // Icon input change
            $('#serviceIcon').on('input', function() {
                updateIconPreview($(this).val());
            });
        }
        
        function loadIconSelector() {
            const iconSelector = $('#iconSelector');
            iconSelector.empty();
            
            commonIcons.forEach(icon => {
                const iconOption = $(`
                    <div class="icon-option" data-icon="${icon}">
                        <i class="${icon}"></i>
                    </div>
                `);
                
                iconOption.on('click', function() {
                    const selectedIcon = $(this).data('icon');
                    $('#serviceIcon').val(selectedIcon);
                    updateIconPreview(selectedIcon);
                    $('.icon-option').removeClass('selected');
                    $(this).addClass('selected');
                });
                
                iconSelector.append(iconOption);
            });
        }
        
        function updateIconPreview(iconClass) {
            $('#iconPreview i').attr('class', iconClass || 'fas fa-cog');
        }
        
        function showServiceModal(serviceId = null) {
            if (serviceId) {
                // Edit mode
                $('#modalTitle').text('Hizmeti Düzenle');
                loadServiceData(serviceId);
            } else {
                // Add mode
                $('#modalTitle').text('Yeni Hizmet Ekle');
                $('#serviceForm')[0].reset();
                $('#serviceId').val('0');
                updateIconPreview('fas fa-cog');
                $('.icon-option').removeClass('selected');
                $('.icon-option[data-icon="fas fa-cog"]').addClass('selected');
            }
            
            $('#serviceModal').modal('show');
        }
        
        function loadServiceData(serviceId) {
            $.post('services.php', {
                action: 'get_service',
                service_id: serviceId,
                csrf_token: $('input[name="csrf_token"]').val()
            }, function(response) {
                if (response.success) {
                    const service = response.data;
                    $('#serviceId').val(service.id);
                    $('#serviceTitle').val(service.title);
                    $('#serviceDescription').val(service.description);
                    $('#serviceIcon').val(service.icon);
                    $('#serviceStatus').val(service.status);
                    $('#serviceOrder').val(service.order_position);
                    
                    updateIconPreview(service.icon);
                    $('.icon-option').removeClass('selected');
                    $('.icon-option[data-icon="' + service.icon + '"]').addClass('selected');
                }
            }, 'json');
        }
        
        function saveService() {
            const formData = $('#serviceForm').serialize() + '&action=save_service';
            
            $.post('services.php', formData, function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Tamam'
                    }).then(() => {
                        $('#serviceModal').modal('hide');
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
        
        function editService(serviceId) {
            showServiceModal(serviceId);
        }
        
        function toggleStatus(serviceId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Hizmet durumunu değiştirmek istediğinizden emin misiniz?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Evet, Değiştir',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('services.php', {
                        action: 'toggle_status',
                        service_id: serviceId,
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
        
        function deleteService(serviceId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu hizmeti silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('services.php', {
                        action: 'delete_service',
                        service_id: serviceId,
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
            const serviceIds = [];
            $('#servicesList tr[data-id]').each(function() {
                serviceIds.push($(this).data('id'));
            });
            
            $.post('services.php', {
                action: 'reorder_services',
                services: serviceIds,
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
        
        function filterServices() {
            const search = $('#searchInput').val();
            const status = $('#statusFilter').val();
            
            let url = 'services.php?';
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
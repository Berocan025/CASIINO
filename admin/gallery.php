<?php
/**
 * Admin Gallery Management
 * Geliştirici: BERAT K
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

$pageTitle = "Galeri Yönetimi";
$db = new Database();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'];
        $response = ['success' => false, 'message' => 'Geçersiz işlem'];
        
        switch ($action) {
            case 'save_gallery':
                $response = saveGallery($_POST, $_FILES);
                break;
                
            case 'delete_gallery':
                $response = deleteGallery($_POST['gallery_id']);
                break;
                
            case 'toggle_status':
                $response = toggleGalleryStatus($_POST['gallery_id']);
                break;
                
            case 'get_gallery':
                $response = getGallery($_POST['gallery_id']);
                break;
        }
        
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Get gallery with pagination
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
$totalGallery = $db->query("SELECT COUNT(*) as count FROM gallery" . $whereClause, $params)->fetch()['count'];
$totalPaginationPages = ceil($totalGallery / $perPage);

// Get gallery items
$gallery = $db->query(
    "SELECT * FROM gallery" . $whereClause . " ORDER BY sort_order ASC, created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
)->fetchAll();

// Gallery functions
function saveGallery($data, $files) {
    global $db;
    
    $title = sanitizeInput($data['title'] ?? '');
    $description = sanitizeInput($data['description'] ?? '');
    $alt_text = sanitizeInput($data['alt_text'] ?? '');
    $status = sanitizeInput($data['status'] ?? 'active');
    $sort_order = (int)($data['sort_order'] ?? 0);
    $gallery_id = (int)($data['gallery_id'] ?? 0);
    
    if (empty($title)) {
        return ['success' => false, 'message' => 'Görsel başlığı gereklidir'];
    }
    
    $galleryData = [
        'title' => $title,
        'description' => $description,
        'alt_text' => $alt_text,
        'status' => $status,
        'sort_order' => $sort_order,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Handle image upload
    if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
        $imageUpload = uploadGalleryImage($files['image']);
        if ($imageUpload['success']) {
            $galleryData['file_path'] = $imageUpload['filename'];
        } else {
            return ['success' => false, 'message' => $imageUpload['message']];
        }
    } elseif ($gallery_id === 0) {
        return ['success' => false, 'message' => 'Görsel dosyası gereklidir'];
    }
    
    if ($gallery_id > 0) {
        $result = $db->update('gallery', $galleryData, ['id' => $gallery_id]);
        $message = $result ? 'Galeri öğesi başarıyla güncellendi' : 'Öğe güncellenirken hata oluştu';
    } else {
        $galleryData['created_at'] = date('Y-m-d H:i:s');
        $result = $db->insert('gallery', $galleryData);
        $message = $result ? 'Yeni galeri öğesi başarıyla eklendi' : 'Öğe eklenirken hata oluştu';
    }
    
    return ['success' => (bool)$result, 'message' => $message];
}

function uploadGalleryImage($file) {
    $uploadDir = '../uploads/gallery/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Dosya boyutu 5MB\'dan büyük olamaz'];
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Sadece JPEG, PNG, GIF ve WebP formatları desteklenir'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Dosya yüklenirken hata oluştu'];
    }
}

function deleteGallery($gallery_id) {
    global $db;
    
    if (!is_numeric($gallery_id) || $gallery_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz galeri ID'];
    }
    
    $gallery = $db->find('gallery', ['id' => $gallery_id]);
    if ($gallery && $gallery['file_path']) {
        $imagePath = '../uploads/gallery/' . $gallery['file_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $result = $db->delete('gallery', ['id' => $gallery_id]);
    $message = $result ? 'Galeri öğesi başarıyla silindi' : 'Öğe silinirken hata oluştu';
    
    return ['success' => (bool)$result, 'message' => $message];
}

function toggleGalleryStatus($gallery_id) {
    global $db;
    
    if (!is_numeric($gallery_id) || $gallery_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz galeri ID'];
    }
    
    $gallery = $db->find('gallery', ['id' => $gallery_id]);
    if (!$gallery) {
        return ['success' => false, 'message' => 'Galeri öğesi bulunamadı'];
    }
    
    $newStatus = $gallery['status'] === 'active' ? 'inactive' : 'active';
    $result = $db->update('gallery', ['status' => $newStatus], ['id' => $gallery_id]);
    
    $message = $result ? 'Galeri durumu güncellendi' : 'Durum güncellenirken hata oluştu';
    
    return ['success' => (bool)$result, 'message' => $message];
}

function getGallery($gallery_id) {
    global $db;
    
    if (!is_numeric($gallery_id) || $gallery_id <= 0) {
        return ['success' => false, 'message' => 'Geçersiz galeri ID'];
    }
    
    $gallery = $db->find('gallery', ['id' => $gallery_id]);
    if (!$gallery) {
        return ['success' => false, 'message' => 'Galeri öğesi bulunamadı'];
    }
    
    return ['success' => true, 'data' => $gallery];
}

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

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.gallery-item {
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.gallery-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

.stat-icon {
    font-size: 3rem;
    background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.status-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.8em;
    font-weight: 500;
}

.status-active {
    background: rgba(40, 167, 69, 0.2);
    color: var(--success-color);
}

.status-inactive {
    background: rgba(220, 53, 69, 0.2);
    color: var(--danger-color);
}
</style>

<!-- Main Content -->
<div class="main-content">
    <div class="topnav">
        <div class="topnav-left">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title"><?php echo $pageTitle; ?></h1>
        </div>
        <div class="topnav-right">
            <div class="admin-dropdown">
                <a href="#" class="admin-profile" onclick="toggleDropdown()">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="admin-info">
                        <div class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></div>
                        <div class="admin-role">Admin</div>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="container-fluid p-4">
            <!-- Page Header -->
            <div class="dashboard-card">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <div>
                                <h2 class="mb-0 text-white">Galeri Yönetimi</h2>
                                <p class="mb-0" style="color: var(--text-light);">Galeri görsellerinizi yönetin</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-primary btn-lg" onclick="showGalleryModal()">
                            <i class="fas fa-plus me-2"></i>
                            Yeni Görsel Ekle
                        </button>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="dashboard-card">
                <div class="gallery-grid" id="galleryGrid">
                    <?php if (empty($gallery)): ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Henüz galeri görseli eklenmemiş</p>
                            <button class="btn btn-primary" onclick="showGalleryModal()">
                                <i class="fas fa-plus me-2"></i>
                                İlk Görseli Ekle
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($gallery as $item): ?>
                            <div class="gallery-item" data-id="<?php echo $item['id']; ?>">
                                <img src="../uploads/gallery/<?php echo htmlspecialchars($item['file_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['alt_text'] ?: $item['title']); ?>" 
                                     class="w-100" style="height: 200px; object-fit: cover;">
                                
                                <div class="p-3">
                                    <h5 class="text-white mb-2"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    
                                    <?php if ($item['description']): ?>
                                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="status-badge status-<?php echo $item['status']; ?>">
                                            <?php echo $item['status'] === 'active' ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                        
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editGallery(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteGallery(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images me-2"></i>
                    <span id="modalTitle">Yeni Görsel Ekle</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="galleryForm" enctype="multipart/form-data">
                    <input type="hidden" id="galleryId" name="gallery_id" value="0">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="galleryTitle" class="form-label">Görsel Başlığı *</label>
                                <input type="text" class="form-control" id="galleryTitle" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="galleryDescription" class="form-label">Açıklama</label>
                                <textarea class="form-control" id="galleryDescription" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="galleryAltText" class="form-label">Alt Metin</label>
                                <input type="text" class="form-control" id="galleryAltText" name="alt_text">
                            </div>
                            
                            <div class="mb-3">
                                <label for="galleryStatus" class="form-label">Durum</label>
                                <select class="form-select" id="galleryStatus" name="status">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Pasif</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="galleryImage" class="form-label">Görsel Dosyası *</label>
                                <input type="file" class="form-control" id="galleryImage" name="image" accept="image/*">
                                <img id="imagePreview" class="img-fluid mt-3 d-none" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="saveGallery()">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function showGalleryModal(galleryId = null) {
    if (galleryId) {
        $('#modalTitle').text('Görseli Düzenle');
        loadGalleryData(galleryId);
    } else {
        $('#modalTitle').text('Yeni Görsel Ekle');
        $('#galleryForm')[0].reset();
        $('#galleryId').val('0');
        $('#imagePreview').addClass('d-none');
    }
    
    $('#galleryModal').modal('show');
}

function loadGalleryData(galleryId) {
    $.post('gallery.php', {
        action: 'get_gallery',
        gallery_id: galleryId,
        csrf_token: $('input[name="csrf_token"]').val()
    }, function(response) {
        if (response.success) {
            const gallery = response.data;
            $('#galleryId').val(gallery.id);
            $('#galleryTitle').val(gallery.title);
            $('#galleryDescription').val(gallery.description);
            $('#galleryAltText').val(gallery.alt_text);
            $('#galleryStatus').val(gallery.status);
            
            if (gallery.file_path) {
                $('#imagePreview').attr('src', '../uploads/gallery/' + gallery.file_path).removeClass('d-none');
            }
        }
    }, 'json');
}

function saveGallery() {
    const formData = new FormData($('#galleryForm')[0]);
    formData.append('action', 'save_gallery');
    
    $.ajax({
        url: 'gallery.php',
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
                    $('#galleryModal').modal('hide');
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

function editGallery(galleryId) {
    showGalleryModal(galleryId);
}

function deleteGallery(galleryId) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu görseli silmek istediğinizden emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Evet, Sil',
        cancelButtonText: 'İptal',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('gallery.php', {
                action: 'delete_gallery',
                gallery_id: galleryId,
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

// Image preview
$('#galleryImage').on('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include 'includes/admin_footer.php'; ?>
<?php
/**
 * Admin Pages Management
 * Geliştirici: BERAT K
 * Pages CRUD operations for content management
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
            case 'save_page':
                $response = savePage($_POST);
                break;
                
            case 'delete_page':
                $response = deletePage($_POST['page_id']);
                break;
                
            case 'toggle_status':
                $response = togglePageStatus($_POST['page_id']);
                break;
                
            case 'reorder_pages':
                $response = reorderPages($_POST['pages']);
                break;
        }
        
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Get pages with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

// Build query
$whereClause = '';
$params = [];

if ($searchTerm) {
    $whereClause .= " WHERE (title LIKE ? OR slug LIKE ? OR content LIKE ?)";
    $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
}

if ($statusFilter) {
    $whereClause .= $whereClause ? " AND status = ?" : " WHERE status = ?";
    $params[] = $statusFilter;
}

// Get total count
$totalPages = $db->query("SELECT COUNT(*) as count FROM pages" . $whereClause, $params)->fetch()['count'];
$totalPaginationPages = ceil($totalPages / $perPage);

// Get pages
$pages = $db->query(
    "SELECT * FROM pages" . $whereClause . " ORDER BY sort_order ASC, created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
)->fetchAll();

// Get page data for editing if ID provided
$editPage = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editPage = $db->query("SELECT * FROM pages WHERE id = ?", [$_GET['edit']])->fetch();
}

$pageTitle = "Sayfa Yönetimi";
include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">📄 Sayfa Yönetimi</h1>
            <p class="text-muted">Web sitesi sayfalarını yönetin</p>
        </div>
        <button class="btn btn-gradient-primary" data-bs-toggle="modal" data-bs-target="#pageModal">
            <i class="fas fa-plus me-2"></i>Yeni Sayfa
        </button>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Sayfa ara...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tüm Durumlar</option>
                        <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Yayında</option>
                        <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>Taslak</option>
                        <option value="private" <?php echo $statusFilter === 'private' ? 'selected' : ''; ?>>Özel</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filtrele
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="?" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>Temizle
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php
        $stats = [
            ['label' => 'Toplam Sayfa', 'value' => $db->query("SELECT COUNT(*) as count FROM pages")->fetch()['count'], 'icon' => 'fas fa-file-alt', 'color' => 'primary'],
            ['label' => 'Yayında', 'value' => $db->query("SELECT COUNT(*) as count FROM pages WHERE status = 'published'")->fetch()['count'], 'icon' => 'fas fa-eye', 'color' => 'success'],
            ['label' => 'Taslak', 'value' => $db->query("SELECT COUNT(*) as count FROM pages WHERE status = 'draft'")->fetch()['count'], 'icon' => 'fas fa-edit', 'color' => 'warning'],
            ['label' => 'Özel', 'value' => $db->query("SELECT COUNT(*) as count FROM pages WHERE status = 'private'")->fetch()['count'], 'icon' => 'fas fa-lock', 'color' => 'info'],
        ];
        
        foreach ($stats as $stat): ?>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-<?php echo $stat['color']; ?>">
                            <i class="<?php echo $stat['icon']; ?>"></i>
                        </div>
                        <div>
                            <h3 class="stat-number"><?php echo $stat['value']; ?></h3>
                            <p class="stat-label"><?php echo $stat['label']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pages Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Sayfalar</h5>
        </div>
        <div class="card-body">
            <?php if (empty($pages)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5>Henüz sayfa yok</h5>
                    <p class="text-muted">İlk sayfanızı oluşturmak için "Yeni Sayfa" butonuna tıklayın.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sıra</th>
                                <th>Başlık</th>
                                <th>Slug</th>
                                <th>Durum</th>
                                <th>Oluşturma</th>
                                <th>Güncelleme</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-pages">
                            <?php foreach ($pages as $page): ?>
                            <tr data-page-id="<?php echo $page['id']; ?>">
                                <td>
                                    <span class="drag-handle text-muted" style="cursor: move;">
                                        <i class="fas fa-grip-vertical"></i>
                                    </span>
                                    <?php echo $page['sort_order']; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($page['title']); ?></h6>
                                            <small class="text-muted"><?php echo substr(strip_tags($page['content']), 0, 60) . '...'; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code><?php echo htmlspecialchars($page['slug']); ?></code>
                                    <a href="/<?php echo $page['slug']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'published' => 'success',
                                        'draft' => 'warning',
                                        'private' => 'info'
                                    ];
                                    $statusText = [
                                        'published' => 'Yayında',
                                        'draft' => 'Taslak',
                                        'private' => 'Özel'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass[$page['status']]; ?>">
                                        <?php echo $statusText[$page['status']]; ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo formatDateTime($page['created_at']); ?>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo $page['updated_at'] ? formatDateTime($page['updated_at']) : '-'; ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary edit-page" 
                                                data-page-id="<?php echo $page['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#pageModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-<?php echo $page['status'] === 'published' ? 'warning' : 'success'; ?> toggle-status" 
                                                data-page-id="<?php echo $page['id']; ?>"
                                                title="<?php echo $page['status'] === 'published' ? 'Yayından Kaldır' : 'Yayınla'; ?>">
                                            <i class="fas fa-<?php echo $page['status'] === 'published' ? 'eye-slash' : 'eye'; ?>"></i>
                                        </button>
                                        <button class="btn btn-outline-danger delete-page" 
                                                data-page-id="<?php echo $page['id']; ?>"
                                                data-page-title="<?php echo htmlspecialchars($page['title']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPaginationPages > 1): ?>
                <nav aria-label="Pages pagination">
                    <ul class="pagination justify-content-center mt-4">
                        <?php for ($i = 1; $i <= $totalPaginationPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>&status=<?php echo urlencode($statusFilter); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Page Modal -->
<div class="modal fade" id="pageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span id="modal-title-new">Yeni Sayfa</span>
                    <span id="modal-title-edit" style="display: none;">Sayfa Düzenle</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="save_page">
                    <input type="hidden" name="page_id" id="page_id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="page_title" class="form-label">Sayfa Başlığı *</label>
                                <input type="text" class="form-control" name="title" id="page_title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Slug -->
                            <div class="mb-3">
                                <label for="page_slug" class="form-label">URL Slug *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><?php echo SITE_URL; ?>/</span>
                                    <input type="text" class="form-control" name="slug" id="page_slug" required>
                                </div>
                                <div class="form-text">URL dostu format kullanın (örn: hakkimizda, iletisim)</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Content -->
                            <div class="mb-3">
                                <label for="page_content" class="form-label">İçerik *</label>
                                <textarea class="form-control" name="content" id="page_content" rows="15" required></textarea>
                                <div class="form-text">HTML kodları kullanabilirsiniz</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <!-- Meta Description -->
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Açıklama</label>
                                <textarea class="form-control" name="meta_description" id="meta_description" rows="3" maxlength="160"></textarea>
                                <div class="form-text">SEO için önemli (maksimum 160 karakter)</div>
                            </div>
                            
                            <!-- Meta Keywords -->
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Meta Anahtar Kelimeler</label>
                                <input type="text" class="form-control" name="meta_keywords" id="meta_keywords">
                                <div class="form-text">Virgül ile ayırın</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="page_status" class="form-label">Durum</label>
                                <select class="form-select" name="status" id="page_status">
                                    <option value="draft">Taslak</option>
                                    <option value="published">Yayında</option>
                                    <option value="private">Özel</option>
                                </select>
                            </div>
                            
                            <!-- Sort Order -->
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sıralama</label>
                                <input type="number" class="form-control" name="sort_order" id="sort_order" value="0" min="0">
                                <div class="form-text">Küçük sayı üstte görünür</div>
                            </div>
                            
                            <!-- Show in Menu -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_in_menu" id="show_in_menu" value="1">
                                    <label class="form-check-label" for="show_in_menu">
                                        Menüde Göster
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Template -->
                            <div class="mb-3">
                                <label for="template" class="form-label">Şablon</label>
                                <select class="form-select" name="template" id="template">
                                    <option value="default">Varsayılan</option>
                                    <option value="full-width">Tam Genişlik</option>
                                    <option value="sidebar">Kenar Çubuğu</option>
                                    <option value="landing">Landing Page</option>
                                </select>
                            </div>
                            
                            <!-- Featured Image -->
                            <div class="mb-3">
                                <label for="featured_image" class="form-label">Öne Çıkan Görsel</label>
                                <input type="url" class="form-control" name="featured_image" id="featured_image">
                                <div class="form-text">Görsel URL'si</div>
                            </div>
                            
                            <!-- Page Preview -->
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Sayfa Önizleme</h6>
                                </div>
                                <div class="card-body">
                                    <p class="small text-muted">Sayfa kaydedildikten sonra önizleme linki burada görünecek.</p>
                                    <div id="page-preview" style="display: none;">
                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-external-link-alt me-1"></i>Önizleme
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-generate slug from title
    $('#page_title').on('input', function() {
        if (!$('#page_id').val()) { // Only for new pages
            const title = $(this).val();
            const slug = generateSlug(title);
            $('#page_slug').val(slug);
        }
    });
    
    // Page form submission
    $('#pageForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Kaydediliyor...');
        
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#pageModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('error', response.message);
                    if (response.errors) {
                        showFormErrors('#pageForm', response.errors);
                    }
                }
            },
            error: function() {
                showAlert('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Edit page
    $('.edit-page').on('click', function() {
        const pageId = $(this).data('page-id');
        loadPageData(pageId);
    });
    
    // Delete page
    $('.delete-page').on('click', function() {
        const pageId = $(this).data('page-id');
        const pageTitle = $(this).data('page-title');
        
        if (confirm(`"${pageTitle}" sayfasını silmek istediğinizden emin misiniz?`)) {
            deletePage(pageId);
        }
    });
    
    // Toggle status
    $('.toggle-status').on('click', function() {
        const pageId = $(this).data('page-id');
        toggleStatus(pageId);
    });
    
    // Modal reset
    $('#pageModal').on('hidden.bs.modal', function() {
        resetForm();
    });
    
    // Sortable pages
    $('#sortable-pages').sortable({
        handle: '.drag-handle',
        update: function(event, ui) {
            const pages = [];
            $('#sortable-pages tr').each(function(index) {
                pages.push({
                    id: $(this).data('page-id'),
                    order: index + 1
                });
            });
            
            $.ajax({
                url: '',
                type: 'POST',
                data: {
                    action: 'reorder_pages',
                    pages: pages,
                    csrf_token: $('input[name="csrf_token"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Sayfa sıralaması güncellendi');
                    }
                }
            });
        }
    });
});

function loadPageData(pageId) {
    $.ajax({
        url: '?ajax=get_page&id=' + pageId,
        type: 'GET',
        success: function(page) {
            $('#page_id').val(page.id);
            $('#page_title').val(page.title);
            $('#page_slug').val(page.slug);
            $('#page_content').val(page.content);
            $('#meta_description').val(page.meta_description);
            $('#meta_keywords').val(page.meta_keywords);
            $('#page_status').val(page.status);
            $('#sort_order').val(page.sort_order);
            $('#show_in_menu').prop('checked', page.show_in_menu == 1);
            $('#template').val(page.template);
            $('#featured_image').val(page.featured_image);
            
            $('#modal-title-new').hide();
            $('#modal-title-edit').show();
            
            // Show preview link
            $('#page-preview a').attr('href', '/' + page.slug);
            $('#page-preview').show();
        }
    });
}

function deletePage(pageId) {
    $.ajax({
        url: '',
        type: 'POST',
        data: {
            action: 'delete_page',
            page_id: pageId,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', response.message);
            }
        }
    });
}

function toggleStatus(pageId) {
    $.ajax({
        url: '',
        type: 'POST',
        data: {
            action: 'toggle_status',
            page_id: pageId,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', response.message);
            }
        }
    });
}

function resetForm() {
    $('#pageForm')[0].reset();
    $('#page_id').val('');
    $('#modal-title-new').show();
    $('#modal-title-edit').hide();
    $('#page-preview').hide();
    clearFormErrors('#pageForm');
}

function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ı/g, 'i')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
}
</script>

<?php include 'includes/admin_footer.php'; ?>

<?php
// AJAX handler functions
function savePage($data) {
    global $db;
    
    $pageId = !empty($data['page_id']) ? (int)$data['page_id'] : null;
    $title = sanitizeInput($data['title']);
    $slug = sanitizeInput($data['slug']);
    $content = $data['content']; // Allow HTML
    $metaDescription = sanitizeInput($data['meta_description'] ?? '');
    $metaKeywords = sanitizeInput($data['meta_keywords'] ?? '');
    $status = in_array($data['status'], ['draft', 'published', 'private']) ? $data['status'] : 'draft';
    $sortOrder = (int)($data['sort_order'] ?? 0);
    $showInMenu = isset($data['show_in_menu']) ? 1 : 0;
    $template = sanitizeInput($data['template'] ?? 'default');
    $featuredImage = sanitizeInput($data['featured_image'] ?? '');
    
    // Validation
    if (empty($title)) {
        return ['success' => false, 'message' => 'Sayfa başlığı gereklidir'];
    }
    
    if (empty($slug)) {
        return ['success' => false, 'message' => 'URL slug gereklidir'];
    }
    
    if (empty($content)) {
        return ['success' => false, 'message' => 'İçerik gereklidir'];
    }
    
    // Check slug uniqueness
    $existingPage = $db->query(
        "SELECT id FROM pages WHERE slug = ? AND id != ?",
        [$slug, $pageId ?: 0]
    )->fetch();
    
    if ($existingPage) {
        return ['success' => false, 'message' => 'Bu URL slug zaten kullanılıyor'];
    }
    
    $pageData = [
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'meta_description' => $metaDescription,
        'meta_keywords' => $metaKeywords,
        'status' => $status,
        'sort_order' => $sortOrder,
        'show_in_menu' => $showInMenu,
        'template' => $template,
        'featured_image' => $featuredImage,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if ($pageId) {
        // Update existing page
        $success = $db->update('pages', $pageData, ['id' => $pageId]);
        $message = $success ? 'Sayfa başarıyla güncellendi' : 'Sayfa güncellenirken hata oluştu';
    } else {
        // Create new page
        $pageData['created_at'] = date('Y-m-d H:i:s');
        $pageId = $db->insert('pages', $pageData);
        $success = (bool)$pageId;
        $message = $success ? 'Sayfa başarıyla oluşturuldu' : 'Sayfa oluşturulurken hata oluştu';
    }
    
    return ['success' => $success, 'message' => $message, 'page_id' => $pageId];
}

function deletePage($pageId) {
    global $db;
    
    $pageId = (int)$pageId;
    if (!$pageId) {
        return ['success' => false, 'message' => 'Geçersiz sayfa ID'];
    }
    
    $success = $db->delete('pages', ['id' => $pageId]);
    $message = $success ? 'Sayfa başarıyla silindi' : 'Sayfa silinirken hata oluştu';
    
    return ['success' => $success, 'message' => $message];
}

function togglePageStatus($pageId) {
    global $db;
    
    $pageId = (int)$pageId;
    if (!$pageId) {
        return ['success' => false, 'message' => 'Geçersiz sayfa ID'];
    }
    
    $page = $db->query("SELECT status FROM pages WHERE id = ?", [$pageId])->fetch();
    if (!$page) {
        return ['success' => false, 'message' => 'Sayfa bulunamadı'];
    }
    
    $newStatus = $page['status'] === 'published' ? 'draft' : 'published';
    $success = $db->update('pages', ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $pageId]);
    
    $statusText = $newStatus === 'published' ? 'yayınlandı' : 'taslağa alındı';
    $message = $success ? "Sayfa başarıyla $statusText" : 'Durum değiştirilirken hata oluştu';
    
    return ['success' => $success, 'message' => $message];
}

function reorderPages($pages) {
    global $db;
    
    if (!is_array($pages)) {
        return ['success' => false, 'message' => 'Geçersiz veri'];
    }
    
    $db->beginTransaction();
    
    try {
        foreach ($pages as $page) {
            $db->update('pages', ['sort_order' => $page['order']], ['id' => $page['id']]);
        }
        
        $db->commit();
        return ['success' => true, 'message' => 'Sıralama güncellendi'];
        
    } catch (Exception $e) {
        $db->rollback();
        return ['success' => false, 'message' => 'Sıralama güncellenirken hata oluştu'];
    }
}
?>
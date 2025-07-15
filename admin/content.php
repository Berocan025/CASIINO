<?php
/**
 * Content Management System
 * Geliştirici: BERAT K
 * Tüm sayfalardaki içerikleri yönetmek için
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
            case 'save_content':
                $response = saveContent($_POST);
                break;
                
            case 'update_logo':
                $response = updateLogo($_FILES);
                break;
                
            case 'update_favicon':
                $response = updateFavicon($_FILES);
                break;
                
            default:
                $response = ['success' => false, 'message' => 'Geçersiz işlem'];
        }
        
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Content management functions
function saveContent($data) {
    global $db;
    
    $content_key = sanitizeInput($data['content_key'] ?? '');
    $content_value = sanitizeInput($data['content_value'] ?? '');
    $page_name = sanitizeInput($data['page_name'] ?? '');
    
    if (empty($content_key) || empty($content_value)) {
        return ['success' => false, 'message' => 'İçerik anahtarı ve değeri gereklidir'];
    }
    
    // Check if content exists
    $existingContent = $db->find('page_content', ['content_key' => $content_key]);
    
    if ($existingContent) {
        // Update existing content
        $result = $db->update('page_content', [
            'content_value' => $content_value,
            'page_name' => $page_name,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['content_key' => $content_key]);
        
        if ($result) {
            return ['success' => true, 'message' => 'İçerik başarıyla güncellendi'];
        } else {
            return ['success' => false, 'message' => 'İçerik güncellenirken hata oluştu'];
        }
    } else {
        // Create new content
        $result = $db->insert('page_content', [
            'content_key' => $content_key,
            'content_value' => $content_value,
            'page_name' => $page_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            return ['success' => true, 'message' => 'İçerik başarıyla eklendi'];
        } else {
            return ['success' => false, 'message' => 'İçerik eklenirken hata oluştu'];
        }
    }
}

function updateLogo($files) {
    if (!isset($files['logo']) || $files['logo']['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Logo dosyası yüklenemedi'];
    }
    
    $uploadDir = '../assets/images/';
    $fileName = 'logo.' . pathinfo($files['logo']['name'], PATHINFO_EXTENSION);
    $uploadPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($files['logo']['tmp_name'], $uploadPath)) {
        return ['success' => true, 'message' => 'Logo başarıyla güncellendi'];
    } else {
        return ['success' => false, 'message' => 'Logo yüklenirken hata oluştu'];
    }
}

function updateFavicon($files) {
    if (!isset($files['favicon']) || $files['favicon']['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Favicon dosyası yüklenemedi'];
    }
    
    $uploadDir = '../assets/images/';
    $fileName = 'favicon.ico';
    $uploadPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($files['favicon']['tmp_name'], $uploadPath)) {
        return ['success' => true, 'message' => 'Favicon başarıyla güncellendi'];
    } else {
        return ['success' => false, 'message' => 'Favicon yüklenirken hata oluştu'];
    }
}

// Get all content
$contents = $db->findAll('page_content', [], 'page_name ASC, content_key ASC') ?? [];

// Group content by page
$pageContents = [];
foreach ($contents as $content) {
    $pageContents[$content['page_name']][] = $content;
}

// Add default contents if empty
if (empty($pageContents)) {
    $defaultContents = [
        'home' => [
            'hero_title' => 'BERAT K',
            'hero_subtitle' => 'CASINO YAYINCISI',
            'hero_description' => 'Profesyonel Casino Streaming & Dijital Pazarlama ile başarıya ulaşın!',
            'services_title' => 'HİZMETLERİM',
            'services_subtitle' => 'Casino dünyasında profesyonel hizmetler ile başarıya ulaşın',
            'portfolio_title' => 'PORTFÖYÜM',
            'portfolio_subtitle' => 'Gerçekleştirdiğim başarılı projeler',
            'gallery_title' => 'GALERİ',
            'gallery_subtitle' => 'En iyi anlarım',
            'contact_title' => 'İLETİŞİM',
            'contact_subtitle' => 'Benimle iletişime geçin'
        ],
        'about' => [
            'page_title' => 'HAKKIMDA',
            'page_subtitle' => 'Profesyonel Casino Yayıncısı',
            'bio_title' => 'Kimim Ben?',
            'bio_content' => 'Merhaba! Ben BERAT K, profesyonel casino yayıncısı ve dijital pazarlama uzmanıyım.',
            'experience_title' => 'Deneyim',
            'experience_content' => '5+ yıllık casino streaming deneyimi',
            'achievements_title' => 'Başarılar',
            'achievements_content' => '100K+ takipçi, 2000+ canlı yayın'
        ],
        'services' => [
            'page_title' => 'HİZMETLERİM',
            'page_subtitle' => 'Profesyonel Casino Streaming Hizmetleri',
            'service_1_title' => 'Casino Streaming',
            'service_1_description' => 'Profesyonel casino yayıncılığı hizmetleri',
            'service_2_title' => 'Dijital Pazarlama',
            'service_2_description' => 'Sosyal medya ve dijital pazarlama stratejileri',
            'service_3_title' => 'Danışmanlık',
            'service_3_description' => 'Casino streaming danışmanlığı'
        ],
        'portfolio' => [
            'page_title' => 'PORTFÖYÜM',
            'page_subtitle' => 'Gerçekleştirdiğim Başarılı Projeler',
            'project_info' => 'Her proje, müşterilerimin hedeflerine ulaşması için özenle tasarlanmıştır.'
        ],
        'gallery' => [
            'page_title' => 'GALERİ',
            'page_subtitle' => 'En İyi Anlarım',
            'gallery_info' => 'Yayın kariyerimden özel fotoğraflar ve unutulmaz anlar'
        ],
        'contact' => [
            'page_title' => 'İLETİŞİM',
            'page_subtitle' => 'Benimle İletişime Geçin',
            'contact_info' => 'Proje teklifleri ve iş birlikleri için benimle iletişime geçebilirsiniz.',
            'office_hours' => 'Mesai Saatleri: 09:00 - 18:00',
            'response_time' => 'Ortalama Yanıt Süresi: 2-4 saat'
        ]
    ];
    
    // Insert default contents
    foreach ($defaultContents as $pageName => $contents) {
        foreach ($contents as $key => $value) {
            $db->insert('page_content', [
                'content_key' => $key,
                'content_value' => $value,
                'page_name' => $pageName,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    // Reload contents
    $contents = $db->findAll('page_content', [], 'page_name ASC, content_key ASC') ?? [];
    $pageContents = [];
    foreach ($contents as $content) {
        $pageContents[$content['page_name']][] = $content;
    }
}

$pageTitle = 'İçerik Yönetimi';
require_once 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h2><i class="fas fa-file-text"></i> İçerik Yönetimi</h2>
        <p>Tüm sayfalardaki metinleri ve medya dosyalarını yönetin</p>
    </div>
    
    <div class="row">
        <!-- Logo ve Favicon Yönetimi -->
        <div class="col-md-4 mb-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-image"></i> Logo ve Favicon</h5>
                </div>
                <div class="admin-card-body">
                    <!-- Logo Upload -->
                    <form id="logoForm" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo Dosyası</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Logo Güncelle
                        </button>
                    </form>
                    
                    <hr>
                    
                    <!-- Favicon Upload -->
                    <form id="faviconForm" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <div class="mb-3">
                            <label for="favicon" class="form-label">Favicon Dosyası</label>
                            <input type="file" class="form-control" id="favicon" name="favicon" accept=".ico,.png" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Favicon Güncelle
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- İçerik Yönetimi -->
        <div class="col-md-8 mb-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="fas fa-edit"></i> Sayfa İçerikleri</h5>
                    <button class="btn btn-success btn-sm" onclick="showContentModal()">
                        <i class="fas fa-plus"></i> Yeni İçerik Ekle
                    </button>
                </div>
                <div class="admin-card-body">
                    <div class="accordion" id="contentAccordion">
                        <?php foreach ($pageContents as $pageName => $contents): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= md5($pageName) ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= md5($pageName) ?>" aria-expanded="false">
                                        <i class="fas fa-file-alt me-2"></i> <?= ucfirst($pageName) ?> Sayfası (<?= count($contents) ?> içerik)
                                    </button>
                                </h2>
                                <div id="collapse<?= md5($pageName) ?>" class="accordion-collapse collapse" data-bs-parent="#contentAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Anahtar</th>
                                                        <th>Değer</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($contents as $content): ?>
                                                        <tr>
                                                            <td><code><?= htmlspecialchars($content['content_key']) ?></code></td>
                                                            <td><?= htmlspecialchars(substr($content['content_value'], 0, 50)) ?>...</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" onclick="editContent('<?= $content['content_key'] ?>', '<?= htmlspecialchars($content['content_value']) ?>', '<?= $content['page_name'] ?>')">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-danger" onclick="deleteContent('<?= $content['content_key'] ?>')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Modal -->
<div class="modal fade" id="contentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İçerik Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="contentForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="contentKey" class="form-label">İçerik Anahtarı</label>
                        <input type="text" class="form-control" id="contentKey" name="content_key" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pageName" class="form-label">Sayfa Adı</label>
                        <select class="form-select" id="pageName" name="page_name" required>
                            <option value="">Sayfa Seçin</option>
                            <option value="home">Ana Sayfa</option>
                            <option value="about">Hakkımda</option>
                            <option value="services">Hizmetler</option>
                            <option value="portfolio">Portfolyo</option>
                            <option value="gallery">Galeri</option>
                            <option value="contact">İletişim</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contentValue" class="form-label">İçerik Değeri</label>
                        <textarea class="form-control" id="contentValue" name="content_value" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Content form submission
    document.getElementById('contentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'save_content');
        
        fetch('content.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Başarılı!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                }).then(() => {
                    document.getElementById('contentModal').querySelector('.btn-close').click();
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Hata!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    });
    
    // Logo form submission
    document.getElementById('logoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'update_logo');
        
        fetch('content.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Başarılı!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                });
            } else {
                Swal.fire({
                    title: 'Hata!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    });
    
    // Favicon form submission
    document.getElementById('faviconForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'update_favicon');
        
        fetch('content.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Başarılı!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Tamam'
                });
            } else {
                Swal.fire({
                    title: 'Hata!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    });
    
    // Show content modal
    function showContentModal() {
        document.getElementById('contentKey').value = '';
        document.getElementById('contentValue').value = '';
        document.getElementById('pageName').value = '';
        new bootstrap.Modal(document.getElementById('contentModal')).show();
    }
    
    // Edit content
    function editContent(key, value, pageName) {
        document.getElementById('contentKey').value = key;
        document.getElementById('contentValue').value = value;
        document.getElementById('pageName').value = pageName;
        new bootstrap.Modal(document.getElementById('contentModal')).show();
    }
    
    // Delete content
    function deleteContent(key) {
        Swal.fire({
            title: 'Emin misiniz?',
            text: 'Bu içerik silinecek!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Delete işlemi burada yapılacak
                location.reload();
            }
        });
    }
</script>

<?php require_once 'includes/admin_footer.php'; ?>
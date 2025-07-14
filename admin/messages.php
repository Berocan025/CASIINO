<?php
/**
 * Admin Messages Management
 * Geliştirici: BERAT K
 * Contact form messages and newsletter management
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
            case 'mark_read':
                $response = markMessageRead($_POST['message_id']);
                break;
                
            case 'mark_unread':
                $response = markMessageUnread($_POST['message_id']);
                break;
                
            case 'delete_message':
                $response = deleteMessage($_POST['message_id']);
                break;
                
            case 'reply_message':
                $response = replyToMessage($_POST);
                break;
                
            case 'bulk_action':
                $response = handleBulkAction($_POST);
                break;
        }
        
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Get messages with filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$dateFrom = isset($_GET['date_from']) ? sanitizeInput($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? sanitizeInput($_GET['date_to']) : '';

// Build query
$whereClause = '';
$params = [];

if ($statusFilter) {
    $whereClause .= " WHERE status = ?";
    $params[] = $statusFilter;
}

if ($searchTerm) {
    $whereClause .= $whereClause ? " AND" : " WHERE";
    $whereClause .= " (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
}

if ($dateFrom) {
    $whereClause .= $whereClause ? " AND" : " WHERE";
    $whereClause .= " DATE(created_at) >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $whereClause .= $whereClause ? " AND" : " WHERE";
    $whereClause .= " DATE(created_at) <= ?";
    $params[] = $dateTo;
}

// Get total count
$totalMessages = $db->query("SELECT COUNT(*) as count FROM messages" . $whereClause, $params)->fetch()['count'];
$totalPages = ceil($totalMessages / $perPage);

// Get messages
$messages = $db->query(
    "SELECT * FROM messages" . $whereClause . " ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
)->fetchAll();

$pageTitle = "Mesajlar";
include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">💬 Mesajlar</h1>
            <p class="text-muted">İletişim formu mesajları ve newsletter aboneleri</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#replyModal">
                <i class="fas fa-reply me-2"></i>Toplu Yanıt
            </button>
            <button class="btn btn-outline-secondary" onclick="exportMessages()">
                <i class="fas fa-download me-2"></i>Dışa Aktar
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php
        $stats = [
            ['label' => 'Toplam Mesaj', 'value' => $db->query("SELECT COUNT(*) as count FROM messages")->fetch()['count'], 'icon' => 'fas fa-envelope', 'color' => 'primary'],
            ['label' => 'Okunmamış', 'value' => $db->query("SELECT COUNT(*) as count FROM messages WHERE status = 'unread'")->fetch()['count'], 'icon' => 'fas fa-envelope-open', 'color' => 'warning'],
            ['label' => 'Bu Hafta', 'value' => $db->query("SELECT COUNT(*) as count FROM messages WHERE created_at >= datetime('now', '-7 days')")->fetch()['count'], 'icon' => 'fas fa-calendar-week', 'color' => 'info'],
            ['label' => 'Newsletter Abonesi', 'value' => $db->query("SELECT COUNT(*) as count FROM newsletter_subscribers WHERE status = 'active'")->fetch()['count'], 'icon' => 'fas fa-users', 'color' => 'success'],
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

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Mesaj ara...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tüm Durumlar</option>
                        <option value="unread" <?php echo $statusFilter === 'unread' ? 'selected' : ''; ?>>Okunmamış</option>
                        <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Okunmuş</option>
                        <option value="replied" <?php echo $statusFilter === 'replied' ? 'selected' : ''; ?>>Yanıtlanmış</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>" placeholder="Başlangıç">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>" placeholder="Bitiş">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="?" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>Temizle
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Mesajlar</h5>
                <div class="bulk-actions" style="display: none;">
                    <select id="bulkAction" class="form-select me-2" style="width: auto;">
                        <option value="">Toplu İşlem Seç</option>
                        <option value="mark_read">Okundu İşaretle</option>
                        <option value="mark_unread">Okunmadı İşaretle</option>
                        <option value="delete">Sil</option>
                    </select>
                    <button class="btn btn-primary btn-sm" onclick="executeBulkAction()">Uygula</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($messages)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>Henüz mesaj yok</h5>
                    <p class="text-muted">İletişim formu üzerinden gelen mesajlar burada görünecek.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Durum</th>
                                <th>Gönderen</th>
                                <th>Konu</th>
                                <th>Hizmet</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $message): ?>
                            <tr class="<?php echo $message['status'] === 'unread' ? 'table-warning' : ''; ?>" data-message-id="<?php echo $message['id']; ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input message-checkbox" value="<?php echo $message['id']; ?>">
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'unread' => 'warning',
                                        'read' => 'info',
                                        'replied' => 'success'
                                    ];
                                    $statusText = [
                                        'unread' => 'Okunmadı',
                                        'read' => 'Okundu',
                                        'replied' => 'Yanıtlandı'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass[$message['status']]; ?>">
                                        <?php echo $statusText[$message['status']]; ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($message['name']); ?></strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($message['email']); ?>
                                        </small>
                                        <?php if ($message['phone']): ?>
                                        <br><small class="text-muted">
                                            <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($message['phone']); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($message['subject']); ?></strong><br>
                                    <small class="text-muted"><?php echo substr(htmlspecialchars($message['message']), 0, 60) . '...'; ?></small>
                                </td>
                                <td>
                                    <?php if ($message['service']): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($message['service']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($message['package']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($message['package']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo formatDateTime($message['created_at']); ?><br>
                                        <i class="fas fa-globe me-1"></i><?php echo htmlspecialchars($message['ip_address']); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary view-message" 
                                                data-message-id="<?php echo $message['id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#messageModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($message['status'] === 'unread'): ?>
                                        <button class="btn btn-outline-warning mark-read" 
                                                data-message-id="<?php echo $message['id']; ?>">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-outline-info mark-unread" 
                                                data-message-id="<?php echo $message['id']; ?>">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn btn-outline-success reply-message" 
                                                data-message-id="<?php echo $message['id']; ?>"
                                                data-email="<?php echo htmlspecialchars($message['email']); ?>"
                                                data-name="<?php echo htmlspecialchars($message['name']); ?>"
                                                data-subject="Re: <?php echo htmlspecialchars($message['subject']); ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#replyModal">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                        <button class="btn btn-outline-danger delete-message" 
                                                data-message-id="<?php echo $message['id']; ?>">
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
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Messages pagination">
                    <ul class="pagination justify-content-center mt-4">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($statusFilter); ?>&search=<?php echo urlencode($searchTerm); ?>&date_from=<?php echo urlencode($dateFrom); ?>&date_to=<?php echo urlencode($dateTo); ?>">
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

<!-- Message View Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mesaj Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- Message content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="replyFromModal">Yanıtla</button>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mesaj Yanıtla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="replyForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="reply_message">
                    <input type="hidden" name="message_id" id="reply_message_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reply_to" class="form-label">Alıcı</label>
                                <input type="email" class="form-control" name="to" id="reply_to" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reply_name" class="form-label">Alıcı Adı</label>
                                <input type="text" class="form-control" name="name" id="reply_name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reply_subject" class="form-label">Konu</label>
                        <input type="text" class="form-control" name="subject" id="reply_subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reply_message" class="form-label">Mesaj</label>
                        <textarea class="form-control" name="message" id="reply_message" rows="8" required></textarea>
                        <div class="form-text">HTML etiketleri kullanabilirsiniz</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mark_replied" id="mark_replied" checked>
                            <label class="form-check-label" for="mark_replied">
                                Mesajı yanıtlandı olarak işaretle
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Gönder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.message-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });
    
    // Individual checkboxes
    $('.message-checkbox').on('change', function() {
        const totalChecked = $('.message-checkbox:checked').length;
        $('#selectAll').prop('checked', totalChecked === $('.message-checkbox').length);
        toggleBulkActions();
    });
    
    // View message
    $('.view-message').on('click', function() {
        const messageId = $(this).data('message-id');
        loadMessage(messageId);
    });
    
    // Mark read/unread
    $('.mark-read, .mark-unread').on('click', function() {
        const messageId = $(this).data('message-id');
        const action = $(this).hasClass('mark-read') ? 'mark_read' : 'mark_unread';
        updateMessageStatus(messageId, action);
    });
    
    // Reply message
    $('.reply-message').on('click', function() {
        $('#reply_message_id').val($(this).data('message-id'));
        $('#reply_to').val($(this).data('email'));
        $('#reply_name').val($(this).data('name'));
        $('#reply_subject').val($(this).data('subject'));
    });
    
    // Delete message
    $('.delete-message').on('click', function() {
        const messageId = $(this).data('message-id');
        if (confirm('Bu mesajı silmek istediğinizden emin misiniz?')) {
            deleteMessage(messageId);
        }
    });
    
    // Reply form submission
    $('#replyForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Gönderiliyor...');
        
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#replyModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('error', response.message);
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
});

function toggleBulkActions() {
    const checkedCount = $('.message-checkbox:checked').length;
    $('.bulk-actions').toggle(checkedCount > 0);
}

function loadMessage(messageId) {
    $.ajax({
        url: '?ajax=get_message&id=' + messageId,
        type: 'GET',
        success: function(message) {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Gönderen:</strong><br>
                        ${message.name}<br>
                        <small class="text-muted">${message.email}</small>
                        ${message.phone ? '<br><small class="text-muted">' + message.phone + '</small>' : ''}
                    </div>
                    <div class="col-md-6">
                        <strong>Tarih:</strong><br>
                        ${message.created_at}<br>
                        <small class="text-muted">IP: ${message.ip_address}</small>
                    </div>
                </div>
                <hr>
                <h5>${message.subject}</h5>
                ${message.service ? '<span class="badge bg-secondary mb-2">' + message.service + '</span>' : ''}
                ${message.package ? '<span class="badge bg-info mb-2 ms-1">' + message.package + '</span>' : ''}
                <div class="mt-3">
                    ${message.message.replace(/\n/g, '<br>')}
                </div>
            `;
            $('#messageContent').html(content);
            
            // Mark as read if unread
            if (message.status === 'unread') {
                updateMessageStatus(messageId, 'mark_read');
            }
        }
    });
}

function updateMessageStatus(messageId, action) {
    $.ajax({
        url: '',
        type: 'POST',
        data: {
            action: action,
            message_id: messageId,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                showAlert('error', response.message);
            }
        }
    });
}

function deleteMessage(messageId) {
    $.ajax({
        url: '',
        type: 'POST',
        data: {
            action: 'delete_message',
            message_id: messageId,
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

function executeBulkAction() {
    const action = $('#bulkAction').val();
    const messageIds = $('.message-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (!action || messageIds.length === 0) {
        showAlert('warning', 'Lütfen bir işlem ve mesaj seçin.');
        return;
    }
    
    if (action === 'delete' && !confirm(`${messageIds.length} mesajı silmek istediğinizden emin misiniz?`)) {
        return;
    }
    
    $.ajax({
        url: '',
        type: 'POST',
        data: {
            action: 'bulk_action',
            bulk_action: action,
            message_ids: messageIds,
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

function exportMessages() {
    window.open('?export=csv', '_blank');
}
</script>

<?php include 'includes/admin_footer.php'; ?>

<?php
// AJAX handler functions
function markMessageRead($messageId) {
    global $db;
    $success = $db->update('messages', ['status' => 'read'], ['id' => (int)$messageId]);
    return ['success' => $success, 'message' => $success ? 'Mesaj okundu olarak işaretlendi' : 'İşlem başarısız'];
}

function markMessageUnread($messageId) {
    global $db;
    $success = $db->update('messages', ['status' => 'unread'], ['id' => (int)$messageId]);
    return ['success' => $success, 'message' => $success ? 'Mesaj okunmadı olarak işaretlendi' : 'İşlem başarısız'];
}

function deleteMessage($messageId) {
    global $db;
    $success = $db->delete('messages', ['id' => (int)$messageId]);
    return ['success' => $success, 'message' => $success ? 'Mesaj silindi' : 'İşlem başarısız'];
}

function replyToMessage($data) {
    $to = filter_var($data['to'], FILTER_SANITIZE_EMAIL);
    $name = sanitizeInput($data['name'] ?? '');
    $subject = sanitizeInput($data['subject']);
    $message = $data['message']; // Allow HTML
    $messageId = (int)$data['message_id'];
    $markReplied = isset($data['mark_replied']);
    
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Geçersiz e-posta adresi'];
    }
    
    // Send email
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: Casino Yayıncısı - BERAT K <noreply@yoursite.com>',
        'Reply-To: info@yoursite.com',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $emailSent = mail($to, $subject, $message, implode("\r\n", $headers));
    
    if ($emailSent && $markReplied) {
        global $db;
        $db->update('messages', ['status' => 'replied'], ['id' => $messageId]);
    }
    
    return ['success' => $emailSent, 'message' => $emailSent ? 'E-posta başarıyla gönderildi' : 'E-posta gönderilemedi'];
}

function handleBulkAction($data) {
    global $db;
    
    $action = $data['bulk_action'];
    $messageIds = $data['message_ids'];
    
    if (!is_array($messageIds) || empty($messageIds)) {
        return ['success' => false, 'message' => 'Mesaj seçilmedi'];
    }
    
    $placeholders = str_repeat('?,', count($messageIds) - 1) . '?';
    
    switch ($action) {
        case 'mark_read':
            $success = $db->query("UPDATE messages SET status = 'read' WHERE id IN ($placeholders)", $messageIds);
            $message = 'Mesajlar okundu olarak işaretlendi';
            break;
            
        case 'mark_unread':
            $success = $db->query("UPDATE messages SET status = 'unread' WHERE id IN ($placeholders)", $messageIds);
            $message = 'Mesajlar okunmadı olarak işaretlendi';
            break;
            
        case 'delete':
            $success = $db->query("DELETE FROM messages WHERE id IN ($placeholders)", $messageIds);
            $message = 'Mesajlar silindi';
            break;
            
        default:
            return ['success' => false, 'message' => 'Geçersiz işlem'];
    }
    
    return ['success' => (bool)$success, 'message' => $success ? $message : 'İşlem başarısız'];
}
?>
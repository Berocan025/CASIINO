<?php
/**
 * General Helper Functions
 * Geliştirici: BERAT K
 * Common functions for the casino portfolio website
 */

/**
 * Format date for display
 * @param string $date
 * @param string $format
 * @return string
 */
function format_date($date, $format = 'd.m.Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '';
    }
    
    try {
        $date_obj = new DateTime($date);
        return $date_obj->format($format);
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Format date for display in Turkish
 * @param string $date
 * @param bool $include_time
 * @return string
 */
function format_date_turkish($date, $include_time = false) {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '';
    }
    
    $turkish_months = [
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
        5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
        9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
    ];
    
    try {
        $date_obj = new DateTime($date);
        $day = $date_obj->format('j');
        $month = $turkish_months[(int)$date_obj->format('n')];
        $year = $date_obj->format('Y');
        $time = $include_time ? ' ' . $date_obj->format('H:i') : '';
        
        return $day . ' ' . $month . ' ' . $year . $time;
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Get time ago in Turkish
 * @param string $date
 * @return string
 */
function time_ago_turkish($date) {
    if (empty($date)) return '';
    
    try {
        $date_obj = new DateTime($date);
        $now = new DateTime();
        $diff = $now->diff($date_obj);
        
        if ($diff->y > 0) {
            return $diff->y . ' yıl önce';
        } elseif ($diff->m > 0) {
            return $diff->m . ' ay önce';
        } elseif ($diff->d > 0) {
            return $diff->d . ' gün önce';
        } elseif ($diff->h > 0) {
            return $diff->h . ' saat önce';
        } elseif ($diff->i > 0) {
            return $diff->i . ' dakika önce';
        } else {
            return 'Az önce';
        }
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Truncate text with ellipsis
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate_text($text, $length = 150, $suffix = '...') {
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length, 'UTF-8') . $suffix;
}

/**
 * Strip HTML tags and truncate
 * @param string $html
 * @param int $length
 * @return string
 */
function strip_and_truncate($html, $length = 150) {
    $text = strip_tags($html);
    return truncate_text($text, $length);
}

/**
 * Generate excerpt from content
 * @param string $content
 * @param int $length
 * @return string
 */
function generate_excerpt($content, $length = 200) {
    // Remove HTML tags
    $excerpt = strip_tags($content);
    
    // Decode HTML entities
    $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
    
    // Remove extra whitespace
    $excerpt = preg_replace('/\s+/', ' ', $excerpt);
    $excerpt = trim($excerpt);
    
    // Truncate
    return truncate_text($excerpt, $length);
}

/**
 * Format file size for display
 * @param int $bytes
 * @return string
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Get page title with site name
 * @param string $page_title
 * @return string
 */
function get_page_title($page_title = '') {
    $site_title = get_setting('site_title', SITE_NAME);
    
    if (empty($page_title)) {
        return $site_title;
    }
    
    return $page_title . SEO_TITLE_SEPARATOR . $site_title;
}

/**
 * Generate meta description
 * @param string $description
 * @return string
 */
function get_meta_description($description = '') {
    if (empty($description)) {
        $description = get_setting('site_description', '');
    }
    
    return truncate_text(strip_tags($description), SEO_MAX_DESCRIPTION_LENGTH, '');
}

/**
 * Get menu items
 * @param int $parent_id
 * @return array
 */
function get_menu_items($parent_id = 0) {
    global $db;
    
    $query = "SELECT * FROM menu_items WHERE parent_id = :parent_id AND status = 'active' ORDER BY sort_order ASC";
    return $db->fetchAll($query, ['parent_id' => $parent_id]);
}

/**
 * Get breadcrumb navigation
 * @param array $items
 * @return string
 */
function get_breadcrumb($items) {
    if (empty($items)) return '';
    
    $breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $last_key = array_key_last($items);
    foreach ($items as $key => $item) {
        if ($key === $last_key) {
            $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">' . $item['title'] . '</li>';
        } else {
            $breadcrumb .= '<li class="breadcrumb-item"><a href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
        }
    }
    
    $breadcrumb .= '</ol></nav>';
    return $breadcrumb;
}

/**
 * Paginate results
 * @param int $total_items
 * @param int $items_per_page
 * @param int $current_page
 * @param string $base_url
 * @return array
 */
function paginate($total_items, $items_per_page, $current_page, $base_url) {
    $total_pages = ceil($total_items / $items_per_page);
    $current_page = max(1, min($current_page, $total_pages));
    
    $pagination = [
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'total_items' => $total_items,
        'items_per_page' => $items_per_page,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'prev_page' => $current_page - 1,
        'next_page' => $current_page + 1,
        'offset' => ($current_page - 1) * $items_per_page,
        'pages' => []
    ];
    
    // Generate page links
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        $pagination['pages'][] = [
            'number' => $i,
            'url' => $base_url . '?page=' . $i,
            'current' => $i === $current_page
        ];
    }
    
    // Add first and last page links if needed
    if ($start_page > 1) {
        array_unshift($pagination['pages'], [
            'number' => 1,
            'url' => $base_url . '?page=1',
            'current' => false
        ]);
        
        if ($start_page > 2) {
            array_splice($pagination['pages'], 1, 0, [['number' => '...', 'url' => '', 'current' => false]]);
        }
    }
    
    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $pagination['pages'][] = ['number' => '...', 'url' => '', 'current' => false];
        }
        
        $pagination['pages'][] = [
            'number' => $total_pages,
            'url' => $base_url . '?page=' . $total_pages,
            'current' => false
        ];
    }
    
    return $pagination;
}

/**
 * Generate pagination HTML
 * @param array $pagination
 * @return string
 */
function pagination_html($pagination) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<nav aria-label="Sayfa navigasyonu"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($pagination['has_prev']) {
        $html .= '<li class="page-item"><a class="page-link" href="?page=' . $pagination['prev_page'] . '">Önceki</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Önceki</span></li>';
    }
    
    // Page numbers
    foreach ($pagination['pages'] as $page) {
        if ($page['number'] === '...') {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        } elseif ($page['current']) {
            $html .= '<li class="page-item active"><span class="page-link">' . $page['number'] . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $page['url'] . '">' . $page['number'] . '</a></li>';
        }
    }
    
    // Next button
    if ($pagination['has_next']) {
        $html .= '<li class="page-item"><a class="page-link" href="?page=' . $pagination['next_page'] . '">Sonraki</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Sonraki</span></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * Get image thumbnail URL
 * @param string $image_path
 * @param int $width
 * @param int $height
 * @return string
 */
function get_thumbnail_url($image_path, $width = 300, $height = 200) {
    if (empty($image_path)) {
        return asset_url('img/placeholder.jpg');
    }
    
    // For now, return original image
    // In production, you might want to implement image resizing
    return upload_url($image_path);
}

/**
 * Send email
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param array $headers
 * @return bool
 */
function send_mail($to, $subject, $message, $headers = []) {
    $default_headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
        'Reply-To: ' . MAIL_FROM,
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $all_headers = array_merge($default_headers, $headers);
    $headers_string = implode("\r\n", $all_headers);
    
    return mail($to, $subject, $message, $headers_string);
}

/**
 * Generate random string
 * @param int $length
 * @return string
 */
function random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    
    return $string;
}

/**
 * Check if current page is active
 * @param string $page
 * @return bool
 */
function is_current_page($page) {
    $current_page = $_SERVER['REQUEST_URI'];
    return strpos($current_page, $page) !== false;
}

/**
 * Get current URL
 * @return string
 */
function current_url() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
           "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Redirect to URL
 * @param string $url
 * @param int $status_code
 */
function redirect($url, $status_code = 302) {
    header('Location: ' . $url, true, $status_code);
    exit();
}

/**
 * Get flash message
 * @param string $key
 * @return string|null
 */
function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Set flash message
 * @param string $key
 * @param string $message
 */
function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Display flash messages
 * @return string
 */
function display_flash_messages() {
    $html = '';
    $flash_types = ['success', 'error', 'warning', 'info'];
    
    foreach ($flash_types as $type) {
        $message = get_flash($type);
        if ($message) {
            $alert_class = $type === 'error' ? 'danger' : $type;
            $html .= '<div class="alert alert-' . $alert_class . ' alert-dismissible fade show" role="alert">';
            $html .= $message;
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
        }
    }
    
    return $html;
}

/**
 * Debug function
 * @param mixed $data
 * @param bool $die
 */
function debug($data, $die = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * Log activity
 * @param string $action
 * @param string $description
 * @param array $data
 */
function log_activity($action, $description, $data = []) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'description' => $description,
        'user_id' => $_SESSION['user_id'] ?? 'guest',
        'ip' => get_client_ip(),
        'data' => $data
    ];
    
    error_log("ACTIVITY: " . json_encode($log_entry, JSON_UNESCAPED_UNICODE));
}
?>
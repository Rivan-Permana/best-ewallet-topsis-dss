<?php
/**
 * Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Sanitize input
 */
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Format number
 */
function formatNumber($number, $decimals = 4) {
    return number_format($number, $decimals, '.', ',');
}

/**
 * Format currency (Indonesian Rupiah)
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Get pagination data
 */
function getPagination($totalRecords, $currentPage = 1, $perPage = 10) {
    $totalPages = ceil($totalRecords / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Build pagination query string
 */
function buildPaginationQuery($page, $search = '') {
    $params = ['page' => $page];
    if (!empty($search)) {
        $params['search'] = $search;
    }
    return http_build_query($params);
}

/**
 * Validate required fields
 */
function validateRequired($fields, $data) {
    $errors = [];
    foreach ($fields as $field => $label) {
        if (empty($data[$field])) {
            $errors[$field] = "$label tidak boleh kosong";
        }
    }
    return $errors;
}

/**
 * Get criteria type badge color
 */
function getCriteriaTypeBadge($type) {
    return $type === 'benefit'
        ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Benefit</span>'
        : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cost</span>';
}

/**
 * Get ranking badge
 */
function getRankingBadge($rank) {
    $colors = [
        1 => 'bg-yellow-400 text-yellow-900',
        2 => 'bg-gray-300 text-gray-900',
        3 => 'bg-orange-400 text-orange-900'
    ];

    $class = $colors[$rank] ?? 'bg-blue-100 text-blue-800';

    return "<span class='px-3 py-1 text-sm font-bold rounded-full $class'>Rank $rank</span>";
}

/**
 * Truncate text
 */
function truncate($text, $length = 50) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

/**
 * Get current page from URL
 */
function getCurrentPage() {
    return isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
}

/**
 * Get search query from URL
 */
function getSearchQuery() {
    return isset($_GET['search']) ? clean($_GET['search']) : '';
}

/**
 * Check if request is POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Get POST data
 */
function post($key, $default = '') {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Get GET data
 */
function get($key, $default = '') {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * Display errors
 */
function displayErrors($errors) {
    if (empty($errors)) return '';

    $html = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">';
    $html .= '<ul class="list-disc list-inside">';
    foreach ($errors as $error) {
        $html .= "<li>$error</li>";
    }
    $html .= '</ul></div>';
    return $html;
}

/**
 * Format date
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Active menu class
 */
function activeMenu($currentPage) {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $currentPage ? 'bg-blue-700' : '';
}

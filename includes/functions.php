<?php
// Common functions for ITSM project

/**
 * Sanitize output for HTML
 */
function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a given URL
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/**
 * Get asset status badge class
 */
function getStatusBadge($status)
{
    switch ($status) {
        case '사용중':
            return 'bg-success';
        case '재고':
            return 'bg-primary';
        case '폐기':
            return 'bg-danger';
        case '수리중':
            return 'bg-warning';
        default:
            return 'bg-secondary';
    }
}
?>
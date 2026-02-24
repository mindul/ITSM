<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Require login for a page
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Check if user is SuperAdmin
 */
function isSuperAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'SuperAdmin';
}

/**
 * Require SuperAdmin role
 */
function requireSuperAdmin()
{
    requireLogin();
    if (!isSuperAdmin()) {
        die("Unauthorized access. SuperAdmin privileges required.");
    }
}

/**
 * Check if user has permission for a specific category
 */
function hasCategoryPermission($category_name)
{
    if (!isLoggedIn())
        return false;

    // SuperAdmin has access to everything
    if (isSuperAdmin())
        return true;

    // If Manager, check assigned tasks
    if ($_SESSION['role'] === 'Manager') {
        $tasks = $_SESSION['assigned_tasks'] ?? [];
        return in_array($category_name, $tasks);
    }

    // General Users have no write/delete permissions
    return false;
}

/**
 * Check if user can edit/delete assets in a specific category
 */
function canEditAsset($category_name)
{
    return hasCategoryPermission($category_name);
}

/**
 * Login user
 */
function loginUser($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Parse assigned tasks
    $tasks = [];
    if (!empty($user['assigned_tasks'])) {
        $tasks = json_decode($user['assigned_tasks'], true);
        if (!is_array($tasks))
            $tasks = [];
    }
    $_SESSION['assigned_tasks'] = $tasks;
}

/**
 * Logout user
 */
function logoutUser()
{
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
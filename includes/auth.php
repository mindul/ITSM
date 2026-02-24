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
 * Require Admin role
 */
function requireAdmin()
{
    requireLogin();
    if ($_SESSION['role'] !== 'Admin') {
        die("Unauthorized access. Admin privileges required.");
    }
}

/**
 * Login user
 */
function loginUser($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
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
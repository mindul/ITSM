<?php
// Database connection configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'doo');
define('DB_PASS', '');
define('DB_NAME', 'itsm_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // In a real production environment, you would log this instead of dying
    die("Connection failed: " . $e->getMessage());
}
?>
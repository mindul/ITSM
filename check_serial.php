<?php
require_once 'config/db.php';

header('Content-Type: application/json');

$serial = $_GET['serial'] ?? '';
$exclude_id = isset($_GET['exclude_id']) ? (int) $_GET['exclude_id'] : 0;

if (empty($serial)) {
    echo json_encode(['exists' => false]);
    exit;
}

$sql = "SELECT COUNT(*) FROM assets WHERE serial_number = ?";
$params = [$serial];

if ($exclude_id > 0) {
    $sql .= " AND id != ?";
    $params[] = $exclude_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$exists = $stmt->fetchColumn() > 0;

echo json_encode(['exists' => $exists]);
?>
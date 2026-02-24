<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin();

$action = $_REQUEST['action'] ?? '';

/**
 * Helper to get category name from ID
 */
function getCategoryName($pdo, $category_id)
{
    if (!$category_id)
        return '';
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetchColumn() ?: '';
}

/**
 * Helper to get category name from asset ID
 */
function getCategoryNameByAsset($pdo, $asset_id)
{
    if (!$asset_id)
        return '';
    $stmt = $pdo->prepare("SELECT c.name FROM assets a JOIN categories c ON a.category_id = c.id WHERE a.id = ?");
    $stmt->execute([$asset_id]);
    return $stmt->fetchColumn() ?: '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model_name = $_POST['model_name'];
    $serial_number = $_POST['serial_number'];
    $category_id = $_POST['category_id'];
    $ip_address = $_POST['ip_address'];
    $vlan_info = $_POST['vlan_info'];
    $location = $_POST['location'];
    $status = $_POST['status'];
    $manager_name = $_POST['manager_name'];
    $introduction_date = $_POST['introduction_date'];

    if ($action === 'create') {
        // Permission check
        $category_name = getCategoryName($pdo, $category_id);
        if (!canEditAsset($category_name)) {
            die("Error: 이 카테고리에 자산을 등록할 권한이 없습니다.");
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO assets (model_name, serial_number, category_id, ip_address, vlan_info, location, status, manager_name, introduction_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$model_name, $serial_number, $category_id, $ip_address, $vlan_info, $location, $status, $manager_name, $introduction_date]);
            $asset_id = $pdo->lastInsertId();

            // Log History
            $stmt = $pdo->prepare("INSERT INTO history_logs (asset_id, change_type, details, worker_name) VALUES (?, '등록', '상세: $model_name ($serial_number)', ?)");
            $stmt->execute([$asset_id, $_SESSION['username']]);

            redirect('assets_list.php?msg=created');
        } catch (PDOException $e) {
            die("Error creating asset: " . $e->getMessage());
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];

        // Permission check (existing category)
        $old_category_name = getCategoryNameByAsset($pdo, $id);
        if (!canEditAsset($old_category_name)) {
            die("Error: 이 자산을 수정할 권한이 없습니다.");
        }

        // Permission check (new category if changed)
        $new_category_name = getCategoryName($pdo, $category_id);
        if (!canEditAsset($new_category_name)) {
            die("Error: 변경하려는 카테고리에 대한 권한이 없습니다.");
        }

        try {
            $stmt = $pdo->prepare("UPDATE assets SET model_name=?, serial_number=?, category_id=?, ip_address=?, vlan_info=?, location=?, status=?, manager_name=?, introduction_date=? WHERE id=?");
            $stmt->execute([$model_name, $serial_number, $category_id, $ip_address, $vlan_info, $location, $status, $manager_name, $introduction_date, $id]);

            // Log History
            $stmt = $pdo->prepare("INSERT INTO history_logs (asset_id, change_type, details, worker_name) VALUES (?, '수정', '수정됨: $model_name', ?)");
            $stmt->execute([$id, $_SESSION['username']]);

            redirect('assets_list.php?msg=updated');
        } catch (PDOException $e) {
            die("Error updating asset: " . $e->getMessage());
        }
    }
} elseif ($action === 'delete') {
    $id = $_GET['id'];

    // Permission check
    $category_name = getCategoryNameByAsset($pdo, $id);
    if (!canEditAsset($category_name)) {
        die("Error: 이 자산을 삭제할 권한이 없습니다.");
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM assets WHERE id = ?");
        $stmt->execute([$id]);
        redirect('assets_list.php?msg=deleted');
    } catch (PDOException $e) {
        die("Error deleting asset: " . $e->getMessage());
    }
}
?>
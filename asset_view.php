<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    redirect('assets_list.php');
}

// Fetch asset with category name
$stmt = $pdo->prepare("SELECT a.*, c.name as category_name 
                       FROM assets a 
                       JOIN categories c ON a.category_id = c.id 
                       WHERE a.id = ?");
$stmt->execute([$id]);
$asset = $stmt->fetch();

if (!$asset) {
    die("자산을 찾을 수 없습니다.");
}

// Fetch history for this specific asset
$stmt = $pdo->prepare("SELECT * FROM history_logs WHERE asset_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$logs = $stmt->fetchAll();
?>

<div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
    <h1 class="h2">자산 상세 정보</h1>
    <div>
        <a href="asset_form.php?id=<?php echo $asset['id']; ?>" class="btn btn-outline-secondary"><i
                class="fas fa-edit me-2"></i>수정</a>
        <a href="assets_list.php" class="btn btn-primary"><i class="fas fa-list me-2"></i>목록으로</a>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">기본 정보</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light" style="width: 30%;">모델명</th>
                        <td>
                            <?php echo h($asset['model_name']); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">자산번호</th>
                        <td>
                            <?php echo h($asset['serial_number']); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">카테고리</th>
                        <td><span class="badge bg-secondary">
                                <?php echo h($asset['category_name']); ?>
                            </span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">상태</th>
                        <td><span class="badge <?php echo getStatusBadge($asset['status']); ?>">
                                <?php echo h($asset['status']); ?>
                            </span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">IP 주소</th>
                        <td><code><?php echo h($asset['ip_address'] ?: '-'); ?></code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">VLAN 정보</th>
                        <td>
                            <?php echo h($asset['vlan_info'] ?: '-'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">위치 (Rack No.)</th>
                        <td>
                            <?php echo h($asset['location'] ?: '-'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">담당자</th>
                        <td>
                            <?php echo h($asset['manager_name'] ?: '-'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">도입일</th>
                        <td>
                            <?php echo h($asset['introduction_date'] ?: '-'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">최종 수정일</th>
                        <td>
                            <?php echo h($asset['last_updated']); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">해당 자산 변경 이력</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($logs)): ?>
                    <div class="p-3 text-center text-muted">이력이 없습니다.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($logs as $log): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold">
                                        <?php echo h($log['change_type']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo h($log['created_at']); ?>
                                    </small>
                                </div>
                                <p class="mb-1 small">
                                    <?php echo h($log['details']); ?>
                                </p>
                                <small class="text-muted">작업자:
                                    <?php echo h($log['worker_name']); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
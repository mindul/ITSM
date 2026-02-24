<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Fetch all history logs with asset details
$query = "SELECT h.*, a.model_name, a.serial_number 
          FROM history_logs h 
          JOIN assets a ON h.asset_id = a.id 
          ORDER BY h.created_at DESC";
$logs = $pdo->query($query)->fetchAll();
?>

<div class="pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">변경 이력 (Audit Log)</h1>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>날짜</th>
                        <th>대상 자산</th>
                        <th>변경 유형</th>
                        <th>상세 내용</th>
                        <th>작업자</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">이력이 없습니다.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><small><?php echo h($log['created_at']); ?></small></td>
                                <td>
                                    <strong><?php echo h($log['model_name']); ?></strong>
                                    <br><small class="text-muted">자산번호: <?php echo h($log['serial_number']); ?></small>
                                </td>
                                <td>
                                    <?php
                                    $badge = 'bg-secondary';
                                    if ($log['change_type'] == '등록')
                                        $badge = 'bg-success';
                                    if ($log['change_type'] == '삭제')
                                        $badge = 'bg-danger';
                                    if ($log['change_type'] == '수정')
                                        $badge = 'bg-info';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo h($log['change_type']); ?></span>
                                </td>
                                <td><?php echo h($log['details']); ?></td>
                                <td><?php echo h($log['worker_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Fetch real stats
$cat_ids = [];
$cat_results = $pdo->query("SELECT id, name FROM categories")->fetchAll();
foreach ($cat_results as $cr) {
    $cat_ids[$cr['name']] = $cr['id'];
}

$total_assets = $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
$server_count = $pdo->query("SELECT COUNT(*) FROM assets WHERE category_id = " . ($cat_ids['서버'] ?? 0))->fetchColumn();
$network_count = $pdo->query("SELECT COUNT(*) FROM assets WHERE category_id = " . ($cat_ids['네트워크장비'] ?? 0))->fetchColumn();
$security_count = $pdo->query("SELECT COUNT(*) FROM assets WHERE category_id = " . ($cat_ids['정보보호시스템'] ?? 0))->fetchColumn();
$others_count = $pdo->query("SELECT COUNT(*) FROM assets WHERE category_id = " . ($cat_ids['기타장비'] ?? 0))->fetchColumn();

// Fetch recent history
$recent_history = $pdo->query("SELECT h.*, a.model_name, a.serial_number 
                               FROM history_logs h 
                               JOIN assets a ON h.asset_id = a.id 
                               ORDER BY h.created_at DESC LIMIT 5")->fetchAll();
?>

<div class="pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">대시보드</h1>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4 dashboard-cards">
    <!-- Summary Cards -->
    <div class="col">
        <a href="assets_list.php" class="text-decoration-none">
            <div class="card bg-primary text-white h-100 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 small uppercase fw-bold">전체 자산</h6>
                        <h2 class="mb-0 fw-bold"><?php echo h($total_assets); ?></h2>
                    </div>
                    <i class="fas fa-boxes fa-2x opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="assets_list.php?category_id=<?php echo $cat_ids['서버'] ?? ''; ?>" class="text-decoration-none">
            <div class="card bg-success text-white h-100 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 small uppercase fw-bold">서버</h6>
                        <h2 class="mb-0 fw-bold"><?php echo h($server_count); ?></h2>
                    </div>
                    <i class="fas fa-server fa-2x opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="assets_list.php?category_id=<?php echo $cat_ids['네트워크장비'] ?? ''; ?>" class="text-decoration-none">
            <div class="card bg-info text-white h-100 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 small uppercase fw-bold">네트워크장비</h6>
                        <h2 class="mb-0 fw-bold"><?php echo h($network_count); ?></h2>
                    </div>
                    <i class="fas fa-network-wired fa-2x opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="assets_list.php?category_id=<?php echo $cat_ids['정보보호시스템'] ?? ''; ?>" class="text-decoration-none">
            <div class="card bg-warning text-white h-100 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 small uppercase fw-bold">정보보호시스템</h6>
                        <h2 class="mb-0 fw-bold"><?php echo h($security_count); ?></h2>
                    </div>
                    <i class="fas fa-shield-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col">
        <a href="assets_list.php?category_id=<?php echo $cat_ids['기타장비'] ?? ''; ?>" class="text-decoration-none">
            <div class="card bg-secondary text-white h-100 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 small uppercase fw-bold">기타장비</h6>
                        <h2 class="mb-0 fw-bold"><?php echo h($others_count); ?></h2>
                    </div>
                    <i class="fas fa-microchip fa-2x opacity-50"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
    .dashboard-cards .card {
        transition: transform 0.2s;
        cursor: pointer;
    }

    .dashboard-cards .card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="row mt-4">
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">자산 분포 현황</h5>
            </div>
            <div class="card-body">
                <canvas id="assetChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">최근 변경 이력</h5>
            </div>
            <div class="card-body px-0">
                <ul class="list-group list-group-flush">
                    <?php if (empty($recent_history)): ?>
                        <li class="list-group-item border-0 text-center text-muted">최근 이력이 없습니다.</li>
                    <?php else: ?>
                        <?php foreach ($recent_history as $log): ?>
                            <li class="list-group-item border-0">
                                <small class="text-muted d-block"><?php echo h($log['created_at']); ?></small>
                                <span class="fw-bold"><?php echo h($log['model_name']); ?></span>
                                <span class="badge bg-light text-dark"><?php echo h($log['change_type']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <div class="text-center mt-3">
                    <a href="history.php" class="btn btn-sm btn-outline-primary">전체 보기</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('assetChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['서버', '네트워크장비', '정보보호시스템', '기타장비'],
                datasets: [{
                    label: '장비별 수량',
                    data: [<?php echo $server_count; ?>, <?php echo $network_count; ?>, <?php echo $security_count; ?>, <?php echo $others_count; ?>],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderColor: [
                        'rgba(25, 135, 84, 1)',
                        'rgba(13, 202, 240, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
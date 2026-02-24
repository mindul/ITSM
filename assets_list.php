<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Fetch current filters and pagination
$category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($page - 1) * $limit;

// Fetch categories for search/filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY FIELD(name, '서버', '네트워크장비', '정보보호시스템', '기타장비')")->fetchAll();

// Build Base Query for Counting
$count_sql = "SELECT COUNT(*) FROM assets WHERE 1=1";
$params = [];

if ($category_id > 0) {
    $count_sql .= " AND category_id = ?";
    $params[] = $category_id;
}

if (!empty($search)) {
    $count_sql .= " AND (model_name LIKE ? OR serial_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get Total Records
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_rows = $stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);
if ($page > $total_pages && $total_pages > 0)
    $page = $total_pages;

// Build Base Query for Data
$sql = "SELECT a.*, c.name as category_name 
        FROM assets a 
        JOIN categories c ON a.category_id = c.id 
        WHERE 1=1";

if ($category_id > 0) {
    $sql .= " AND a.category_id = ?";
}

if (!empty($search)) {
    $sql .= " AND (a.model_name LIKE ? OR a.serial_number LIKE ?)";
}

$sql .= " ORDER BY a.last_updated DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$assets = $stmt->fetchAll();
?>

<div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
    <h1 class="h2">자산 목록</h1>
    <a href="asset_form.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>신규 자산 등록</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        $msg = $_GET['msg'];
        if ($msg === 'created')
            echo "새로운 자산이 성공적으로 등록되었습니다.";
        if ($msg === 'updated')
            echo "자산 정보가 성공적으로 수정되었습니다.";
        if ($msg === 'deleted')
            echo "자산이 삭제되었습니다.";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" method="GET">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="모델명 또는 자산번호 검색"
                    value="<?php echo h($search); ?>">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">전체 카테고리</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo h($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-secondary">검색/필터 적용</button>
                <a href="assets_list.php" class="btn btn-link text-decoration-none">초기화</a>
            </div>
            <div class="col-md-4 text-end d-flex align-items-center justify-content-end">
                <label class="me-2 fw-bold">표시 개수:</label>
                <select name="limit" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10개씩</option>
                    <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20개씩</option>
                    <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50개씩</option>
                    <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100개씩</option>
                </select>
                <input type="hidden" name="page" value="1">
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-4">
                <thead>
                    <tr>
                        <th>유형</th>
                        <th>모델명</th>
                        <th>자산번호</th>
                        <th>IP 주소</th>
                        <th>위치</th>
                        <th>상태</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assets)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">등록된 자산이 없습니다.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assets as $asset): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo h($asset['category_name']); ?></span></td>
                                <td><strong><?php echo h($asset['model_name']); ?></strong></td>
                                <td><?php echo h($asset['serial_number']); ?></td>
                                <td><code><?php echo h($asset['ip_address'] ?: '-'); ?></code></td>
                                <td><?php echo h($asset['location'] ?: '-'); ?></td>
                                <td><span
                                        class="badge <?php echo getStatusBadge($asset['status']); ?>"><?php echo h($asset['status']); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="asset_view.php?id=<?php echo $asset['id']; ?>" class="btn btn-outline-primary"
                                            title="상세보기"><i class="fas fa-eye"></i></a>
                                        <a href="asset_form.php?id=<?php echo $asset['id']; ?>"
                                            class="btn btn-outline-secondary" title="수정"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-outline-danger delete-btn" data-id="<?php echo $asset['id']; ?>"
                                            title="삭제"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link"
                                href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">이전</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link"
                            href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">다음</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            if (confirm('정말로 이 자산을 삭제하시겠습니까?')) {
                window.location.href = 'asset_actions.php?action=delete&id=' + id;
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
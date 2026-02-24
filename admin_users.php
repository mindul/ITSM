<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Restricted to SuperAdmin
requireSuperAdmin();

$msg = '';
$error = '';

// Handle Actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($userId > 0) {
        // Prevent deleting kadmin
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && $user['username'] === 'kadmin') {
            $error = "최고관리자 계정은 보호됩니다.";
        } else {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
                if ($stmt->execute([$userId]))
                    $msg = "계정이 승인되었습니다.";
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                if ($stmt->execute([$userId]))
                    $msg = "계정이 삭제되었습니다.";
            } elseif ($action === 'set_manager') {
                $stmt = $pdo->prepare("UPDATE users SET role = 'Manager' WHERE id = ?");
                $stmt->execute([$userId]);
            } elseif ($action === 'set_user') {
                $stmt = $pdo->prepare("UPDATE users SET role = 'User' WHERE id = ?");
                $stmt->execute([$userId]);
            }
        }
    }
}

// Fetch Users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

include 'includes/header.php';
?>

<div class="pt-3 pb-2 mb-4 border-bottom d-flex justify-content-between align-items-center">
    <h1 class="h2"><i class="fas fa-users-cog me-2 text-primary"></i>계정 관리</h1>
    <span class="badge bg-secondary">총
        <?php echo count($users); ?>명
    </span>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo h($msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo h($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>아이디</th>
                        <th>가입일</th>
                        <th>담당업무</th>
                        <th>권한</th>
                        <th>상태</th>
                        <th class="text-center">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr <?php echo $u['username'] === 'kadmin' ? 'class="table-light"' : ''; ?>>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle fa-2x text-muted me-2"></i>
                                    <strong>
                                        <?php echo h($u['username']); ?>
                                    </strong>
                                </div>
                            </td>
                            <td><small class="text-muted">
                                    <?php echo h($u['created_at']); ?>
                                </small></td>
                            <td>
                                <?php
                                $tasks = $u['assigned_tasks'] ? json_decode($u['assigned_tasks'], true) : [];
                                if ($tasks) {
                                    foreach ($tasks as $task) {
                                        $badge_class = ($task === '모니터링') ? 'bg-light text-dark border' : 'bg-info text-dark';
                                        echo '<span class="badge ' . $badge_class . ' me-1">' . h($task) . '</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted small">-</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($u['role'] === 'SuperAdmin'): ?>
                                    <span class="badge bg-danger">최고관리자</span>
                                <?php elseif ($u['role'] === 'Manager'): ?>
                                    <span class="badge bg-success">중간관리자</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">일반사용자</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['is_approved']): ?>
                                    <span class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i>승인됨</span>
                                <?php else: ?>
                                    <span class="text-warning small fw-bold animated pulse infinite"><i
                                            class="fas fa-clock me-1"></i>승인대기</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($u['username'] !== 'kadmin'): ?>
                                    <div class="btn-group btn-group-sm">
                                        <?php if (!$u['is_approved']): ?>
                                            <a href="?action=approve&id=<?php echo $u['id']; ?>" class="btn btn-success" title="승인">
                                                <i class="fas fa-user-check"></i> 승인
                                            </a>
                                        <?php endif; ?>
                                        <button class="btn btn-outline-danger delete-user-btn" data-id="<?php echo $u['id']; ?>"
                                            title="계정 삭제">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">시스템 계정</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle User Deletion
        document.querySelectorAll('.delete-user-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                if (confirm('정말로 이 계정을 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.')) {
                    window.location.href = 'admin_users.php?action=delete&id=' + id;
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
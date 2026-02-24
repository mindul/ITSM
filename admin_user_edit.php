<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Restricted to SuperAdmin
requireSuperAdmin();

$userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($userId <= 0) {
    header("Location: admin_users.php");
    exit;
}

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die("사용자를 찾을 수 없습니다.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $tasks = isset($_POST['tasks']) ? $_POST['tasks'] : [];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if ($new_password !== '' && $new_password !== $confirm_password) {
        $error = "새 비밀번호가 일치하지 않습니다.";
    } else {
        try {
            $pdo->beginTransaction();

            $sql = "UPDATE users SET role = ?, assigned_tasks = ? WHERE id = ?";
            $assigned_tasks_json = json_encode($tasks, JSON_UNESCAPED_UNICODE);
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$role, $assigned_tasks_json, $userId]);

            if ($new_password !== '') {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $userId]);
            }

            $pdo->commit();
            $success = "사용자 정보가 성공적으로 수정되었습니다.";

            // Refresh local user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "오류가 발생했습니다: " . $e->getMessage();
        }
    }
}

$current_tasks = json_decode($user['assigned_tasks'] ?? '[]', true) ?: [];

include 'includes/header.php';
?>

<div class="pt-3 pb-2 mb-4 border-bottom d-flex justify-content-between align-items-center">
    <h1 class="h2"><i class="fas fa-user-edit me-2 text-primary"></i>계정 정보 수정</h1>
    <a href="admin_users.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> 목록으로</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo h($error); ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>
                        <?php echo h($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4 text-center">
                        <i class="fas fa-user-circle fa-4x text-muted mb-2"></i>
                        <h4 class="mb-0">
                            <?php echo h($user['username']); ?>
                        </h4>
                        <small class="text-muted">가입일:
                            <?php echo h($user['created_at']); ?>
                        </small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">권한 설정</label>
                        <select name="role" class="form-select" <?php echo $user['username'] === 'kadmin' ? 'disabled' : ''; ?>>
                            <option value="SuperAdmin" <?php echo $user['role'] === 'SuperAdmin' ? 'selected' : ''; ?>
                                >최고관리자</option>
                            <option value="Manager" <?php echo $user['role'] === 'Manager' ? 'selected' : ''; ?>>중간관리자
                            </option>
                            <option value="User" <?php echo $user['role'] === 'User' ? 'selected' : ''; ?>>일반사용자</option>
                        </select>
                        <?php if ($user['username'] === 'kadmin'): ?>
                            <input type="hidden" name="role" value="SuperAdmin">
                            <div class="form-text">최고관리자 계정의 권한은 변경할 수 없습니다.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold d-block">담당업무 지원</label>
                        <div class="row g-2">
                            <?php
                            $options = ['서버', '네트워크장비', '정보보호시스템', '기타장비', '모니터링'];
                            foreach ($options as $idx => $opt):
                                $id = "task" . ($idx + 1);
                                $checked = in_array($opt, $current_tasks) ? 'checked' : '';
                                ?>
                                <div class="col-6">
                                    <div class="form-check p-2 border rounded bg-light px-4">
                                        <input class="form-check-input" type="checkbox" name="tasks[]"
                                            value="<?php echo h($opt); ?>" id="<?php echo $id; ?>" <?php echo $checked; ?>>
                                        <label class="form-check-label ms-1" for="<?php echo $id; ?>">
                                            <?php echo h($opt); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="card-title fw-bold"><i class="fas fa-key me-1"></i> 비밀번호 변경 <small
                                    class="text-muted">(변경 시에만 입력)</small></h6>
                            <div class="mb-2">
                                <label class="form-label small">새 비밀번호</label>
                                <input type="password" name="new_password" class="form-control form-control-sm">
                            </div>
                            <div>
                                <label class="form-label small">비밀번호 확인</label>
                                <input type="password" name="confirm_password" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">수정사항 저장</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
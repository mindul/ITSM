<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Restricted to SuperAdmin
requireSuperAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $tasks = isset($_POST['tasks']) ? $_POST['tasks'] : [];

    if (empty($username) || empty($name) || empty($password)) {
        $error = "모든 필수 필드를 입력해주세요.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "이미 존재하는 아이디입니다.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $assigned_tasks_json = json_encode($tasks, JSON_UNESCAPED_UNICODE);

            $stmt = $pdo->prepare("INSERT INTO users (username, name, password, role, is_approved, assigned_tasks) VALUES (?, ?, ?, ?, 1, ?)");
            if ($stmt->execute([$username, $name, $hashed_password, $role, $assigned_tasks_json])) {
                $success = "새 사용자가 성공적으로 등록되었습니다.";
            } else {
                $error = "오류가 발생했습니다. 다시 시도해주세요.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="pt-3 pb-2 mb-4 border-bottom d-flex justify-content-between align-items-center">
    <h1 class="h2"><i class="fas fa-user-plus me-2 text-primary"></i>신규 사용자 계정 등록</h1>
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
                    <div class="mb-3">
                        <label class="form-label fw-bold">아이디</label>
                        <input type="text" name="username" class="form-control" placeholder="아이디 입력" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">성명</label>
                        <input type="text" name="name" class="form-control" placeholder="성명 입력" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">비밀번호</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="비밀번호 입력" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">권한 설정</label>
                        <select name="role" class="form-select">
                            <option value="Manager">중간관리자</option>
                            <option value="User" selected>일반사용자</option>
                            <option value="SuperAdmin">최고관리자</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold d-block">담당업무 지원</label>
                        <div class="row g-2">
                            <?php
                            $options = ['서버', '네트워크장비', '정보보호시스템', '기타장비', '모니터링'];
                            foreach ($options as $idx => $opt):
                                $id = "task" . ($idx + 1);
                                ?>
                                <div class="col-6">
                                    <div class="form-check p-2 border rounded bg-light ps-5 pe-3">
                                        <input class="form-check-input" type="checkbox" name="tasks[]"
                                            value="<?php echo h($opt); ?>" id="<?php echo $id; ?>">
                                        <label class="form-check-label ms-3" for="<?php echo $id; ?>">
                                            <?php echo h($opt); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">사용자 등록</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function (e) {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
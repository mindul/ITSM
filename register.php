<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tasks = isset($_POST['tasks']) ? $_POST['tasks'] : [];

    if (empty($username) || empty($password)) {
        $error = "모든 필드를 입력해주세요.";
    } elseif ($password !== $confirm_password) {
        $error = "비밀번호가 일치하지 않습니다.";
    } elseif (empty($tasks)) {
        $error = "최소 하나 이상의 담당업무를 선택해주세요.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "이미 존재하는 아이디입니다.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Determine role: Any asset category makes them a Manager.
            // Only '모니터링' makes them a General User.
            $manager_tasks = ['서버', '네트워크장비', '정보보호시스템', '기타장비'];
            $role = 'User';
            foreach ($tasks as $task) {
                if (in_array($task, $manager_tasks)) {
                    $role = 'Manager';
                    break;
                }
            }

            $assigned_tasks_json = json_encode($tasks, JSON_UNESCAPED_UNICODE);

            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, is_approved, assigned_tasks) VALUES (?, ?, ?, 0, ?)");
            if ($stmt->execute([$username, $hashed_password, $role, $assigned_tasks_json])) {
                $success = "가입 신청이 완료되었습니다. 관리자 승인 후 로그인이 가능합니다.";
                // Redirect after a short delay or show message
                header("Refresh: 3; url=index.php");
            } else {
                $error = "오류가 발생했습니다. 다시 시도해주세요.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입 - ITSM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .reg-card {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .btn-reg {
            background: #198754;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: bold;
            margin-top: 20px;
        }

        .task-option {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .task-option:hover {
            background: #e9ecef;
        }

        .form-check-input:checked+.form-check-label {
            font-weight: bold;
            color: #198754;
        }
    </style>
</head>

<body>
    <div class="reg-card">
        <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
            <h3>계정 가입 신청</h3>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo h($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> <?php echo h($success); ?>
                <div class="mt-2"><small>3초 후 초기 화면으로 이동합니다...</small></div>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">아이디</label>
                    <input type="text" name="username" class="form-control" placeholder="아이디 입력" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">비밀번호</label>
                    <input type="password" name="password" class="form-control" placeholder="비밀번호 입력" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">비밀번호 확인</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="비밀번호 확인" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold d-block">담당업무 (중복 선택 가능)</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-check task-option">
                                <input class="form-check-input" type="checkbox" name="tasks[]" value="서버" id="task1">
                                <label class="form-check-label" for="task1">서버</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check task-option">
                                <input class="form-check-input" type="checkbox" name="tasks[]" value="네트워크장비" id="task2">
                                <label class="form-check-label" for="task2">네트워크장비</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check task-option">
                                <input class="form-check-input" type="checkbox" name="tasks[]" value="정보보호시스템" id="task3">
                                <label class="form-check-label" for="task3">정보보호시스템</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check task-option">
                                <input class="form-check-input" type="checkbox" name="tasks[]" value="기타장비" id="task4">
                                <label class="form-check-label" for="task4">기타장비</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check task-option">
                                <input class="form-check-input" type="checkbox" name="tasks[]" value="모니터링" id="task5">
                                <label class="form-check-label" for="task5">모니터링 (일반사용자)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-reg">신청</button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-4">
            <small class="text-muted">이미 계정이 있으신가요? <a href="login.php" class="text-decoration-none">로그인</a></small>
        </div>
    </div>
</body>

</html>
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

    if (empty($username) || empty($password)) {
        $error = "모든 필드를 입력해주세요.";
    } elseif ($password !== $confirm_password) {
        $error = "비밀번호가 일치하지 않습니다.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "이미 존재하는 아이디입니다.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'User')");
            if ($stmt->execute([$username, $hashed_password])) {
                $success = "회원가입이 완료되었습니다. 로그인을 해주세요.";
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
            background: #f4f7f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .reg-card {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
    </style>
</head>

<body>
    <div class="reg-card">
        <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
            <h3>회원가입</h3>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo h($error); ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo h($success); ?>
                <div class="mt-2"><a href="login.php" class="btn btn-sm btn-outline-success">로그인하러 가기</a></div>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">아이디</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">비밀번호</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">비밀번호 확인</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-reg">가입하기</button>
        </form>
        <div class="text-center mt-4">
            <small class="text-muted">이미 계정이 있으신가요? <a href="login.php">로그인</a></small>
        </div>
    </div>
</body>

</html>
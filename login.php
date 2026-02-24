<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "아이디와 비밀번호를 입력해주세요.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "아이디 또는 비밀번호가 올바르지 않습니다.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 - ITSM</title>
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

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-login {
            background: #1e3c72;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: bold;
            margin-top: 20px;
        }

        .btn-login:hover {
            background: #2a5298;
            color: white;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="fas fa-user-lock fa-3x text-primary mb-3"></i>
            <h3>ITSM 로그인</h3>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo h($error); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">아이디</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">비밀번호</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login">로그인</button>
        </form>
        <div class="text-center mt-4">
            <small class="text-muted">계정이 없으신가요? <a href="register.php">회원가입</a></small>
        </div>
    </div>
</body>

</html>
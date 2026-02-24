<?php
require_once 'includes/auth.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITSM - IT 자산 통합 관리 시스템</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero {
            text-align: center;
            padding: 50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .btn-start,
        .btn-secondary-custom {
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 30px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .btn-start {
            background-color: #00d2ff;
            border: 2px solid transparent;
            color: #1e3c72;
        }

        .btn-start:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: white;
            color: #000;
        }

        .btn-secondary-custom {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            text-decoration: none;
        }

        .btn-secondary-custom:hover {
            background-color: white;
            color: #1e3c72;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="hero">
                    <i class="fas fa-network-wired fa-5x mb-4 animated fadeInDown"></i>
                    <h1 class="display-3 mb-3 fw-bold">ITSM Platform</h1>
                    <p class="lead mb-5">효율적인 IT 자산 통합 관리와 가시성 확보를 위한 차세대 플랫폼</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center align-items-center">
                        <a href="login.php" class="btn btn-start me-sm-3">로그인</a>
                        <a href="register.php" class="btn btn-secondary-custom">회원가입</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
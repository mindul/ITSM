<?php
require_once 'includes/auth.php';
requireLogin();
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
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .75);
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, .1);
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: #0d6efd;
        }

        .main-content {
            padding: 20px;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4 px-3">
                        <div class="p-2 border border-secondary rounded bg-dark">
                            <i class="fas fa-user-circle me-1"></i> <?php echo h($_SESSION['username']); ?> 
                            <span class="badge bg-info ms-1"><?php echo h($_SESSION['role']); ?></span>
                        </div>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> 대시보드
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'assets_list.php' ? 'active' : ''; ?>" href="assets_list.php">
                                <i class="fas fa-boxes me-2"></i> 자산 관리
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'network.php' ? 'active' : ''; ?>" href="network.php">
                                <i class="fas fa-project-diagram me-2"></i> 네트워크 관리
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>" href="history.php">
                                <i class="fas fa-history me-2"></i> 변경 이력
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> 로그아웃
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4 main-content">
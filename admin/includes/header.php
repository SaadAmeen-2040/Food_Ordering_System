<?php
require_once '../includes/db_connect.php';

// Check if user is logged in as admin (except for login page)
$current_page = basename($_SERVER['PHP_SELF']);
if(!isset($_SESSION['admin_id']) && $current_page != 'login.php') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodies Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    
    <?php if(isset($_SESSION['admin_id'])): ?>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="admin-sidebar p-3" style="width: 280px; position: fixed; top: 0; bottom: 0; left: 0;">
            <a href="index.php" class="d-flex align-items-center mb-4 mt-3 text-white text-decoration-none px-2">
                <i class="fa-solid fa-utensils fs-3 text-primary me-3"></i>
                <span class="fs-4 fw-bold brand-font">Admin Panel</span>
            </a>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="index.php" class="admin-nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-chart-line w-20px text-center"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="categories.php" class="admin-nav-link <?= $current_page == 'categories.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-list w-20px text-center"></i> Categories
                    </a>
                </li>
                <li>
                    <a href="foods.php" class="admin-nav-link <?= $current_page == 'foods.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-burger w-20px text-center"></i> Foods
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="admin-nav-link <?= $current_page == 'orders.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-cart-shopping w-20px text-center"></i> Orders
                    </a>
                </li>
                <li>
                    <a href="messages.php" class="admin-nav-link <?= $current_page == 'messages.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-envelope w-20px text-center"></i> Messages
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="admin-nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-gear w-20px text-center"></i> Settings
                    </a>
                </li>
            </ul>
            
            <hr class="text-secondary">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=ff4757&color=fff" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong><?= $_SESSION['admin_username'] ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li><a class="dropdown-item" href="../index.php" target="_blank">View Website</a></li>
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Sign out</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content Wrapper -->
        <div class="flex-grow-1" style="margin-left: 280px;">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-3 px-4 sticky-top">
                <div class="container-fluid">
                    <h5 class="fw-bold mb-0 text-muted text-uppercase tracking-wider">
                        <?= str_replace('.php', '', $current_page) ?>
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted"><i class="fa-regular fa-clock me-1"></i> <?= date('M d, Y') ?></span>
                    </div>
                </div>
            </nav>
            
            <!-- Page Content Start -->
            <div class="p-4">
    <?php endif; ?>

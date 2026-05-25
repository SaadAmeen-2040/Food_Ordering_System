<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-glass fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <i class="fa-solid fa-utensils text-primary"></i>
            Foodies
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : '' ?>" href="menu.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a>
                </li>
            </ul>
            
            <div class="d-flex flex-column flex-lg-row align-items-center gap-3 mt-4 mt-lg-0 pb-3 pb-lg-0">
                <a href="cart.php" class="text-dark text-decoration-none position-relative fs-5 mb-2 mb-lg-0">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php 
                    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    if($cart_count > 0): 
                    ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown w-100 text-center text-lg-start">
                        <a href="#" class="d-inline-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-weight: bold; margin: 0 auto;">
                                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start shadow border-0 mt-2 text-center text-lg-start">
                            <li><a class="dropdown-item" href="dashboard.php"><i class="fa-solid fa-user me-2"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column flex-sm-row gap-2 w-100 justify-content-center justify-content-lg-start">
                        <a href="login.php" class="btn btn-outline-custom btn-sm px-4">Log In</a>
                        <a href="register.php" class="btn btn-primary-custom btn-sm px-4">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<!-- Padding to account for fixed navbar -->
<div style="height: 76px;"></div>

<?php 
include 'includes/header.php'; 
include 'includes/navbar.php'; 

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$redirect = isset($_GET['redirect']) ? sanitize_input($_GET['redirect']) : 'dashboard.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $redirect_url = sanitize_input($_POST['redirect_url']);
    
    if(empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])) {
                // Login success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                
                header("Location: $redirect_url");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found with this email.";
        }
    }
}
?>

<div class="container py-5" style="min-height: calc(100vh - 400px);">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 p-5 pb-0 text-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-user fs-1"></i>
                    </div>
                    <h2 class="brand-font fw-bold">Welcome Back</h2>
                    <p class="text-muted">Log in to continue your delicious journey</p>
                </div>
                
                <div class="card-body p-5 pt-4">
                    <?php if($error): ?>
                        <div class="alert alert-danger text-center"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-success text-center"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                    <?php endif; ?>
                    
                    <form action="" method="POST">
                        <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($redirect) ?>">
                        
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-custom" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label text-muted small fw-bold">Password</label>
                                <a href="#" class="small text-decoration-none text-primary">Forgot Password?</a>
                            </div>
                            <input type="password" name="password" class="form-control form-control-custom" required>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-muted small" for="rememberMe">
                                Remember me
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom w-100 py-3 mb-4">Log In</button>
                        
                        <p class="text-center text-muted mb-0">Don't have an account? <a href="register.php" class="text-primary fw-bold text-decoration-none">Sign Up</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

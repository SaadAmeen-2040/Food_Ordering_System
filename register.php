<?php 
include 'includes/header.php'; 
include 'includes/navbar.php'; 

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    
    // Validation
    if(empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($address)) {
        $error = "All fields are required.";
    } elseif($password !== $cpassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if($stmt->get_result()->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (full_name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("sssss", $full_name, $email, $phone, $hashed_password, $address);
            
            if($insert->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>log in</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-12 bg-primary text-white p-5 text-center" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
                        <h2 class="brand-font fw-bold mb-0">Join Foodies</h2>
                        <p class="mb-0 opacity-75">Create an account to order food faster</p>
                    </div>
                    <div class="col-12 p-5">
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php else: ?>
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Full Name</label>
                                    <input type="text" name="full_name" class="form-control form-control-custom" required value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Email Address</label>
                                    <input type="email" name="email" class="form-control form-control-custom" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Phone Number</label>
                                    <input type="text" name="phone" class="form-control form-control-custom" required value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold">Delivery Address</label>
                                    <textarea name="address" class="form-control form-control-custom" rows="2" required><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small fw-bold">Password</label>
                                        <input type="password" name="password" class="form-control form-control-custom" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label text-muted small fw-bold">Confirm Password</label>
                                        <input type="password" name="cpassword" class="form-control form-control-custom" required>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary-custom w-100 py-3 mb-4">Create Account</button>
                                
                                <p class="text-center text-muted mb-0">Already have an account? <a href="login.php" class="text-primary fw-bold text-decoration-none">Log In</a></p>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

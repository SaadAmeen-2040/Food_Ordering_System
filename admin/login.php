<?php
require_once '../includes/db_connect.php';

if(isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if($res->num_rows == 1) {
        $admin = $res->fetch_assoc();
        if(password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid credentials";
        }
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Foodies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="width: 100%; max-width: 400px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 70px; height: 70px;">
                    <i class="fa-solid fa-shield-halved fs-2"></i>
                </div>
                <h3 class="brand-font fw-bold">Admin Portal</h3>
                <p class="text-muted small">Sign in to manage the system</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger py-2 text-center small"><?= $error ?></div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 bg-light" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 bg-light" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 mb-3">Secure Login</button>
                <a href="../index.php" class="btn btn-outline-secondary w-100 rounded-pill small">Back to Website</a>
            </form>
        </div>
    </div>

</body>
</html>

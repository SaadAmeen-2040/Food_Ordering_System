<?php include 'includes/header.php'; ?>

<?php
$admin_id = $_SESSION['admin_id'];
$success_msg = '';
$error_msg = '';

// Fetch current admin details
$stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if(isset($_POST['update_credentials'])) {
    $new_username = sanitize_input($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if(!empty($new_password)) {
        if($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $new_username, $hashed_password, $admin_id);
            
            if($update_stmt->execute()) {
                $_SESSION['admin_username'] = $new_username;
                $admin['username'] = $new_username;
                $success_msg = "Username and Password updated successfully.";
            } else {
                $error_msg = "Failed to update credentials. Username might already exist.";
            }
        } else {
            $error_msg = "New password and confirm password do not match.";
        }
    } else {
        // Only update username
        $update_stmt = $conn->prepare("UPDATE admins SET username = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_username, $admin_id);
        
        if($update_stmt->execute()) {
            $_SESSION['admin_username'] = $new_username;
            $admin['username'] = $new_username;
            $success_msg = "Username updated successfully.";
        } else {
            $error_msg = "Failed to update username. It might already exist.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-header bg-white border-0 p-4 pb-0 text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-user-shield fs-3"></i>
                </div>
                <h4 class="fw-bold mb-0">Admin Settings</h4>
                <p class="text-muted small">Update your login credentials</p>
            </div>
            
            <div class="card-body p-4 pt-2">
                <?php if($success_msg): ?>
                    <div class="alert alert-success py-2 small rounded-3"><?= $success_msg ?></div>
                <?php endif; ?>
                
                <?php if($error_msg): ?>
                    <div class="alert alert-danger py-2 small rounded-3"><?= $error_msg ?></div>
                <?php endif; ?>
                
                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                            <input type="text" name="username" class="form-control border-start-0 bg-light" value="<?= htmlspecialchars($admin['username']) ?>" required>
                        </div>
                    </div>
                    
                    <hr class="my-4 text-muted opacity-25">
                    <p class="text-muted small mb-3">Leave password fields blank if you only want to change your username.</p>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" name="new_password" class="form-control border-start-0 bg-light" placeholder="Enter new password">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" name="confirm_password" class="form-control border-start-0 bg-light" placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <button type="submit" name="update_credentials" class="btn btn-primary-custom w-100 py-2 rounded-pill shadow-sm">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

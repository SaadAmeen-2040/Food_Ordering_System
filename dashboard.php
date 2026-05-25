<?php 
include 'includes/header.php'; 
include 'includes/navbar.php'; 

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Update profile handling
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize_input($_POST['full_name']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    
    $update_stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    $update_stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
    if($update_stmt->execute()) {
        $_SESSION['message'] = "Profile updated successfully.";
        $_SESSION['user_name'] = $full_name; // update session name
        header("Location: dashboard.php");
        exit;
    }
}
?>

<div class="bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
    <div class="container py-3">
        <h1 class="display-5 fw-bold mb-0 brand-font">Welcome, <?= htmlspecialchars(explode(' ', $user['full_name'])[0]) ?>!</h1>
        <p class="lead mb-0 opacity-75">Manage your orders and account details</p>
    </div>
</div>

<div class="container mb-5">
    <?php
        if(isset($_SESSION['message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    ' . $_SESSION['message'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['message']);
        }
    ?>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body p-4 text-center border-bottom">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fs-2 fw-bold"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
                    </div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name']) ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div class="list-group list-group-flush p-2">
                    <a href="#orders" data-bs-toggle="tab" class="list-group-item list-group-item-action active border-0 rounded mb-1"><i class="fa-solid fa-bag-shopping me-2 w-20px text-center"></i> My Orders</a>
                    <a href="#profile" data-bs-toggle="tab" class="list-group-item list-group-item-action border-0 rounded mb-1"><i class="fa-solid fa-user-pen me-2 w-20px text-center"></i> Edit Profile</a>
                    <a href="logout.php" class="list-group-item list-group-item-action text-danger border-0 rounded"><i class="fa-solid fa-right-from-bracket me-2 w-20px text-center"></i> Logout</a>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Orders Tab -->
                <div class="tab-pane fade show active" id="orders">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 border-bottom pb-3">Order History</h4>
                            
                            <?php
                            $order_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
                            $order_stmt = $conn->prepare($order_sql);
                            $order_stmt->bind_param("i", $user_id);
                            $order_stmt->execute();
                            $orders = $order_stmt->get_result();
                            
                            if($orders->num_rows > 0):
                            ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($order = $orders->fetch_assoc()): 
                                                $status_color = 'secondary';
                                                switch($order['status']) {
                                                    case 'Ordered': $status_color = 'warning'; break;
                                                    case 'On Delivery': $status_color = 'info'; break;
                                                    case 'Delivered': $status_color = 'success'; break;
                                                    case 'Cancelled': $status_color = 'danger'; break;
                                                }
                                            ?>
                                            <tr>
                                                <td class="fw-bold">#<?= $order['id'] ?></td>
                                                <td><?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></td>
                                                <td class="fw-bold">Rs. <?= number_format($order['total_amount'], 2) ?></td>
                                                <td><span class="badge bg-<?= $status_color ?> text-dark bg-opacity-25 py-2 px-3 rounded-pill"><?= $order['status'] ?></span></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id'] ?>">Details</button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Order Details Modal -->
                                            <div class="modal fade" id="orderModal<?= $order['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 border-0 shadow">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold">Order #<?= $order['id'] ?> Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="mb-4">
                                                                <p class="mb-1 text-muted small">Order Date</p>
                                                                <p class="fw-bold"><?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></p>
                                                            </div>
                                                            <h6 class="fw-bold mb-3 border-bottom pb-2">Items</h6>
                                                            <ul class="list-group list-group-flush mb-4">
                                                                <?php
                                                                $item_sql = "SELECT oi.*, f.title FROM order_items oi JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = ?";
                                                                $item_stmt = $conn->prepare($item_sql);
                                                                $item_stmt->bind_param("i", $order['id']);
                                                                $item_stmt->execute();
                                                                $items = $item_stmt->get_result();
                                                                while($item = $items->fetch_assoc()):
                                                                ?>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                                    <div>
                                                                        <h6 class="mb-0"><?= $item['title'] ?></h6>
                                                                        <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                                                    </div>
                                                                    <span class="fw-bold">Rs. <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                                                </li>
                                                                <?php endwhile; ?>
                                                            </ul>
                                                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                                                                <span class="fw-bold">Total Amount</span>
                                                                <span class="fw-bold fs-5 text-primary">Rs. <?= number_format($order['total_amount'], 2) ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa-solid fa-receipt fs-2 text-muted"></i>
                                    </div>
                                    <h5 class="fw-bold">No orders yet</h5>
                                    <p class="text-muted">You haven't placed any orders. Start exploring our menu!</p>
                                    <a href="menu.php" class="btn btn-primary-custom px-4 mt-2">Browse Menu</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Tab -->
                <div class="tab-pane fade" id="profile">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-5">
                            <h4 class="fw-bold mb-4 border-bottom pb-3">Edit Profile</h4>
                            <form action="" method="POST">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small fw-bold">Full Name</label>
                                        <input type="text" name="full_name" class="form-control form-control-custom" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small fw-bold">Email Address (Read Only)</label>
                                        <input type="email" class="form-control form-control-custom bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted small fw-bold">Phone Number</label>
                                        <input type="text" name="phone" class="form-control form-control-custom" value="<?= htmlspecialchars($user['phone']) ?>" required>
                                    </div>
                                    <div class="col-12 mb-4">
                                        <label class="form-label text-muted small fw-bold">Delivery Address</label>
                                        <textarea name="address" class="form-control form-control-custom" rows="3" required><?= htmlspecialchars($user['address']) ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary-custom px-5 py-2">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php include 'includes/header.php'; ?>

<?php
// Get statistics
$cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$food_count = $conn->query("SELECT COUNT(*) as count FROM foods")->fetch_assoc()['count'];
$order_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status='Delivered'")->fetch_assoc()['total'];
$revenue = $revenue ? $revenue : 0;
?>

<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Total Categories</p>
                <h3 class="fw-bold mb-0"><?= $cat_count ?></h3>
            </div>
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fa-solid fa-list"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Total Foods</p>
                <h3 class="fw-bold mb-0"><?= $food_count ?></h3>
            </div>
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fa-solid fa-burger"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Total Orders</p>
                <h3 class="fw-bold mb-0"><?= $order_count ?></h3>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Revenue</p>
                <h3 class="fw-bold mb-0">Rs. <?= number_format($revenue, 2) ?></h3>
            </div>
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-muted">
                            <tr>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
                            if($recent_orders->num_rows > 0):
                                while($ro = $recent_orders->fetch_assoc()):
                                    $status_color = 'secondary';
                                    switch($ro['status']) {
                                        case 'Ordered': $status_color = 'warning'; break;
                                        case 'On Delivery': $status_color = 'info'; break;
                                        case 'Delivered': $status_color = 'success'; break;
                                        case 'Cancelled': $status_color = 'danger'; break;
                                    }
                            ?>
                                <tr>
                                    <td class="fw-bold">#<?= $ro['id'] ?></td>
                                    <td>Rs. <?= number_format($ro['total_amount'], 2) ?></td>
                                    <td><span class="badge bg-<?= $status_color ?> bg-opacity-25 text-<?= $status_color ?> rounded-pill px-3"><?= $ro['status'] ?></span></td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($ro['order_date'])) ?></td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">No orders yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-bell fs-2"></i>
                </div>
                <h5 class="fw-bold">System Status</h5>
                <p class="text-muted">The system is running smoothly. All services are operational.</p>
                <div class="mt-3 w-100">
                    <a href="../index.php" target="_blank" class="btn btn-primary-custom w-100 mb-2">View Customer Site</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

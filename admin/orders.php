<?php include 'includes/header.php'; ?>

<?php
// Handle Update Order Status
if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = sanitize_input($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Order status updated successfully.</div>";
    }
}

// Fetch order statistics counts
$stat_total = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$stat_ordered = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='Ordered'")->fetch_assoc()['count'];
$stat_delivery = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='On Delivery'")->fetch_assoc()['count'];
$stat_delivered = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='Delivered'")->fetch_assoc()['count'];
$stat_cancelled = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status='Cancelled'")->fetch_assoc()['count'];

// Fetch all orders
$all_orders = [];
$res = $conn->query("SELECT o.*, u.full_name, u.email, u.phone, u.address FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
if($res && $res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        $all_orders[] = $row;
    }
}
?>

<style>
.nav-tabs-custom .nav-link {
    color: var(--text-light);
    background-color: var(--white);
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-right: 5px;
    font-weight: 500;
    transition: var(--transition);
}
.nav-tabs-custom .nav-link:hover {
    background-color: rgba(255, 71, 87, 0.05);
    color: var(--primary-color);
}
.nav-tabs-custom .nav-link.active {
    background-color: var(--primary-color) !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
}
.order-row {
    transition: var(--transition);
}
.order-row:hover {
    background-color: rgba(255, 71, 87, 0.02) !important;
}
</style>

<!-- Order Statistics -->
<div class="row g-3 mb-4">
    <!-- Total Orders -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Total Orders</p>
                <h4 class="fw-bold mb-0 text-dark"><?= $stat_total ?></h4>
            </div>
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="fa-solid fa-cart-shopping fs-5"></i>
            </div>
        </div>
    </div>
    <!-- Ordered -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Ordered</p>
                <h4 class="fw-bold mb-0 text-warning"><?= $stat_ordered ?></h4>
            </div>
            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="fa-solid fa-clock-rotate-left fs-5"></i>
            </div>
        </div>
    </div>
    <!-- On Delivery -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">On Delivery</p>
                <h4 class="fw-bold mb-0 text-info"><?= $stat_delivery ?></h4>
            </div>
            <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="fa-solid fa-truck-fast fs-5"></i>
            </div>
        </div>
    </div>
    <!-- Delivered -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Delivered</p>
                <h4 class="fw-bold mb-0 text-success"><?= $stat_delivered ?></h4>
            </div>
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="fa-solid fa-square-check fs-5"></i>
            </div>
        </div>
    </div>
    <!-- Cancelled -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <p class="text-muted small mb-1 fw-bold text-uppercase">Cancelled</p>
                <h4 class="fw-bold mb-0 text-danger"><?= $stat_cancelled ?></h4>
            </div>
            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="fa-solid fa-rectangle-xmark fs-5"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="fw-bold mb-0">Manage Orders</h5>
        <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text bg-light border-end-0 border-0 rounded-start-pill ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" id="orderSearch" class="form-control bg-light border-0 rounded-end-pill py-2 pe-3" placeholder="Search by ID, name or phone...">
        </div>
    </div>
    
    <div class="card-body p-4 pt-2">
        <!-- Status Filter Tabs -->
        <ul class="nav nav-tabs nav-tabs-custom mb-4 border-bottom-0 gap-2" id="statusTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 border-0" id="tab-all" data-status="all" type="button"><i class="fa-solid fa-list me-2"></i>All</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 border-0" id="tab-ordered" data-status="Ordered" type="button"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Ordered</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 border-0" id="tab-delivery" data-status="On Delivery" type="button"><i class="fa-solid fa-truck-fast me-2 text-info"></i>On Delivery</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 border-0" id="tab-delivered" data-status="Delivered" type="button"><i class="fa-solid fa-square-check me-2 text-success"></i>Delivered</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 border-0" id="tab-cancelled" data-status="Cancelled" type="button"><i class="fa-solid fa-rectangle-xmark me-2 text-danger"></i>Cancelled</button>
            </li>
        </ul>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-muted">
                    <tr>
                        <th style="width: 10%;">Order ID</th>
                        <th style="width: 35%;">Customer Details</th>
                        <th style="width: 15%;">Amount</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 13%;">Status</th>
                        <th style="width: 12%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(count($all_orders) > 0):
                        foreach($all_orders as $row):
                            $status_color = 'secondary';
                            $status_icon = 'fa-circle-question';
                            switch($row['status']) {
                                case 'Ordered':
                                    $status_color = 'warning';
                                    $status_icon = 'fa-clock-rotate-left';
                                    break;
                                case 'On Delivery':
                                    $status_color = 'info';
                                    $status_icon = 'fa-truck-fast';
                                    break;
                                case 'Delivered':
                                    $status_color = 'success';
                                    $status_icon = 'fa-square-check';
                                    break;
                                case 'Cancelled':
                                    $status_color = 'danger';
                                    $status_icon = 'fa-rectangle-xmark';
                                    break;
                            }
                            
                            $search_text = '#' . $row['id'] . ' ' . $row['full_name'] . ' ' . $row['phone'] . ' ' . $row['address'] . ' ' . $row['email'];
                    ?>
                        <tr class="order-row" data-order-status="<?= $row['status'] ?>" data-search-text="<?= htmlspecialchars(strtolower($search_text)) ?>">
                            <td class="fw-bold text-muted">#<?= $row['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fa-solid fa-user text-muted me-2 small" style="width: 16px;"></i>
                                    <strong class="text-dark"><?= htmlspecialchars($row['full_name']) ?></strong>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fa-solid fa-phone text-muted me-2 small" style="width: 16px;"></i>
                                    <small class="text-muted"><?= htmlspecialchars($row['phone']) ?></small>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="fa-solid fa-location-dot text-muted me-2 mt-1 small" style="width: 16px;"></i>
                                    <small class="text-muted text-wrap" style="max-width: 250px;"><?= htmlspecialchars($row['address']) ?></small>
                                </div>
                            </td>
                            <td class="fw-bold text-primary">Rs. <?= number_format($row['total_amount'], 2) ?></td>
                            <td class="text-muted small"><?= date('M d, Y', strtotime($row['order_date'])) ?><br><?= date('h:i A', strtotime($row['order_date'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $status_color ?> bg-opacity-10 text-<?= $status_color ?> px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid <?= $status_icon ?> fs-7"></i>
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#viewOrderModal<?= $row['id'] ?>">Details</button>
                                    <button type="button" class="btn btn-sm btn-primary-custom px-3" data-bs-toggle="modal" data-bs-target="#updateOrderModal<?= $row['id'] ?>">Update</button>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endforeach;
                    else:
                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No orders found.</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Render Modals Outside Table Structure -->
<?php
foreach($all_orders as $row):
    $status_color = 'secondary';
    $status_icon = 'fa-circle-question';
    switch($row['status']) {
        case 'Ordered':
            $status_color = 'warning';
            $status_icon = 'fa-clock-rotate-left';
            break;
        case 'On Delivery':
            $status_color = 'info';
            $status_icon = 'fa-truck-fast';
            break;
        case 'Delivered':
            $status_color = 'success';
            $status_icon = 'fa-square-check';
            break;
        case 'Cancelled':
            $status_color = 'danger';
            $status_icon = 'fa-rectangle-xmark';
            break;
    }
?>
    <!-- Order Details Modal -->
    <div class="modal fade" id="viewOrderModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt text-primary"></i>
                        Order #<?= $row['id'] ?> Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4 mb-4">
                        <!-- Customer Column -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light rounded-3 p-3 h-100">
                                <h6 class="text-primary small fw-bold text-uppercase mb-3 d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-address-card"></i> Customer Details
                                </h6>
                                <p class="mb-2"><strong>Name:</strong> <?= htmlspecialchars($row['full_name']) ?></p>
                                <p class="mb-2"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($row['email']) ?></a></p>
                                <p class="mb-2"><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
                                <p class="mb-0"><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                            </div>
                        </div>
                        <!-- Order Info Column -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light rounded-3 p-3 h-100">
                                <h6 class="text-primary small fw-bold text-uppercase mb-3 d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-circle-info"></i> Order Overview
                                </h6>
                                <p class="mb-2"><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($row['order_date'])) ?></p>
                                <p class="mb-2">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-<?= $status_color ?> bg-opacity-10 text-<?= $status_color ?> px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid <?= $status_icon ?> fs-7"></i>
                                        <?= $row['status'] ?>
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Payment Method:</strong> Cash on Delivery (COD)</p>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="text-muted small fw-bold text-uppercase mb-3 border-bottom pb-2">Items Ordered</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-3">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Food Item</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $items_res = $conn->query("SELECT oi.*, f.title FROM order_items oi JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = " . $row['id']);
                                $subtotal = 0;
                                if($items_res && $items_res->num_rows > 0):
                                    while($item = $items_res->fetch_assoc()):
                                        $item_total = $item['price'] * $item['quantity'];
                                        $subtotal += $item_total;
                                ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($item['title']) ?></td>
                                        <td class="text-end text-muted">Rs. <?= number_format($item['price'], 2) ?></td>
                                        <td class="text-center fw-bold"><?= $item['quantity'] ?></td>
                                        <td class="text-end fw-bold text-dark">Rs. <?= number_format($item_total, 2) ?></td>
                                    </tr>
                                <?php 
                                    endwhile;
                                endif;
                                $delivery_fee = 250.00;
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="row justify-content-end text-end">
                        <div class="col-md-6 col-lg-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Subtotal</span>
                                <span class="fw-bold">Rs. <?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Delivery Fee</span>
                                <span class="fw-bold">Rs. <?= number_format($delivery_fee, 2) ?></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total Amount</span>
                                <span class="fw-bold text-primary fs-5">Rs. <?= number_format($row['total_amount'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="printInvoice(<?= $row['id'] ?>)"><i class="fa-solid fa-print me-1"></i> Print Invoice</button>
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Update Status Modal -->
    <div class="modal fade" id="updateOrderModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Update Order #<?= $row['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        
                        <!-- Order items summary -->
                        <div class="bg-light p-3 rounded-3 mb-3 border">
                            <h6 class="fw-bold mb-2 small text-uppercase text-muted d-flex align-items-center gap-2">
                                <i class="fa-solid fa-receipt text-primary"></i> Order Summary
                            </h6>
                            <ul class="list-group list-group-flush bg-transparent">
                                <?php
                                $sum_res = $conn->query("SELECT oi.quantity, f.title FROM order_items oi JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = " . $row['id']);
                                if($sum_res && $sum_res->num_rows > 0):
                                    while($sum_row = $sum_res->fetch_assoc()):
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 py-1 small">
                                        <span class="text-dark"><?= htmlspecialchars($sum_row['title']) ?></span>
                                        <span class="badge bg-secondary rounded-pill">x<?= $sum_row['quantity'] ?></span>
                                    </li>
                                <?php 
                                    endwhile;
                                endif;
                                ?>
                            </ul>
                            <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">Total Amount:</span>
                                <span class="fw-bold text-primary small">Rs. <?= number_format($row['total_amount'], 2) ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Status</label>
                            <select name="status" class="form-select form-control-custom">
                                <option value="Ordered" <?= $row['status'] == 'Ordered' ? 'selected' : '' ?>>Ordered</option>
                                <option value="On Delivery" <?= $row['status'] == 'On Delivery' ? 'selected' : '' ?>>On Delivery</option>
                                <option value="Delivered" <?= $row['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="Cancelled" <?= $row['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_status" class="btn btn-primary-custom px-4">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('orderSearch');
    const tabButtons = document.querySelectorAll('#statusTabs button');
    const orderRows = document.querySelectorAll('.order-row');
    let currentStatus = 'all';
    let searchQuery = '';

    function filterTable() {
        orderRows.forEach(row => {
            const status = row.getAttribute('data-order-status');
            const searchText = row.getAttribute('data-search-text').toLowerCase();
            const statusMatches = (currentStatus === 'all' || status === currentStatus);
            const searchMatches = (searchQuery === '' || searchText.includes(searchQuery));

            if (statusMatches && searchMatches) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', function(e) {
        searchQuery = e.target.value.toLowerCase().trim();
        filterTable();
    });

    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentStatus = this.getAttribute('data-status');
            filterTable();
        });
    });
});

function printInvoice(orderId) {
    var modalBody = document.querySelector('#viewOrderModal' + orderId + ' .modal-body').innerHTML;
    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Invoice #' + orderId + '</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
    printWindow.document.write('<style>body{padding: 30px; font-family: sans-serif;} .btn, .btn-close, .modal-footer{display:none;} .text-end { text-align: right; } .d-flex { display: flex; } .justify-content-between { justify-content: space-between; } .mb-2 { margin-bottom: 0.5rem; } .my-2 { margin-top: 0.5rem; margin-bottom: 0.5rem; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="text-center mb-4"><h2>Foodies Restaurant</h2><p class="text-muted">Order Invoice</p></div>');
    printWindow.document.write(modalBody);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>

<?php include 'includes/footer.php'; ?>

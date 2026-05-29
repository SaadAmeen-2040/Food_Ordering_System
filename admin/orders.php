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
?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Manage Orders</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-muted">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Details</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT o.*, u.full_name, u.email, u.phone, u.address FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
                    if($res->num_rows > 0):
                        while($row = $res->fetch_assoc()):
                            $status_color = 'secondary';
                            switch($row['status']) {
                                case 'Ordered': $status_color = 'warning'; break;
                                case 'On Delivery': $status_color = 'info'; break;
                                case 'Delivered': $status_color = 'success'; break;
                                case 'Cancelled': $status_color = 'danger'; break;
                            }
                    ?>
                        <tr>
                            <td class="fw-bold">#<?= $row['id'] ?></td>
                            <td>
                                <strong><?= $row['full_name'] ?></strong><br>
                                <small class="text-muted"><?= $row['phone'] ?></small><br>
                                <small class="text-muted"><?= $row['address'] ?></small>
                            </td>
                            <td class="fw-bold text-primary">Rs. <?= number_format($row['total_amount'], 2) ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($row['order_date'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $status_color ?> bg-opacity-25 text-<?= $status_color ?> px-3 rounded-pill"><?= $row['status'] ?></span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#viewOrderModal<?= $row['id'] ?>">Details</button>
                                    <button type="button" class="btn btn-sm btn-primary-custom px-3" data-bs-toggle="modal" data-bs-target="#updateOrderModal<?= $row['id'] ?>">Update</button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Order Details Modal -->
                        <div class="modal fade" id="viewOrderModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Order #<?= $row['id'] ?> Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6">
                                                <h6 class="text-muted small fw-bold text-uppercase mb-2">Customer Details</h6>
                                                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($row['full_name']) ?></p>
                                                <p class="mb-1"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($row['email']) ?></a></p>
                                                <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
                                                <p class="mb-0"><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-muted small fw-bold text-uppercase mb-2">Order Info</h6>
                                                <p class="mb-1"><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($row['order_date'])) ?></p>
                                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?= $status_color ?> bg-opacity-25 text-<?= $status_color ?> px-3 rounded-pill"><?= $row['status'] ?></span></p>
                                                <p class="mb-0"><strong>Payment Method:</strong> Cash on Delivery (COD)</p>
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
                                                            <td class="text-end">Rs. <?= number_format($item['price'], 2) ?></td>
                                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                                            <td class="text-end fw-bold text-dark">Rs. <?= number_format($item_total, 2) ?></td>
                                                        </tr>
                                                    <?php 
                                                        endwhile;
                                                    endif;
                                                    // Delivery fee is 250 in this app (as defined in checkout.php)
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
                                            <div class="bg-light p-3 rounded-3 mb-3">
                                                <h6 class="fw-bold mb-2 small text-uppercase text-muted">Order Summary</h6>
                                                <ul class="list-group list-group-flush bg-transparent">
                                                    <?php
                                                    $sum_res = $conn->query("SELECT oi.quantity, f.title FROM order_items oi JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = " . $row['id']);
                                                    if($sum_res && $sum_res->num_rows > 0):
                                                        while($sum_row = $sum_res->fetch_assoc()):
                                                    ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 py-1 small">
                                                            <span><?= htmlspecialchars($sum_row['title']) ?></span>
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
                    <?php 
                        endwhile;
                    else:
                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No orders found.</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
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


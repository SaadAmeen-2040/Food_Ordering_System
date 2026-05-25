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
                    $res = $conn->query("SELECT o.*, u.full_name, u.phone, u.address FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
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
                                <button type="button" class="btn btn-sm btn-primary-custom px-3" data-bs-toggle="modal" data-bs-target="#updateOrderModal<?= $row['id'] ?>">Update</button>
                            </td>
                        </tr>
                        
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

<?php include 'includes/footer.php'; ?>

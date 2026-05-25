<?php 
include 'includes/header.php'; 
include 'includes/navbar.php'; 

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

// Check if cart is empty
if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: menu.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$delivery_fee = 250.00;

// Calculate total
$subtotal = 0;
foreach($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$total_amount = $subtotal + $delivery_fee;

$error = '';
$success = '';

// Fetch user details to pre-fill form
$stmt = $conn->prepare("SELECT full_name, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Basic validation
    $delivery_address = sanitize_input($_POST['address']);
    $phone = sanitize_input($_POST['phone']);
    
    if(empty($delivery_address) || empty($phone)) {
        $error = "Delivery address and phone number are required.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Update user address/phone if changed
            $update_user = $conn->prepare("UPDATE users SET address = ?, phone = ? WHERE id = ?");
            $update_user->bind_param("ssi", $delivery_address, $phone, $user_id);
            $update_user->execute();
            
            // Create Order
            $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Ordered')");
            $order_stmt->bind_param("id", $user_id, $total_amount);
            $order_stmt->execute();
            $order_id = $conn->insert_id;
            
            // Insert Order Items
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach($_SESSION['cart'] as $item) {
                $item_stmt->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
                $item_stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart
            unset($_SESSION['cart']);
            
            $success = "Order placed successfully! Your order ID is #$order_id. You can track its status in your dashboard.";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to place order. Please try again later.";
        }
    }
}
?>

<div class="bg-light py-5 mb-5 text-center border-bottom">
    <div class="container py-3">
        <h1 class="display-5 fw-bold mb-0 brand-font">Checkout</h1>
    </div>
</div>

<div class="container mb-5">
    <?php if($success): ?>
        <div class="card border-0 shadow-lg rounded-4 text-center py-5">
            <div class="card-body py-5">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                    <i class="fa-solid fa-check fs-1"></i>
                </div>
                <h2 class="brand-font fw-bold mb-3">Order Confirmed!</h2>
                <p class="lead text-muted mb-4"><?= $success ?></p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="dashboard.php" class="btn btn-primary-custom px-4 py-2">Go to Dashboard</a>
                    <a href="menu.php" class="btn btn-outline-custom px-4 py-2">Order More Food</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="fa-solid fa-map-location-dot text-primary me-2"></i>Delivery Details</h4>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form action="" method="POST" id="checkoutForm">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold">Full Name</label>
                                    <input type="text" class="form-control form-control-custom bg-light" value="<?= htmlspecialchars($user_data['full_name']) ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold">Email Address</label>
                                    <input type="email" class="form-control form-control-custom bg-light" value="<?= htmlspecialchars($user_data['email']) ?>" readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted small fw-bold">Phone Number *</label>
                                    <input type="text" name="phone" class="form-control form-control-custom" required value="<?= htmlspecialchars($user_data['phone']) ?>">
                                </div>
                                <div class="col-12 mb-4">
                                    <label class="form-label text-muted small fw-bold">Delivery Address *</label>
                                    <textarea name="address" class="form-control form-control-custom" rows="3" required><?= htmlspecialchars($user_data['address']) ?></textarea>
                                </div>
                                
                                <h4 class="fw-bold mb-3 mt-4 border-bottom pb-3"><i class="fa-solid fa-credit-card text-primary me-2"></i>Payment Method</h4>
                                
                                <div class="col-12 mb-4">
                                    <div class="form-check p-4 border rounded-3 bg-light shadow-sm">
                                        <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="cod" value="cod" checked>
                                        <label class="form-check-label fw-bold d-flex align-items-center gap-2" for="cod">
                                            <i class="fa-solid fa-money-bill-wave text-success fs-4"></i> Cash on Delivery
                                        </label>
                                        <p class="text-muted small ms-5 mb-0 mt-1">Pay with cash when your order arrives.</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold border-bottom pb-3 mb-4">Your Order</h5>
                        
                        <div class="mb-4" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach($_SESSION['cart'] as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <h6 class="mb-0"><?= $item['title'] ?></h6>
                                        <small class="text-muted">Qty: <?= $item['qty'] ?></small>
                                    </div>
                                    <span class="fw-bold">Rs. <?= number_format($item['price'] * $item['qty'], 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">Rs. <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Delivery Fee</span>
                            <span class="fw-bold">Rs. <?= number_format($delivery_fee, 2) ?></span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total</span>
                            <span class="fw-bold fs-5 text-primary">Rs. <?= number_format($total_amount, 2) ?></span>
                        </div>
                        
                        <button type="submit" form="checkoutForm" class="btn btn-primary-custom w-100 py-3 fs-6 fw-bold shadow-lg">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

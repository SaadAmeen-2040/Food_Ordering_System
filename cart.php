<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- Page Header -->
<div class="bg-light py-5 mb-5 text-center border-bottom">
    <div class="container py-3">
        <h1 class="display-5 fw-bold mb-0 brand-font">Your Shopping Cart</h1>
    </div>
</div>

<div class="container mb-5" style="min-height: 400px;">
    <?php
        if(isset($_SESSION['message'])) {
            echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    ' . $_SESSION['message'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['message']);
        }
    ?>

    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="border-bottom text-muted">
                                    <tr>
                                        <th scope="col" style="width: 50%;">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col" class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $subtotal = 0;
                                    foreach($_SESSION['cart'] as $item): 
                                        $item_total = $item['price'] * $item['qty'];
                                        $subtotal += $item_total;
                                    ?>
                                    <tr class="border-bottom">
                                        <td class="py-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div style="width: 80px; height: 80px; border-radius: 15px; overflow: hidden; flex-shrink: 0;">
                                                    <img src="images/<?= $item['image'] ?>" class="w-100 h-100 object-fit-cover" alt="<?= $item['title'] ?>">
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold"><?= $item['title'] ?></h6>
                                                    <form action="cart_action.php" method="POST">
                                                        <input type="hidden" name="food_id" value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="action" value="remove">
                                                        <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none small"><i class="fa-solid fa-trash-can me-1"></i>Remove</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rs. <?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <form action="cart_action.php" method="POST" class="d-flex align-items-center" style="max-width: 120px;">
                                                <input type="hidden" name="food_id" value="<?= $item['id'] ?>">
                                                <input type="hidden" name="action" value="update">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="qty" value="<?= $item['qty'] ?>" min="1" max="20" class="form-control text-center" onchange="this.form.submit()">
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end fw-bold">Rs. <?= number_format($item_total, 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3">
                            <a href="menu.php" class="text-decoration-none text-muted"><i class="fa-solid fa-arrow-left me-2"></i>Continue Shopping</a>
                            <form action="cart_action.php" method="POST">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Clear Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold border-bottom pb-3 mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">Rs. <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Delivery Fee</span>
                            <?php $delivery = 250.00; ?>
                            <span class="fw-bold">Rs. <?= number_format($delivery, 2) ?></span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total</span>
                            <span class="fw-bold fs-5 text-primary">Rs. <?= number_format($subtotal + $delivery, 2) ?></span>
                        </div>
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="checkout.php" class="btn btn-primary-custom w-100 py-3 fs-6">Proceed to Checkout</a>
                        <?php else: ?>
                            <div class="alert alert-warning small text-center mb-3">You must log in to checkout.</div>
                            <a href="login.php?redirect=checkout.php" class="btn btn-primary-custom w-100 py-3 fs-6">Log In to Checkout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fa-solid fa-cart-shopping text-muted opacity-25" style="font-size: 80px;"></i>
            </div>
            <h3 class="brand-font mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="menu.php" class="btn btn-primary-custom px-5 py-2">Browse Menu</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

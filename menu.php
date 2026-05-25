<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- Page Header -->
<div class="bg-primary text-white py-5 mb-5 text-center" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
    <div class="container py-4">
        <h1 class="display-4 fw-bold mb-3 brand-font">Our Menu</h1>
        <p class="lead mb-0">Discover our delicious varieties and order your favorites</p>
    </div>
</div>

<div class="container mb-5">
    <?php
        // Display messages
        if(isset($_SESSION['message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>' . $_SESSION['message'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['message']);
        }
    ?>

    <div class="row g-4">
        <!-- Sidebar - Categories Filter -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Categories</h5>
                    
                    <form action="menu.php" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search food..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            <button class="btn btn-primary-custom px-3" type="submit" style="border-radius: 0 50px 50px 0;"><i class="fa-solid fa-search"></i></button>
                        </div>
                    </form>

                    <div class="list-group list-group-flush">
                        <a href="menu.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= !isset($_GET['category']) ? 'active bg-primary border-primary text-white' : 'text-dark' ?> rounded mb-1">
                            All Categories
                        </a>
                        
                        <?php
                        $cat_sql = "SELECT id, title FROM categories WHERE active='Yes'";
                        $cat_res = @$conn->query($cat_sql);
                        if($cat_res && $cat_res->num_rows > 0):
                            while($cat = $cat_res->fetch_assoc()):
                                $activeClass = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active bg-primary border-primary text-white' : 'text-dark';
                        ?>
                            <a href="menu.php?category=<?= $cat['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $activeClass ?> rounded mb-1">
                                <?= $cat['title'] ?>
                            </a>
                        <?php 
                            endwhile;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Food Items Grid -->
        <div class="col-lg-9">
            <div class="row g-4">
                <?php
                // Build query based on filters
                $food_sql = "SELECT * FROM foods WHERE active='Yes'";
                
                if(isset($_GET['category']) && is_numeric($_GET['category'])) {
                    $cat_id = $_GET['category'];
                    $food_sql .= " AND category_id = $cat_id";
                }
                
                if(isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = sanitize_input($_GET['search']);
                    $food_sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
                }
                
                $food_res = @$conn->query($food_sql);
                
                if($food_res && $food_res->num_rows > 0):
                    while($food = $food_res->fetch_assoc()):
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="food-card h-100">
                        <div class="food-img-container">
                            <div class="food-price-badge">Rs. <?= number_format($food['price'], 2) ?></div>
                            <img src="images/<?= $food['image_name'] ?>" class="food-img" alt="<?= $food['title'] ?>">
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h4 class="brand-font mb-2 fs-5"><?= $food['title'] ?></h4>
                            <p class="text-muted small mb-4 flex-grow-1"><?= $food['description'] ?></p>
                            
                            <form action="cart_action.php" method="POST" class="mt-auto">
                                <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                                <input type="hidden" name="action" value="add">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" name="qty" value="1" min="1" max="20" class="form-control text-center" style="width: 70px; border-radius: 50px;">
                                    <button type="submit" class="btn btn-primary-custom flex-grow-1">Add to Cart</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="col-12 text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="150" class="mb-4 opacity-50" alt="No food">
                        <h3 class="text-muted">No food items found matching your criteria.</h3>
                        <p>Try searching for something else or browse all categories.</p>
                        <a href="menu.php" class="btn btn-outline-custom mt-3">View All Menu</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

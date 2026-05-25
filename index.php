<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-shape"></div>
    <div class="hero-shape-2"></div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-in-up">
                <span class="badge bg-danger bg-opacity-10 text-danger mb-3 px-3 py-2 rounded-pill fw-bold">#1 Food Delivery Service</span>
                <h1 class="display-3 fw-bold mb-4">Craving Something <span class="text-primary">Delicious?</span></h1>
                <p class="lead mb-5 text-muted">Discover the best food from your favorite restaurants delivered fast right to your doorstep.</p>
                
                <form action="menu.php" method="GET" class="search-form mb-4">
                    <div class="position-relative">
                        <i class="fa-solid fa-location-dot position-absolute top-50 start-0 translate-middle-y ms-3 text-primary"></i>
                        <input type="text" name="search" class="search-input ps-5" placeholder="What would you like to eat?">
                        <button type="submit" class="search-btn">Search</button>
                    </div>
                </form>
                
                <div class="d-flex align-items-center gap-4 mt-5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-motorcycle"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Fast Delivery</h6>
                            <small class="text-muted">Within 30 mins</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Best Quality</h6>
                            <small class="text-muted">Top Rated Food</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0 text-center fade-in-up" style="animation-delay: 0.2s;">
                <!-- Placeholder for Hero Image, you should replace this with a real image -->
                <img src="images/pk-restaurant.jpg" alt="Restaurant Image" class="img-fluid rounded-circle shadow-lg hero-img" style="border: 15px solid white; object-fit: cover; width: 500px; height: 500px;">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-primary fw-bold text-uppercase tracking-wider">Top Categories</h6>
                <h2 class="brand-font mb-0">Explore Our Menu</h2>
            </div>
            <a href="menu.php" class="text-primary text-decoration-none fw-bold">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row g-4">
            <?php
            // Fetch categories (assuming database is connected and has data, use error suppression for initial load if no table exists yet)
            $sql = "SELECT * FROM categories WHERE active='Yes' LIMIT 3";
            $res = @$conn->query($sql); // @ suppresses error if table doesn't exist yet
            
            if($res && $res->num_rows > 0):
                while($row = $res->fetch_assoc()):
            ?>
                <div class="col-md-4">
                    <a href="menu.php?category=<?= $row['id'] ?>" class="text-decoration-none">
                        <div class="category-card">
                            <img src="images/<?= $row['image_name'] ?>" alt="<?= $row['title'] ?>">
                            <h3 class="category-title"><?= $row['title'] ?></h3>
                        </div>
                    </a>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <!-- Fallback if no categories -->
                <div class="col-md-4">
                    <div class="category-card"><img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?q=80&w=600&auto=format&fit=crop" alt="Pizza"><h3 class="category-title">Pizza</h3></div>
                </div>
                <div class="col-md-4">
                    <div class="category-card"><img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=600&auto=format&fit=crop" alt="Burger"><h3 class="category-title">Burgers</h3></div>
                </div>
                <div class="col-md-4">
                    <div class="category-card"><img src="https://images.unsplash.com/photo-1579871494447-9811cf80d66c?q=80&w=600&auto=format&fit=crop" alt="Sushi"><h3 class="category-title">Sushi</h3></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Food Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-primary fw-bold text-uppercase tracking-wider">Popular Dishes</h6>
            <h2 class="brand-font mb-0">Our Featured Food</h2>
        </div>
        
        <div class="row g-4">
            <?php
            $sql2 = "SELECT * FROM foods WHERE active='Yes' LIMIT 6";
            $res2 = @$conn->query($sql2);
            
            if($res2 && $res2->num_rows > 0):
                while($row2 = $res2->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="food-card">
                    <div class="food-img-container">
                        <div class="food-price-badge">Rs. <?= $row2['price'] ?></div>
                        <img src="images/<?= $row2['image_name'] ?>" class="food-img" alt="<?= $row2['title'] ?>">
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h4 class="brand-font mb-2"><?= $row2['title'] ?></h4>
                        <p class="text-muted small mb-4 flex-grow-1"><?= substr($row2['description'], 0, 80) ?>...</p>
                        <form action="cart_action.php" method="POST" class="mt-auto">
                            <input type="hidden" name="food_id" value="<?= $row2['id'] ?>">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="qty" value="1">
                            <button type="submit" class="btn btn-primary-custom w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
                // Fallback dummy items
                for($i=0; $i<3; $i++):
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="food-card">
                    <div class="food-img-container">
                        <div class="food-price-badge">Rs. 800</div>
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600&auto=format&fit=crop" class="food-img" alt="Food Item">
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h4 class="brand-font mb-2">Delicious Biryani</h4>
                        <p class="text-muted small mb-4 flex-grow-1">A very delicious biryani that you will surely love. Made with fresh ingredients.</p>
                        <button class="btn btn-primary-custom w-100">Add to Cart</button>
                    </div>
                </div>
            </div>
            <?php 
                endfor;
            endif; 
            ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="menu.php" class="btn btn-outline-custom">View Full Menu</a>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 text-white text-center position-relative" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=1200&auto=format&fit=crop') center/cover; background-attachment: fixed;">
    <div class="container py-5 position-relative z-1">
        <h2 class="display-5 fw-bold mb-3 brand-font">Order Now and Get Free Delivery!</h2>
        <p class="lead mb-4 mx-auto" style="max-width: 600px;">Experience the best food in town delivered straight to your door. No delivery fee on your first order.</p>
        <a href="menu.php" class="btn btn-primary-custom btn-lg px-5">Order Now</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

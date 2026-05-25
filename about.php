<?php 
include 'includes/header.php'; 
include 'includes/navbar.php'; 
?>

<!-- Page Header -->
<div class="bg-primary text-white py-5 mb-5 text-center" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
    <div class="container py-4">
        <h1 class="display-4 fw-bold mb-3 brand-font">About Us</h1>
        <p class="lead mb-0">Our story and passion for great food</p>
    </div>
</div>

<div class="container mb-5 py-3">
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=800&auto=format&fit=crop" alt="Restaurant Interior" class="img-fluid rounded-4 shadow-lg">
        </div>
        <div class="col-lg-6">
            <h6 class="text-primary fw-bold text-uppercase tracking-wider">Our Story</h6>
            <h2 class="brand-font fw-bold mb-4">Delivering Happiness, One Meal at a Time</h2>
            <p class="text-muted mb-4">Founded in 2023, Foodies started with a simple mission: to make high-quality, delicious food accessible to everyone, anywhere. We believe that good food has the power to bring people together, create memories, and bring a smile to your face.</p>
            <p class="text-muted mb-4">We partner with the best local chefs and restaurants to curate a menu that satisfies every craving, from comforting classics to exotic new flavors. Our state-of-the-art delivery system ensures your food arrives hot, fresh, and on time.</p>
            <div class="row g-4 mt-2">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-utensils fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">500+</h4>
                            <p class="text-muted small mb-0">Delicious Dishes</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-users fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">10k+</h4>
                            <p class="text-muted small mb-0">Happy Customers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="text-center mb-5 mt-5 pt-4">
        <h6 class="text-primary fw-bold text-uppercase tracking-wider">Our Team</h6>
        <h2 class="brand-font fw-bold mb-5">Meet The Masterminds</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?q=80&w=400&auto=format&fit=crop" class="card-img-top object-fit-cover" style="height: 250px;" alt="Chef">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">Gordon James</h5>
                        <p class="text-primary small fw-bold mb-3">Head Chef</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="text-muted hover-primary"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="text-muted hover-primary"><i class="fa-brands fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 text-center h-100 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?q=80&w=400&auto=format&fit=crop" class="card-img-top object-fit-cover" style="height: 250px;" alt="Manager">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">Sarah Smith</h5>
                        <p class="text-primary small fw-bold mb-3">Operations Manager</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="text-muted hover-primary"><i class="fa-brands fa-linkedin"></i></a>
                            <a href="#" class="text-muted hover-primary"><i class="fa-brands fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php 
include 'includes/header.php'; 
include 'includes/navbar.php'; 

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    
    if(empty($name) || empty($email) || empty($message)) {
        $error = "Name, email, and message are required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if($stmt->execute()) {
            $success = "Thank you for reaching out! We have received your message and will get back to you shortly.";
        } else {
            $error = "Failed to send message. Please try again later.";
        }
    }
}
?>

<!-- Page Header -->
<div class="bg-primary text-white py-5 mb-5 text-center" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));">
    <div class="container py-4">
        <h1 class="display-4 fw-bold mb-3 brand-font">Contact Us</h1>
        <p class="lead mb-0">We'd love to hear from you</p>
    </div>
</div>

<div class="container mb-5 py-3">
    <div class="row g-5">
        <div class="col-lg-5">
            <h2 class="brand-font fw-bold mb-4">Get In Touch</h2>
            <p class="text-muted mb-5">Have a question about our menu, delivery, or a recent order? Don't hesitate to contact us. Our team is always ready to assist you.</p>
            
            <div class="d-flex align-items-center gap-4 mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-location-dot fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Our Location</h5>
                    <p class="text-muted mb-0">123 Food Street, Culinary District, NY 10001</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4 mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-phone fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Call Us</h5>
                    <p class="text-muted mb-0">+1 (555) 123-4567<br>Mon-Sun: 9:00 AM - 10:00 PM</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-envelope fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Email Us</h5>
                    <p class="text-muted mb-0">info@foodies.com<br>support@foodies.com</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <h4 class="fw-bold mb-4">Send us a Message</h4>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success"><i class="fa-solid fa-check-circle me-2"></i><?= $success ?></div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Your Name *</label>
                                <input type="text" name="name" class="form-control form-control-custom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">Your Email *</label>
                                <input type="email" name="email" class="form-control form-control-custom" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted small fw-bold">Subject</label>
                                <input type="text" name="subject" class="form-control form-control-custom">
                            </div>
                            <div class="col-12 mb-4">
                                <label class="form-label text-muted small fw-bold">Message *</label>
                                <textarea name="message" class="form-control form-control-custom" rows="5" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom px-5 py-3 w-100 fw-bold">Send Message <i class="fa-solid fa-paper-plane ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php include 'includes/header.php'; ?>

<?php
// Handle Add Food
if(isset($_POST['add_food'])) {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $active = sanitize_input($_POST['active']);
    $image_name = 'placeholder.jpg';
    
    // Check if image uploaded
    if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = "Food_".rand(1000, 9999).'.'.$ext;
        $source_path = $_FILES['image']['tmp_name'];
        $destination_path = "../images/".$image_name;
        move_uploaded_file($source_path, $destination_path);
    }
    
    $stmt = $conn->prepare("INSERT INTO foods (title, description, price, image_name, category_id, active) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $title, $description, $price, $image_name, $category_id, $active);
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Food item added successfully.</div>";
    }
}

// Handle Update Food
if(isset($_POST['update_food'])) {
    $id = intval($_POST['food_id']);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $price = doubleval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $active = sanitize_input($_POST['active']);
    
    // Check if new image uploaded
    if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        // Fetch old image name to delete file from folder
        $img_stmt = $conn->prepare("SELECT image_name FROM foods WHERE id = ?");
        $img_stmt->bind_param("i", $id);
        $img_stmt->execute();
        $old_img_res = $img_stmt->get_result()->fetch_assoc();
        if($old_img_res) {
            $old_image = $old_img_res['image_name'];
            if($old_image != "" && $old_image != "placeholder.jpg") {
                $old_path = "../images/".$old_image;
                if(file_exists($old_path)) {
                    unlink($old_path);
                }
            }
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = "Food_".rand(1000, 9999).'.'.$ext;
        $source_path = $_FILES['image']['tmp_name'];
        $destination_path = "../images/".$image_name;
        move_uploaded_file($source_path, $destination_path);
        
        $stmt = $conn->prepare("UPDATE foods SET title=?, description=?, price=?, image_name=?, category_id=?, active=? WHERE id=?");
        $stmt->bind_param("ssdsssi", $title, $description, $price, $image_name, $category_id, $active, $id);
    } else {
        $stmt = $conn->prepare("UPDATE foods SET title=?, description=?, price=?, category_id=?, active=? WHERE id=?");
        $stmt->bind_param("ssdiss", $title, $description, $price, $category_id, $active, $id);
    }
    
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Food item updated successfully.</div>";
    }
}

// Handle Delete Food
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Fetch image name to delete file from folder
    $img_stmt = $conn->prepare("SELECT image_name FROM foods WHERE id = ?");
    $img_stmt->bind_param("i", $id);
    $img_stmt->execute();
    $img_res = $img_stmt->get_result()->fetch_assoc();
    if($img_res) {
        $image_name = $img_res['image_name'];
        if($image_name != "" && $image_name != "placeholder.jpg") {
            $path = "../images/".$image_name;
            if(file_exists($path)) {
                unlink($path);
            }
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM foods WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Food item deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to delete food item.</div>";
    }
}
?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Manage Foods</h5>
        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addFoodModal"><i class="fa-solid fa-plus me-1"></i> Add New</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-muted">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Price (Rs.)</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT f.*, c.title as cat_title FROM foods f LEFT JOIN categories c ON f.category_id = c.id");
                    if($res->num_rows > 0):
                        while($row = $res->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td class="fw-bold"><?= $row['title'] ?></td>
                            <td>Rs. <?= number_format($row['price'], 2) ?></td>
                            <td><?= $row['cat_title'] ?></td>
                            <td>
                                <?php if($row['active'] == 'Yes'): ?>
                                    <span class="badge bg-success bg-opacity-25 text-success px-3 rounded-pill">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-25 text-danger px-3 rounded-pill">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#updateFoodModal<?= $row['id'] ?>">Edit</button>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this food item?')">Delete</a>
                            </td>
                        </tr>
                        
                        <!-- Update Food Modal -->
                        <div class="modal fade" id="updateFoodModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header border-bottom-0">
                                        <h5 class="modal-title fw-bold">Update Food Item</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="food_id" value="<?= $row['id'] ?>">
                                            <div class="row g-3">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted small fw-bold">Title</label>
                                                    <input type="text" name="title" class="form-control form-control-custom" value="<?= $row['title'] ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted small fw-bold">Price (Rs.)</label>
                                                    <input type="number" step="0.01" name="price" class="form-control form-control-custom" value="<?= $row['price'] ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted small fw-bold">Category</label>
                                                    <select name="category_id" class="form-select form-control-custom" required>
                                                        <?php
                                                        $cats = $conn->query("SELECT id, title FROM categories WHERE active='Yes'");
                                                        while($c = $cats->fetch_assoc()) {
                                                            $selected = ($c['id'] == $row['category_id']) ? 'selected' : '';
                                                            echo "<option value='{$c['id']}' $selected>{$c['title']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label text-muted small fw-bold">Active</label>
                                                    <select name="active" class="form-select form-control-custom">
                                                        <option value="Yes" <?= $row['active'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                                                        <option value="No" <?= $row['active'] == 'No' ? 'selected' : '' ?>>No</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="form-label text-muted small fw-bold">Image (Leave blank to keep current)</label>
                                                    <input type="file" name="image" class="form-control form-control-custom">
                                                    <div class="mt-2 text-muted small">Current: <?= $row['image_name'] ?></div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="form-label text-muted small fw-bold">Description</label>
                                                    <textarea name="description" class="form-control form-control-custom" rows="3" required><?= $row['description'] ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_food" class="btn btn-primary-custom px-4">Update Food Item</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No food items found.</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Food Modal -->
<div class="modal fade" id="addFoodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add Food Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Title</label>
                            <input type="text" name="title" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Price (Rs.)</label>
                            <input type="number" step="0.01" name="price" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Category</label>
                            <select name="category_id" class="form-select form-control-custom" required>
                                <?php
                                $cats = $conn->query("SELECT id, title FROM categories WHERE active='Yes'");
                                while($c = $cats->fetch_assoc()) {
                                    echo "<option value='{$c['id']}'>{$c['title']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">Active</label>
                            <select name="active" class="form-select form-control-custom">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted small fw-bold">Image</label>
                            <input type="file" name="image" class="form-control form-control-custom">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted small fw-bold">Description</label>
                            <textarea name="description" class="form-control form-control-custom" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_food" class="btn btn-primary-custom px-4">Save Food Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

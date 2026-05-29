<?php include 'includes/header.php'; ?>

<?php
// Handle Add Category
if(isset($_POST['add_category'])) {
    $title = sanitize_input($_POST['title']);
    $active = sanitize_input($_POST['active']);
    $image_name = 'placeholder.jpg'; 
    
    // Check if image uploaded
    if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = "Category_".rand(1000, 9999).'.'.$ext;
        $source_path = $_FILES['image']['tmp_name'];
        $destination_path = "../images/".$image_name;
        move_uploaded_file($source_path, $destination_path);
    }
    
    $stmt = $conn->prepare("INSERT INTO categories (title, image_name, active) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $image_name, $active);
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Category added successfully.</div>";
    }
}

// Handle Update Category
if(isset($_POST['update_category'])) {
    $id = intval($_POST['category_id']);
    $title = sanitize_input($_POST['title']);
    $active = sanitize_input($_POST['active']);
    
    // Check if new image uploaded
    if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        // Fetch old image name to delete the file from folder
        $img_stmt = $conn->prepare("SELECT image_name FROM categories WHERE id = ?");
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
        $image_name = "Category_".rand(1000, 9999).'.'.$ext;
        $source_path = $_FILES['image']['tmp_name'];
        $destination_path = "../images/".$image_name;
        move_uploaded_file($source_path, $destination_path);
        
        $stmt = $conn->prepare("UPDATE categories SET title=?, image_name=?, active=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $image_name, $active, $id);
    } else {
        $stmt = $conn->prepare("UPDATE categories SET title=?, active=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $active, $id);
    }
    
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Category updated successfully.</div>";
    }
}

// Handle Delete Category
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Fetch image name to delete file from folder
    $img_stmt = $conn->prepare("SELECT image_name FROM categories WHERE id = ?");
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
    
    // Fetch all foods belonging to this category to delete their image files from folder
    $foods_stmt = $conn->prepare("SELECT image_name FROM foods WHERE category_id = ?");
    $foods_stmt->bind_param("i", $id);
    $foods_stmt->execute();
    $foods_res = $foods_stmt->get_result();
    if($foods_res && $foods_res->num_rows > 0) {
        while($f_row = $foods_res->fetch_assoc()) {
            $f_image = $f_row['image_name'];
            if($f_image != "" && $f_image != "placeholder.jpg") {
                $f_path = "../images/".$f_image;
                if(file_exists($f_path)) {
                    unlink($f_path);
                }
            }
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        echo "<div class='alert alert-success'>Category deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to delete category.</div>";
    }
}
?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Manage Categories</h5>
        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="fa-solid fa-plus me-1"></i> Add New</button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-muted">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM categories");
                    if($res->num_rows > 0):
                        while($row = $res->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td class="fw-bold"><?= $row['title'] ?></td>
                            <td>
                                <?php if($row['active'] == 'Yes'): ?>
                                    <span class="badge bg-success bg-opacity-25 text-success px-3 rounded-pill">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-25 text-danger px-3 rounded-pill">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#updateCategoryModal<?= $row['id'] ?>">Edit</button>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this category? Related food items will also be deleted.')">Delete</a>
                            </td>
                        </tr>
                        
                        <!-- Update Category Modal -->
                        <div class="modal fade" id="updateCategoryModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0">
                                    <div class="modal-header border-bottom-0">
                                        <h5 class="modal-title fw-bold">Update Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="category_id" value="<?= $row['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Title</label>
                                                <input type="text" name="title" class="form-control form-control-custom" value="<?= $row['title'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Image (Leave blank to keep current)</label>
                                                <input type="file" name="image" class="form-control form-control-custom">
                                                <div class="mt-2 text-muted small">Current: <?= $row['image_name'] ?></div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-bold">Active</label>
                                                <select name="active" class="form-select form-control-custom">
                                                    <option value="Yes" <?= $row['active'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                                                    <option value="No" <?= $row['active'] == 'No' ? 'selected' : '' ?>>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_category" class="btn btn-primary-custom px-4">Update Category</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No categories found.</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Title</label>
                        <input type="text" name="title" class="form-control form-control-custom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Image</label>
                        <input type="file" name="image" class="form-control form-control-custom">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Active</label>
                        <select name="active" class="form-select form-control-custom">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-primary-custom px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

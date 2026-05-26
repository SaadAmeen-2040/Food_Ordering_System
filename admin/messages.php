<?php
require_once 'includes/header.php';

// Handle deletion
if(isset($_POST['delete_message'])) {
    $id = sanitize_input($_POST['message_id']);
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $success = "Message deleted successfully.";
    } else {
        $error = "Failed to delete message.";
    }
}

// Fetch messages
$result = $conn->query("SELECT * FROM contacts ORDER BY id DESC");
$messages = [];
if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-gray-800">Customer Messages</h2>
</div>

<?php if(isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-2"></i><?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Subject</th>
                        <th class="py-3" style="width: 35%;">Message</th>
                        <th class="py-3 text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($messages)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-envelope-open fs-1 mb-3 d-block"></i>
                                No messages received yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($messages as $msg): ?>
                            <tr>
                                <td class="px-4 fw-bold text-muted">#<?= $msg['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($msg['name']) ?></td>
                                <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($msg['email']) ?></a></td>
                                <td><?= htmlspecialchars($msg['subject']) ?></td>
                                <td>
                                    <div style="max-height: 100px; overflow-y: auto;" class="text-muted small">
                                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                                    </div>
                                    <?php if(isset($msg['created_at'])): ?>
                                        <div class="text-muted" style="font-size: 0.75rem; margin-top: 5px;">
                                            <i class="fa-regular fa-clock me-1"></i> <?= date('M d, Y h:i A', strtotime($msg['created_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end px-4">
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                        <button type="submit" name="delete_message" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

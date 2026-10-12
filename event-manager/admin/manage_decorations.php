<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Manage Decorations";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_decoration'])) {
        $name = $_POST['name'];
        $style = $_POST['style'];
        $description = $_POST['description'];
        $base_price = $_POST['base_price'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("INSERT INTO decorations (name, style, description, base_price, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $style, $description, $base_price, $image_url);
        $stmt->execute();
    } elseif (isset($_POST['update_decoration'])) {
        $decoration_id = $_POST['decoration_id'];
        $name = $_POST['name'];
        $style = $_POST['style'];
        $description = $_POST['description'];
        $base_price = $_POST['base_price'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("UPDATE decorations SET name=?, style=?, description=?, base_price=?, image_url=? WHERE decoration_id=?");
        $stmt->bind_param("sssssi", $name, $style, $description, $base_price, $image_url, $decoration_id);
        $stmt->execute();
    } elseif (isset($_POST['delete_decoration'])) {
        $decoration_id = $_POST['decoration_id'];
        $stmt = $conn->prepare("DELETE FROM decorations WHERE decoration_id=?");
        $stmt->bind_param("i", $decoration_id);
        $stmt->execute();
    }
}

$decorations = $conn->query("SELECT * FROM decorations ORDER BY name");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Decorations</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDecorationModal">
            <i class="bi bi-plus"></i> Add New Decoration
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Style</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($decoration = $decorations->fetch_assoc()): ?>
                <tr>
                    <td><?= $decoration['decoration_id'] ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($decoration['image_url']) ?>" width="50" height="50" class="rounded me-2">
                        <?= htmlspecialchars($decoration['name']) ?>
                    </td>
                    <td><?= htmlspecialchars($decoration['style']) ?></td>
                    <td>₹<?= number_format($decoration['base_price'], 2) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDecorationModal<?= $decoration['decoration_id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDecorationModal<?= $decoration['decoration_id'] ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Edit Decoration Modal -->
                <div class="modal fade" id="editDecorationModal<?= $decoration['decoration_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Decoration</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="decoration_id" value="<?= $decoration['decoration_id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($decoration['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Style</label>
                                        <input type="text" class="form-control" name="style" value="<?= htmlspecialchars($decoration['style']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($decoration['description']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Base Price (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="base_price" value="<?= $decoration['base_price'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Image URL</label>
                                        <input type="text" class="form-control" name="image_url" value="<?= htmlspecialchars($decoration['image_url']) ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_decoration" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Decoration Modal -->
                <div class="modal fade" id="deleteDecorationModal<?= $decoration['decoration_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="decoration_id" value="<?= $decoration['decoration_id'] ?>">
                                    <p>Are you sure you want to delete <?= htmlspecialchars($decoration['name']) ?>?</p>
                                    <p class="text-danger">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_decoration" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Decoration Modal -->
    <div class="modal fade" id="addDecorationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Decoration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Style</label>
                            <input type="text" class="form-control" name="style" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Base Price (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="base_price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" name="image_url">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_decoration" class="btn btn-primary">Add Decoration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
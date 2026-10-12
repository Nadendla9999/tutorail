<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Manage DJ Services";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_dj'])) {
        $name = $_POST['name'];
        $genre = $_POST['genre'];
        $equipment = $_POST['equipment_description'];
        $base_price = $_POST['base_price'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("INSERT INTO dj_sets (name, genre, equipment_description, base_price, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $genre, $equipment, $base_price, $image_url);
        $stmt->execute();
    } elseif (isset($_POST['update_dj'])) {
        $dj_id = $_POST['dj_id'];
        $name = $_POST['name'];
        $genre = $_POST['genre'];
        $equipment = $_POST['equipment_description'];
        $base_price = $_POST['base_price'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("UPDATE dj_sets SET name=?, genre=?, equipment_description=?, base_price=?, image_url=? WHERE dj_id=?");
        $stmt->bind_param("sssssi", $name, $genre, $equipment, $base_price, $image_url, $dj_id);
        $stmt->execute();
    } elseif (isset($_POST['delete_dj'])) {
        $dj_id = $_POST['dj_id'];
        $stmt = $conn->prepare("DELETE FROM dj_sets WHERE dj_id=?");
        $stmt->bind_param("i", $dj_id);
        $stmt->execute();
    }
}

$djs = $conn->query("SELECT * FROM dj_sets ORDER BY name");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage DJ Services</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDJModal">
            <i class="bi bi-plus"></i> Add New DJ
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DJ Name</th>
                    <th>Genre</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($dj = $djs->fetch_assoc()): ?>
                <tr>
                    <td><?= $dj['dj_id'] ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($dj['image_url']) ?>" width="50" height="50" class="rounded me-2">
                        <?= htmlspecialchars($dj['name']) ?>
                    </td>
                    <td><?= htmlspecialchars($dj['genre']) ?></td>
                    <td>₹<?= number_format($dj['base_price'], 2) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDJModal<?= $dj['dj_id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDJModal<?= $dj['dj_id'] ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Edit DJ Modal -->
                <div class="modal fade" id="editDJModal<?= $dj['dj_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit DJ Service</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="dj_id" value="<?= $dj['dj_id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">DJ Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($dj['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Genre</label>
                                        <input type="text" class="form-control" name="genre" value="<?= htmlspecialchars($dj['genre']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Equipment Description</label>
                                        <textarea class="form-control" name="equipment_description" rows="3"><?= htmlspecialchars($dj['equipment_description']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Base Price (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="base_price" value="<?= $dj['base_price'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Image URL</label>
                                        <input type="text" class="form-control" name="image_url" value="<?= htmlspecialchars($dj['image_url']) ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_dj" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete DJ Modal -->
                <div class="modal fade" id="deleteDJModal<?= $dj['dj_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="dj_id" value="<?= $dj['dj_id'] ?>">
                                    <p>Are you sure you want to delete <?= htmlspecialchars($dj['name']) ?>?</p>
                                    <p class="text-danger">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_dj" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add DJ Modal -->
    <div class="modal fade" id="addDJModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New DJ Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">DJ Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Genre</label>
                            <input type="text" class="form-control" name="genre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Equipment Description</label>
                            <textarea class="form-control" name="equipment_description" rows="3"></textarea>
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
                        <button type="submit" name="add_dj" class="btn btn-primary">Add DJ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
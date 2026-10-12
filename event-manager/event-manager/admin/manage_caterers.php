<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Manage Caterers";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_caterer'])) {
        $name = $_POST['name'];
        $cuisine_type = $_POST['cuisine_type'];
        $menu_description = $_POST['menu_description'];
        $price_per_person = $_POST['price_per_person'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("INSERT INTO caterers (name, cuisine_type, menu_description, price_per_person, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $cuisine_type, $menu_description, $price_per_person, $image_url);
        $stmt->execute();
    } elseif (isset($_POST['update_caterer'])) {
        $caterer_id = $_POST['caterer_id'];
        $name = $_POST['name'];
        $cuisine_type = $_POST['cuisine_type'];
        $menu_description = $_POST['menu_description'];
        $price_per_person = $_POST['price_per_person'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("UPDATE caterers SET name=?, cuisine_type=?, menu_description=?, price_per_person=?, image_url=? WHERE caterer_id=?");
        $stmt->bind_param("sssssi", $name, $cuisine_type, $menu_description, $price_per_person, $image_url, $caterer_id);
        $stmt->execute();
    } elseif (isset($_POST['delete_caterer'])) {
        $caterer_id = $_POST['caterer_id'];
        $stmt = $conn->prepare("DELETE FROM caterers WHERE caterer_id=?");
        $stmt->bind_param("i", $caterer_id);
        $stmt->execute();
    }
}

$caterers = $conn->query("SELECT * FROM caterers ORDER BY name");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Caterers</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCatererModal">
            <i class="bi bi-plus"></i> Add New Caterer
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Cuisine</th>
                    <th>Price/Person</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($caterer = $caterers->fetch_assoc()): ?>
                <tr>
                    <td><?= $caterer['caterer_id'] ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($caterer['image_url']) ?>" width="50" height="50" class="rounded me-2">
                        <?= htmlspecialchars($caterer['name']) ?>
                    </td>
                    <td><?= htmlspecialchars($caterer['cuisine_type']) ?></td>
                    <td>₹<?= number_format($caterer['price_per_person'], 2) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCatererModal<?= $caterer['caterer_id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCatererModal<?= $caterer['caterer_id'] ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Edit Caterer Modal -->
                <div class="modal fade" id="editCatererModal<?= $caterer['caterer_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Caterer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="caterer_id" value="<?= $caterer['caterer_id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($caterer['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Cuisine Type</label>
                                        <input type="text" class="form-control" name="cuisine_type" value="<?= htmlspecialchars($caterer['cuisine_type']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Menu Description</label>
                                        <textarea class="form-control" name="menu_description" rows="3"><?= htmlspecialchars($caterer['menu_description']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price Per Person (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="price_per_person" value="<?= $caterer['price_per_person'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Image URL</label>
                                        <input type="text" class="form-control" name="image_url" value="<?= htmlspecialchars($caterer['image_url']) ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_caterer" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Caterer Modal -->
                <div class="modal fade" id="deleteCatererModal<?= $caterer['caterer_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="caterer_id" value="<?= $caterer['caterer_id'] ?>">
                                    <p>Are you sure you want to delete <?= htmlspecialchars($caterer['name']) ?>?</p>
                                    <p class="text-danger">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_caterer" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Caterer Modal -->
    <div class="modal fade" id="addCatererModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Caterer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cuisine Type</label>
                            <input type="text" class="form-control" name="cuisine_type" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Menu Description</label>
                            <textarea class="form-control" name="menu_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price Per Person (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_person" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" name="image_url">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_caterer" class="btn btn-primary">Add Caterer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
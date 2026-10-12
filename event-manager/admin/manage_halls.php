<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Manage Function Halls";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_hall'])) {
        $name = $_POST['name'];
        $capacity = $_POST['capacity'];
        $location = $_POST['location'];
        $amenities = $_POST['amenities'];
        $base_price = $_POST['base_price'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("INSERT INTO function_halls (name, capacity, location, amenities, base_price, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $name, $capacity, $location, $amenities, $base_price, $image_url);
        $stmt->execute();
    } elseif (isset($_POST['update_hall'])) {
        $hall_id = $_POST['hall_id'];
        $name = $_POST['name'];
        $capacity = $_POST['capacity'];
        $location = $_POST['location'];
        $amenities = $_POST['amenities'];
        $base_price = $_POST['base_price'];
        $image_url = $_POST['image_url'];
        
        $stmt = $conn->prepare("UPDATE function_halls SET name=?, capacity=?, location=?, amenities=?, base_price=?, image_url=? WHERE hall_id=?");
        $stmt->bind_param("sissssi", $name, $capacity, $location, $amenities, $base_price, $image_url, $hall_id);
        $stmt->execute();
    } elseif (isset($_POST['delete_hall'])) {
        $hall_id = $_POST['hall_id'];
        $stmt = $conn->prepare("DELETE FROM function_halls WHERE hall_id=?");
        $stmt->bind_param("i", $hall_id);
        $stmt->execute();
    }
}

$halls = $conn->query("SELECT * FROM function_halls ORDER BY name");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Function Halls</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHallModal">
            <i class="bi bi-plus"></i> Add New Hall
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Capacity</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($hall = $halls->fetch_assoc()): ?>
                <tr>
                    <td><?= $hall['hall_id'] ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($hall['image_url']) ?>" width="50" height="50" class="rounded me-2">
                        <?= htmlspecialchars($hall['name']) ?>
                    </td>
                    <td><?= $hall['capacity'] ?></td>
                    <td><?= htmlspecialchars($hall['location']) ?></td>
                    <td>₹<?= number_format($hall['base_price'], 2) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editHallModal<?= $hall['hall_id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteHallModal<?= $hall['hall_id'] ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Edit Hall Modal -->
                <div class="modal fade" id="editHallModal<?= $hall['hall_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Function Hall</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="hall_id" value="<?= $hall['hall_id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($hall['name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Capacity</label>
                                        <input type="number" class="form-control" name="capacity" value="<?= $hall['capacity'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location</label>
                                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($hall['location']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Amenities</label>
                                        <textarea class="form-control" name="amenities" rows="3"><?= htmlspecialchars($hall['amenities']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Base Price (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="base_price" value="<?= $hall['base_price'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Image URL</label>
                                        <input type="text" class="form-control" name="image_url" value="<?= htmlspecialchars($hall['image_url']) ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_hall" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Hall Modal -->
                <div class="modal fade" id="deleteHallModal<?= $hall['hall_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="hall_id" value="<?= $hall['hall_id'] ?>">
                                    <p>Are you sure you want to delete <?= htmlspecialchars($hall['name']) ?>?</p>
                                    <p class="text-danger">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_hall" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Hall Modal -->
    <div class="modal fade" id="addHallModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Function Hall</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amenities</label>
                            <textarea class="form-control" name="amenities" rows="3"></textarea>
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
                        <button type="submit" name="add_hall" class="btn btn-primary">Add Hall</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
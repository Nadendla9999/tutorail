<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Manage Managers";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_manager'])) {
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, phone, role) VALUES (?, ?, ?, ?, ?, 'manager')");
        $stmt->bind_param("sssss", $username, $password, $email, $full_name, $phone);
        $stmt->execute();
    } elseif (isset($_POST['update_manager'])) {
        $user_id = $_POST['user_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, full_name=?, phone=? WHERE user_id=?");
        $stmt->bind_param("ssssi", $username, $email, $full_name, $phone, $user_id);
        $stmt->execute();
    } elseif (isset($_POST['delete_manager'])) {
        $user_id = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

$managers = $conn->query("SELECT * FROM users WHERE role='manager' ORDER BY username");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Event Managers</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addManagerModal">
            <i class="bi bi-plus"></i> Add New Manager
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($manager = $managers->fetch_assoc()): ?>
                <tr>
                    <td><?= $manager['user_id'] ?></td>
                    <td><?= htmlspecialchars($manager['username']) ?></td>
                    <td><?= htmlspecialchars($manager['full_name']) ?></td>
                    <td><?= htmlspecialchars($manager['email']) ?></td>
                    <td><?= htmlspecialchars($manager['phone']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editManagerModal<?= $manager['user_id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteManagerModal<?= $manager['user_id'] ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Edit Manager Modal -->
                <div class="modal fade" id="editManagerModal<?= $manager['user_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Manager</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?= $manager['user_id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($manager['username']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($manager['full_name']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($manager['email']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($manager['phone']) ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_manager" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Manager Modal -->
                <div class="modal fade" id="deleteManagerModal<?= $manager['user_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="user_id" value="<?= $manager['user_id'] ?>">
                                    <p>Are you sure you want to delete manager <?= htmlspecialchars($manager['username']) ?>?</p>
                                    <p class="text-danger">This will also remove all their event assignments.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="delete_manager" class="btn btn-danger">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Manager Modal -->
    <div class="modal fade" id="addManagerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_manager" class="btn btn-primary">Add Manager</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
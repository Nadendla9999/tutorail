<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Assign Events";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_manager'])) {
    $event_id = $_POST['event_id'];
    $manager_id = $_POST['manager_id'];
    
    // Update event status
    $stmt = $conn->prepare("UPDATE events SET status='assigned' WHERE event_id=?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    
    // Create assignment
    $stmt = $conn->prepare("INSERT INTO event_assignments (event_id, manager_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $event_id, $manager_id);
    $stmt->execute();
}

// Get pending events
$events = $conn->query("SELECT * FROM events WHERE status='pending' ORDER BY event_date");

// Get available managers
$managers = $conn->query("SELECT * FROM users WHERE role='manager' ORDER BY username");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Assign Events to Managers</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($events->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event ID</th>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Budget</th>
                                <th>Manager</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($event = $events->fetch_assoc()): ?>
                            <tr>
                                <td><?= $event['event_id'] ?></td>
                                <td><?= htmlspecialchars($event['event_name']) ?></td>
                                <td><?= date('M j, Y', strtotime($event['event_date'])) ?></td>
                                <td>₹<?= number_format($event['budget'], 2) ?></td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                        <select name="manager_id" class="form-select me-2" required>
                                            <option value="">Select Manager</option>
                                            <?php while ($manager = $managers->fetch_assoc()): ?>
                                                <option value="<?= $manager['user_id'] ?>"><?= htmlspecialchars($manager['username']) ?></option>
                                            <?php endwhile; 
                                            $managers->data_seek(0); // Reset pointer ?>
                                        </select>
                                        <button type="submit" name="assign_manager" class="btn btn-sm btn-primary">
                                            Assign
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="../user/view_event.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-outline-info">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    No pending events to assign!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5>Current Assignments</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Manager</th>
                            <th>Assigned On</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $assignments = $conn->query("
                            SELECT e.event_id, e.event_name, u.username, ea.assigned_at, e.status 
                            FROM event_assignments ea
                            JOIN events e ON ea.event_id = e.event_id
                            JOIN users u ON ea.manager_id = u.user_id
                            ORDER BY ea.assigned_at DESC
                        ");
                        
                        while ($assignment = $assignments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $assignment['event_id'] ?></td>
                            <td><?= htmlspecialchars($assignment['event_name']) ?></td>
                            <td><?= htmlspecialchars($assignment['username']) ?></td>
                            <td><?= date('M j, Y', strtotime($assignment['assigned_at'])) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $assignment['status'] == 'completed' ? 'success' : 'info' ?>">
                                    <?= ucfirst($assignment['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
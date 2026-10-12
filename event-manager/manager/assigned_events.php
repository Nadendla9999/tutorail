<?php
$required_role = ['manager'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Assigned Events";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$manager_id = $_SESSION['user_id'];
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Your Assigned Events</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event ID</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $events = $conn->query("
                            SELECT e.event_id, e.event_name, e.event_date, e.budget, e.status 
                            FROM events e
                            JOIN event_assignments ea ON e.event_id = ea.event_id
                            WHERE ea.manager_id = $manager_id
                            ORDER BY e.event_date DESC
                        ");
                        
                        while ($event = $events->fetch_assoc()): ?>
                        <tr>
                            <td><?= $event['event_id'] ?></td>
                            <td><?= htmlspecialchars($event['event_name']) ?></td>
                            <td><?= date('M j, Y', strtotime($event['event_date'])) ?></td>
                            <td>₹<?= number_format($event['budget'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $event['status'] == 'completed' ? 'success' : 
                                    ($event['status'] == 'assigned' ? 'info' : 'warning') ?>">
                                    <?= ucfirst($event['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="assign_resources.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <?= $event['status'] == 'assigned' ? 'Manage' : 'View' ?>
                                </a>
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
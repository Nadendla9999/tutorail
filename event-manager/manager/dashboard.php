<?php
$required_role = ['manager'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Manager Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Get manager stats
$manager_id = $_SESSION['user_id'];
$assigned_events = $conn->query("SELECT COUNT(*) FROM event_assignments WHERE manager_id = $manager_id")->fetch_row()[0];
$completed_events = $conn->query("SELECT COUNT(*) FROM events e JOIN event_assignments ea ON e.event_id = ea.event_id WHERE ea.manager_id = $manager_id AND e.status = 'completed'")->fetch_row()[0];
$pending_events = $conn->query("SELECT COUNT(*) FROM events e JOIN event_assignments ea ON e.event_id = ea.event_id WHERE ea.manager_id = $manager_id AND e.status = 'assigned'")->fetch_row()[0];

// Get average rating
$avg_rating = $conn->query("SELECT AVG(rating) FROM manager_performance WHERE manager_id = $manager_id")->fetch_row()[0];
$avg_rating = $avg_rating ? round($avg_rating, 1) : 'N/A';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="assigned_events.php" class="btn btn-sm btn-outline-secondary">View All Events</a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Assigned Events</h5>
                    <h2 class="card-text"><?= $assigned_events ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Completed Events</h5>
                    <h2 class="card-text"><?= $completed_events ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending Events</h5>
                    <h2 class="card-text"><?= $pending_events ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Recent Assigned Events</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $events = $conn->query("
                                    SELECT e.event_id, e.event_name, e.event_date, e.status 
                                    FROM events e
                                    JOIN event_assignments ea ON e.event_id = ea.event_id
                                    WHERE ea.manager_id = $manager_id
                                    ORDER BY e.event_date DESC
                                    LIMIT 5
                                ");
                                
                                while ($event = $events->fetch_assoc()): ?>
                                <tr>
                                    <td><a href="assign_resources.php?id=<?= $event['event_id'] ?>"><?= htmlspecialchars($event['event_name']) ?></a></td>
                                    <td><?= date('M j, Y', strtotime($event['event_date'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $event['status'] == 'completed' ? 'success' : 
                                            ($event['status'] == 'assigned' ? 'info' : 'warning') ?>">
                                            <?= ucfirst($event['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Your Performance</h5>
                </div>
                <div class="card-body text-center">
                    <?php if ($avg_rating != 'N/A'): ?>
                        <div class="display-4 text-warning mb-3">
                            <?= $avg_rating ?>/5
                        </div>
                        <div class="mb-3">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="bi bi-star<?= $i < floor($avg_rating) ? '-fill' : '' ?> text-warning" style="font-size: 2rem;"></i>
                            <?php endfor; ?>
                        </div>
                        <a href="performance.php" class="btn btn-outline-primary">View Details</a>
                    <?php else: ?>
                        <p class="text-muted">No ratings yet</p>
                        <p>Complete events to receive ratings</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
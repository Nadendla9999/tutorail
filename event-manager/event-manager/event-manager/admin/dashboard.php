<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Admin Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Get counts for dashboard
$events_count = $conn->query("SELECT COUNT(*) FROM events")->fetch_row()[0];
$users_count = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetch_row()[0];
$managers_count = $conn->query("SELECT COUNT(*) FROM users WHERE role='manager'")->fetch_row()[0];
$pending_events = $conn->query("SELECT COUNT(*) FROM events WHERE status='pending'")->fetch_row()[0];
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Events</h5>
                    <h2 class="card-text"><?= $events_count ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Registered Users</h5>
                    <h2 class="card-text"><?= $users_count ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Managers</h5>
                    <h2 class="card-text"><?= $managers_count ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
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
            <div class="card">
                <div class="card-header">
                    <h5>Recent Events</h5>
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
                                $result = $conn->query("SELECT event_id, event_name, event_date, status FROM events ORDER BY event_date DESC LIMIT 5");
                                while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['event_name']) ?></td>
                                    <td><?= date('M j', strtotime($row['event_date'])) ?></td>
                                    <td><span class="badge bg-<?= 
                                        $row['status'] == 'completed' ? 'success' : 
                                        ($row['status'] == 'assigned' ? 'info' : 'warning') ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span></td>
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
                <div class="card-header">
                    <h5>Manager Performance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Manager</th>
                                    <th>Events</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT u.username, COUNT(e.event_id) as events, AVG(mp.rating) as rating 
                                    FROM users u 
                                    LEFT JOIN event_assignments ea ON u.user_id = ea.manager_id 
                                    LEFT JOIN events e ON ea.event_id = e.event_id 
                                    LEFT JOIN manager_performance mp ON e.event_id = mp.event_id 
                                    WHERE u.role='manager' 
                                    GROUP BY u.user_id 
                                    ORDER BY rating DESC 
                                    LIMIT 5");
                                while ($row = $result->fetch_assoc()): 
                                    $rating = $row['rating'] ? round($row['rating'], 1) : 'N/A';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= $row['events'] ?></td>
                                    <td>
                                        <?php if ($rating != 'N/A'): ?>
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <i class="bi bi-star<?= $i < floor($rating) ? '-fill' : '' ?> text-warning"></i>
                                            <?php endfor; ?>
                                            (<?= $rating ?>)
                                        <?php else: ?>
                                            <?= $rating ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
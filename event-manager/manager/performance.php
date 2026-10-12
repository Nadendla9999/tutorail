<?php
$required_role = ['manager'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$manager_id = $_SESSION['user_id'];

// Get performance data
$performance = $conn->query("
    SELECT 
        COUNT(DISTINCT ea.event_id) as total_events,
        COUNT(DISTINCT mp.event_id) as rated_events,
        AVG(mp.rating) as avg_rating
    FROM event_assignments ea
    LEFT JOIN manager_performance mp ON ea.event_id = mp.event_id
    WHERE ea.manager_id = $manager_id
")->fetch_assoc();

// Get all ratings
$ratings = $conn->query("
    SELECT e.event_name, mp.rating, mp.feedback, mp.evaluated_at
    FROM manager_performance mp
    JOIN events e ON mp.event_id = e.event_id
    WHERE mp.manager_id = $manager_id
    ORDER BY mp.evaluated_at DESC
");

$title = "Your Performance";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Your Performance</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Events</h5>
                    <h2 class="card-text"><?= $performance['total_events'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h5 class="card-title">Rated Events</h5>
                    <h2 class="card-text"><?= $performance['rated_events'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h5 class="card-title">Average Rating</h5>
                    <h2 class="card-text">
                        <?= $performance['avg_rating'] ? round($performance['avg_rating'], 1) . '/5' : 'N/A' ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Your Ratings</h5>
        </div>
        <div class="card-body">
            <?php if ($ratings->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Rating</th>
                                <th>Feedback</th>
                                <th>Date Rated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rating = $ratings->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($rating['event_name']) ?></td>
                                <td>
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="bi bi-star<?= $i < $rating['rating'] ? '-fill' : '' ?> text-warning"></i>
                                    <?php endfor; ?>
                                </td>
                                <td><?= htmlspecialchars($rating['feedback']) ?></td>
                                <td><?= date('M j, Y', strtotime($rating['evaluated_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    You haven't received any ratings yet. Complete assigned events to receive ratings.
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
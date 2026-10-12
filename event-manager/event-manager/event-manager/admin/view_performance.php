<?php
$required_role = ['admin'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "Manager Performance";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rating'])) {
    $event_id = $_POST['event_id'];
    $manager_id = $_POST['manager_id'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];
    
    $stmt = $conn->prepare("INSERT INTO manager_performance (manager_id, event_id, rating, feedback) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $manager_id, $event_id, $rating, $feedback);
    $stmt->execute();
    
    // Update event status to completed
    $stmt = $conn->prepare("UPDATE events SET status='completed' WHERE event_id=?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
}

// Get completed events without ratings
$unrated_events = $conn->query("
    SELECT e.event_id, e.event_name, u.user_id, u.username 
    FROM event_assignments ea
    JOIN events e ON ea.event_id = e.event_id
    JOIN users u ON ea.manager_id = u.user_id
    LEFT JOIN manager_performance mp ON e.event_id = mp.event_id
    WHERE e.status='assigned' AND mp.event_id IS NULL
");

// Get manager performance data
$performance = $conn->query("
    SELECT u.user_id, u.username, 
           COUNT(DISTINCT mp.event_id) as events_completed,
           AVG(mp.rating) as avg_rating
    FROM users u
    LEFT JOIN manager_performance mp ON u.user_id = mp.manager_id
    WHERE u.role='manager'
    GROUP BY u.user_id
    ORDER BY avg_rating DESC
");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manager Performance</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Rate Completed Events</h5>
                </div>
                <div class="card-body">
                    <?php if ($unrated_events->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while ($event = $unrated_events->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <form method="POST">
                                    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                    <input type="hidden" name="manager_id" value="<?= $event['user_id'] ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0"><?= htmlspecialchars($event['event_name']) ?></h6>
                                        <span class="badge bg-info"><?= htmlspecialchars($event['username']) ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Rating (1-5)</label>
                                        <select name="rating" class="form-select" required>
                                            <option value="">Select Rating</option>
                                            <option value="1">1 - Poor</option>
                                            <option value="2">2 - Fair</option>
                                            <option value="3">3 - Good</option>
                                            <option value="4">4 - Very Good</option>
                                            <option value="5">5 - Excellent</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Feedback</label>
                                        <textarea name="feedback" class="form-control" rows="2"></textarea>
                                    </div>
                                    <button type="submit" name="submit_rating" class="btn btn-sm btn-primary">
                                        Submit Rating
                                    </button>
                                </form>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No completed events awaiting rating.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Performance Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Manager</th>
                                    <th>Events</th>
                                    <th>Avg Rating</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($manager = $performance->fetch_assoc()): 
                                    $rating = $manager['avg_rating'] ? round($manager['avg_rating'], 1) : 'N/A';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($manager['username']) ?></td>
                                    <td><?= $manager['events_completed'] ?></td>
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
                                    <td>
                                        <?php if ($rating != 'N/A'): ?>
                                            <?php if ($rating >= 4.5): ?>
                                                <span class="badge bg-success">Excellent</span>
                                            <?php elseif ($rating >= 3.5): ?>
                                                <span class="badge bg-primary">Good</span>
                                            <?php elseif ($rating >= 2.5): ?>
                                                <span class="badge bg-info">Average</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Needs Improvement</span>
                                            <?php endif; ?>
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
<?php
$required_role = ['user'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "User Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="create_event.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Event
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Events -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Upcoming Events</h5>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ? AND event_date >= CURDATE() ORDER BY event_date LIMIT 3");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if($result->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($event = $result->fetch_assoc()): ?>
                                <a href="view_event.php?id=<?= $event['event_id'] ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($event['event_name']) ?></h6>
                                        <small><?= date('M j, Y', strtotime($event['event_date'])) ?></small>
                                    </div>
                                    <small>Status: <span class="badge bg-<?= 
                                        $event['status'] == 'completed' ? 'success' : 
                                        ($event['status'] == 'assigned' ? 'info' : 'warning') ?>">
                                        <?= ucfirst($event['status']) ?>
                                    </span></small>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No upcoming events found.</p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="my_events.php" class="btn btn-sm btn-outline-primary">View All Events</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="browse_services.php?type=halls" class="card card-hover text-decoration-none">
                                <div class="card-body text-center">
                                    <i class="bi bi-building display-5 text-primary mb-2"></i>
                                    <h6>Book Venue</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="browse_services.php?type=caterers" class="card card-hover text-decoration-none">
                                <div class="card-body text-center">
                                    <i class="bi bi-egg-fried display-5 text-primary mb-2"></i>
                                    <h6>Arrange Catering</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="browse_services.php?type=decorations" class="card card-hover text-decoration-none">
                                <div class="card-body text-center">
                                    <i class="bi bi-flower1 display-5 text-primary mb-2"></i>
                                    <h6>Select Decorations</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="browse_services.php?type=djs" class="card card-hover text-decoration-none">
                                <div class="card-body text-center">
                                    <i class="bi bi-boombox display-5 text-primary mb-2"></i>
                                    <h6>Hire DJ</h6>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
<?php
$required_role = ['user'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

if (!isset($_GET['id'])) {
    header("Location: my_events.php");
    exit();
}

$event_id = (int)$_GET['id'];

// Get event details
$stmt = $conn->prepare("SELECT e.*, 
    fh.name AS hall_name, fh.image_url AS hall_image,
    c.name AS caterer_name, c.image_url AS caterer_image,
    d.name AS decoration_name, d.image_url AS decoration_image,
    dj.name AS dj_name, dj.image_url AS dj_image
    FROM events e
    LEFT JOIN event_assignments ea ON e.event_id = ea.event_id
    LEFT JOIN function_halls fh ON ea.hall_id = fh.hall_id
    LEFT JOIN caterers c ON ea.caterer_id = c.caterer_id
    LEFT JOIN decorations d ON ea.decoration_id = d.decoration_id
    LEFT JOIN dj_sets dj ON ea.dj_id = dj.dj_id
    WHERE e.event_id = ? AND e.user_id = ?");
$stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: my_events.php");
    exit();
}

$event = $result->fetch_assoc();

$title = "View Event: " . htmlspecialchars($event['event_name']);
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Event: <?= htmlspecialchars($event['event_name']) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="my_events.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to My Events
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Event Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($event['event_date'])) ?></p>
                            <p><strong>Time:</strong> <?= date('g:i A', strtotime($event['start_time'])) ?> - <?= date('g:i A', strtotime($event['end_time'])) ?></p>
                            <p><strong>Guests:</strong> <?= $event['guest_count'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Budget:</strong> ₹<?= number_format($event['budget'], 2) ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?= 
                                    $event['status'] == 'completed' ? 'success' : 
                                    ($event['status'] == 'assigned' ? 'info' : 'warning') ?>">
                                    <?= ucfirst($event['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Assigned Services</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Function Hall -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Venue</h6>
                                    <?php if ($event['hall_name']): ?>
                                        <img src="<?= htmlspecialchars($event['hall_image']) ?>" class="img-fluid rounded mb-2" style="max-height: 150px;">
                                        <p class="card-text"><?= htmlspecialchars($event['hall_name']) ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">Not assigned yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Caterer -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Catering</h6>
                                    <?php if ($event['caterer_name']): ?>
                                        <img src="<?= htmlspecialchars($event['caterer_image']) ?>" class="img-fluid rounded mb-2" style="max-height: 150px;">
                                        <p class="card-text"><?= htmlspecialchars($event['caterer_name']) ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">Not assigned yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Decoration -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Decorations</h6>
                                    <?php if ($event['decoration_name']): ?>
                                        <img src="<?= htmlspecialchars($event['decoration_image']) ?>" class="img-fluid rounded mb-2" style="max-height: 150px;">
                                        <p class="card-text"><?= htmlspecialchars($event['decoration_name']) ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">Not assigned yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- DJ -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">Entertainment</h6>
                                    <?php if ($event['dj_name']): ?>
                                        <img src="<?= htmlspecialchars($event['dj_image']) ?>" class="img-fluid rounded mb-2" style="max-height: 150px;">
                                        <p class="card-text"><?= htmlspecialchars($event['dj_name']) ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">Not assigned yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Event Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit Event Details
                        </a>
                        <a href="#" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i> Cancel Event
                        </a>
                        <?php if ($event['status'] == 'assigned'): ?>
                            <button class="btn btn-success mt-3" disabled>
                                <i class="bi bi-check-circle"></i> Manager Assigned
                            </button>
                        <?php elseif ($event['status'] == 'completed'): ?>
                            <button class="btn btn-success mt-3" disabled>
                                <i class="bi bi-check-circle"></i> Event Completed
                            </button>
                        <?php else: ?>
                            <button class="btn btn-warning mt-3" disabled>
                                <i class="bi bi-hourglass"></i> Waiting for Manager
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Manager Contact</h5>
                </div>
                <div class="card-body">
                    <?php if ($event['status'] == 'assigned' || $event['status'] == 'completed'): ?>
                        <p class="card-text">Your event manager will contact you soon.</p>
                        <p class="card-text">For urgent queries, please call:</p>
                        <p class="card-text"><i class="bi bi-telephone"></i> +91 9876543210</p>
                    <?php else: ?>
                        <p class="text-muted">A manager will be assigned to your event soon.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
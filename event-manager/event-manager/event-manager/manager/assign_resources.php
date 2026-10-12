<?php
$required_role = ['manager'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

if (!isset($_GET['id'])) {
    header("Location: assigned_events.php");
    exit();
}

$event_id = (int)$_GET['id'];
$manager_id = $_SESSION['user_id'];

// Verify the event is assigned to this manager
$check = $conn->query("SELECT 1 FROM event_assignments WHERE event_id = $event_id AND manager_id = $manager_id");
if ($check->num_rows == 0) {
    header("Location: assigned_events.php");
    exit();
}

// Get event details
$event = $conn->query("SELECT * FROM events WHERE event_id = $event_id")->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hall_id = $_POST['hall_id'] ?? null;
    $caterer_id = $_POST['caterer_id'] ?? null;
    $decoration_id = $_POST['decoration_id'] ?? null;
    $dj_id = $_POST['dj_id'] ?? null;
    
    // Calculate total cost
    $total_cost = 0;
    
    if ($hall_id) {
        $hall = $conn->query("SELECT base_price FROM function_halls WHERE hall_id = $hall_id")->fetch_assoc();
        $total_cost += $hall['base_price'];
    }
    
    if ($caterer_id) {
        $caterer = $conn->query("SELECT price_per_person FROM caterers WHERE caterer_id = $caterer_id")->fetch_assoc();
        $total_cost += ($caterer['price_per_person'] * $event['guest_count']);
    }
    
    if ($decoration_id) {
        $decoration = $conn->query("SELECT base_price FROM decorations WHERE decoration_id = $decoration_id")->fetch_assoc();
        $total_cost += $decoration['base_price'];
    }
    
    if ($dj_id) {
        $dj = $conn->query("SELECT base_price FROM dj_sets WHERE dj_id = $dj_id")->fetch_assoc();
        $total_cost += $dj['base_price'];
    }
    
    // Check if budget is sufficient
    if ($total_cost > $event['budget']) {
        $error = "Selected resources exceed the event budget by ₹" . number_format($total_cost - $event['budget'], 2);
    } else {
        // Update assignment
        $stmt = $conn->prepare("
            UPDATE event_assignments 
            SET hall_id = ?, caterer_id = ?, decoration_id = ?, dj_id = ?, final_price = ?
            WHERE event_id = ?
        ");
        $stmt->bind_param("iiiiii", $hall_id, $caterer_id, $decoration_id, $dj_id, $total_cost, $event_id);
        $stmt->execute();
        
        $success = "Resources assigned successfully! Total cost: ₹" . number_format($total_cost, 2);
    }
}

// Get current assignments
$assignment = $conn->query("
    SELECT * FROM event_assignments 
    WHERE event_id = $event_id
")->fetch_assoc();

// Get available resources
$halls = $conn->query("SELECT * FROM function_halls WHERE is_available = 1");
$caterers = $conn->query("SELECT * FROM caterers WHERE is_available = 1");
$decorations = $conn->query("SELECT * FROM decorations WHERE is_available = 1");
$djs = $conn->query("SELECT * FROM dj_sets WHERE is_available = 1");

$title = "Assign Resources - " . htmlspecialchars($event['event_name']);
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Assign Resources: <?= htmlspecialchars($event['event_name']) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="assigned_events.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Events
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Event Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Date:</strong> <?= date('F j, Y', strtotime($event['event_date'])) ?></p>
                    <p><strong>Time:</strong> <?= date('g:i A', strtotime($event['start_time'])) ?> - <?= date('g:i A', strtotime($event['end_time'])) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Guests:</strong> <?= $event['guest_count'] ?></p>
                    <p><strong>Budget:</strong> ₹<?= number_format($event['budget'], 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST">
        <div class="row">
            <!-- Function Hall Selection -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Function Hall</h5>
                    </div>
                    <div class="card-body">
                        <select name="hall_id" class="form-select mb-3">
                            <option value="">Select Function Hall</option>
                            <?php while ($hall = $halls->fetch_assoc()): ?>
                                <option value="<?= $hall['hall_id'] ?>" <?= $assignment['hall_id'] == $hall['hall_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hall['name']) ?> (₹<?= number_format($hall['base_price'], 2) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if ($halls->num_rows > 0): ?>
                            <div class="row">
                                <?php $halls->data_seek(0); // Reset pointer ?>
                                <?php while ($hall = $halls->fetch_assoc()): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card card-hover">
                                            <img src="<?= htmlspecialchars($hall['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($hall['name']) ?>">
                                            <div class="card-body">
                                                <h6><?= htmlspecialchars($hall['name']) ?></h6>
                                                <p class="small text-muted">Capacity: <?= $hall['capacity'] ?></p>
                                                <p class="small text-muted">₹<?= number_format($hall['base_price'], 2) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No function halls available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Caterer Selection -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Catering Service</h5>
                    </div>
                    <div class="card-body">
                        <select name="caterer_id" class="form-select mb-3">
                            <option value="">Select Caterer</option>
                            <?php while ($caterer = $caterers->fetch_assoc()): 
                                $total = $caterer['price_per_person'] * $event['guest_count'];
                            ?>
                                <option value="<?= $caterer['caterer_id'] ?>" <?= $assignment['caterer_id'] == $caterer['caterer_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($caterer['name']) ?> (₹<?= number_format($total, 2) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if ($caterers->num_rows > 0): ?>
                            <div class="row">
                                <?php $caterers->data_seek(0); // Reset pointer ?>
                                <?php while ($caterer = $caterers->fetch_assoc()): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card card-hover">
                                            <img src="<?= htmlspecialchars($caterer['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($caterer['name']) ?>">
                                            <div class="card-body">
                                                <h6><?= htmlspecialchars($caterer['name']) ?></h6>
                                                <p class="small text-muted"><?= htmlspecialchars($caterer['cuisine_type']) ?></p>
                                                <p class="small text-muted">₹<?= number_format($caterer['price_per_person'], 2) ?>/person</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No caterers available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Decoration Selection -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Decorations</h5>
                    </div>
                    <div class="card-body">
                        <select name="decoration_id" class="form-select mb-3">
                            <option value="">Select Decoration</option>
                            <?php while ($decoration = $decorations->fetch_assoc()): ?>
                                <option value="<?= $decoration['decoration_id'] ?>" <?= $assignment['decoration_id'] == $decoration['decoration_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($decoration['name']) ?> (₹<?= number_format($decoration['base_price'], 2) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if ($decorations->num_rows > 0): ?>
                            <div class="row">
                                <?php $decorations->data_seek(0); // Reset pointer ?>
                                <?php while ($decoration = $decorations->fetch_assoc()): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card card-hover">
                                            <img src="<?= htmlspecialchars($decoration['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($decoration['name']) ?>">
                                            <div class="card-body">
                                                <h6><?= htmlspecialchars($decoration['name']) ?></h6>
                                                <p class="small text-muted"><?= htmlspecialchars($decoration['style']) ?></p>
                                                <p class="small text-muted">₹<?= number_format($decoration['base_price'], 2) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No decorations available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- DJ Selection -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">DJ/Entertainment</h5>
                    </div>
                    <div class="card-body">
                        <select name="dj_id" class="form-select mb-3">
                            <option value="">Select DJ Service</option>
                            <?php while ($dj = $djs->fetch_assoc()): ?>
                                <option value="<?= $dj['dj_id'] ?>" <?= $assignment['dj_id'] == $dj['dj_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dj['name']) ?> (₹<?= number_format($dj['base_price'], 2) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if ($djs->num_rows > 0): ?>
                            <div class="row">
                                <?php $djs->data_seek(0); // Reset pointer ?>
                                <?php while ($dj = $djs->fetch_assoc()): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card card-hover">
                                            <img src="<?= htmlspecialchars($dj['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($dj['name']) ?>">
                                            <div class="card-body">
                                                <h6><?= htmlspecialchars($dj['name']) ?></h6>
                                                <p class="small text-muted"><?= htmlspecialchars($dj['genre']) ?></p>
                                                <p class="small text-muted">₹<?= number_format($dj['base_price'], 2) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No DJ services available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save"></i> Save Assignments
            </button>
        </div>
    </form>
</main>

<?php require_once '../includes/footer.php'; ?>
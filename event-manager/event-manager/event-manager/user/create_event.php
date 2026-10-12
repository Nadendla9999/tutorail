<?php
$required_role = ['user'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $guest_count = (int)$_POST['guest_count'];
    $budget = (float)$_POST['budget'];
    $hall_id = $_POST['hall_id'] ?? null;
    $caterer_id = $_POST['caterer_id'] ?? null;
    $decoration_id = $_POST['decoration_id'] ?? null;
    $dj_id = $_POST['dj_id'] ?? null;

    // Validate inputs
    if (empty($event_name)) $errors[] = "Event name is required";
    if (empty($event_date)) $errors[] = "Event date is required";
    if (empty($start_time)) $errors[] = "Start time is required";
    if (empty($end_time)) $errors[] = "End time is required";
    if ($guest_count < 1) $errors[] = "Guest count must be at least 1";
    if ($budget < 1000) $errors[] = "Budget must be at least ₹1000";

    if (empty($errors)) {
        // Insert event
        $stmt = $conn->prepare("INSERT INTO events (user_id, event_name, event_date, start_time, end_time, guest_count, budget, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("issssid", $_SESSION['user_id'], $event_name, $event_date, $start_time, $end_time, $guest_count, $budget);
        
        if ($stmt->execute()) {
            $event_id = $stmt->insert_id;
            $success = true;
            
            // Redirect to view event page
            header("Location: view_event.php?id=$event_id");
            exit();
        } else {
            $errors[] = "Failed to create event. Please try again.";
        }
    }
}

$title = "Create New Event";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Create New Event</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="my_events.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to My Events
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="create_event.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="event_name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="event_date" class="form-label">Event Date</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="guest_count" class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" id="guest_count" name="guest_count" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="budget" name="budget" min="1000" step="500" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-calendar-plus"></i> Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
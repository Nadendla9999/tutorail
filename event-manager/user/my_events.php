<?php
$required_role = ['user'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$title = "My Events";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Events</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="create_event.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Event
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY event_date DESC");
                        $stmt->bind_param("i", $_SESSION['user_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0):
                            while ($event = $result->fetch_assoc()):
                                $start_time = date("g:i A", strtotime($event['start_time']));
                                $end_time = date("g:i A", strtotime($event['end_time']));
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($event['event_name']) ?></td>
                            <td><?= date('M j, Y', strtotime($event['event_date'])) ?></td>
                            <td><?= "$start_time - $end_time" ?></td>
                            <td><?= $event['guest_count'] ?></td>
                            <td>₹<?= number_format($event['budget'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $event['status'] == 'completed' ? 'success' : 
                                    ($event['status'] == 'assigned' ? 'info' : 'warning') ?>">
                                    <?= ucfirst($event['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_event.php?id=<?= $event['event_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">You haven't created any events yet.</div>
                                <a href="create_event.php" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle"></i> Create Your First Event
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
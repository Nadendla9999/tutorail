<?php
$required_role = ['user'];
require_once '../includes/auth.php';
require_once '../includes/db_connection.php';

$type = $_GET['type'] ?? 'halls';
$valid_types = ['halls', 'caterers', 'decorations', 'djs'];
if (!in_array($type, $valid_types)) {
    header("Location: browse_services.php?type=halls");
    exit();
}

$title = "Browse " . ucfirst($type);
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Browse <?= ucfirst($type) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <?php foreach ($valid_types as $t): ?>
                    <a href="?type=<?= $t ?>" class="btn btn-sm btn-<?= $type == $t ? 'primary' : 'outline-secondary' ?>">
                        <?= ucfirst($t) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <?php
        $table = '';
        $icon = '';
        $id_key = '';
        $desc_key = '';
        $label_key = '';
        $price_key = '';

        switch ($type) {
            case 'halls':
                $table = 'function_halls';
                $icon = 'building';
                $id_key = 'hall_id';
                $desc_key = 'amenities';
                $label_key = 'capacity';
                $price_key = 'base_price';
                break;
            case 'caterers':
                $table = 'caterers';
                $icon = 'egg-fried';
                $id_key = 'caterer_id';
                $desc_key = 'menu_description';
                $label_key = 'cuisine_type';
                $price_key = 'price_per_person';
                break;
            case 'decorations':
                $table = 'decorations';
                $icon = 'flower1';
                $id_key = 'decoration_id';
                $desc_key = 'description';
                $label_key = 'style';
                $price_key = 'base_price';
                break;
            case 'djs':
                $table = 'dj_sets';
                $icon = 'boombox';
                $id_key = 'dj_id';
                $desc_key = 'equipment_description';
                $label_key = 'genre';
                $price_key = 'base_price';
                break;
        }

        $query = "SELECT * FROM $table WHERE is_available = 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0):
            while ($item = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                                <span class="badge bg-success">Available</span>
                            </div>
                            <p class="card-text">
                                <i class="bi bi-<?= $icon ?> text-primary"></i>
                                <?= ucfirst($label_key) ?>: <?= htmlspecialchars($item[$label_key]) ?>
                            </p>
                            <p class="card-text text-truncate"><?= htmlspecialchars($item[$desc_key]) ?></p>
                            <h6 class="text-primary">₹<?= number_format($item[$price_key], 2) ?></h6>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="create_event.php?<?= $type ?>=<?= $item[$id_key] ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Add to Event
                            </a>
                            <button class="btn btn-outline-secondary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $item[$id_key] ?>">
                                Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="detailsModal<?= $item[$id_key] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($item['name']) ?> Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" class="img-fluid rounded mb-3" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Description:</h6>
                                        <p><?= htmlspecialchars($item[$desc_key]) ?></p>
                                        <h6>Pricing:</h6>
                                        <p>₹<?= number_format($item[$price_key], 2) ?></p>
                                        <?php if ($type == 'halls'): ?>
                                            <h6>Capacity:</h6>
                                            <p><?= $item['capacity'] ?> people</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <a href="create_event.php?<?= $type ?>=<?= $item[$id_key] ?>" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add to Event
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile;
        else: ?>
            <div class="col-12">
                <div class="alert alert-info">No <?= $type ?> available at the moment.</div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

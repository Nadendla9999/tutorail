<?php
/**
 * Event Manager Application - Utility Functions
 */

/**
 * Redirect to a specified URL
 * @param string $url The URL to redirect to
 * @param int $statusCode HTTP status code (default: 303)
 */
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}

/**
 * Sanitize input data
 * @param string $data The data to sanitize
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format date for display
 * @param string $date Date string
 * @param string $format Output format (default: 'F j, Y')
 * @return string Formatted date
 */
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Format time for display
 * @param string $time Time string
 * @param string $format Output format (default: 'g:i A')
 * @return string Formatted time
 */
function format_time($time, $format = 'g:i A') {
    return date($format, strtotime($time));
}

/**
 * Format currency (Indian Rupees)
 * @param float $amount The amount to format
 * @return string Formatted currency string
 */
function format_currency($amount) {
    return '₹' . number_format($amount, 2);
}

/**
 * Check if a date is available for booking
 * @param mysqli $conn Database connection
 * @param string $table Resource table ('function_halls', 'caterers', etc.)
 * @param int $resource_id The ID of the resource
 * @param string $date Event date (Y-m-d)
 * @param string $start_time Event start time (H:i:s)
 * @param string $end_time Event end time (H:i:s)
 * @return bool True if available, false if booked
 */
function is_resource_available($conn, $table, $resource_id, $date, $start_time, $end_time) {
    $stmt = $conn->prepare("
        SELECT 1 FROM availability 
        WHERE {$table}_id = ? 
        AND date = ? 
        AND (
            (? BETWEEN start_time AND end_time)
            OR (? BETWEEN start_time AND end_time)
        AND is_booked = 1
    ");
    $stmt->bind_param("isss", $resource_id, $date, $start_time, $end_time);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0;
}

/**
 * Calculate total event cost based on selected resources
 * @param mysqli $conn Database connection
 * @param array $resources Array of resource IDs ['hall_id' => x, 'caterer_id' => y, ...]
 * @param int $guest_count Number of guests
 * @return float Total cost
 */
function calculate_event_cost($conn, $resources, $guest_count) {
    $total = 0;
    
    if (!empty($resources['hall_id'])) {
        $stmt = $conn->prepare("SELECT base_price FROM function_halls WHERE hall_id = ?");
        $stmt->bind_param("i", $resources['hall_id']);
        $stmt->execute();
        $total += $stmt->get_result()->fetch_assoc()['base_price'];
    }
    
    if (!empty($resources['caterer_id'])) {
        $stmt = $conn->prepare("SELECT price_per_person FROM caterers WHERE caterer_id = ?");
        $stmt->bind_param("i", $resources['caterer_id']);
        $stmt->execute();
        $price = $stmt->get_result()->fetch_assoc()['price_per_person'];
        $total += $price * $guest_count;
    }
    
    if (!empty($resources['decoration_id'])) {
        $stmt = $conn->prepare("SELECT base_price FROM decorations WHERE decoration_id = ?");
        $stmt->bind_param("i", $resources['decoration_id']);
        $stmt->execute();
        $total += $stmt->get_result()->fetch_assoc()['base_price'];
    }
    
    if (!empty($resources['dj_id'])) {
        $stmt = $conn->prepare("SELECT base_price FROM dj_sets WHERE dj_id = ?");
        $stmt->bind_param("i", $resources['dj_id']);
        $stmt->execute();
        $total += $stmt->get_result()->fetch_assoc()['base_price'];
    }
    
    return $total;
}

/**
 * Generate star rating HTML
 * @param float $rating The rating (0-5)
 * @param int $max Maximum rating (default: 5)
 * @return string HTML for star rating display
 */
function generate_star_rating($rating, $max = 5) {
    $html = '';
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    
    for ($i = 1; $i <= $max; $i++) {
        if ($i <= $fullStars) {
            $html .= '<i class="bi bi-star-fill text-warning"></i>';
        } elseif ($i == $fullStars + 1 && $hasHalfStar) {
            $html .= '<i class="bi bi-star-half text-warning"></i>';
        } else {
            $html .= '<i class="bi bi-star text-warning"></i>';
        }
    }
    
    return $html;
}

/**
 * Get status badge HTML
 * @param string $status The status value
 * @return string HTML badge
 */
function get_status_badge($status) {
    $classes = [
        'pending' => 'bg-warning',
        'assigned' => 'bg-info',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];
    
    $class = $classes[strtolower($status)] ?? 'bg-secondary';
    return '<span class="badge ' . $class . '">' . ucfirst($status) . '</span>';
}

/**
 * Check if user has permission to access a resource
 * @param string $required_role Required role ('admin', 'manager', 'user')
 * @param string $user_role Current user's role
 * @return bool True if authorized, false otherwise
 */
function has_permission($required_role, $user_role) {
    $hierarchy = ['user' => 1, 'manager' => 2, 'admin' => 3];
    return $hierarchy[$user_role] >= $hierarchy[$required_role];
}

/**
 * Log activity to database
 * @param mysqli $conn Database connection
 * @param int $user_id User ID performing the action
 * @param string $action Description of the action
 * @param string $entity_type Type of entity affected
 * @param int $entity_id ID of entity affected
 */
function log_activity($conn, $user_id, $action, $entity_type = null, $entity_id = null) {
    $stmt = $conn->prepare("
        INSERT INTO activity_logs 
        (user_id, action, entity_type, entity_id) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("issi", $user_id, $action, $entity_type, $entity_id);
    $stmt->execute();
}

/**
 * Get notification count for user
 * @param mysqli $conn Database connection
 * @param int $user_id User ID
 * @return int Number of unread notifications
 */
function get_unread_notification_count($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM notifications 
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_row()[0];
}
<?php
session_start();
require_once 'db.php';

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Sanitize input
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

// Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Display flash message
function flashMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        echo '<div class="alert alert-' . $type . '">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Set flash message
function setFlash($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Upload image
function uploadImage($file, $target_dir = "uploads/") {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== 0) {
        return ['success' => false, 'message' => 'Error uploading file.'];
    }

    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Only JPG, PNG, and GIF files are allowed.'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size must be less than 5MB.'];
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $target = $target_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'message' => 'Failed to move uploaded file.'];
}

// Generate mock image placeholder
function getProductImage($image) {
    if (!empty($image) && file_exists("uploads/" . $image)) {
        return "uploads/" . $image;
    }
    // Generate a colored placeholder based on product name hash
    return "assets/images/placeholder.jpg";
}
?>
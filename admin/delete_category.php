<?php
declare(strict_types=1);
session_start();

// Enhanced security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/config.php';
require_once '../classes/Category.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if ($id > 0) {
        // Attempt deletion. Category::delete should return true on success or false on failure.
        if (Category::delete($id)) {
            $_SESSION['message'] = "Category deleted successfully! 🎉";
        } else {
            $_SESSION['message'] = "Failed to delete category. It might be in use.";
        }
    } else {
        $_SESSION['message'] = "Invalid category ID.";
    }
}

header("Location: categories.php");
exit();
?>

<?php
// admin/delete_product.php
require_once '../includes/config.php';
require_once '../classes/Product.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }
    
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        throw new Exception("Invalid product ID");
    }

    // Add CSRF protection here
    Product::delete((int)$_POST['id']);
    $_SESSION['message'] = "Product deleted successfully";
    $_SESSION['message_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['message'] = "Error deleting product: " . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
}

header("Location: products.php");
exit;
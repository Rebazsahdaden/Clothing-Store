<?php
// users/order_product.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Product.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

// Validate input
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
if (!$product_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get product details
    $product = Product::getById($product_id);
    if (!$product) {
        throw new Exception("Product not found");
    }
    
    // Check available stock
    if ($product['stock'] <= 0) {
        throw new Exception("Product is out of stock");
    }
    
    // Start transaction
    $db->beginTransaction();

    // Create order
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_price, order_date, status) 
                         VALUES (:user_id, :total_price, NOW(), 'pending')");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':total_price' => $product['price']
    ]);
    $order_id = $db->lastInsertId();

    // Create order item
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price)
                         VALUES (:order_id, :product_id, 1, :price)");
    $stmt->execute([
        ':order_id' => $order_id,
        ':product_id' => $product_id,
        ':price' => $product['price']
    ]);
    
    // Decrement stock by 1
    $stmt = $db->prepare("UPDATE products SET stock = stock - 1 WHERE id = :product_id AND stock > 0");
    $stmt->execute([':product_id' => $product_id]);
    
    if ($stmt->rowCount() === 0) {
        // If no rows were updated, it means there was an issue with updating the stock (e.g., race condition)
        throw new Exception("Failed to update product stock.");
    }

    // Commit transaction
    $db->commit();

    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully']);

} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

<?php
class Order {
    private $db;
    
    public static function getUserOrders($userId) {
        global $pdo;
        
        $stmt = $pdo->prepare("
            SELECT o.id, o.order_date, o.total_amount, 
                   oi.product_id, oi.quantity, oi.price,
                   p.name, p.image
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE o.user_id = ?
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$userId]);
        
        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $orderId = $row['id'];
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'id' => $orderId,
                    'order_date' => $row['order_date'],
                    'total_amount' => $row['total_amount'],
                    'items' => []
                ];
            }
            $orders[$orderId]['items'][] = [
                'product_id' => $row['product_id'],
                'name' => $row['name'],
                'image' => $row['image'],
                'quantity' => $row['quantity'],
                'price' => $row['price']
            ];
        }
        return array_values($orders);
    }

    public static function cancelOrder($orderId, $userId) {
        global $pdo;
        try {
            $pdo->beginTransaction();
            
            // Verify order ownership
            $stmt = $pdo->prepare("
                SELECT id FROM orders 
                WHERE id = ? AND user_id = ?
                FOR UPDATE
            ");
            $stmt->execute([$orderId, $userId]);
            
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdo->rollBack();
                return false;
            }
            
            // Update status
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET status = 'cancelled' 
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);
            
            $pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error cancelling order: " . $e->getMessage());
            return false;
        }
    }

    public static function getAllOrders() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
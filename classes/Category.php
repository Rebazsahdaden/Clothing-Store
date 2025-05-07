<?php

// classes/Category.php
$message = $_SESSION['message'] ?? '';
$messageType = strpos($message, 'failed') !== false ? 'error' : 'success';
unset($_SESSION['message']);
class Category {
    // Get all categories
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Create a new category
    public static function create($name) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        return $stmt->execute(['name' => $name]);
    }

    public static function update($id, $name) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }
    
    public static function getById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete(int $id): bool {
        global $pdo; // Assuming you're using a global $pdo instance
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

}
?>

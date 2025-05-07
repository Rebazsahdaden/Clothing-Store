<?php
// classes/Product.php

class Product {
    // Get all products with their category name using a left join
    public static function getAll() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// classes/Product.php (partial implementation)
public static function getFilteredProducts($filters) {
    global $pdo; // Ensure $pdo is available
    
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];
    $types = '';

    // Search filter
    if (!empty($filters['search'])) {
        $sql .= " AND (name LIKE ? OR description LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        array_push($params, $searchTerm, $searchTerm);
        $types .= 'ss';
    }

    // Category filter
    if (!empty($filters['category'])) {
        $sql .= " AND category_id = ?";
        $params[] = $filters['category'];
        $types .= 'i';
    }

    // Price filters
    if ($filters['min_price'] !== null) {
        $sql .= " AND price >= ?";
        $params[] = $filters['min_price'];
        $types .= 'd';
    }
    if ($filters['max_price'] !== null) {
        $sql .= " AND price <= ?";
        $params[] = $filters['max_price'];
        $types .= 'd';
    }

    // Sorting
    switch ($filters['sort']) {
        case 'price_asc': $sql .= " ORDER BY price ASC"; break;
        case 'price_desc': $sql .= " ORDER BY price DESC"; break;
        case 'popular': $sql .= " ORDER BY sold_count DESC"; break;
        default: $sql .= " ORDER BY created_at DESC"; break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    public static function getCategories() {
        global $pdo;
        
        try {
            $stmt = $pdo->query("SELECT id, name FROM categories");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Category error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get a single product by IDpublic static function getById(int $id): array {
    public static function getById(int $id): array {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product ?: [];
}
    
    
    // Create a new product
    public static function create($name, $price, $stock, $description, $category_id, $image = '') {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, description, category_id, image) VALUES (:name, :price, :stock, :description, :category_id, :image)");
        return $stmt->execute([
            'name'        => $name,
            'price'       => $price,
            'stock'       => $stock,
            'description' => $description,
            'category_id' => $category_id ?: null,
            'image'       => $image
        ]);
    }
    
    // Update an existing product
    public static function update($id, $name, $price, $stock, $description, $category_id, $image = '') {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, stock = :stock, description = :description, category_id = :category_id, image = :image WHERE id = :id");
        return $stmt->execute([
            'name'        => $name,
            'price'       => $price,
            'stock'       => $stock,
            'description' => $description,
            'category_id' => $category_id ?: null,
            'image'       => $image,
            'id'          => $id
        ]);
    }
    
    // Optionally, add a delete method
    public static function delete(int $id): bool {
        global $pdo;
        
        try {
            $pdo->beginTransaction();

            // First delete related order items
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
            $stmt->execute([$id]);

            // Then delete the product
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Delete Product Error: " . $e->getMessage());
            throw new Exception("Could not delete product. It might be referenced in existing orders.");
        }

    }
}

?>

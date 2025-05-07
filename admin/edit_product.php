<?php
// admin/edit_product.php

// Include config, header, and required classes
require_once '../includes/config.php';
require_once '../admin/header.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';
require_once '../classes/Database.php';



if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product = Product::getById($_GET['id']);
if (!$product) {
    echo "<p>Product not found.</p>";
    require_once '../includes/footer.php';
    exit;
}

$message = '';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $price       = trim($_POST['price']);
    $stock       = trim($_POST['stock']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category_id'];

    // Handle image upload if a new file is provided
    $image = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        $target = "../uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // Update product data
    if (Product::update($_GET['id'], $name, $price, $stock, $description, $category_id, $image)) {
        $message = "Product updated successfully!";
        // Refresh product details
        $product = Product::getById($_GET['id']);
    } else {
        $message = "Failed to update product.";
    }
}

$categories = Category::getAll();
?>

<!-- Inline CSS styling -->
<style>
  .container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 1.5rem;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: Arial, sans-serif;
  }
  h1 {
    color: #333;
    margin-bottom: 1rem;
  }
  .message {
    padding: 0.5rem 1rem;
    border: 1px solid #b2d8b2;
    background: #e0ffe0;
    color: #2d662d;
    border-radius: 4px;
    margin-bottom: 1rem;
  }
  form div {
    margin-bottom: 1rem;
  }
  label {
    display: block;
    font-weight: bold;
    margin-bottom: 0.3rem;
  }
  input[type="text"],
  input[type="number"],
  textarea,
  select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  input[type="submit"] {
    padding: 0.5rem 1rem;
    background-color: #007BFF;
    border: none;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  input[type="submit"]:hover {
    background-color: #0056b3;
  }
  img {
    margin-top: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
</style>

<div class="container">
  <h1>Edit Product</h1>
  
  <!-- Display a success or error message -->
  <?php if (!empty($message)): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  
  <form method="POST" action="edit_product.php?id=<?php echo $product['id']; ?>" enctype="multipart/form-data">
    <div>
      <label for="name">Product Name:</label>
      <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
    </div>
    
    <div>
      <label for="price">Price:</label>
      <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
    </div>
    
    <div>
      <label for="stock">Stock:</label>
      <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
    </div>
    
    <div>
      <label for="category_id">Category:</label>
      <select name="category_id" id="category_id" required>
        <option value="">Select Category</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($cat['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div>
      <label for="description">Description:</label>
      <textarea name="description" id="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>
    
    <div>
      <?php if (!empty($product['image'])): ?>
        <p>Current Image:</p>
        <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" width="150" alt="<?php echo htmlspecialchars($product['name']); ?>">
      <?php endif; ?>
      <label for="image">Change Image:</label>
      <input type="file" name="image" id="image">
    </div>
    
    <div>
      <input type="submit" value="Update Product">
    </div>
  </form>
</div>


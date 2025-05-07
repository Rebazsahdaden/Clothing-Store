<?php
// admin/add_product.php
require_once '../includes/config.php';
require_once '../admin/header.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name        = trim($_POST['name']);
        $price       = trim($_POST['price']);
        $stock       = trim($_POST['stock']);
        $description = trim($_POST['description']);
        $category_id = $_POST['category_id'];
        $image       = '';

        // Validate required fields
        if (empty($name) || empty($price) || empty($stock)) {
            throw new Exception("All fields are required");
        }

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPG, PNG, and WEBP are allowed.");
            }

            $image = time() . '_' . basename($_FILES['image']['name']);
            $target = "../uploads/" . $image;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                throw new Exception("Failed to upload image");
            }
        }

        if (Product::create($name, $price, $stock, $description, $category_id, $image)) {
            $message = "Product added successfully!";
            $messageType = 'success';
        } else {
            throw new Exception("Failed to add product");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

$categories = Category::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Product - Admin</title>
  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --surface: #ffffff;
      --background: #f8fafc;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --success: #22c55e;
      --error: #ef4444;
      --border: #e2e8f0;
      --shadow: 0 1px 3px rgba(0,0,0,0.1);
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--background);
      color: var(--text-primary);
    }

    .admin-container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 2rem;
      background: var(--surface);
      border-radius: 1rem;
      box-shadow: var(--shadow);
    }

    .form-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid var(--border);
    }

    h1 {
      font-size: 1.875rem;
      margin: 0;
    }

    .alert {
      padding: 1rem;
      border-radius: 0.75rem;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .alert-success {
      background: #f0fdf4;
      color: #166534;
      border: 1px solid #86efac;
    }

    .alert-error {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fca5a5;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.5rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    label {
      font-weight: 500;
      color: var(--text-primary);
    }

    input, textarea, select {
      padding: 0.875rem;
      border: 1px solid var(--border);
      border-radius: 0.75rem;
      font-size: 1rem;
      transition: var(--transition);
    }

    input:focus, textarea:focus, select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .file-upload {
      position: relative;
      border: 2px dashed var(--border);
      border-radius: 0.75rem;
      padding: 2rem;
      text-align: center;
      transition: var(--transition);
    }

    .file-upload:hover {
      border-color: var(--primary);
      background: rgba(99, 102, 241, 0.05);
    }

    .file-input {
      position: absolute;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
      top: 0;
      left: 0;
    }

    .submit-btn {
      background: var(--primary);
      color: white;
      border: none;
      padding: 1rem 2rem;
      border-radius: 0.75rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .submit-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
    }

    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .admin-container {
        margin: 1rem;
        padding: 1.5rem;
      }
      
      h1 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <div class="form-header">
      <h1>Add New Product</h1>
      <a href="products.php" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
      </a>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <?php if ($messageType === 'success'): ?>
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          <?php else: ?>
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          <?php endif; ?>
        </svg>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="add_product.php" enctype="multipart/form-data">
      <div class="form-grid">
        <div class="form-group">
          <label for="name">Product Name</label>
          <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
          <label for="price">Price</label>
          <input type="number" step="0.01" name="price" id="price" required>
        </div>

        <div class="form-group">
          <label for="stock">Stock Quantity</label>
          <input type="number" name="stock" id="stock" required>
        </div>

        <div class="form-group">
          <label for="category_id">Category</label>
          <select name="category_id" id="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group full-width">
          <label for="description">Description</label>
          <textarea name="description" id="description" rows="4" required></textarea>
        </div>

        <div class="form-group full-width">
          <div class="file-upload">
            <input type="file" name="image" id="image" class="file-input">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="17 8 12 3 7 8"></polyline>
              <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            <p>Click to upload product image</p>
          </div>
        </div>
      </div>

      <button type="submit" class="submit-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 5v14M5 12h14"/>
        </svg>
        Add Product
      </button>
    </form>
  </div>
  </body>
</html>
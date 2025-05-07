<?php
// admin/products.php
require_once '../includes/config.php';
require_once '../admin/header.php';
require_once '../classes/Product.php';
require_once '../classes/Database.php';

$products = Product::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products - Clothing Store Admin</title>
  <style>
    :root {
      --primary: #6366f1;
      --primary-hover: #4f46e5;
      --danger: #ef4444;
      --surface: #ffffff;
      --background: #f8fafc;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --border: #e2e8f0;
      --shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--background);
      color: var(--text-primary);
    }

    .container {
      max-width: 1400px;
      margin: 2rem auto;
      padding: 2rem;
      background: var(--surface);
      border-radius: 1rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .header {
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
      color: var(--text-primary);
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: 0.75rem;
      font-weight: 500;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
      border: 1px solid var(--primary);
    }

    .btn-primary:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
    }

    .product-table {
      width: 100%;
      border-collapse: collapse;
      background: var(--surface);
      border-radius: 0.75rem;
      overflow: hidden;
    }

    .product-table thead {
      background: var(--background);
      border-bottom: 2px solid var(--border);
    }

    .product-table th,
    .product-table td {
      padding: 1rem 1.5rem;
      text-align: left;
    }

    .product-table tr:nth-child(even) {
      background: var(--background);
    }

    .product-table tr:hover {
      background: #f1f5f9;
    }

    .stock-indicator {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.875rem;
    }

    .stock-low { background: #fef2f2; color: #dc2626; }
    .stock-medium { background: #fffbeb; color: #d97706; }
    .stock-high { background: #ecfdf5; color: #059669; }

    .actions-cell {
      display: flex;
      gap: 0.75rem;
    }

    .action-link {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.5rem;
      border-radius: 0.5rem;
      transition: all 0.2s;
      text-decoration: none;
    }

    .edit-link {
      color: var(--primary);
      background: rgba(99, 102, 241, 0.1);
    }

    .edit-link:hover {
      background: rgba(99, 102, 241, 0.2);
    }

    .delete-btn {
      border: none;
      cursor: pointer;
      background: rgba(239, 68, 68, 0.1);
      color: var(--danger);
    }

    .delete-btn:hover {
      background: rgba(239, 68, 68, 0.2);
    }

    @media (max-width: 768px) {
      .container {
        padding: 1rem;
        margin: 1rem;
      }
      
      .product-table, 
      .product-table thead, 
      .product-table tbody, 
      .product-table th, 
      .product-table td, 
      .product-table tr { 
        display: block; 
      }
      
      .product-table thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
      }
      
      .product-table tr {
        padding: 1rem;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
      }
      
      .product-table td {
        padding-left: 50%;
        position: relative;
        border: none;
      }
      
      .product-table td:before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        width: calc(50% - 1rem);
        font-weight: 600;
        color: var(--text-primary);
      }
      
      .actions-cell {
        justify-content: flex-end;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Product Management</h1>
      <a href="add_product.php" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        New Product
      </a>
    </div>

    <?php if ($products && count($products)): ?>
      <table class="product-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product): 
            $stockClass = '';
            if ($product['stock'] == 0) {
                $stockClass = 'stock-low';
                $stockText = 'Out of Stock';
            } elseif ($product['stock'] <= 10) {
                $stockClass = 'stock-low';
                $stockText = 'Low Stock';
            } elseif ($product['stock'] <= 20) {
                $stockClass = 'stock-medium';
                $stockText = 'Medium Stock';
            } else {
                $stockClass = 'stock-high';
                $stockText = 'In Stock';
            }
          ?>
          <tr>
            <td data-label="Product">
              <div class="font-medium"><?= htmlspecialchars($product['name']) ?></div>
              <div class="text-sm text-secondary">ID: <?= htmlspecialchars($product['id']) ?></div>
            </td>
            <td data-label="Price">$<?= number_format($product['price'], 2) ?></td>
            <td data-label="Stock">
              <div class="stock-indicator <?= $stockClass ?>">
                <span><?= $stockText ?></span>
                <span>(<?= $product['stock'] ?>)</span>
              </div>
            </td>
            <td data-label="Category"><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
            <td data-label="Actions">
              <div class="actions-cell">
                <a href="edit_product.php?id=<?= urlencode($product['id']) ?>" class="action-link edit-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                  </svg>
                  Edit
                </a>
                <form method="POST" action="delete_product.php" onsubmit="return confirm('Are you sure you want to delete this product?')">
                  <input type="hidden" name="id" value="<?= $product['id'] ?>">
                  <button type="submit" class="action-link delete-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <h3>No Products Found</h3>
        <p>Get started by adding a new product</p>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
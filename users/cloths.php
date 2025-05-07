<?php
// users/cloths.php
session_start();

// At the top of users/cloths.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../classes/Product.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
$category = isset($_GET['category']) ? intval($_GET['category']) : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Get filtered products
$products = Product::getFilteredProducts([
    'search' => $search,
    'min_price' => $min_price,
    'max_price' => $max_price,
    'category' => $category,
    'sort' => $sort
]);

// Get available categories
$categories = Product::getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>UrbanThreads | Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-X+..." crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f2f2f2;
      color: #333;
    }

    .hero {
      background: linear-gradient(to right, #111, #333);
      color: white;
      padding: 4rem 2rem;
      text-align: center;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 0.5rem;
    }

    .hero p {
      font-size: 1.2rem;
      opacity: 0.85;
    }

    .container {
      width: 95%;
      max-width: 1200px;
      margin: 2rem auto;
    }

    .collection-heading {
      text-align: center;
      font-size: 1.8rem;
      font-weight: 600;
      margin-bottom: 2rem;
      color: #222;
    }

    .products-grid {
      display: grid;
      gap: 2rem;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    .product-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 20px rgba(0,0,0,0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .product-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 32px rgba(0,0,0,0.12);
    }

    .product-image {
      position: relative;
      height: 280px;
      overflow: hidden;
      background: #f0f0f0;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .product-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background: crimson;
      color: white;
      padding: 0.3rem 0.6rem;
      font-size: 0.75rem;
      border-radius: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .product-info {
      padding: 1.2rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .product-info h3 {
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
      color: #111;
    }

    .product-price {
      font-weight: 600;
      color: #e74c3c;
      margin-bottom: 0.5rem;
    }

    .product-description {
      font-size: 0.9rem;
      color: #555;
      margin-bottom: auto;
    }

    .product-actions {
      display: flex;
      justify-content: space-between;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .product-actions button {
      flex: 1;
      padding: 0.6rem 0.8rem;
      border: none;
      border-radius: 6px;
      font-size: 0.9rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      transition: background 0.3s ease;
    }

    .quick-view-btn {
      background: #ddd;
      color: #333;
    }

    .quick-view-btn:hover {
      background: #ccc;
    }

    .add-to-cart-btn {
      background: #111;
      color: white;
    }

    .add-to-cart-btn:hover {
      background: #000;
    }

    .no-products {
      text-align: center;
      color: #777;
      font-size: 1.2rem;
      margin-top: 2rem;
    }
    /* Add to existing styles */
.product-card.ordered {
    animation: pulseSuccess 1.5s ease;
}
.filters-container {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }

    .filter-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .filter-input {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
    }

    .filter-button {
        background: #111;
        color: white;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .filter-button:hover {
        background: #000;
    }

    .reset-button {
        background: #e74c3c;
        margin-left: 1rem;
    }

    .sort-select {
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        width: 100%;
    }

@keyframes pulseSuccess {
    0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.5); }
    70% { box-shadow: 0 0 0 15px rgba(46, 204, 113, 0); }
    100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
}

.cart-count {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #e74c3c;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
  </style>
</head>
<body>

<div class="hero">
  <h1>Welcome to ClothHub</h1>
  <p>Hello, <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?>!</p>
</div>
<div class="container">
  <div class="filters-container">
    <form method="GET" action="cloths.php">
      <div class="filter-group">
        <input type="text" 
               name="search" 
               class="filter-input"
               placeholder="Search products..."
               value="<?= htmlspecialchars($search) ?>">
        
        <select name="category" class="filter-input">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"
              <?= $category == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <input type="number" 
       name="min_price" 
       class="filter-input"
       placeholder="Min price"
       step="0.01"
       value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>">

<input type="number" 
       name="max_price" 
       class="filter-input"
       placeholder="Max price"
       step="0.01"
       value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>">
      </div>

      <div class="filter-group">
        <select name="sort" class="sort-select">
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
          <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price Low to High</option>
          <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price High to Low</option>
        </select>

        <button type="submit" class="filter-button">Apply Filters</button>
        <a href="cloths.php" class="filter-button reset-button">Reset</a>
      </div>
    </form>
  </div>
<div class="container">
  <div class="collection-heading">Explore Our Latest Collection</div>
  <?php if ($products): ?>
    <div class="products-grid">
      <?php foreach ($products as $product): ?>
        <div class="product-card" data-product-id="<?= $product['id'] ?>">
        <div class="product-image">
            <?php if (!empty($product['image'])): ?>
              <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
              <img src="../uploads/default.png" alt="Default Image">
            <?php endif; ?>
            <?php if (!empty($product['is_new'])): ?>
              <div class="product-badge">New</div>
            <?php endif; ?>
          </div>
          <div class="product-info">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
            <div class="product-description"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
            <div class="product-actions">
              <button class="quick-view-btn"><i class="fas fa-eye"></i> View</button>
              <button class="add-to-cart-btn"><i class="fas fa-shopping-cart"></i> Cart</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="no-products">No products available right now. Come back later!</p>
  <?php endif; ?>
</div>

<script>
document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', async () => {
        const productCard = button.closest('.product-card');
        const productId = productCard.dataset.productId;

        try {
            const response = await fetch('order_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + encodeURIComponent(productId)
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                // Visual feedback
                productCard.classList.add('ordered');
                setTimeout(() => {
                    productCard.classList.remove('ordered');
                }, 2000);
                
                // Update cart indicator
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = parseInt(cartCount.textContent) + 1;
                }
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error - please try again');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

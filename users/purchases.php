<?php
// users/purchases.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../classes/Order.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
  // CSRF validation
  if (!isset($_POST['csrf_token'])
      || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
      $_SESSION['error'] = 'Invalid CSRF token.';
  } else {
      $orderId = (int)$_POST['order_id'];
      if (Order::cancelOrder($orderId, $_SESSION['user_id'])) {
          $_SESSION['message'] = "Order #{$orderId} has been cancelled.";
      } else {
          $_SESSION['error'] = "Unable to cancel Order #{$orderId}.";
      }
  }
  // prevent form resubmission
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit();
}


// Get user's orders
$orders = Order::getUserOrders($_SESSION['user_id']);

foreach ($orders as &$order) {
  if (!isset($order['status'])) {
      $order['status'] = 'pending';
  }
}
unset($order);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">

<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchases</title>
    <style>
      /* Reset & base */
      * { box-sizing: border-box; margin: 0; padding: 0; }
      body {
        background: #f5f7fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        line-height: 1.5;
      }
      a { text-decoration: none; color: inherit; }

      /* Container */
      .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
      }
      .container h2 {
        font-size: 2rem;
        margin-bottom: 20px;
        text-align: center;
        color: #444;
      }

      /* Orders list */
      .orders-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      /* Order card */
      .order-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: transform .2s;
      }
      .order-card:hover {
        transform: translateY(-2px);
      }

      .order-header {
        display: flex;
        justify-content: space-between;
        background: #007bff;
        color: #fff;
        padding: 12px 16px;
        font-weight: 600;
      }

      .order-items {
        padding: 16px;
        border-top: 1px solid #eee;
      }
      .order-item {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
      }
      .order-item:last-child {
        margin-bottom: 0;
      }
      .order-item img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
      }
      .item-info h4 {
        font-size: 1.1rem;
        margin-bottom: 6px;
        color: #222;
      }
      .item-info p {
        font-size: .95rem;
        color: #555;
      }
      .cancel-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .cancel-btn:hover {
            background: #c82333;
        }
        .cancelled-status {
            color: #dc3545;
            font-weight: bold;
        }
        .message-alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
      .order-total {
        padding: 12px 16px;
        text-align: right;
        font-size: 1.1rem;
        font-weight: 600;
        background: #f9f9f9;
        border-top: 1px solid #eee;
      }

      .no-orders {
        text-align: center;
        font-size: 1rem;
        color: #777;
        margin: 40px 0;
      }

      @media (max-width: 600px) {
        .order-item {
          flex-direction: column;
          align-items: center;
        }
        .order-item img {
          margin-bottom: 10px;
        }
        .order-header, .order-total {
          flex-direction: column;
          text-align: center;
        }
      }
/* Add this to your <style> section */
.grand-total {
  font-weight: 600;
  color: #2c3e50;
  border: 1px solid #007bff;
  margin-bottom: 30px;
  font-size: 1.2rem;
  text-align: right;
  padding: 10px 20px;
  background: #e3f2fd;
  border-radius: 5px;
}
    </style>
</head>
<body>
  <div class="container">
    <h2>My Purchases</h2>
    <!-- … message alerts … -->

    <?php if (!empty($orders)):
        $grandTotal = 0;
        foreach ($orders as $order) {
            foreach ($order['items'] as $item) {
                $grandTotal += $item['price'] * $item['quantity'];
            }
        }
    ?>
      <div class="grand-total">
        Total Spent: $<?= number_format($grandTotal, 2) ?>
      </div>

      <div class="orders-list">
        <?php foreach ($orders as $order): ?>
          <div class="order-card">
            <div class="order-header">
              <div>
                <span>Order #<?= htmlspecialchars($order['id']) ?></span>
                <?php if ($order['status'] === 'cancelled'): ?>
                    <span class="cancelled-status">(Cancelled)</span>
                <?php endif; ?>
              </div>
              <div>
                <span><?= date('M d, Y', strtotime($order['order_date'])) ?></span>
                <?php if ($order['status'] !== 'cancelled'): ?>
                    <form method="post" class="cancel-form">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" name="cancel_order" class="cancel-btn"
                                onclick="return confirm('Are you sure you want to cancel this order?')">
                            Cancel Order
                        </button>
                    </form>
                <?php endif; ?>
              </div>
            </div>
            <div class="order-items">
              <?php foreach ($order['items'] as $item): ?>
                <div class="order-item">
                  <img src="../uploads/<?= htmlspecialchars($item['image']) ?>"
                       alt="<?= htmlspecialchars($item['name']) ?>">
                  <div class="item-info">
                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                    <p>Quantity: <?= (int)$item['quantity'] ?></p>
                    <p>Price: $<?= number_format($item['price'], 2) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="no-orders">You haven't made any purchases yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>

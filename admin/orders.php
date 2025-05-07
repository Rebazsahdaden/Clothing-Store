<?php
declare(strict_types=1);
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/config.php';
require_once '../admin/header.php';
require_once '../classes/Order.php';

$orders = Order::getAllOrders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin Panel</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --border: #e2e8f0;
        }

        .orders-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--surface);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border);
        }

        h1 {
            font-size: 1.875rem;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        h1 svg {
            width: 1.5em;
            height: 1.5em;
        }

        .order-grid {
            display: grid;
            gap: 1.5rem;
        }

        .order-card {
            background: var(--surface);
            border-radius: 1rem;
            padding: 1.5rem;
            border-left: 4px solid var(--primary);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-3px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .order-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 1rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-pending { background: #fef3c7; color: #b45309; }
        .status-processing { background: #dbeafe; color: #1d4ed8; }
        .status-shipped { background: #dcfce7; color: #047857; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .order-details p {
            margin: 0;
            color: var(--text-secondary);
        }

        .order-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .status-select {
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid var(--border);
            background: var(--background);
            color: var(--text-primary);
            flex-grow: 1;
        }

        .update-btn {
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            background: var(--primary);
            color: white;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .update-btn:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 768px) {
            .orders-container {
                padding: 1rem;
                margin: 1rem;
            }
            
            .order-actions {
                flex-direction: column;
            }
            
            .status-select {
                width: 100%;
            }
            
            .update-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @keyframes newOrder {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .new-order {
            animation: newOrder 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <div class="admin-header">
            <h1>
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                Order Management
            </h1>
        </div>

        <div class="order-grid">
            <?php foreach ($orders as $order): ?>
                <div class="order-card" data-order-id="<?= $order['id'] ?>">
                    <div class="order-header">
                        <h3>Order #<?= $order['id'] ?></h3>
                        <span class="order-status status-<?= $order['status'] ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                    
                    <div class="order-details">
                        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Unknown') ?></p>
                        <p><strong>Total:</strong> $<?= number_format((float)$order['total_price'], 2) ?></p>
                        <p><strong>Date:</strong> <?= date('M j, Y g:i a', strtotime($order['order_date'])) ?></p>
                        <p><strong>Items:</strong> <?= $order['item_count'] ?? 0 ?></p>
                    </div>

                    <div class="order-actions">
                        <select class="status-select" data-order-id="<?= $order['id'] ?>">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button class="update-btn" onclick="updateStatus(<?= $order['id'] ?>)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                            </svg>
                            Update
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Keep the existing JavaScript functionality
        function updateStatus(orderId) {
            const status = document.querySelector(`select[data-order-id="${orderId}"]`).value;
            
            fetch(`update_order_status.php?order_id=${orderId}&status=${status}`)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const statusBadge = document.querySelector(`.order-card[data-order-id="${orderId}"] .order-status`);
                        statusBadge.className = `order-status status-${status}`;
                        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    }
                });
        }

        const eventSource = new EventSource('order_updates.php');
        
        eventSource.onmessage = function(e) {
            const order = JSON.parse(e.data);
            const orderGrid = document.querySelector('.order-grid');
            
            const orderCard = document.createElement('div');
            orderCard.className = 'order-card new-order';
            orderCard.innerHTML = `
                <div class="order-header">
                    <h3>Order #${order.id}</h3>
                    <span class="order-status status-${order.status}">
                        ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                </div>
                <div class="order-details">
                    <p><strong>Customer:</strong> ${order.customer_name}</p>
                    <p><strong>Total:</strong> $${order.total_amount.toFixed(2)}</p>
                    <p><strong>Date:</strong> ${new Date(order.order_date).toLocaleString()}</p>
                    <p><strong>Items:</strong> ${order.item_count}</p>
                </div>
                <div class="order-actions">
                    <select class="status-select" data-order-id="${order.id}">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <button class="update-btn" onclick="updateStatus(${order.id})">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                        </svg>
                        Update
                    </button>
                </div>
            `;

            orderGrid.prepend(orderCard);
        };
    </script>
</body>
</html>
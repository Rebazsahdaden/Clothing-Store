<?php
// admin/dashboard.php
declare(strict_types=1);
session_start();

// Enhanced security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/config.php';
require_once '../admin/header.php';

// Set username and last login with defaults if not available
$username = $_SESSION['admin_user']['username'] ?? 'Administrator';
$lastLoginDisplay = $_SESSION['admin_user']['last_login'] ?? date('M j, Y \a\t g:i a');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Administration Panel for Clothing Store Management System" />
    <title>Admin Dashboard - Clothing Store</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.5;
        }

        .admin-dashboard {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 4rem;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, var(--primary), #4f46e5);
            border-radius: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(45deg);
        }

        .dashboard-header h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .welcome-message {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
            position: relative;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .dashboard-card {
            background: var(--surface);
            border-radius: 1rem;
            padding: 2rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .card-icon {
            width: 64px;
            height: 64px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .card-icon svg {
            width: 32px;
            height: 32px;
            color: var(--primary);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .card-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-align: center;
            opacity: 0.8;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: var(--surface);
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
        }

        @media (max-width: 768px) {
            .admin-dashboard {
                padding: 0 1rem;
            }
            
            .dashboard-header {
                padding: 2rem 1rem;
                margin-bottom: 2rem;
            }
            
            .dashboard-header h1 {
                font-size: 2rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            background: var(--surface);
            color: var(--text-secondary);
            transition: var(--transition);
            text-decoration: none;
            border: 1px solid var(--border);
            margin: 2rem auto;
        }

        .logout-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <main class="admin-dashboard">
        <section class="dashboard-header">
            <h1>Store Administration</h1>
            <p class="welcome-message">
                Welcome back, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!<br>
                Last session: <?= htmlspecialchars($lastLoginDisplay, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </section>

        <section class="dashboard-grid">
            <a href="products.php" class="dashboard-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
                <h2 class="card-title">Product Management</h2>
                <p class="card-description">Manage inventory, pricing, and product listings</p>
            </a>

            <a href="categories.php" class="dashboard-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <h2 class="card-title">Category Organization</h2>
                <p class="card-description">Structure product categories and collections</p>
            </a>

        </section>
        <section class="dashboard-grid">
            <a href="orders.php" class="dashboard-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
                <h2 class="card-title">Order Processing</h2>
                <p class="card-description">Manage inventory, pricing, and product listings</p>
            </a>
        </section>

        </div>
    </main>
    
</body>
</html>

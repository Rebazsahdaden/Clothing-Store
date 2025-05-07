<?php
// admin/login.php
session_start();

// -- Simple rate-limit: max 3 attempts per 5 minutes
$maxAttempts  = 3;
$lockoutTime  = 5 * 60; // seconds
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['admin_login'] = ['count'=>0, 'first'=>time()];
}
$loginData = &$_SESSION['admin_login'];

// -- CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf_token = $_SESSION['csrf_token'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) CSRF check
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($csrf_token, $_POST['csrf_token'])
    ) {
        $message = 'Please reload the page and try again.';
    }
    // 2) Check lockout
    elseif (
        $loginData['count'] >= $maxAttempts &&
        time() - $loginData['first'] < $lockoutTime
    ) {
        $message = 'Too many attempts. Try again later.';
    }
    else {
        // 3) Sanitize inputs
        $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
        $password = $_POST['password'] ?? '';

        // 4) Basic validation
        if ($username === '' || $password === '') {
            $message = 'All fields are required.';
        } else {
            require_once __DIR__ . '/../classes/Admin.php';
            $admin = new Admin();

            // 5) Attempt login
            if ($admin->simpleLogin($username, $password)) {
                // success! reset attempts, regenerate session, redirect
                $_SESSION['admin_login'] = ['count'=>0, 'first'=>time()];
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                header('Location: /Clothing-Store-Management-System/admin/dashboard.php');
                exit;
            } else {
                // failure: increment attempts
                $loginData['count']++;
                if ($loginData['count'] === 1) {
                    $loginData['first'] = time();
                }
                $message = 'Invalid username or password.';
                // small delay
                usleep(300000);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Clothing Store</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --error: #ef4444;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--background);
            min-height: 100vh;
            display: grid;
            place-items: center;
            margin: 0;
            padding: 1rem;
        }

        .auth-container {
            max-width: 440px;
            width: 100%;
            padding: 2rem;
            background: var(--surface);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .auth-title {
            font-size: 1.875rem;
            margin: 0;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            justify-content: center;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        .form-input {
            width: 90%;
            padding: 0.875rem 1rem;
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s;
            background: var(--background);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .submit-btn {
            background: var(--primary);
            color: white;
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            justify-content: center;
        }

        .submit-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .auth-message {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .auth-container {
                padding: 1.5rem;
            }
            
            .auth-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1 class="auth-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M9.5 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path d="M15 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
                Admin Portal
            </h1>
        </div>

        <?php if ($message): ?>
            <div class="auth-message">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="">
        <input type="hidden"
       name="csrf_token"
       value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden"
           name="csrf_token"
           value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

    <div class="form-group">
        <input class="form-input"
               type="text"
               name="username"
               placeholder="Admin username"
               required
               autocomplete="username"
               autocapitalize="none">
    </div>

            <div class="form-group">
                <input class="form-input" 
                       type="password" 
                       name="password" 
                       id="password" 
                       placeholder="Password"
                       required
                       autocomplete="current-password">
            </div>

            <button type="submit" class="submit-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M12 8l4 4-4 4M8 12h8"/>
                </svg>
                Secure Login
            </button>
        </form>
    </div>
</body>
</html>
<?php
// forgot-password.php
session_start();

require_once 'includes/config.php';
require_once 'classes/Auth.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("Security error. Please refresh the page.");
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }

        // Here you would typically add password reset logic
        // For demonstration purposes, we'll just show a success message
        $message = "Password reset instructions have been sent to your email.";
        $messageType = 'success';

    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - Clothing Store</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --success: #22c55e;
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
        }

        .success {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .auth-links {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .auth-link {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
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
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M18 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                    <path d="M6 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                </svg>
                Password Recovery
            </h1>
        </div>

        <?php if ($message): ?>
            <div class="auth-message <?= $messageType ?>">
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

        <form class="auth-form" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <input class="form-input" 
                       type="email" 
                       name="email" 
                       id="email" 
                       placeholder="Enter your email"
                       required
                       autocomplete="email">
            </div>

            <button type="submit" class="submit-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                Reset Password
            </button>
        </form>

        <div class="auth-links">
            <a href="login.php" class="auth-link">Remember your password? Sign In</a>
            <a href="register.php" class="auth-link">Need an account? Register</a>
        </div>
    </div>
</body>
</html>
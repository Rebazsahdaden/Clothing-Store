<?php
// login.php
session_start();

// 1) Ensure config is present
if (!file_exists(__DIR__ . '/includes/config.php')) {
    http_response_code(500);
    exit('Configuration error');
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/classes/Auth.php';

// 2) Simple rate-limit: 5 attempts per 15 minutes
$maxAttempts = 5;
$lockoutTime = 15 * 60; // seconds
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = ['count' => 0, 'first' => time()];
}
$attemptData = &$_SESSION['login_attempts'];

// 3) CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf_token = $_SESSION['csrf_token'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // a) CSRF check
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $message = 'Please reload the page and try again.';
    }
    // b) Lockout check
    elseif (
        $attemptData['count'] >= $maxAttempts &&
        time() - $attemptData['first'] < $lockoutTime
    ) {
        $message = 'Too many attempts. Try again later.';
    }
    else {
        // c) Sanitize inputs
        $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
        $password = $_POST['password'] ?? '';

        // d) Basic validation
        if ($username === '' || $password === '') {
            $message = 'Please fill in all fields.';
        } 
        else {
            // e) Attempt login
            if (Auth::login($username, $password)) {
                // Success: clear attempts, regenerate session, redirect
                $_SESSION['login_attempts'] = ['count' => 0, 'first' => time()];
                session_regenerate_id(true);
                header('Location: users/cloths.php');
                exit;
            } else {
                $message = 'Invalid username or password.';
            }
        }

        // f) On any failure: increment attempts + delay
        if ($message !== '') {
            $attemptData['count']++;
            if ($attemptData['count'] === 1) {
                $attemptData['first'] = time();
            }
            usleep(300000); // 0.3s
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Secure Login – Clothing Store</title>
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

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .form-input {
            width: 90%;
            padding: 0.875rem 1rem 0.875rem 3rem;
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
            border: 1px solid transparent;
        }

        .error {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        .auth-links {
            margin-top: 2rem;
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
            
            .form-input {
                padding-left: 2.5rem;
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
                </svg>
              Log In
            </h1>
        </div>

        <?php if ($message): ?>
            <div class="auth-message error">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="login.php">
      <!-- CSRF token -->
      <input type="hidden" name="csrf_token"
             value="<?= htmlspecialchars($csrf_token, ENT_QUOTES) ?>">

      <div class="form-group">
        <i class="fas fa-user input-icon"></i>
        <input class="form-input"
               type="text" name="username"
               placeholder="Username"
               autocomplete="username"
               required
               value="<?= isset($_POST['username'])
                            ? htmlspecialchars($_POST['username'], ENT_QUOTES)
                            : '' ?>">
      </div>

      <div class="form-group">
        <i class="fas fa-lock input-icon"></i>
        <input class="form-input"
               type="password" name="password"
               placeholder="Password"
               autocomplete="current-password"
               required>
      </div>

            <button class="submit-btn" type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M12 8l4 4-4 4M8 12h8"/>
                </svg>
                Sign In
            </button>
        </form>

        <div class="auth-links">
        <a class="auth-link" href="register.php">Register</a>
            <a class="auth-link" href="forgot-password.php">Forgot Password?</a>
            <a class="auth-link" href="//localhost/Clothing-Store-Management-System/admin/login.php">Admin Portal</a>
        </div>
    </div>
</body>
</html>
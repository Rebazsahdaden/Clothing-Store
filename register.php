<?php
// register.php
session_start();
require_once 'includes/config.php';
require_once 'classes/User.php';

// --- Simple rate-limit: max 5 registers per 10 minutes ---
$maxAttempts = 5;
$lockoutTime = 10 * 60; // seconds
if (!isset($_SESSION['reg_attempts'])) {
    $_SESSION['reg_attempts'] = ['count'=>0, 'first'=>time()];
}
$regData = &$_SESSION['reg_attempts'];

// --- CSRF token generation ---
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
    // 2) Rate-limit check
    elseif (
        $regData['count'] >= $maxAttempts &&
        time() - $regData['first'] < $lockoutTime
    ) {
        $message = 'Too many attempts. Please wait and try again later.';
    }
    else {
        // 3) Sanitize & validate inputs
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']       ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        // Username: 3–20 chars, letters/numbers/_ only
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            $message = 'Invalid username.';
        }
        // Email format
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Invalid email address.';
        }
        // Password length
        elseif (strlen($password) < 8) {
            $message = 'Password must be at least 8 characters.';
        }
        // Passwords match?
        elseif ($password !== $confirm) {
            $message = 'Passwords do not match.';
        }
        else {
            // 4) Attempt registration
            if (User::register($username, $email, $password)) {
                header('Location: login.php');
                exit;
            } else {
                $message = 'Registration failed. Try different credentials.';
            }
        }

        // 5) On any failure: increment attempts + tiny delay
        if ($message !== '') {
            $regData['count']++;
            if ($regData['count'] === 1) {
                $regData['first'] = time();
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
  <title>Create Account – Clothing Store</title>
  <style>
:root {
    --primary: #6366f1;
    --primary-hover: #4f46e5;
    --surface: #ffffff;
    --background: #f8fafc;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border: #e2e8f0;
    --error: #ef4444;
}

.auth-container {
    max-width: 440px;
    margin: 2rem auto;
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
    background: #fef2f2;
    border: 1px solid #fca5a5;
    color: #991b1b;
}

.extra-links {
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
        margin: 1rem;
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
        <!-- SVG icon… --> Create Account
      </h1>
    </div>

    <?php if ($message): ?>
      <div class="auth-message">
        <?= htmlspecialchars($message, ENT_QUOTES) ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="register.php">
      <!-- CSRF token -->
      <input type="hidden"
             name="csrf_token"
             value="<?= htmlspecialchars($csrf_token, ENT_QUOTES) ?>">

      <div class="form-group">
        <input class="form-input"
               type="text" name="username"
               placeholder="Username"
               required
               value="<?= isset($_POST['username']) 
                            ? htmlspecialchars($_POST['username'], ENT_QUOTES) 
                            : '' ?>">
      </div>

      <div class="form-group">
        <input class="form-input"
               type="email" name="email"
               placeholder="Email"
               required
               value="<?= isset($_POST['email']) 
                            ? htmlspecialchars($_POST['email'], ENT_QUOTES) 
                            : '' ?>">
      </div>

      <div class="form-group">
        <input class="form-input"
               type="password" name="password"
               placeholder="Password"
               required>
      </div>

      <div class="form-group">
        <input class="form-input"
               type="password" name="confirm_password"
               placeholder="Confirm Password"
               required>
      </div>

      <button class="submit-btn" type="submit">
        <!-- SVG icon… --> Register
      </button>
    </form>

    <div class="extra-links">
      <a href="login.php" class="auth-link">Already have an account? Sign In</a>
      <a href="forgot-password.php" class="auth-link">Forgot Password?</a>
    </div>
  </div>
</body>
</html>
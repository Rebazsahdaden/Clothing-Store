<?php
// admin/edit_category.php
require_once '../includes/config.php';
require_once '../admin/header.php';
require_once '../classes/Category.php';

$id = $_GET['id'] ?? null;
$category = null;
$message = '';
$messageType = 'success';

// Fetch category details
if ($id) {
    $category = Category::getById($id);
    if (!$category) {
        $message = "Category not found.";
        $messageType = 'error';
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    try {
        if (empty($name)) {
            throw new Exception("Category name cannot be empty.");
        }

        if (Category::update($id, $name)) {
            $message = "Category updated successfully! 🎉";
            $messageType = 'success';
            $category = Category::getById($id); // refresh
        } else {
            throw new Exception("Failed to update category.");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}
?>

<style>
:root {
    --primary-color: #6366f1;
    --success-color: #22c55e;
    --error-color: #ef4444;
    --background: #f8fafc;
    --surface: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
}

.container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: var(--surface);
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
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

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
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

.form-section {
    margin-bottom: 2.5rem;
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.form-group {
    margin-bottom: 1.25rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
}

input[type="text"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

input[type="text"]:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: transform 0.1s, filter 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    filter: brightness(110%);
}

.btn-secondary {
    background: #e2e8f0;
    color: var(--text-primary);
}

.btn-secondary:hover {
    background: #cbd5e1;
}

.btn:active {
    transform: scale(0.98);
}

@media (max-width: 768px) {
    .container {
        padding: 1rem;
        margin: 1rem;
    }
    
    h1 {
        font-size: 1.5rem;
    }
}
</style>

<div class="container">
    <div class="header">
        <h1>
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            Edit Category
        </h1>
    </div>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-<?= $messageType ?>">
            <?php if ($messageType === 'success'): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            <?php endif; ?>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($category): ?>
        <form method="POST" action="edit_category.php?id=<?= urlencode($id) ?>" class="form-section">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       required 
                       maxlength="50"
                       value="<?= htmlspecialchars($category['name']) ?>"
                       placeholder="Enter category name">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                    </svg>
                    Update Category
                </button>
                <a href="categories.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
// admin/categories.php
require_once '../includes/config.php';
require_once '../admin/header.php';
require_once '../classes/Category.php';

$message = '';
$messageType = 'success'; // Default to success type

// Handle create category form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    try {
        if (empty($name)) {
            throw new Exception("Category name cannot be empty.");
        }
        
        if (Category::create($name)) {
            $message = "Category added successfully! 🎉";
            $messageType = 'success';
        } else {
            throw new Exception("Failed to add category.");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

// Retrieve all categories
$categories = Category::getAll();
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

h2 {
    font-size: 1.25rem;
    color: var(--text-primary);
    margin-top: 0;
    margin-bottom: 1.5rem;
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

.btn:active {
    transform: scale(0.98);
}

.category-list {
    background: var(--surface);
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.category-item {
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background 0.2s;
}

.category-item:not(:last-child) {
    border-bottom: 1px solid #e2e8f0;
}

.category-item:hover {
    background: #f8fafc;
}

.actions {
    display: flex;
    gap: 0.75rem;
}

.action-link {
    text-decoration: none;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.edit-link {
    color: var(--primary-color);
    background: rgba(99, 102, 241, 0.1);
}

.edit-link:hover {
    background: rgba(99, 102, 241, 0.2);
}

.delete-link {
    color: var(--error-color);
    background: rgba(239, 68, 68, 0.1);
}

.delete-link:hover {
    background: rgba(239, 68, 68, 0.2);
}

.empty-state {
    padding: 2rem;
    text-align: center;
    color: var(--text-secondary);
    border: 2px dashed #e2e8f0;
    border-radius: 8px;
    margin: 1.5rem 0;
}
</style>

<div class="container">
    <div class="header">
        <h1>
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            Manage Categories
        </h1>
    </div>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-<?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h2>Create New Category</h2>
        <form method="POST" action="categories.php">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       required
                       placeholder="Enter category name"
                       maxlength="50">
            </div>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Add Category
            </button>
        </form>
    </div>

    <div class="category-list">
        <h2>Existing Categories</h2>
        <?php if (!empty($categories)) : ?>
            <?php foreach ($categories as $cat) : ?>
                <div class="category-item">
                    <span><?= htmlspecialchars($cat['name']) ?></span>
                    <div class="actions">
                        <a href="edit_category.php?id=<?= urlencode($cat['id']) ?>" 
                           class="action-link edit-link">
                            Edit
                        </a>
                        <a href="delete_category.php?id=<?= urlencode($cat['id']) ?>" 
                           class="action-link delete-link"
                           onclick="return confirm('Are you sure you want to delete this category?')">
                            Delete
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="empty-state">
                No categories found. Start by creating your first category!
            </div>
        <?php endif; ?>
    </div>
</div>


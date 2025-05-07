<?php
// index.php
session_start();
require_once 'includes/config.php';
require_once 'includes/header.php';
require_once 'classes/Product.php';

$products = Product::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClothHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2d2a2e;
            --accent: #e54f6d;
            --surface: #ffffff;
            --background: #f9f9f9;
            --text-primary: #1a181a;
            --text-secondary: #666;
            --transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .hero {
            height: 50vh;
            display: flex;
            align-items: flex-end;
            position: relative;
            overflow: hidden;
            margin-bottom: 6rem;
        }

        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
           
        }

        .hero-content {
            position: relative;
            z-index: 1;
            width: 100%;
            padding: 4rem;
            background: linear-gradient(transparent 10%, rgba(0,0,0,0.8));
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 4.5rem;
            color: white;
            margin: 0 0 1rem;
            line-height: 1.1;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        .hero-subtitle {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.9);
            max-width: 600px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s 0.2s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        .collection-grid {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 4rem;
        }

        .product-card {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: var(--surface);
            transform-style: preserve-3d;
            transition: var(--transition);
        }

        .product-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(transparent 60%, rgba(0,0,0,0.1));
            z-index: 1;
        }

        .product-card:hover {
            transform: translateY(-10px) rotateX(2deg) rotateY(2deg);
            box-shadow: 0 45px 60px -20px rgba(0,0,0,0.15);
        }

        .product-media {
            position: relative;
            height: 480px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 1.2s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: var(--accent);
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 4px 15px rgba(229, 79, 109, 0.3);
        }

        .product-details {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            z-index: 2;
            transform: translateY(100%);
            transition: var(--transition);
        }

        .product-card:hover .product-details {
            transform: translateY(0);
        }

        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin: 0 0 1rem;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 600;
            opacity: 0;
            transform: translateY(20px);
            transition: var(--transition);
        }

        .product-card:hover .product-price {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.1s;
        }

        .quick-view-btn {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: var(--surface);
            color: var(--text-primary);
            border: none;
            padding: 1rem 2rem;
            border-radius: 2rem;
            font-weight: 600;
            cursor: pointer;
            opacity: 0;
            transition: var(--transition);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .product-card:hover .quick-view-btn {
            opacity: 1;
            bottom: 6rem;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero {
                height: 70vh;
                margin-bottom: 4rem;
            }
            
            .hero-title {
                font-size: 2.8rem;
            }
            
            .collection-grid {
                padding: 0 1rem;
                gap: 2rem;
            }
            
            .product-media {
                height: 360px;
            }
        }
    </style>
</head>
<body>
    <section class="hero">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="uploads/hero-video.mp4" type="video/mp4">
        </video>
        <div class="hero-content">
            <h1 class="hero-title">ClothHub</h1>
            <p class="hero-subtitle">Discover our curated collection of timeless pieces designed for modern living</p>
        </div>
    </section>

    <main class="collection-grid">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <div class="product-media">
                        <?php if (!empty($product['image'])): ?>
                            <img class="product-image" 
                                 src="uploads/<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 loading="lazy">
                        <?php endif; ?>
                        <div class="product-badge">
                            New Arrival
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-price">
                            $<?= number_format($product['price'], 2) ?>
                        </p>
                    </div>
                    <button class="quick-view-btn">
                        Quick Preview
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <!-- Add your empty state design here -->
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
<?php require_once ('includes/footer.php'); ?>
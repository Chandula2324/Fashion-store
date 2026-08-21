<?php
require_once 'includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('index.php');
}

$productId = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('index.php');
}

$product = $result->fetch_assoc();
$sizes = explode(',', $product['sizes']);

// Get cart count
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Fashion Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo"><span>&#128090;</span> Fashion<span>Store</span></a>
            <button class="menu-toggle" onclick="document.querySelector('.nav').classList.toggle('active')">&#9776;</button>
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="index.php?category=Men">Men</a>
                <a href="index.php?category=Women">Women</a>
                <a href="index.php?category=Kids">Kids</a>
            </nav>
            <div class="nav-right">
                <form class="search-bar" action="index.php" method="GET">
                    <input type="text" name="search" placeholder="Search products...">
                    <button type="submit">&#128269;</button>
                </form>
                <a href="cart.php" class="cart-icon">
                    &#128722;
                    <?php if ($cartCount > 0): ?><span class="cart-count"><?php echo $cartCount; ?></span><?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <section class="product-detail">
        <div class="container">
            <div class="product-detail-grid">
                <div class="product-detail-image">
                    <?php if (!empty($product['image']) && file_exists("uploads/" . $product['image'])): ?>
                        <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#999;font-size:6rem;">&#128090;</div>
                    <?php endif; ?>
                </div>
                <div class="product-detail-info">
                    <div class="product-detail-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-detail-price">$<?php echo number_format($product['price'], 2); ?></div>
                    <p class="product-detail-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                    <div class="product-detail-meta">
                        <div class="meta-item">
                            <span class="meta-label">Category</span>
                            <span class="meta-value"><?php echo $product['category']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Type</span>
                            <span class="meta-value"><?php echo $product['type']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Color</span>
                            <span class="meta-value"><?php echo htmlspecialchars($product['color']); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Stock</span>
                            <span class="meta-value"><?php echo $product['stock']; ?> units</span>
                        </div>
                    </div>

                    <form action="cart.php?action=add" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="size" id="selected-size" value="<?php echo trim($sizes[0]); ?>">

                        <div class="size-selector">
                            <label class="size-selector-label">Select Size</label>
                            <div class="size-selector-options">
                                <?php foreach ($sizes as $i => $sz): 
                                    $sz = trim($sz);
                                ?>
                                <button type="button" class="size-btn <?php echo $i === 0 ? 'selected' : ''; ?>" data-size="<?php echo $sz; ?>"><?php echo $sz; ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn" data-action="minus">-</button>
                            <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="10" readonly>
                            <button type="button" class="quantity-btn" data-action="plus">+</button>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block add-to-cart" data-id="<?php echo $product['id']; ?>">
                            &#128722; Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom" style="border-top:none;padding-top:0;">
                <p>&copy; 2026 Fashion Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
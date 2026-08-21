<?php
require_once 'includes/functions.php';

// Get filter parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}
if ($type) {
    $sql .= " AND type = ?";
    $params[] = $type;
    $types .= "s";
}
if ($search) {
    $sql .= " AND (name LIKE ? OR brand LIKE ? OR description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Get unique categories and types for filters
$categories = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$types_result = $conn->query("SELECT DISTINCT type FROM products ORDER BY type");

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
    <title>Fashion Store - Online Clothing Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">
                <span>&#128090;</span> Fashion<span>Store</span>
            </a>
            <button class="menu-toggle" onclick="document.querySelector('.nav').classList.toggle('active')">&#9776;</button>
            <nav class="nav">
                <a href="index.php" class="active">Home</a>
                <a href="index.php?category=Men">Men</a>
                <a href="index.php?category=Women">Women</a>
                <a href="index.php?category=Kids">Kids</a>
            </nav>
            <div class="nav-right">
                <form class="search-bar" action="index.php" method="GET">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">&#128269;</button>
                </form>
                <a href="cart.php" class="cart-icon">
                    &#128722;
                    <?php if ($cartCount > 0): ?>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Style Meets Comfort</h1>
            <p>Discover the latest trends in fashion for Men, Women, and Kids. Quality clothing at affordable prices.</p>
            <a href="#products" class="btn btn-primary btn-lg">Shop Now</a>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <form class="filter-bar" method="GET" action="index.php">
                <?php if ($search): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category']; ?>" <?php echo $category == $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo $cat['category']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Type</label>
                    <select name="type" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <?php while ($t = $types_result->fetch_assoc()): ?>
                        <option value="<?php echo $t['type']; ?>" <?php echo $type == $t['type'] ? 'selected' : ''; ?>>
                            <?php echo $t['type']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php if ($category || $type || $search): ?>
                <a href="index.php" class="btn btn-outline btn-sm">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <div class="container">
            <?php if ($search): ?>
            <h2 class="section-title">Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
            <?php elseif ($category || $type): ?>
            <h2 class="section-title">
                <?php echo $category ? $category . "'s" : "All"; ?> 
                <?php echo $type ? $type : "Clothing"; ?>
            </h2>
            <?php else: ?>
            <h2 class="section-title">Latest Arrivals</h2>
            <?php endif; ?>

            <?php flashMessage(); ?>

            <div class="product-grid">
                <?php if ($products->num_rows > 0): ?>
                    <?php while ($product = $products->fetch_assoc()): 
                        $sizes = explode(',', $product['sizes']);
                    ?>
                    <div class="product-card fade-in">
                        <div class="product-image">
                            <?php if (!empty($product['image']) && file_exists("uploads/" . $product['image'])): ?>
                                <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#999;font-size:4rem;">&#128090;</div>
                            <?php endif; ?>
                            <span class="product-badge"><?php echo $product['category']; ?></span>
                        </div>
                        <div class="product-info">
                            <div class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-meta">
                                <span>&#128308; <?php echo htmlspecialchars($product['color']); ?></span>
                                <span>&#128230; <?php echo $product['type']; ?></span>
                            </div>
                            <div class="product-sizes">
                                <?php foreach ($sizes as $sz): ?>
                                <span class="size-option" data-size="<?php echo trim($sz); ?>"><?php echo trim($sz); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-actions">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline btn-sm">View</a>
                                <button class="btn btn-primary btn-sm add-to-cart" data-id="<?php echo $product['id']; ?>" data-size="M">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column:1/-1;text-align:center;padding:var(--space-2xl);">
                        <p style="font-size:var(--font-size-xl);color:var(--color-text-light);">No products found.</p>
                        <a href="index.php" class="btn btn-primary" style="margin-top:var(--space-lg);">View All Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>About Us</h3>
                    <p style="color:rgba(255,255,255,0.7);font-size:0.9375rem;line-height:1.7;">Fashion Store is your one-stop destination for trendy and affordable clothing for the whole family.</p>
                </div>
                <div class="footer-column">
                    <h3>Categories</h3>
                    <a href="index.php?category=Men">Men</a>
                    <a href="index.php?category=Women">Women</a>
                    <a href="index.php?category=Kids">Kids</a>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <a href="#">Contact Us</a>
                    <a href="#">Shipping Info</a>
                    <a href="#">Returns</a>
                </div>
                <div class="footer-column">
                    <h3>Admin</h3>
                    <a href="login.php">Admin Login</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Fashion Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
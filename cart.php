<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'add' && isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $size = isset($_POST['size']) ? sanitize($_POST['size']) : 'M';
        $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            $cartItemId = $productId . '_' . $size;
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

            if (isset($_SESSION['cart'][$cartItemId])) {
                $_SESSION['cart'][$cartItemId]['quantity'] += $qty;
            } else {
                $_SESSION['cart'][$cartItemId] = [
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'size' => $size,
                    'quantity' => $qty,
                    'image' => $product['image']
                ];
            }
            setFlash('Item added to cart!', 'success');
        }
        redirect('cart.php');
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $id = sanitize($_GET['id']);
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        setFlash('Item removed from cart.', 'info');
    }
    redirect('cart.php');
}

if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    setFlash('Cart cleared.', 'info');
    redirect('cart.php');
}

$subtotal = 0;
$cartItems = $_SESSION['cart'] ?? [];
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal > 0 ? 5.99 : 0;
$total = $subtotal + $shipping;
$cartCount = array_sum(array_column($cartItems, 'quantity'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Fashion Store</title>
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
                <a href="cart.php" class="cart-icon">
                    &#128722;
                    <?php if ($cartCount > 0): ?><span class="cart-count"><?php echo $cartCount; ?></span><?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <section class="cart-section">
        <div class="container">
            <h2 class="section-title">Shopping Cart</h2>
            <?php flashMessage(); ?>

            <?php if (!empty($cartItems)): ?>
            <div class="cart-grid">
                <div class="cart-items">
                    <?php foreach ($cartItems as $id => $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <?php if (!empty($item['image']) && file_exists("uploads/" . $item['image'])): ?>
                                <img src="uploads/<?php echo $item['image']; ?>" alt="">
                            <?php else: ?>
                                <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:2rem;">&#128090;</div>
                            <?php endif; ?>
                        </div>
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>Size: <?php echo $item['size']; ?></p>
                            <p>Qty: <?php echo $item['quantity']; ?></p>
                        </div>
                        <div class="cart-item-price">
                            $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            <br><a href="cart.php?action=remove&id=<?php echo urlencode($id); ?>" class="btn btn-sm btn-outline" style="margin-top:8px;">Remove</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div style="text-align:right;margin-top:var(--space-lg);">
                        <a href="cart.php?action=clear" class="btn btn-outline btn-sm">Clear Cart</a>
                    </div>
                </div>
                <div class="cart-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-row"><span>Subtotal</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
                    <div class="summary-row"><span>Shipping</span><span>$<?php echo number_format($shipping, 2); ?></span></div>
                    <div class="summary-row total"><span>Total</span><span>$<?php echo number_format($total, 2); ?></span></div>
                    <a href="checkout.php" class="btn btn-primary btn-block btn-lg" style="margin-top:var(--space-lg);">Proceed to Checkout</a>
                    <a href="index.php" class="btn btn-outline btn-block" style="margin-top:var(--space-sm);">Continue Shopping</a>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">&#128722;</div>
                <h3>Your cart is empty</h3>
                <p style="color:var(--color-text-light);margin-bottom:var(--space-lg);">Looks like you haven't added anything yet.</p>
                <a href="index.php" class="btn btn-primary">Start Shopping</a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom"><p>&copy; 2026 Fashion Store. All rights reserved.</p></div>
        </div>
    </footer>
</body>
</html>

<?php
require_once 'includes/functions.php';

$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    redirect('cart.php');
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 5.99;
$total = $subtotal + $shipping;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['customer_name'] ?? '');
    $email = sanitize($_POST['customer_email'] ?? '');
    $phone = sanitize($_POST['customer_phone'] ?? '');
    $address = sanitize($_POST['customer_address'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'card');

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        setFlash('Please fill in all required fields.', 'error');
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, payment_status) VALUES (?, ?, ?, ?, ?, 'Completed')");
        $stmt->bind_param("ssssd", $name, $email, $phone, $address, $total);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        foreach ($cartItems as $item) {
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, size, price, quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("iissdi", $orderId, $item['product_id'], $item['name'], $item['size'], $item['price'], $item['quantity']);
            $stmt2->execute();
        }

        unset($_SESSION['cart']);
        setFlash('Order placed successfully! Thank you for your purchase.', 'success');
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Fashion Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php" class="logo"><span>&#128090;</span> Fashion<span>Store</span></a>
        </div>
    </header>

    <section class="cart-section">
        <div class="container">
            <h2 class="section-title">Secure Checkout</h2>
            <?php flashMessage(); ?>
            <div class="cart-grid">
                <form method="POST" class="checkout-form">
                    <h3 style="margin-bottom:var(--space-lg);">Shipping Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="customer_name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="customer_email" required placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone *</label>
                            <input type="tel" name="customer_phone" required placeholder="+1 234 567 890">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Shipping Address *</label>
                        <textarea name="customer_address" rows="3" required placeholder="123 Street, City, Country"></textarea>
                    </div>

                    <h3 style="margin:var(--space-xl) 0 var(--space-lg);">Payment Method</h3>
                    <div class="payment-methods">
                        <div class="payment-method selected" data-method="card">
                            <div class="payment-method-icon">&#128179;</div>
                            <div><strong>Credit Card</strong></div>
                        </div>
                        <div class="payment-method" data-method="paypal">
                            <div class="payment-method-icon">&#128240;</div>
                            <div><strong>PayPal</strong></div>
                        </div>
                        <div class="payment-method" data-method="cod">
                            <div class="payment-method-icon">&#128176;</div>
                            <div><strong>Cash on Delivery</strong></div>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" id="payment-method" value="card">

                    <div style="background:var(--color-bg);padding:var(--space-lg);border-radius:var(--radius-md);margin-bottom:var(--space-lg);">
                        <p style="font-size:var(--font-size-sm);color:var(--color-text-light);">
                            &#128274; This is a secure mock payment gateway. No real transactions will be processed.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">Place Order ($<?php echo number_format($total,2); ?>)</button>
                </form>

                <div class="cart-summary">
                    <h2>Order Summary</h2>
                    <?php foreach ($cartItems as $item): ?>
                    <div class="summary-row">
                        <span><?php echo htmlspecialchars($item['name']); ?> (<?php echo $item['size']; ?>) x<?php echo $item['quantity']; ?></span>
                        <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="summary-row"><span>Subtotal</span><span>$<?php echo number_format($subtotal,2); ?></span></div>
                    <div class="summary-row"><span>Shipping</span><span>$<?php echo number_format($shipping,2); ?></span></div>
                    <div class="summary-row total"><span>Total</span><span>$<?php echo number_format($total,2); ?></span></div>
                </div>
            </div>
        </div>
    </section>
    <script src="assets/js/main.js"></script>
</body>
</html>

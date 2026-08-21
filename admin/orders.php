<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

if (isset($_GET['action']) && $_GET['action'] === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $orderId = intval($_POST['order_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $field = sanitize($_POST['field'] ?? 'order_status');
    if ($orderId && in_array($field, ['order_status','payment_status']) && $status) {
        $stmt = $conn->prepare("UPDATE orders SET $field = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        echo json_encode(['success' => true]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
flashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Fashion Store Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><h2><span>&#128090;</span> Admin</h2></div>
            <nav class="sidebar-nav">
                <a href="index.php">&#128202; Dashboard</a>
                <a href="products.php">&#128093; Products</a>
                <a href="orders.php" class="active">&#128230; Orders</a>
            </nav>
            <div class="sidebar-footer"><a href="logout.php">&#128682; Logout</a></div>
        </aside>

        <main class="main-content">
            <div class="top-bar"><h1>Order Management</h1></div>

            <div class="card">
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders->fetch_assoc()): 
                                $items = $conn->query("SELECT * FROM order_items WHERE order_id = " . $order['id']);
                            ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                    <small style="color:var(--admin-text-light);"><?php echo htmlspecialchars($order['customer_address']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($order['customer_email']); ?><br><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                <td>
                                    <?php while ($item = $items->fetch_assoc()): ?>
                                    <div style="margin-bottom:4px;">
                                        <?php echo htmlspecialchars($item['product_name']); ?> 
                                        <span class="badge badge-info"><?php echo $item['size']; ?></span> 
                                        x<?php echo $item['quantity']; ?>
                                    </div>
                                    <?php endwhile; ?>
                                </td>
                                <td><strong>$<?php echo number_format($order['total_amount'],2); ?></strong></td>
                                <td>
                                    <span class="badge badge-<?php echo $order['payment_status']=='Completed'?'success':'warning'; ?>">
                                        <?php echo $order['payment_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <select class="status-select" data-order-id="<?php echo $order['id']; ?>" data-field="order_status" style="padding:4px 8px;border-radius:4px;border:1px solid var(--admin-border);">
                                        <option value="Processing" <?php echo $order['order_status']=='Processing'?'selected':''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order['order_status']=='Shipped'?'selected':''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order['order_status']=='Delivered'?'selected':''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order['order_status']=='Cancelled'?'selected':''; ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">&#9776;</button>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

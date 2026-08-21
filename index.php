<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$totalProducts = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$totalOrders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT SUM(total_amount) as s FROM orders WHERE payment_status='Completed'")->fetch_assoc()['s'] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) as c FROM orders WHERE order_status='Processing'")->fetch_assoc()['c'];
$recentOrders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fashion Store Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><span>&#128090;</span> Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="active">&#128202; Dashboard</a>
                <a href="products.php">&#128093; Products</a>
                <a href="orders.php">&#128230; Orders</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php">&#128682; Logout (<?php echo $_SESSION['admin_username']; ?>)</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['admin_username'],0,1)); ?></div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">&#128093;</div>
                    <div class="stat-info"><h3>Products</h3><p><?php echo $totalProducts; ?></p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">&#128230;</div>
                    <div class="stat-info"><h3>Total Orders</h3><p><?php echo $totalOrders; ?></p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">&#128176;</div>
                    <div class="stat-info"><h3>Revenue</h3><p>$<?php echo number_format($totalRevenue,2); ?></p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">&#9200;</div>
                    <div class="stat-info"><h3>Pending</h3><p><?php echo $pendingOrders; ?></p></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>Recent Orders</h2><a href="orders.php" class="btn btn-sm btn-secondary">View All</a></div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recentOrders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'],2); ?></td>
                                <td><span class="badge badge-<?php echo $order['order_status']=='Processing'?'warning':($order['order_status']=='Delivered'?'success':'info'); ?>"><?php echo $order['order_status']; ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
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

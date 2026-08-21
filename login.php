<?php
require_once 'includes/functions.php';

if (isAdmin()) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('admin/index.php');
        } else {
            $error = 'Invalid password.';
        }
    } else {
        $error = 'Invalid username.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Fashion Store</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="logo" style="display:flex;justify-content:center;font-size:1.5rem;margin-bottom:var(--space-xl);">
            <span>&#128090;</span> Fashion<span>Store</span>
        </div>
        <h1 style="text-align:center;margin-bottom:var(--space-xl);font-size:1.25rem;">Admin Login</h1>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%;">Login</button>
        </form>
        <p style="text-align:center;margin-top:var(--space-lg);font-size:0.875rem;color:var(--admin-text-light);">
            Default: admin / admin123
        </p>
    </div>
</body>
</html>

<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$message = '';
$editProduct = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = sanitize($_POST['name']);
    $brand = sanitize($_POST['brand']);
    $category = sanitize($_POST['category']);
    $type = sanitize($_POST['type']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $sizes = sanitize($_POST['sizes']);
    $color = sanitize($_POST['color']);
    $stock = intval($_POST['stock']);
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $upload = uploadImage($_FILES['image']);
        if ($upload['success']) {
            $image = $upload['filename'];
        } else {
            $message = $upload['message'];
        }
    }

    if (empty($message)) {
        if ($id > 0) {
            if ($image) {
                $stmt = $conn->prepare("UPDATE products SET name=?, brand=?, category=?, type=?, description=?, price=?, sizes=?, color=?, stock=?, image=? WHERE id=?");
                $stmt->bind_param("sssssdsssii", $name, $brand, $category, $type, $description, $price, $sizes, $color, $stock, $image, $id);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name=?, brand=?, category=?, type=?, description=?, price=?, sizes=?, color=?, stock=? WHERE id=?");
                $stmt->bind_param("sssssdsssi", $name, $brand, $category, $type, $description, $price, $sizes, $color, $stock, $id);
            }
            $stmt->execute();
            setFlash('Product updated successfully!', 'success');
        } else {
            if (empty($image)) $image = 'default.jpg';
            $stmt = $conn->prepare("INSERT INTO products (name, brand, category, type, description, price, sizes, color, stock, image) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssdssis", $name, $brand, $category, $type, $description, $price, $sizes, $color, $stock, $image);
            $stmt->execute();
            setFlash('Product added successfully!', 'success');
        }
        redirect('products.php');
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $id");
    setFlash('Product deleted.', 'info');
    redirect('products.php');
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM products WHERE id = $id");
    $editProduct = $res->fetch_assoc();
}

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
flashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Fashion Store Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header"><h2><span>&#128090;</span> Admin</h2></div>
            <nav class="sidebar-nav">
                <a href="index.php">&#128202; Dashboard</a>
                <a href="products.php" class="active">&#128093; Products</a>
                <a href="orders.php">&#128230; Orders</a>
            </nav>
            <div class="sidebar-footer"><a href="logout.php">&#128682; Logout</a></div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <h1>Inventory Management</h1>
                <button class="btn btn-primary" onclick="openModal()">+ Add Product</button>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-error"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr><th>Image</th><th>Name</th><th>Brand</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $products->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($p['image']) && file_exists("../uploads/" . $p['image'])): ?>
                                        <img src="../uploads/<?php echo $p['image']; ?>" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                    <?php else: ?>
                                        <div style="width:50px;height:50px;background:#eee;border-radius:4px;display:flex;align-items:center;justify-content:center;">&#128090;</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['brand']); ?></td>
                                <td><span class="badge badge-info"><?php echo $p['category']; ?></span></td>
                                <td>$<?php echo number_format($p['price'],2); ?></td>
                                <td><?php echo $p['stock']; ?></td>
                                <td>
                                    <a href="products.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger delete-btn">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal-overlay <?php echo ($editProduct || isset($_GET['add'])) ? 'active' : ''; ?>" id="productModal">
        <div class="modal">
            <div class="modal-header">
                <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($editProduct): ?>
                    <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group"><label>Name *</label><input type="text" name="name" required value="<?php echo $editProduct['name'] ?? ''; ?>"></div>
                        <div class="form-group"><label>Brand *</label><input type="text" name="brand" required value="<?php echo $editProduct['brand'] ?? ''; ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="Men" <?php echo ($editProduct['category']??'')=='Men'?'selected':''; ?>>Men</option>
                                <option value="Women" <?php echo ($editProduct['category']??'')=='Women'?'selected':''; ?>>Women</option>
                                <option value="Kids" <?php echo ($editProduct['category']??'')=='Kids'?'selected':''; ?>>Kids</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Type *</label><input type="text" name="type" required placeholder="Shirts, Trousers, Dresses" value="<?php echo $editProduct['type'] ?? ''; ?>"></div>
                    </div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?php echo $editProduct['description'] ?? ''; ?></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label>Price *</label><input type="number" step="0.01" name="price" required value="<?php echo $editProduct['price'] ?? ''; ?>"></div>
                        <div class="form-group"><label>Stock *</label><input type="number" name="stock" required value="<?php echo $editProduct['stock'] ?? '0'; ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Sizes (comma separated) *</label><input type="text" name="sizes" required placeholder="S, M, L, XL" value="<?php echo $editProduct['sizes'] ?? 'S, M, L, XL'; ?>"></div>
                        <div class="form-group"><label>Color *</label><input type="text" name="color" required value="<?php echo $editProduct['color'] ?? ''; ?>"></div>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" id="product-image" accept="image/*">
                        <div class="image-preview" id="image-preview">
                            <?php if ($editProduct && !empty($editProduct['image']) && file_exists("../uploads/" . $editProduct['image'])): ?>
                                <img src="../uploads/<?php echo $editProduct['image']; ?>">
                            <?php else: ?>
                                <span style="color:#999;">No image</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="display:flex;gap:var(--space-md);justify-content:flex-end;margin-top:var(--space-xl);">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">&#9776;</button>
    <script>
        function openModal() { document.getElementById('productModal').classList.add('active'); }
        function closeModal() { 
            document.getElementById('productModal').classList.remove('active'); 
            <?php if ($editProduct): ?>window.location.href='products.php';<?php endif; ?>
        }
    </script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>

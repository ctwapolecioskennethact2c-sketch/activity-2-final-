<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = adminOnly();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
    $price = $_POST['price'] ?? '';
    if ($name === '')
        $error = 'Product name is required.';
    elseif ($quantity === false || $quantity < 0)
        $error = 'Quantity must be zero or greater.';
    elseif (!is_numeric($price) || (float) $price < 0 || !preg_match('/^\d{1,7}(\.\d{1,3})?$/', $price))
        $error = 'Enter a valid price with up to 3 decimal places.';
    else {
        $stmt = $conn->prepare("INSERT INTO products(name,description,quantity,price,created_by) VALUES(?,?,?,?,?)");
        $stmt->bind_param("ssidi", $name, $description, $quantity, $price, $admin['id']);
        $stmt->execute();
        $stmt->close();
        redirectTo('/admin/products.php?msg=Product%20added%20successfully');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body><?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    <main class="container narrow">
        <div class="page-heading">
            <div>
                <p class="eyebrow">PRODUCT MANAGEMENT</p>
                <h1>Add product</h1>
            </div>
        </div><?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST" class="panel form-grid"><input type="hidden" name="csrf_token"
                value="<?= e(csrfToken()) ?>"><label>Product name<input name="name" maxlength="150"
                    required></label><label>Description<textarea name="description"
                    rows="4"></textarea></label><label>Quantity<input type="number" name="quantity" min="0" step="1"
                    required></label><label>Price<input type="number" name="price" min="0" step="0.001"
                    required></label>
            <div class="form-actions"><button class="btn">Save product</button><a class="btn secondary"
                    href="<?= BASE_URL ?>/admin/products.php">Cancel</a></div>
        </form>
    </main>
</body>

</html>
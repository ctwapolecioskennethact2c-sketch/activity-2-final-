<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = adminOnly();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Invalid product ID.');
}
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$product) {
    http_response_code(404);
    exit('Product not found.');
}
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
        $stmt = $conn->prepare("UPDATE products SET name=?,description=?,quantity=?,price=? WHERE id=?");
        $stmt->bind_param("ssidi", $name, $description, $quantity, $price, $id);
        $stmt->execute();
        $stmt->close();
        redirectTo('/admin/products.php?msg=Product%20updated%20successfully');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Product | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body><?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    <main class="container narrow">
        <div class="page-heading">
            <div>
                <p class="eyebrow">PRODUCT MANAGEMENT</p>
                <h1>Edit product</h1>
            </div>
        </div><?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST" class="panel form-grid"><input type="hidden" name="csrf_token"
                value="<?= e(csrfToken()) ?>"><label>Product name<input name="name" maxlength="150" required
                    value="<?= e($product['name']) ?>"></label><label>Description<textarea name="description"
                    rows="4"><?= e($product['description']) ?></textarea></label><label>Quantity<input type="number"
                    name="quantity" min="0" step="1" required
                    value="<?= e($product['quantity']) ?>"></label><label>Price<input type="number" name="price" min="0"
                    step="0.001" required value="<?= e($product['price']) ?>"></label>
            <div class="form-actions"><button class="btn">Update product</button><a class="btn secondary"
                    href="<?= BASE_URL ?>/admin/products.php">Cancel</a></div>
        </form>
    </main>
</body>

</html>
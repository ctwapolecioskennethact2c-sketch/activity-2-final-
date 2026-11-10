<?php
require_once __DIR__ . '/../includes/auth.php';
userOnly();
$result = $conn->query("SELECT id,name,description,quantity,price,created_at FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Browse Products | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body><?php include __DIR__ . '/../includes/user_nav.php'; ?>
    <main class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">PRODUCT CATALOG</p>
                <h1>Products</h1>
                <p class="muted">View product details and quantities.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody><?php while ($p = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($p['name']) ?></td>
                            <td><?= e($p['description']) ?></td>
                            <td><?= (int) $p['quantity'] ?></td>
                            <td>₱<?= number_format((float) $p['price'], 3) ?></td>
                        </tr><?php endwhile; ?><?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="4">No products available.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = adminOnly();
$userCount = (int) $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$productCount = (int) $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    <main class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">ADMIN PANEL</p>
                <h1>Welcome, <?= e($admin['username']) ?>!</h1>
                <p class="muted">Manage users and products in TECHHUB.</p>
            </div>
        </div>
        <div class="stats">
            <section class="stat-card"><span>Total users</span><strong><?= $userCount ?></strong><a
                    href="<?= BASE_URL ?>/admin/users.php">Manage users →</a></section>
            <section class="stat-card"><span>Total products</span><strong><?= $productCount ?></strong><a
                    href="<?= BASE_URL ?>/admin/products.php">Manage products →</a></section>
        </div>
        <div class="panel">
            <h2>Administrator access</h2>
            <p>You can manage user accounts, change account status, and perform create, read, update, and delete
                operations on products.</p>
        </div>
    </main>
</body>

</html>
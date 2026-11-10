<?php
require_once __DIR__ . '/../includes/auth.php';
$user = userOnly();
$count = (int) $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/user_nav.php'; ?>
    <main class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">USER DASHBOARD</p>
                <h1>Welcome, <?= e($user['username']) ?>!</h1>
                <p class="muted">Browse the products available in TECHHUB.</p>
            </div>
        </div>
        <div class="stats">
            <section class="stat-card"><span>Available product records</span><strong><?= $count ?></strong><a
                    href="<?= BASE_URL ?>/user/products.php">Browse products →</a></section>
        </div>
        <div class="panel">
            <h2>Your account</h2>
            <p>Role: <span class="pill">user</span> &nbsp; Status: <span class="pill active">active</span></p>
            <p class="muted">You can view product information. Only administrators can add, edit, or delete products and
                manage accounts.</p>
        </div>
    </main>
</body>

</html>
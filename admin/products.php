<?php
require_once __DIR__ . '/../includes/auth.php';
adminOnly();
$result = $conn->query("SELECT p.*,u.username AS creator FROM products p LEFT JOIN users u ON p.created_by=u.id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body><?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    <main class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">PRODUCT MANAGEMENT</p>
                <h1>Products</h1>
                <p class="muted">Create, view, update, and delete products.</p>
            </div><a class="btn" href="<?= BASE_URL ?>/admin/add_product.php">+ Add product</a>
        </div><?php if (isset($_GET['msg'])): ?>
            <div class="alert success"><?= e($_GET['msg']) ?></div><?php endif; ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Created by</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody><?php while ($p = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int) $p['id'] ?></td>
                            <td><?= e($p['name']) ?></td>
                            <td><?= e($p['description']) ?></td>
                            <td><?= (int) $p['quantity'] ?></td>
                            <td>₱<?= number_format((float) $p['price'], 3) ?></td>
                            <td><?= e($p['creator'] ?? 'Deleted user') ?></td>
                            <td class="actions"><a
                                    href="<?= BASE_URL ?>/admin/edit_product.php?id=<?= (int) $p['id'] ?>">Edit</a>
                                <form method="POST" action="<?= BASE_URL ?>/admin/delete_product.php" class="inline-form"
                                    onsubmit="return confirm('Delete this product?');"><input type="hidden"
                                        name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="id"
                                        value="<?= (int) $p['id'] ?>"><button class="link-button danger-text">Delete</button>
                                </form>
                            </td>
                        </tr><?php endwhile; ?><?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="7">No products yet. Click Add product to create one.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
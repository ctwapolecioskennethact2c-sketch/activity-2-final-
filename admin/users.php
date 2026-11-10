<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = adminOnly();
$result = $conn->query("SELECT id, username, role, status, created_at FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    <main class="container">
        <div class="page-heading">
            <div>
                <p class="eyebrow">USER MANAGEMENT</p>
                <h1>Users</h1>
                <p class="muted">View, add, edit, block, activate, or delete accounts.</p>
            </div><a class="btn" href="<?= BASE_URL ?>/admin/add_user.php">+ Add user</a>
        </div>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert success"><?= e($_GET['msg']) ?></div><?php endif; ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int) $row['id'] ?></td>
                            <td><?= e($row['username']) ?></td>
                            <td><span class="pill"><?= e($row['role']) ?></span></td>
                            <td><span
                                    class="pill <?= $row['status'] === 'active' ? 'active' : 'blocked' ?>"><?= e($row['status']) ?></span>
                            </td>
                            <td><?= e($row['created_at']) ?></td>
                            <td class="actions">
                                <a href="<?= BASE_URL ?>/admin/edit_user.php?id=<?= (int) $row['id'] ?>">Edit</a>
                                <form method="POST" action="<?= BASE_URL ?>/admin/toggle_user.php" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input
                                        type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="link-button"
                                        type="submit"><?= $row['status'] === 'active' ? 'Block' : 'Activate' ?></button></form>
                                <?php if ((int) $row['id'] !== (int) $admin['id']): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/delete_user.php" class="inline-form"
                                        onsubmit="return confirm('Delete this user? Products created by this user will remain.');">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input
                                            type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button
                                            class="link-button danger-text" type="submit">Delete</button></form>
                                <?php else: ?><span class="muted">Current account</span><?php endif; ?>
                            </td>
                        </tr><?php endwhile; ?>
                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="6">No users found.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>
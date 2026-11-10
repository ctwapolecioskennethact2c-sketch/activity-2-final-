<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = adminOnly();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Invalid user ID.');
}
$stmt = $conn->prepare("SELECT id,username,role,status FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$target = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$target) {
    http_response_code(404);
    exit('User not found.');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? '';
    $status = $_POST['status'] ?? '';
    $newPassword = $_POST['password'] ?? '';
    if (strlen($username) < 3 || strlen($username) > 50)
        $error = 'Username must be 3 to 50 characters.';
    elseif (!in_array($role, ['admin', 'user'], true) || !in_array($status, ['active', 'blocked'], true))
        $error = 'Invalid role or status.';
    elseif ($id === (int) $admin['id'] && ($role !== 'admin' || $status !== 'active'))
        $error = 'You cannot remove your own admin role or block your own account.';
    elseif ($newPassword !== '' && strlen($newPassword) < 8)
        $error = 'New password must be at least 8 characters.';
    else {
        try {
            if ($newPassword !== '') {
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username=?,role=?,status=?,password=? WHERE id=?");
                $stmt->bind_param("ssssi", $username, $role, $status, $hash, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=?,role=?,status=? WHERE id=?");
                $stmt->bind_param("sssi", $username, $role, $status, $id);
            }
            $stmt->execute();
            $stmt->close();
            redirectTo('/admin/users.php?msg=User%20updated%20successfully');
        } catch (mysqli_sql_exception $ex) {
            $error = $ex->getCode() === 1062 ? 'Username already exists.' : 'Could not update user.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body><?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    <main class="container narrow">
        <div class="page-heading">
            <div>
                <p class="eyebrow">USER MANAGEMENT</p>
                <h1>Edit user</h1>
            </div>
        </div><?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form class="panel form-grid" method="POST"><input type="hidden" name="csrf_token"
                value="<?= e(csrfToken()) ?>"><label>Username<input name="username" maxlength="50" required
                    value="<?= e($target['username']) ?>"></label><label>New password <span class="muted">(leave blank to
                    keep current)</span><input type="password" name="password" minlength="8"></label><label>Role<select
                    name="role">
                    <option value="user" <?= $target['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $target['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select></label><label>Status<select name="status">
                    <option value="active" <?= $target['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="blocked" <?= $target['status'] === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                </select></label>
            <div class="form-actions"><button class="btn">Update user</button><a class="btn secondary"
                    href="<?= BASE_URL ?>/admin/users.php">Cancel</a></div>
        </form>
    </main>
</body>

</html>
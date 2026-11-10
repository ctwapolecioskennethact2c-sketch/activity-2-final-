<?php
require_once __DIR__ . '/../includes/auth.php';
adminOnly();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $status = $_POST['status'] ?? 'active';
    if (strlen($username) < 3 || strlen($username) > 50)
        $error = 'Username must be 3 to 50 characters.';
    elseif (strlen($password) < 8)
        $error = 'Password must be at least 8 characters.';
    elseif (!in_array($role, ['admin', 'user'], true) || !in_array($status, ['active', 'blocked'], true))
        $error = 'Invalid role or status.';
    else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username,password,role,status) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $username, $hash, $role, $status);
            $stmt->execute();
            $stmt->close();
            redirectTo('/admin/users.php?msg=User%20added%20successfully');
        } catch (mysqli_sql_exception $ex) {
            $error = $ex->getCode() === 1062 ? 'Username already exists.' : 'Could not add user.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add User | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body><?php include __DIR__ . '/../includes/admin_nav.php'; ?>
    <main class="container narrow">
        <div class="page-heading">
            <div>
                <p class="eyebrow">USER MANAGEMENT</p>
                <h1>Add user</h1>
            </div>
        </div><?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form class="panel form-grid" method="POST"><input type="hidden" name="csrf_token"
                value="<?= e(csrfToken()) ?>"><label>Username<input name="username" maxlength="50"
                    required></label><label>Temporary password<input type="password" name="password" minlength="8"
                    required></label><label>Role<select name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select></label><label>Status<select name="status">
                    <option value="active">Active</option>
                    <option value="blocked">Blocked</option>
                </select></label>
            <div class="form-actions"><button class="btn">Save user</button><a class="btn secondary"
                    href="<?= BASE_URL ?>/admin/users.php">Cancel</a></div>
        </form>
    </main>
</body>

</html>
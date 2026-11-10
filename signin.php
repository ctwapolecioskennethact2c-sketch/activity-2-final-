<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password, role, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $account = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$account || !password_verify($password, $account['password'])) {
        $error = 'Incorrect username or password.';
    } elseif ($account['status'] !== 'active') {
        $error = 'Your account is blocked. Contact the administrator.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $account['id'];
        $_SESSION['role'] = $account['role'];
        redirectTo($account['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body class="auth-page">
    <main class="auth-card">
        <div class="brand">TECH<span>HUB</span></div>
        <h1>Welcome back</h1>
        <p class="muted">Sign in to continue to TECHHUB.</p>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert success">Registration successful. You can now sign in.</div><?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <label>Username</label><input name="username" required autocomplete="username">
            <label>Password</label><input type="password" name="password" required autocomplete="current-password">
            <button class="btn full" type="submit">Sign in</button>
        </form>
        <p class="center">Don't have an account? <a href="<?= BASE_URL ?>/register.php">Register</a></p>
    </main>
</body>

</html>
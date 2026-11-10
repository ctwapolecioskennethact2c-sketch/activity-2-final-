<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'Username must be 3 to 50 characters.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            // Public registration can only create regular user accounts.
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, 'user', 'active')");
            $stmt->bind_param("ss", $username, $hash);
            $stmt->execute();
            $stmt->close();
            redirectTo('/signin.php?registered=1');
        } catch (mysqli_sql_exception $ex) {
            $error = $ex->getCode() === 1062 ? 'That username is already taken.' : 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | TECHHUB</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>

<body class="auth-page">
    <main class="auth-card">
        <div class="brand">TECH<span>HUB</span></div>
        <h1>Create account</h1>
        <p class="muted">Register as a regular user.</p>
        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <label>Username</label><input name="username" maxlength="50" required
                value="<?= e($_POST['username'] ?? '') ?>">
            <label>Password</label><input type="password" name="password" minlength="8" required>
            <label>Confirm password</label><input type="password" name="confirm_password" minlength="8" required>
            <button class="btn full" type="submit">Create account</button>
        </form>
        <p class="center">Already registered? <a href="<?= BASE_URL ?>/signin.php">Sign in</a></p>
    </main>
</body>

</html>
<?php
require_once __DIR__ . '/includes/auth.php';
$user = currentUser();
if ($user) {
    redirectTo($user['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php');
}
redirectTo('/signin.php');
?>
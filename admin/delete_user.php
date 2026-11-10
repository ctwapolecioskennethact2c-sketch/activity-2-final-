<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = adminOnly();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
verifyCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id === (int) $admin['id'])
    redirectTo('/admin/users.php?msg=Invalid%20operation');
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
redirectTo('/admin/users.php?msg=User%20deleted');

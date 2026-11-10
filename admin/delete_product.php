<?php
require_once __DIR__ . '/../includes/auth.php';
adminOnly();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
verifyCsrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id)
    redirectTo('/admin/products.php?msg=Invalid%20product%20ID');
$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
redirectTo('/admin/products.php?msg=Product%20deleted');

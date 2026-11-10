<?php
require_once __DIR__ . '/../database.php';

function redirectTo(string $path): void
{
    header("Location: " . BASE_URL . $path);
    exit;
}

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Invalid request token. Please go back and try again.');
    }
}

function currentUser(): ?array
{
    global $conn;
    if (empty($_SESSION['user_id']))
        return null;

    $stmt = $conn->prepare("SELECT id, username, role, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user || $user['status'] !== 'active') {
        $_SESSION = [];
        session_destroy();
        return null;
    }

    $_SESSION['role'] = $user['role'];
    return $user;
}

function requireLogin(): array
{
    $user = currentUser();
    if (!$user)
        redirectTo('/signin.php');
    return $user;
}

function requireRole(string $role): array
{
    $user = requireLogin();
    if ($user['role'] !== $role) {
        http_response_code(403);
        exit('Access denied. You do not have permission to view this page.');
    }
    return $user;
}

function adminOnly(): array
{
    return requireRole('admin');
}
function userOnly(): array
{
    return requireRole('user');
}

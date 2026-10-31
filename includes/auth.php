<?php
// includes/auth.php - authentication helpers
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

session_start();

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id'] ?? null);
}

function currentUser(): ?array {
    return isLoggedIn() ? [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'full_name' => $_SESSION['full_name'] ?? '',
    ] : null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/Activity_1/index.php';
        header('Location: /Activity_1/auth/login.php?msg=' . urlencode('Please log in to continue.'));
        exit;
    }
}

function loginUser(array $user) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'] ?? '';
    $_SESSION['logged_in_at'] = time();
}

function logoutUser() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function findUserByUsername(string $username): ?array {
    global $conn;
    $sql = "SELECT user_id, username, email, password_hash, full_name, is_active FROM users WHERE username = ?";
    $stmt = sqlsrv_query($conn, $sql, [$username]);
    if ($stmt === false) {
        return null;
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    return $row ?: null;
}

function findUserByEmail(string $email): ?array {
    global $conn;
    $sql = "SELECT user_id, username, email, password_hash, full_name, is_active FROM users WHERE email = ?";
    $stmt = sqlsrv_query($conn, $sql, [$email]);
    if ($stmt === false) {
        return null;
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    return $row ?: null;
}

function createUser(string $username, string $email, string $password, string $full_name = ''): bool {
    global $conn;
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)";
    $stmt = sqlsrv_query($conn, $sql, [$username, $email, $passwordHash, $full_name !== '' ? $full_name : null]);
    if ($stmt === false) {
        return false;
    }
    sqlsrv_free_stmt($stmt);
    return true;
}

function usernameExists(string $username): bool {
    return findUserByUsername($username) !== null;
}

function emailExists(string $email): bool {
    return findUserByEmail($email) !== null;
}

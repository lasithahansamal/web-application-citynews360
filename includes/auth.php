<?php
// includes/auth.php - Session Management and Authorization Helpers

function startAppSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startAppSession();
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    startAppSession();
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
}

function getCurrentUser() {
    startAppSession();
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
            'full_name' => $_SESSION['full_name'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? 'USER'
        ];
    }
    return null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?msg=please_login');
        exit;
    }
}

function requireAdmin() {
    startAppSession();
    if (!isAdmin()) {
        header('Location: login.php?error=unauthorized');
        exit;
    }
}

<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please login first.';
        redirect('auth/login.php');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION[$type] = $message;
}

function get_flash(string $type): ?string
{
    if (!isset($_SESSION[$type])) {
        return null;
    }

    $message = $_SESSION[$type];
    unset($_SESSION[$type]);

    return $message;
}

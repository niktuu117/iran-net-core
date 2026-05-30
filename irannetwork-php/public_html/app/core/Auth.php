<?php
declare(strict_types=1);

/**
 * Auth skeleton — fully implemented in Phase 2.
 */
class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['user']    = $user;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function requireLogin(string $redirect = '/admin/login'): void
    {
        if (!self::check()) {
            header('Location: ' . $redirect);
            exit;
        }
    }
}

<?php
declare(strict_types=1);

/**
 * CSRF helper — token per session.
 */
class Csrf
{
    public static function name(): string
    {
        return defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : '_csrf';
    }

    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        $n = htmlspecialchars(self::name(), ENT_QUOTES, 'UTF-8');
        $t = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="' . $n . '" value="' . $t . '">';
    }

    public static function verify(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['_csrf'])
            && hash_equals($_SESSION['_csrf'], $token);
    }

    /** Validate CSRF from $_POST; aborts with 419 if invalid. */
    public static function check(): void
    {
        $token = $_POST[self::name()] ?? null;
        if (!self::verify(is_string($token) ? $token : null)) {
            http_response_code(419);
            exit('CSRF token mismatch.');
        }
    }
}

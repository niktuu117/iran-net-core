<?php
declare(strict_types=1);

/**
 * Auth — handles admin login/logout via MySQL users table.
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

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function role(): string
    {
        $u = self::user();
        return (string)($u['role'] ?? '');
    }

    public static function isSuperAdmin(): bool { return self::role() === 'super_admin'; }
    public static function isAdmin(): bool      { return in_array(self::role(), ['super_admin','admin'], true); }
    public static function isEditor(): bool     { return in_array(self::role(), ['super_admin','admin','editor'], true); }

    /**
     * Permission gate. Centralised so views/middlewares share one source of truth.
     * - super_admin: everything
     * - admin:       content + settings
     * - editor:      content only
     */
    public static function can(string $permission): bool
    {
        $role = self::role();
        if ($role === 'super_admin') return true;

        $matrix = [
            'admin'  => ['manage_content','manage_settings','manage_media','manage_redirects','manage_seo','view_messages','manage_users_limited'],
            'editor' => ['manage_content','manage_media','view_messages'],
        ];
        // 'manage_users' is super_admin-only by default
        return in_array($permission, $matrix[$role] ?? [], true);
    }


    /**
     * Attempt to log in. Returns user array on success, null on failure.
     */
    public static function attempt(string $email, string $password): ?array
    {
        $user = Database::fetch(
            'SELECT id, name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1',
            [strtolower(trim($email))]
        );
        if (!$user || $user['status'] !== 'active') {
            return null;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }
        self::login($user);
        return $user;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        unset($user['password_hash']);
        $_SESSION['user_id'] = (int)$user['id'];
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

    public static function requireLogin(string $redirect = '/admin/login.php'): void
    {
        if (!self::check()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function requireAdmin(string $redirect = '/admin/login.php'): void
    {
        if (!self::check() || !self::isEditor()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function requirePermission(string $permission, string $redirect = '/admin/dashboard.php'): void
    {
        self::requireAdmin();
        if (!self::can($permission)) {
            flash('error','دسترسی غیرمجاز.');
            header('Location: ' . $redirect);
            exit;
        }
    }
}

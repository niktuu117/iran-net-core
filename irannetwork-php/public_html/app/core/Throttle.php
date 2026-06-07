<?php
declare(strict_types=1);

/**
 * Throttle — simple file-based rate limiter (per-IP, per-key).
 * Used to slow brute-force on login and abuse on the contact form.
 */
class Throttle
{
    private static function dir(): string
    {
        $d = defined('CACHE_DIR') ? CACHE_DIR : (__DIR__ . '/../../cache');
        $d .= '/throttle';
        if (!is_dir($d)) @mkdir($d, 0775, true);
        return $d;
    }

    private static function key(string $name): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return self::dir() . '/' . sha1($name . '|' . $ip) . '.json';
    }

    /** Returns seconds until allowed (0 if allowed now). */
    public static function check(string $name, int $maxAttempts, int $windowSeconds): int
    {
        $f = self::key($name);
        if (!is_file($f)) return 0;
        $d = @json_decode((string)@file_get_contents($f), true);
        if (!is_array($d)) return 0;
        if (($d['reset'] ?? 0) < time()) { @unlink($f); return 0; }
        if (($d['count'] ?? 0) >= $maxAttempts) return max(1, (int)$d['reset'] - time());
        return 0;
    }

    public static function hit(string $name, int $windowSeconds): void
    {
        $f = self::key($name);
        $d = is_file($f) ? @json_decode((string)@file_get_contents($f), true) : null;
        if (!is_array($d) || ($d['reset'] ?? 0) < time()) {
            $d = ['count' => 0, 'reset' => time() + $windowSeconds];
        }
        $d['count'] = (int)($d['count'] ?? 0) + 1;
        @file_put_contents($f, json_encode($d), LOCK_EX);
    }

    public static function clear(string $name): void { @unlink(self::key($name)); }
}

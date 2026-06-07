<?php
declare(strict_types=1);

/**
 * Cache — lightweight filesystem cache (no Redis required).
 * Phase 4 foundation for page / query caching.
 *
 * Usage:
 *   $val = Cache::remember('posts:home', 600, fn() => Database::fetchAll('...'));
 *   Cache::forget('posts:home');
 *   Cache::flush();           // clear everything
 *   Cache::flushTag('posts'); // clear by tag prefix
 */
class Cache
{
    private static function dir(): string
    {
        $d = defined('CACHE_DIR') ? CACHE_DIR : (__DIR__ . '/../../cache');
        if (!is_dir($d)) @mkdir($d, 0775, true);
        return $d;
    }

    private static function path(string $key): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_\-:.]/', '_', $key) ?? $key;
        return self::dir() . '/' . sha1($safe) . '.cache';
    }

    public static function get(string $key): mixed
    {
        $f = self::path($key);
        if (!is_file($f)) return null;
        $raw = @file_get_contents($f);
        if ($raw === false) return null;
        $data = @unserialize($raw);
        if (!is_array($data) || !isset($data['exp'], $data['val'])) return null;
        if ($data['exp'] > 0 && $data['exp'] < time()) { @unlink($f); return null; }
        return $data['val'];
    }

    public static function put(string $key, mixed $value, int $ttlSeconds = 600): bool
    {
        $payload = serialize(['exp' => $ttlSeconds > 0 ? time() + $ttlSeconds : 0, 'val' => $value, 'key' => $key]);
        return @file_put_contents(self::path($key), $payload, LOCK_EX) !== false;
    }

    public static function forget(string $key): void { @unlink(self::path($key)); }

    public static function remember(string $key, int $ttl, callable $cb): mixed
    {
        $hit = self::get($key);
        if ($hit !== null) return $hit;
        $val = $cb();
        self::put($key, $val, $ttl);
        return $val;
    }

    public static function flush(): void
    {
        foreach (glob(self::dir() . '/*.cache') ?: [] as $f) @unlink($f);
    }

    /** Clear cache entries whose key starts with the given prefix (e.g. "posts:"). */
    public static function flushTag(string $prefix): void
    {
        foreach (glob(self::dir() . '/*.cache') ?: [] as $f) {
            $raw = @file_get_contents($f);
            if ($raw === false) continue;
            $d = @unserialize($raw);
            if (is_array($d) && isset($d['key']) && str_starts_with((string)$d['key'], $prefix)) @unlink($f);
        }
    }
}

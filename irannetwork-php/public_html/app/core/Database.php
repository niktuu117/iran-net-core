<?php
declare(strict_types=1);

/**
 * PDO Database wrapper (singleton).
 * Used starting in Phase 2.
 */
class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        if (!defined('DB_HOST')) {
            throw new RuntimeException('Database config is missing. Create app/config/config.php.');
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4'
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        return self::$instance;
    }
}

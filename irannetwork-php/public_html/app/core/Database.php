<?php
declare(strict_types=1);

/**
 * PDO Database wrapper (singleton) + simple query helpers.
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
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, $charset);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$charset}_unicode_ci",
        ];
        self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
        return self::$instance;
    }

    public static function isConfigured(): bool
    {
        return defined('DB_HOST') && DB_NAME !== 'your_database_name';
    }

    /** Run a prepared statement and return PDOStatement. */
    public static function execute(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::execute($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::execute($sql, $params)->fetchAll();
    }

    public static function fetchColumn(string $sql, array $params = []): mixed
    {
        $val = self::execute($sql, $params)->fetchColumn();
        return $val === false ? null : $val;
    }

    public static function lastInsertId(): int
    {
        return (int) self::connection()->lastInsertId();
    }
}

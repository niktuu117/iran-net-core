<?php
declare(strict_types=1);

/**
 * BaseModel — minimal CRUD wrapper around a single table.
 * Subclasses just set $table and (optionally) $fillable.
 */
abstract class BaseModel
{
    protected string $table = '';
    /** @var string[] */
    protected array $fillable = [];

    protected function filter(array $data): array
    {
        if (empty($this->fillable)) return $data;
        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        return Database::fetchAll("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
    }

    public function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM `{$this->table}` WHERE id = ? LIMIT 1", [$id]);
    }

    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`" . ($where ? " WHERE {$where}" : '');
        return (int) Database::fetchColumn($sql, $params);
    }

    public function paginate(int $page = 1, int $perPage = 20, string $where = '', array $params = [], string $orderBy = 'id DESC'): array
    {
        $page    = max(1, $page);
        $perPage = max(1, $perPage);
        $offset  = ($page - 1) * $perPage;
        $whereSql = $where ? " WHERE {$where}" : '';
        $rows = Database::fetchAll(
            "SELECT * FROM `{$this->table}`{$whereSql} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $total = $this->count($where, $params);
        return [
            'data'     => $rows,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) ceil($total / $perPage),
        ];
    }

    public function create(array $data): int
    {
        $data = $this->filter($data);
        if (empty($data)) return 0;
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $cols);
        $sql = "INSERT INTO `{$this->table}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $placeholders) . ")";
        Database::execute($sql, $data);
        return Database::lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->filter($data);
        if (empty($data)) return false;
        $sets = [];
        foreach (array_keys($data) as $c) $sets[] = "`{$c}` = :{$c}";
        $sql = "UPDATE `{$this->table}` SET " . implode(',', $sets) . " WHERE id = :id";
        $data['id'] = $id;
        Database::execute($sql, $data);
        return true;
    }

    public function delete(int $id): bool
    {
        Database::execute("DELETE FROM `{$this->table}` WHERE id = ?", [$id]);
        return true;
    }
}

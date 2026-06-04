<?php
declare(strict_types=1);

class Redirect extends BaseModel
{
    protected string $table = 'redirects';
    protected array $fillable = ['old_url','new_url','status_code','is_active'];

    public function findActive(string $oldUrl): ?array
    {
        $row = Database::fetch(
            'SELECT * FROM redirects WHERE old_url = ? AND is_active = 1 LIMIT 1',
            [$oldUrl]
        );
        if ($row) {
            try { Database::execute('UPDATE redirects SET hits = hits + 1 WHERE id = ?', [(int)$row['id']]); }
            catch (Throwable $e) { /* ignore */ }
        }
        return $row;
    }
}

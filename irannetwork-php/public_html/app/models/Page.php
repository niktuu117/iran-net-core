<?php
declare(strict_types=1);

class Page extends BaseModel
{
    protected string $table = 'pages';
    protected array $fillable = ['title','slug','h1','content','status'];

    public function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM pages WHERE slug = ? LIMIT 1', [$slug]);
    }
}

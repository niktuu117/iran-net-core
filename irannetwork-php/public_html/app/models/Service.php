<?php
declare(strict_types=1);

class Service extends BaseModel
{
    protected string $table = 'services';
    protected array $fillable = ['title','slug','h1','excerpt','content','status','featured_image','featured_image_alt','sort_order'];

    public function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM services WHERE slug = ? LIMIT 1', [$slug]);
    }
}

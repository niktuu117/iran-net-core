<?php
declare(strict_types=1);

class Post extends BaseModel
{
    protected string $table = 'posts';
    protected array $fillable = [
        'title','slug','excerpt','content','status','featured','show_on_homepage',
        'published_at','scheduled_at','featured_image','featured_image_alt',
        'author_id','category_id'
    ];

    public function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM posts WHERE slug = ? LIMIT 1', [$slug]);
    }

    public function syncTags(int $postId, array $tagIds): void
    {
        Database::execute('DELETE FROM post_tags WHERE post_id = ?', [$postId]);
        foreach (array_unique(array_map('intval', $tagIds)) as $tid) {
            if ($tid > 0) Database::execute('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?,?)', [$postId, $tid]);
        }
    }

    public function syncServices(int $postId, array $serviceIds): void
    {
        Database::execute('DELETE FROM post_services WHERE post_id = ?', [$postId]);
        foreach (array_unique(array_map('intval', $serviceIds)) as $sid) {
            if ($sid > 0) Database::execute('INSERT IGNORE INTO post_services (post_id, service_id) VALUES (?,?)', [$postId, $sid]);
        }
    }

    public function getTagIds(int $postId): array
    {
        return array_map('intval', array_column(
            Database::fetchAll('SELECT tag_id FROM post_tags WHERE post_id = ?', [$postId]),
            'tag_id'
        ));
    }

    public function getServiceIds(int $postId): array
    {
        return array_map('intval', array_column(
            Database::fetchAll('SELECT service_id FROM post_services WHERE post_id = ?', [$postId]),
            'service_id'
        ));
    }
}

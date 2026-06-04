<?php
declare(strict_types=1);

class SeoMeta extends BaseModel
{
    protected string $table = 'seo_meta';
    protected array $fillable = [
        'entity_type','entity_id','seo_title','meta_description','focus_keyword','secondary_keywords',
        'canonical_url','robots_index','robots_follow','og_title','og_description','og_image',
        'twitter_title','twitter_description','twitter_image','schema_type','enable_schema',
        'include_in_sitemap','sitemap_priority','sitemap_changefreq','seo_score','readability_score'
    ];

    public function findFor(string $type, int $id): ?array
    {
        return Database::fetch('SELECT * FROM seo_meta WHERE entity_type = ? AND entity_id = ? LIMIT 1', [$type, $id]);
    }

    /** Insert-or-update by (entity_type, entity_id). */
    public function upsert(string $type, int $id, array $data): int
    {
        $existing = $this->findFor($type, $id);
        $data['entity_type'] = $type;
        $data['entity_id']   = $id;
        if ($existing) {
            $this->update((int)$existing['id'], $data);
            return (int)$existing['id'];
        }
        return $this->create($data);
    }

    /** All sitemap-included items for a given type. */
    public function sitemapItems(string $type): array
    {
        return Database::fetchAll(
            'SELECT * FROM seo_meta WHERE entity_type = ? AND include_in_sitemap = 1',
            [$type]
        );
    }
}

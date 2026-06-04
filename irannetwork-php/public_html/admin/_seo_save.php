<?php
/**
 * Standalone helper for saving SEO meta from $_POST['seo'].
 * Safe to require from save handlers — no HTML output.
 */
declare(strict_types=1);

if (!function_exists('seo_save_from_post')) {
function seo_save_from_post(string $entityType, int $entityId): void
{
    if ($entityId <= 0) return;
    $raw = $_POST['seo'] ?? [];
    if (!is_array($raw)) return;
    $data = [
        'seo_title'           => trim((string)($raw['seo_title'] ?? '')) ?: null,
        'meta_description'    => trim((string)($raw['meta_description'] ?? '')) ?: null,
        'focus_keyword'       => trim((string)($raw['focus_keyword'] ?? '')) ?: null,
        'secondary_keywords'  => trim((string)($raw['secondary_keywords'] ?? '')) ?: null,
        'canonical_url'       => trim((string)($raw['canonical_url'] ?? '')) ?: null,
        'robots_index'        => !empty($raw['robots_index']) ? 1 : 0,
        'robots_follow'       => !empty($raw['robots_follow']) ? 1 : 0,
        'og_title'            => trim((string)($raw['og_title'] ?? '')) ?: null,
        'og_description'      => trim((string)($raw['og_description'] ?? '')) ?: null,
        'og_image'            => trim((string)($raw['og_image'] ?? '')) ?: null,
        'twitter_title'       => trim((string)($raw['twitter_title'] ?? '')) ?: null,
        'twitter_description' => trim((string)($raw['twitter_description'] ?? '')) ?: null,
        'twitter_image'       => trim((string)($raw['twitter_image'] ?? '')) ?: null,
        'schema_type'         => trim((string)($raw['schema_type'] ?? '')) ?: null,
        'enable_schema'       => !empty($raw['enable_schema']) ? 1 : 0,
        'include_in_sitemap'  => !empty($raw['include_in_sitemap']) ? 1 : 0,
        'sitemap_priority'    => is_numeric($raw['sitemap_priority'] ?? null) ? (float)$raw['sitemap_priority'] : 0.5,
        'sitemap_changefreq'  => in_array($raw['sitemap_changefreq'] ?? '', ['always','hourly','daily','weekly','monthly','yearly','never'], true) ? $raw['sitemap_changefreq'] : 'weekly',
    ];
    (new SeoMeta())->upsert($entityType, $entityId, $data);
}
}

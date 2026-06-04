<?php
declare(strict_types=1);

/**
 * SeoController — sitemap.xml and robots.txt rendered dynamically.
 */
class SeoController
{
    public function sitemap(array $params = []): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        $items = [];
        $items[] = ['loc'=>site_url('/'),'changefreq'=>'weekly','priority'=>'1.0'];
        $items[] = ['loc'=>site_url('/services'),'changefreq'=>'weekly','priority'=>'0.9'];
        $items[] = ['loc'=>site_url('/blog'),'changefreq'=>'daily','priority'=>'0.9'];
        $items[] = ['loc'=>site_url('/contact'),'changefreq'=>'monthly','priority'=>'0.5'];

        $excludeIds = [];

        // Services
        $services = Database::fetchAll("SELECT id, slug, updated_at FROM services WHERE status='published'");
        foreach ($services as $s) {
            $m = (new SeoMeta())->findFor('service', (int)$s['id']);
            if ($m && !(int)$m['include_in_sitemap']) continue;
            $items[] = [
                'loc'=>site_url('/services/' . $s['slug']),
                'lastmod'=>date('c', strtotime($s['updated_at'])),
                'changefreq'=>$m['sitemap_changefreq'] ?? 'monthly',
                'priority'=>(string)($m['sitemap_priority'] ?? '0.8'),
            ];
        }
        // Posts
        $posts = Database::fetchAll("SELECT id, slug, updated_at FROM posts WHERE status='published'");
        foreach ($posts as $p) {
            $m = (new SeoMeta())->findFor('post', (int)$p['id']);
            if ($m && !(int)$m['include_in_sitemap']) continue;
            $items[] = [
                'loc'=>site_url('/blog/' . $p['slug']),
                'lastmod'=>date('c', strtotime($p['updated_at'])),
                'changefreq'=>$m['sitemap_changefreq'] ?? 'weekly',
                'priority'=>(string)($m['sitemap_priority'] ?? '0.7'),
            ];
        }
        // Pages
        $pages = Database::fetchAll("SELECT id, slug, updated_at FROM pages WHERE status='published'");
        $reserved = ['contact'];
        foreach ($pages as $p) {
            if (in_array($p['slug'], $reserved, true)) continue;
            $m = (new SeoMeta())->findFor('page', (int)$p['id']);
            if ($m && !(int)$m['include_in_sitemap']) continue;
            $items[] = [
                'loc'=>site_url('/' . $p['slug']),
                'lastmod'=>date('c', strtotime($p['updated_at'])),
                'changefreq'=>$m['sitemap_changefreq'] ?? 'monthly',
                'priority'=>(string)($m['sitemap_priority'] ?? '0.6'),
            ];
        }
        // Categories
        $cats = Database::fetchAll('SELECT id, slug, updated_at FROM categories');
        foreach ($cats as $c) {
            $m = (new SeoMeta())->findFor('category', (int)$c['id']);
            if ($m && !(int)$m['include_in_sitemap']) continue;
            $items[] = ['loc'=>site_url('/category/' . $c['slug']), 'changefreq'=>'weekly','priority'=>'0.5'];
        }
        // Tags
        $tags = Database::fetchAll('SELECT id, slug FROM tags');
        foreach ($tags as $t) {
            $m = (new SeoMeta())->findFor('tag', (int)$t['id']);
            if ($m && !(int)$m['include_in_sitemap']) continue;
            $items[] = ['loc'=>site_url('/tag/' . $t['slug']), 'changefreq'=>'weekly','priority'=>'0.4'];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($items as $it) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($it['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            if (!empty($it['lastmod']))    echo '    <lastmod>' . $it['lastmod'] . "</lastmod>\n";
            if (!empty($it['changefreq'])) echo '    <changefreq>' . $it['changefreq'] . "</changefreq>\n";
            if (!empty($it['priority']))   echo '    <priority>' . $it['priority'] . "</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
    }

    public function robots(array $params = []): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        $extra = (string)site_setting('robots_extra', '');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /uploads/private/\n";
        echo "Disallow: /app/\n";
        if ($extra !== '') echo $extra . "\n";
        echo "\nSitemap: " . site_url('/sitemap.xml') . "\n";
    }
}

<?php
declare(strict_types=1);

class BlogController extends Controller
{
    public function index(array $params = []): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 9;
        $where = "status='published' AND (published_at IS NULL OR published_at <= NOW())";
        $postModel = new Post();
        $paged = $postModel->paginate($page, $perPage, $where, [], 'COALESCE(published_at, created_at) DESC');

        $featured = Database::fetchAll(
            "SELECT id, title, slug, excerpt, featured_image, featured_image_alt FROM posts
             WHERE status='published' AND featured=1 LIMIT 3"
        );

        $seo = Seo::build(
            ['title'=>'مقالات و آموزش‌ها','content'=>''],
            (new SeoMeta())->findFor('page', 0),
            [
                'title'       => 'مقالات و آموزش‌ها | ایران نتورک',
                'description' => 'مقالات تخصصی شبکه، سرور، امنیت، ویپ و دیجیتال مارکتینگ — ایران نتورک.',
                'canonical'   => site_url('/blog'),
            ]
        );

        $this->view('public/blog-index', [
            'seo'      => $seo,
            'pageTitle'=> 'مقالات و آموزش‌ها | ایران نتورک',
            'paged'    => $paged,
            'featured' => $featured,
            'categories' => (new Category())->all('name ASC'),
            'schemas'  => [Seo::breadcrumbs([
                ['name'=>'خانه','url'=>site_url('/')],
                ['name'=>'مقالات','url'=>site_url('/blog')],
            ])],
        ]);
    }

    public function show(array $params): void
    {
        $slug = (string)($params['slug'] ?? '');
        $post = (new Post())->findBySlug($slug);
        if (!$post || $post['status'] !== 'published') {
            (new PagesController())->notFound(); return;
        }

        $author = $post['author_id'] ? Database::fetch('SELECT name FROM users WHERE id = ?', [(int)$post['author_id']]) : null;
        $category = $post['category_id'] ? Database::fetch('SELECT id, name, slug FROM categories WHERE id = ?', [(int)$post['category_id']]) : null;
        $tagIds = (new Post())->getTagIds((int)$post['id']);
        $tags = $tagIds ? Database::fetchAll('SELECT id, name, slug FROM tags WHERE id IN (' . implode(',', array_fill(0, count($tagIds), '?')) . ')', $tagIds) : [];
        $svcIds = (new Post())->getServiceIds((int)$post['id']);
        $services = $svcIds ? Database::fetchAll('SELECT id, title, slug FROM services WHERE id IN (' . implode(',', array_fill(0, count($svcIds), '?')) . ') AND status="published"', $svcIds) : [];
        $faqs = Database::fetchAll('SELECT question, answer FROM faqs WHERE post_id = ? AND is_active=1 ORDER BY sort_order ASC', [(int)$post['id']]);
        $related = $post['category_id'] ? Database::fetchAll(
            "SELECT id, title, slug, excerpt, featured_image FROM posts
             WHERE category_id = ? AND id <> ? AND status='published' ORDER BY published_at DESC LIMIT 3",
            [(int)$post['category_id'], (int)$post['id']]
        ) : [];

        $meta = (new SeoMeta())->findFor('post', (int)$post['id']);
        $seo = Seo::build($post, $meta, [
            'canonical' => site_url('/blog/' . $post['slug']),
            'title'     => ($post['title'] ?? '') . ' | ایران نتورک',
            'description'=> excerpt($post['excerpt'] ?? $post['content'] ?? '', 30),
            'image'     => $post['featured_image'] ?? null,
        ]);
        $seo['og_type'] = 'article';

        $schemas = [
            Seo::article(array_merge($post, ['author_name' => $author['name'] ?? null])),
            Seo::breadcrumbs([
                ['name'=>'خانه','url'=>site_url('/')],
                ['name'=>'مقالات','url'=>site_url('/blog')],
                ['name'=>$post['title'],'url'=>site_url('/blog/' . $post['slug'])],
            ]),
        ];
        if ($faqs) $schemas[] = Seo::faqPage($faqs);

        $this->view('public/blog-show', [
            'seo'=>$seo,'pageTitle'=>$post['title'],'post'=>$post,'author'=>$author,
            'category'=>$category,'tags'=>$tags,'services'=>$services,'faqs'=>$faqs,
            'related'=>$related,'schemas'=>$schemas,
        ]);
    }

    public function byCategory(array $params): void
    {
        $slug = (string)($params['slug'] ?? '');
        $cat = Database::fetch('SELECT * FROM categories WHERE slug = ?', [$slug]);
        if (!$cat) { (new PagesController())->notFound(); return; }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $paged = (new Post())->paginate($page, 9, "status='published' AND category_id = ?", [(int)$cat['id']], 'published_at DESC');

        $meta = (new SeoMeta())->findFor('category', (int)$cat['id']);
        $seo = Seo::build($cat, $meta, [
            'title'     => 'دسته: ' . $cat['name'] . ' | ایران نتورک',
            'description'=> $cat['description'] ?? ('مقالات دسته‌بندی ' . $cat['name']),
            'canonical' => site_url('/category/' . $cat['slug']),
        ]);

        $this->view('public/blog-list', [
            'seo'=>$seo,'pageTitle'=>'دسته: ' . $cat['name'],
            'paged'=>$paged,'heading'=>'دسته‌بندی: ' . $cat['name'],
            'description'=>$cat['description'] ?? '',
            'schemas'=>[Seo::breadcrumbs([
                ['name'=>'خانه','url'=>site_url('/')],
                ['name'=>'مقالات','url'=>site_url('/blog')],
                ['name'=>$cat['name'],'url'=>site_url('/category/'.$cat['slug'])],
            ])],
        ]);
    }

    public function byTag(array $params): void
    {
        $slug = (string)($params['slug'] ?? '');
        $tag = Database::fetch('SELECT * FROM tags WHERE slug = ?', [$slug]);
        if (!$tag) { (new PagesController())->notFound(); return; }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 9; $offset = ($page-1)*$perPage;
        $rows = Database::fetchAll(
            "SELECT p.* FROM posts p
             JOIN post_tags pt ON pt.post_id = p.id
             WHERE pt.tag_id = ? AND p.status='published'
             ORDER BY p.published_at DESC LIMIT {$perPage} OFFSET {$offset}",
            [(int)$tag['id']]
        );
        $total = (int)Database::fetchColumn(
            "SELECT COUNT(*) FROM posts p JOIN post_tags pt ON pt.post_id=p.id WHERE pt.tag_id=? AND p.status='published'",
            [(int)$tag['id']]
        );
        $paged = ['data'=>$rows,'total'=>$total,'page'=>$page,'per_page'=>$perPage,'pages'=>(int)ceil($total/$perPage)];

        $meta = (new SeoMeta())->findFor('tag', (int)$tag['id']);
        $seo = Seo::build($tag, $meta, [
            'title'     => 'برچسب: ' . $tag['name'] . ' | ایران نتورک',
            'description'=> 'مقالات با برچسب ' . $tag['name'],
            'canonical' => site_url('/tag/' . $tag['slug']),
        ]);

        $this->view('public/blog-list', [
            'seo'=>$seo,'pageTitle'=>'برچسب: ' . $tag['name'],
            'paged'=>$paged,'heading'=>'برچسب: ' . $tag['name'],
            'description'=>'',
            'schemas'=>[Seo::breadcrumbs([
                ['name'=>'خانه','url'=>site_url('/')],
                ['name'=>'مقالات','url'=>site_url('/blog')],
                ['name'=>$tag['name'],'url'=>site_url('/tag/'.$tag['slug'])],
            ])],
        ]);
    }
}

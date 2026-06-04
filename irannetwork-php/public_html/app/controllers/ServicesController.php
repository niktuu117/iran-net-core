<?php
declare(strict_types=1);

class ServicesController extends Controller
{
    public function index(array $params = []): void
    {
        $services = Database::fetchAll(
            "SELECT id, title, slug, excerpt, featured_image, featured_image_alt
             FROM services WHERE status='published' ORDER BY sort_order ASC, id ASC"
        );

        $meta = (new SeoMeta())->findFor('page', 0); // global default
        $seo = Seo::build(['title'=>'خدمات ایران نتورک','content'=>''], null, [
            'title'      => 'خدمات ایران نتورک | شبکه، سرور، امنیت و ویپ',
            'description'=> 'فهرست کامل خدمات تخصصی ایران نتورک: پشتیبانی شبکه، نصب و راه‌اندازی، امنیت، ویپ، سرور و دیجیتال مارکتینگ.',
            'canonical'  => site_url('/services'),
        ]);

        $this->view('public/services-index', [
            'seo'=>$seo,'pageTitle'=>'خدمات ایران نتورک','services'=>$services,
            'schemas'=>[Seo::breadcrumbs([
                ['name'=>'خانه','url'=>site_url('/')],
                ['name'=>'خدمات','url'=>site_url('/services')],
            ])],
        ]);
    }

    public function show(array $params): void
    {
        $slug = (string)($params['slug'] ?? '');
        $service = (new Service())->findBySlug($slug);
        if (!$service || $service['status'] !== 'published') {
            (new PagesController())->notFound(); return;
        }
        $faqs = Database::fetchAll('SELECT question, answer FROM faqs WHERE service_id = ? AND is_active=1 ORDER BY sort_order ASC', [(int)$service['id']]);
        $relatedPosts = Database::fetchAll(
            "SELECT p.id, p.title, p.slug, p.excerpt FROM posts p
             JOIN post_services ps ON ps.post_id=p.id
             WHERE ps.service_id=? AND p.status='published'
             ORDER BY p.published_at DESC LIMIT 4",
            [(int)$service['id']]
        );
        $otherServices = Database::fetchAll(
            "SELECT id, title, slug FROM services WHERE status='published' AND id<>? ORDER BY sort_order ASC LIMIT 6",
            [(int)$service['id']]
        );

        $meta = (new SeoMeta())->findFor('service', (int)$service['id']);
        $seo = Seo::build($service, $meta, [
            'title'      => ($service['title'] ?? '') . ' | ایران نتورک',
            'description'=> $service['excerpt'] ?? excerpt($service['content'] ?? '', 30),
            'canonical'  => site_url('/services/' . $service['slug']),
            'image'      => $service['featured_image'] ?? null,
        ]);

        $schemas = [
            Seo::service($service),
            Seo::breadcrumbs([
                ['name'=>'خانه','url'=>site_url('/')],
                ['name'=>'خدمات','url'=>site_url('/services')],
                ['name'=>$service['title'],'url'=>site_url('/services/' . $service['slug'])],
            ]),
        ];
        if ($faqs) $schemas[] = Seo::faqPage($faqs);

        $this->view('public/service-show', [
            'seo'=>$seo,'pageTitle'=>$service['title'],'service'=>$service,
            'faqs'=>$faqs,'relatedPosts'=>$relatedPosts,'otherServices'=>$otherServices,
            'schemas'=>$schemas,
        ]);
    }
}

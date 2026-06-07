<?php
declare(strict_types=1);

class PagesController extends Controller
{
    public function home(array $params = []): void
    {
        $featured = Database::fetchAll(
            "SELECT id, title, slug, excerpt, featured_image FROM posts
             WHERE status='published' AND (featured=1 OR show_on_homepage=1)
             ORDER BY published_at DESC LIMIT 3"
        );
        $services = Database::fetchAll(
            "SELECT title, slug, excerpt FROM services WHERE status='published'
             ORDER BY sort_order ASC LIMIT 8"
        );
        $seo = Seo::build(['title'=>'ایران نتورک'], null, [
            'title'=>'ایران نتورک | خدمات تخصصی شبکه، سرور و امنیت',
            'description'=>'ایران نتورک ارائه‌دهنده خدمات حرفه‌ای شبکه، پشتیبانی سرور، امنیت، ویپ و دیجیتال مارکتینگ.',
            'canonical'=>site_url('/'),
        ]);
        $this->view('public/home', [
            'seo'=>$seo,'pageTitle'=>'ایران نتورک',
            'featuredPosts'=>$featured,'homeServices'=>$services,
        ]);
    }

    /** Generic CMS page by slug (about, faq, rules, contact, custom). */
    public function dynamicPage(array $params): void
    {
        $slug = (string)($params['slug'] ?? '');
        // reserved slugs handled by their own routes
        if (in_array($slug, ['blog','services','admin','sitemap.xml','robots.txt','404','uploads','assets','contact'], true)) {
            $this->notFound(); return;
        }
        $page = (new Page())->findBySlug($slug);
        if (!$page || $page['status'] !== 'published') { $this->notFound(); return; }

        $faqs = Database::fetchAll('SELECT question, answer FROM faqs WHERE page_id=? AND is_active=1 ORDER BY sort_order ASC', [(int)$page['id']]);
        $meta = (new SeoMeta())->findFor('page', (int)$page['id']);
        $seo = Seo::build($page, $meta, [
            'title'      => ($page['title'] ?? '') . ' | ایران نتورک',
            'description'=> excerpt($page['content'] ?? '', 30),
            'canonical'  => site_url('/' . $page['slug']),
        ]);

        $schemas = [Seo::breadcrumbs([
            ['name'=>'خانه','url'=>site_url('/')],
            ['name'=>$page['title'],'url'=>site_url('/'.$page['slug'])],
        ])];
        if ($faqs) $schemas[] = Seo::faqPage($faqs);

        $this->view('public/page-show', [
            'seo'=>$seo,'pageTitle'=>$page['title'],'page'=>$page,'faqs'=>$faqs,'schemas'=>$schemas,
        ]);
    }

    public function contact(array $params = []): void
    {
        // Contact remains a dedicated view (form + info)
        $page = (new Page())->findBySlug('contact');
        $meta = $page ? (new SeoMeta())->findFor('page', (int)$page['id']) : null;
        $seo = Seo::build($page ?? ['title'=>'تماس با ما','content'=>''], $meta, [
            'title'=>'تماس با ایران نتورک | مشاوره و درخواست خدمات',
            'description'=>'برای دریافت مشاوره خدمات شبکه، سرور، امنیت و ویپ با کارشناسان ایران نتورک در ارتباط باشید.',
            'canonical'=>site_url('/contact'),
        ]);
        $this->view('public/contact', ['seo'=>$seo,'pageTitle'=>'تماس با ما']);
    }

    public function submitContact(array $params = []): void
    {
        if (!Csrf::verify($_POST[Csrf::name()] ?? null)) {
            flash('contact_error', 'توکن امنیتی نامعتبر است. لطفاً صفحه را تازه کنید.');
            redirect('/contact');
        }
        // Rate limit: 5 submissions / 10 minutes per IP
        $wait = Throttle::check('contact', 5, 600);
        if ($wait > 0) {
            flash('contact_error', 'تعداد ارسال‌ها زیاد بوده. لطفاً چند دقیقه دیگر تلاش کنید.');
            redirect('/contact');
        }
        Throttle::hit('contact', 600);

        $name    = trim((string)($_POST['name'] ?? ''));
        $phone   = trim((string)($_POST['phone'] ?? ''));
        $email   = trim((string)($_POST['email'] ?? ''));
        $service = trim((string)($_POST['service'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));
        // Honeypot — bots fill hidden fields
        if (!empty($_POST['website'] ?? '')) { redirect('/contact'); }

        $errors = [];
        if (mb_strlen($name) < 2 || mb_strlen($name) > 150)   $errors[] = 'نام را به‌درستی وارد کنید.';
        if (!preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) $errors[] = 'شماره تماس معتبر نیست.';
        if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190)) $errors[] = 'ایمیل معتبر نیست.';
        if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) $errors[] = 'متن پیام باید بین ۱۰ تا ۵۰۰۰ کاراکتر باشد.';

        if ($errors) {
            keep_old(compact('name','phone','email','service','message'));
            flash('contact_error', implode(' ', $errors));
            redirect('/contact');
        }
        try {
            if (Database::isConfigured()) {
                (new ContactMessage())->create([
                    'name'=>$name,'phone'=>$phone,
                    'email'=>$email !== '' ? $email : null,
                    'service'=>$service !== '' ? $service : null,
                    'message'=>$message,'status'=>'new',
                ]);
            }
            clear_old();
            flash('contact_success', 'پیام شما با موفقیت ارسال شد. به‌زودی با شما در ارتباط خواهیم بود.');
        } catch (Throwable $e) {
            flash('contact_error', 'ذخیره پیام ناموفق بود. لطفاً بعداً تلاش کنید.');
        }
        redirect('/contact');
    }

    public function notFound(array $params = []): void
    {
        http_response_code(404);
        $seo = Seo::build(['title'=>'یافت نشد','content'=>''], null, [
            'title'=>'صفحه یافت نشد | ایران نتورک',
            'description'=>'صفحه‌ای که به دنبال آن هستید پیدا نشد.',
            'canonical'=>site_url('/404'),
        ]);
        $seo['robots'] = 'noindex,follow';
        $this->view('public/404', ['seo'=>$seo,'pageTitle'=>'یافت نشد']);
    }
}

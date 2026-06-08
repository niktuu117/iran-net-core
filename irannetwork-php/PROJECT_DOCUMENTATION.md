# IranNetwork CMS — مستندات کامل توسعه (v1.1)

این فایل مرجع تکنیکال کامل پروژه است. هر توسعه‌دهنده‌ای که می‌خواهد CMS را گسترش دهد باید اول این سند را بخواند.

## فهرست
1. معماری
2. ساختار پوشه‌ها
3. Routing
4. Controllers / Models / Views
5. Database
6. Admin System
7. SEO System
8. Security
9. Cache
10. Deployment
11. Backup & Restore
12. Extending The CMS

---

## ۱) معماری

- **Stack**: PHP 8.1+ / MySQL 5.7+ یا MariaDB 10.4+ / PDO / Apache + .htaccess.
- **الگو**: MVC ساده، بدون فریم‌ورک، بدون Composer dependency.
- **Front Controller**: تمام درخواست‌ها به `public_html/index.php` می‌رسند که Router را اجرا می‌کند.
- **Auto-load**: SPL Autoload کلاس‌ها را از `app/core`, `app/controllers`, `app/models` بارگذاری می‌کند.

```
[Browser] → [.htaccess rewrite] → index.php → Router → Controller → Model → Database
                                                            ↓
                                                          View ← Layout
```

## ۲) ساختار پوشه‌ها

```
irannetwork-php/
├── public_html/                ← document_root در cPanel
│   ├── .htaccess               ← Rewrite + Security + Cache headers
│   ├── index.php               ← Front controller (Router bootstrap)
│   ├── admin/                  ← پنل مدیریت (PHP صفحه به صفحه)
│   │   ├── _bootstrap.php      ← session + autoload + config
│   │   ├── _layout.php         ← قالب RTL ادمین
│   │   ├── _seo_partial.php    ← فرم SEO قابل include
│   │   ├── _seo_save.php       ← منطق ذخیره seo_meta
│   │   ├── login.php / logout.php / setup.php
│   │   ├── posts/ services/ pages/ categories/ tags/ media/
│   │   ├── faqs/ messages/ redirects/ settings/ users/
│   │   └── dashboard.php
│   ├── app/
│   │   ├── config/
│   │   │   ├── config.example.php  ← الگو
│   │   │   └── config.php          ← (در سرور، ignored)
│   │   ├── core/
│   │   │   ├── Router.php          ← static + dynamic routes + redirect table
│   │   │   ├── Database.php        ← PDO singleton
│   │   │   ├── Auth.php            ← session login + role/can()
│   │   │   ├── Csrf.php            ← token-based CSRF
│   │   │   ├── Helpers.php         ← e/url/slugify/site_setting/social_links/...
│   │   │   ├── Seo.php             ← meta + JSON-LD render
│   │   │   ├── Throttle.php        ← rate limit per IP
│   │   │   ├── Cache.php           ← file-cache (Phase 4)
│   │   │   └── Controller.php
│   │   ├── controllers/
│   │   │   ├── PagesController.php ← home/about/contact/faq/rules/page-show
│   │   │   ├── BlogController.php  ← /blog, /blog/{slug}
│   │   │   ├── ServicesController.php
│   │   │   └── SeoController.php   ← /sitemap.xml + /robots.txt
│   │   ├── models/                 ← BaseModel + Post/Service/Page/...
│   │   └── views/
│   │       ├── layouts/{header,footer,main}.php
│   │       └── public/...
│   ├── assets/css/main.css | admin.css
│   ├── assets/js/main.js   | admin.js
│   ├── uploads/media/              ← فایل‌های آپلودی + .htaccess امن
│   └── cache/                      ← file-cache و throttle (با .htaccess)
├── database/
│   ├── schema.sql                  ← Phase 2 schema (12 جدول اصلی)
│   ├── seed.sql                    ← داده اولیه
│   ├── seo_redirects.sql           ← Phase 3 migration (seo_meta + redirects)
│   ├── phase5.sql                  ← Phase 5 migration (roles + social + offices)
│   └── migration_redirects.sql     ← الگوی Redirectهای مهاجرت از وردپرس
├── README.md
├── DEPLOYMENT.md
├── BACKUP.md
├── MIGRATION.md
├── MIGRATION_REPORT.md             ← Phase 5
└── PROJECT_DOCUMENTATION.md        ← این فایل
```

## ۳) Routing

تمام routes در `public_html/index.php` ثبت می‌شوند.

- **Static**: `$router->get('/about', [PagesController::class,'about']);`
- **Dynamic**: `$router->get('/blog/{slug}', [BlogController::class,'show']);` — پارامتر `$params['slug']` به متد پاس می‌شود.
- **Redirect Table**: قبل از match، Router جدول `redirects` را چک می‌کند و اگر URL درخواست `old_url` باشد، با کد 301/302 redirect می‌کند.
- **Reserved Slugs**: تابع `is_reserved_slug()` در `Helpers.php` تداخل اسلاگ پست/صفحه با مسیرهای سیستمی را جلوگیری می‌کند.

افزودن route جدید:
```php
$router->get('/portfolio/{slug}', [PortfolioController::class, 'show']);
```

## ۴) Controllers / Models / Views

### Controller
کلاسی با متدهای public. هر متد یا View را render می‌کند یا redirect می‌دهد.

```php
class PortfolioController extends Controller {
    public function show(array $params): void {
        $row = (new Portfolio())->bySlug($params['slug']);
        if (!$row) { (new PagesController())->notFound(); return; }
        $this->view('public/portfolio-show', ['row'=>$row], $row['title']);
    }
}
```

### Model
ارث‌بری از `BaseModel`. کافی است `$table` و اختیاراً `$fillable` را تعیین کنید:
```php
class Portfolio extends BaseModel {
    protected string $table = 'portfolio';
    protected array $fillable = ['title','slug','content','status'];
    public function bySlug(string $s): ?array {
        return Database::fetch("SELECT * FROM portfolio WHERE slug=? AND status='published'", [$s]);
    }
}
```

CRUD: `->all()`, `->find($id)`, `->paginate($page,$perPage,$where,$params)`, `->create($data)`, `->update($id,$data)`, `->delete($id)`.

### View
PHP خالص + escape با `e()`. هر view با layout `views/layouts/main.php` wrap می‌شود.

## ۵) Database

### جدول‌های اصلی
| جدول | کاربرد | کلیدهای خارجی |
|---|---|---|
| `users` | کاربران ادمین | — |
| `categories` | دسته‌ی مقالات | — |
| `tags` | برچسب | — |
| `services` | سرویس‌ها | — |
| `pages` | صفحات استاتیک | — |
| `posts` | مقالات | author_id→users, category_id→categories |
| `post_tags` | M2M پست-برچسب | post_id, tag_id |
| `post_services` | M2M پست-سرویس | post_id, service_id |
| `media` | رسانه‌ها | — |
| `faqs` | سوالات متداول | post_id/service_id/page_id (nullable) |
| `contact_messages` | پیام‌های فرم تماس | — |
| `site_settings` | key/value تنظیمات | — |
| `seo_meta` | متادیتای SEO هر entity | unique(entity_type, entity_id) |
| `redirects` | جدول redirectها | unique(old_url) |

### نقش‌ها (Phase 5)
`users.role` با ENUM(`super_admin`,`admin`,`editor`,`user`). ماتریس دسترسی در `Auth::can()`.

### Migration order
```
schema.sql → seed.sql → seo_redirects.sql → phase5.sql → migration_redirects.sql (اختیاری)
```

## ۶) Admin System

- ورود از `/admin/login.php` (rate-limited: ۵ تلاش در ۱۵ دقیقه per-IP).
- `setup.php` فقط در حالت خالی بودن جدول users اجرا می‌شود (اولین حساب → super_admin).
- هر صفحه ادمین با `_bootstrap.php` شروع و با `_layout.php` wrap می‌شود.
- بخش‌ها: Dashboard / Posts / Services / Pages / Categories / Tags / Media / FAQs / Messages / Redirects / Users / Settings.
- محافظت با `Auth::requireAdmin()` و `Auth::requirePermission('...')` در صفحاتی مثل Users/Settings.
- CSRF در همه فرم‌ها: `<?= Csrf::field() ?>` + `Csrf::check()`.

## ۷) SEO System

- متادیتا per-entity در `seo_meta` ذخیره می‌شود.
- `Seo.php` Title/Description/OG/Twitter/Canonical/JSON-LD را در `<head>` render می‌کند.
- `SeoController` به‌صورت dynamic:
  - `GET /sitemap.xml` ← URLهای منتشر‌شده + اولویت + changefreq از `seo_meta`.
  - `GET /robots.txt` ← شامل `Sitemap:` directive + `robots_extra` از تنظیمات.
- در فرم Edit مقاله/سرویس/صفحه، تب SEO با `_seo_partial.php` نمایش داده می‌شود و توسط `_seo_save.php` ذخیره می‌شود.
- Schema.org پشتیبانی‌شده: Article, Service, FAQPage, Organization, BreadcrumbList.

## ۸) Security

- CSRF: token-based روی همه‌ی فرم‌های POST.
- Auth: bcrypt + session regenerate.
- Throttle: file-based rate-limit برای login و contact (در `Throttle.php`).
- Upload: SVG غیرفعال است؛ `uploads/.htaccess` با Content-Disposition + CSP sandbox جلوی XSS را می‌گیرد.
- Output: همه‌ی متغیرها با `e()` escape می‌شوند؛ HTML غنی با `sanitize_html()` فیلتر می‌شود.
- DB: PDO + Prepared Statements (هیچ raw query با input نداریم).
- Headers: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy (در .htaccess).
- Hidden files: `.env`, `*.sql`, `config.php`, `README.md` با FilesMatch بلاک شده‌اند.
- Honeypot روی فرم تماس.

## ۹) Cache

- `Cache.php`: file-based cache در `cache/`. متدها: `get/set/remember/flush/flushTag`.
- TTL پیش‌فرض از `CACHE_TTL` در config.
- استفاده:
  ```php
  $services = Cache::remember('services.published', 600, fn() =>
      Database::fetchAll("SELECT * FROM services WHERE status='published'"));
  ```
- Static assets با `Cache-Control: max-age=31536000, immutable` (در .htaccess).

## ۱۰) Deployment

نگاه کنید به `DEPLOYMENT.md`. خلاصه:

1. Upload `public_html/*` به `public_html` در cPanel.
2. ساخت دیتابیس از cPanel → MySQL Databases.
3. Import به ترتیب: `schema.sql` → `seed.sql` → `seo_redirects.sql` → `phase5.sql`.
4. کپی `app/config/config.example.php` به `app/config/config.php` و پر کردن DB creds + APP_KEY + APP_URL.
5. `chmod 755 uploads/ cache/` و اطمینان از writable بودن.
6. مراجعه به `/admin/setup.php` و ساخت اولین super_admin.
7. ورود به `/admin/settings/` و پر کردن اطلاعات تماس، شبکه‌های اجتماعی، lat/lng دفترها.
8. (اختیاری) Import `migration_redirects.sql` بعد از ویرایش.

## ۱۱) Backup & Restore

نگاه کنید به `BACKUP.md`. خلاصه:

- **Backup فایل‌ها**: `tar -czf backup-$(date +%F).tar.gz public_html/` (شامل uploads + cache).
- **Backup دیتابیس**: `mysqldump -u USER -p DBNAME > backup-$(date +%F).sql`.
- **Restore فایل‌ها**: extract روی `public_html/`.
- **Restore دیتابیس**: `mysql -u USER -p DBNAME < backup.sql`.
- زمان‌بندی روزانه با cPanel Cron Jobs پیشنهاد می‌شود.

## ۱۲) Extending The CMS

### افزودن صفحه استاتیک جدید
از `/admin/pages/create.php` صفحه بسازید. مسیر `/{slug}` خودکار با route catch-all کار نمی‌کند — اگر می‌خواهید همه صفحات از `/{slug}` accessible باشند، در `index.php` این route را آخر اضافه کنید:
```php
$router->get('/{slug}', [PagesController::class, 'page']);
```
(در نظر داشته باشید reserved_slugs چک شود.)

### افزودن ماژول جدید (مثلاً Portfolio)
1. **Migration**: فایل `database/portfolio.sql` با CREATE TABLE.
2. **Model**: `app/models/Portfolio.php` extends BaseModel.
3. **Controller**: `app/controllers/PortfolioController.php` با متد `index()` و `show($params)`.
4. **Views**: `app/views/public/portfolio-index.php` و `portfolio-show.php`.
5. **Routes** (در `public_html/index.php`):
   ```php
   $router->get('/portfolio', [PortfolioController::class, 'index']);
   $router->get('/portfolio/{slug}', [PortfolioController::class, 'show']);
   ```
6. **Admin**: پوشه `public_html/admin/portfolio/` با `index.php`, `edit.php`, `delete.php`. در هر فایل:
   ```php
   require_once __DIR__.'/../_bootstrap.php';
   Auth::requireAdmin();
   ```
7. **Sidebar**: یک لینک به `admin/_layout.php` اضافه کنید.
8. **SEO**: در فرم edit، include کنید `_seo_partial.php` و در submit `_seo_save.php` را call کنید با `entity_type='portfolio'` (و enum مربوطه را در `seo_meta` schema قبلاً اضافه کرده باشید).

### افزودن نقش جدید
1. ALTER ENUM روی `users.role`.
2. در `Auth::can()` ماتریس را گسترش دهید.
3. در ادمین، استفاده از `Auth::requirePermission('...')`.

### قواعد سبک کد
- PHP strict types (`declare(strict_types=1);`).
- همه‌ی فرم‌ها CSRF داشته باشند.
- همه‌ی output ها `e()` شوند.
- مدل‌ها هیچ HTML خروجی ندهند.
- کنترلرها logic نهایی دارند و view را با `$this->view()` فراخوانی می‌کنند.
- جدول‌های جدید ENUM/utf8mb4/InnoDB.

---

**نسخه**: v1.1 (Phase 5)  
**آخرین به‌روزرسانی**: 2026

# ایران نتورک — IranNetwork CMS

پروژه‌ی اختصاصی سایت **ایران نتورک** ساخته‌شده با **PHP 8.1+** و **MySQL/MariaDB**، آماده‌ی نصب روی هاست **cPanel** معمولی. بدون نیاز به Node.js، Docker، Composer یا سرویس‌های ابری.

> این مخزن داخل Lovable فقط به عنوان **ادیتور کد** استفاده می‌شود. اجرای زنده‌ی PHP/MySQL داخل Lovable وجود ندارد و تست واقعی باید روی cPanel یا یک محیط لوکال PHP (XAMPP/Laragon) انجام شود.

---

## وضعیت پروژه

- ✅ **فاز ۱** — Foundation، طراحی، صفحات استاتیک
- ✅ **فاز ۲** — دیتابیس، پنل ادمین، CRUDها، آپلود رسانه، فرم تماس
- ✅ **فاز ۳** — محتوای داینامیک + ماژول سئو + sitemap + redirects + Schema.org
- ✅ **فاز ۴** — Production hardening، cache foundation، throttling، docs

> **Release Candidate:** IranNetwork CMS **v1.0 — Production Ready**
> راهنماهای تخصصی: [DEPLOYMENT.md](./DEPLOYMENT.md) · [BACKUP.md](./BACKUP.md) · [MIGRATION.md](./MIGRATION.md)

---

## فاز ۴ — چه چیزی اضافه شد؟

### امنیت
- 🔒 **حذف SVG از آپلود** (XSS از طریق `<script>` داخل SVG)
- 🔒 **Login Throttle**: حداکثر ۵ تلاش ناموفق در ۱۵ دقیقه per-IP
- 🔒 **Contact Throttle + Honeypot**: ۵ ارسال در ۱۰ دقیقه + فیلد مخفی ضد بات
- 🔒 **.htaccess hardened**: deny app/, cache/, database/, `config.php`; HSTS-ready؛ Cache-Control جداگانه برای HTML و assets
- 🔒 **uploads/.htaccess**: SVG با CSP sandbox + Content-Disposition: attachment
- 🔒 **Reserved slugs**: `is_reserved_slug()` برای جلوگیری از تصادم slug صفحات با مسیرهای سیستمی

### کارایی
- ⚡ **`Cache` class**: file-based cache (بدون نیاز به Redis) با `remember()`, `flush()`, `flushTag()`
- ⚡ **`Throttle` class**: rate-limiter فایل-محور per-IP
- ⚡ **Cache headers**: immutable برای assets، no-cache برای HTML
- ⚡ **Gzip + Expires** برای تمام انواع متنی و فونت

### تولید (Production)
- 📚 [DEPLOYMENT.md](./DEPLOYMENT.md) — نصب گام‌به‌گام cPanel + Production Checklist
- 📚 [BACKUP.md](./BACKUP.md) — بکاپ فایل + دیتابیس + cron نمونه + restore
- 📚 [MIGRATION.md](./MIGRATION.md) — مهاجرت SEO از وردپرس قدیم با Redirect 301
- 📦 `.gitignore` کامل (config.php، uploads، cache، logs، .env)

### مسیر داینامیک sitemap.xml و robots.txt
فایل استاتیک `robots.txt` حذف شد و `.htaccess` این دو مسیر را به روتر می‌سپارد تا نسخه‌ی داینامیک `SeoController` همیشه ارائه شود.

---


---

## فاز ۳ — چه چیزی اضافه شد؟

### ۱. مسیرهای داینامیک (Dynamic Routing)
- `/` — صفحه اصلی (با مقالات Featured از DB)
- `/blog` — لیست مقالات با pagination
- `/blog/{slug}` — صفحه مقاله از DB (با نویسنده، دسته، برچسب، سرویس مرتبط، FAQ، مقالات مرتبط)
- `/services` — لیست سرویس‌ها از DB
- `/services/{slug}` — جزئیات سرویس از DB (با FAQ، مقالات مرتبط، سایر سرویس‌ها)
- `/category/{slug}` — مقالات یک دسته
- `/tag/{slug}` — مقالات یک برچسب
- `/contact` — فرم تماس
- `/{page-slug}` — هر صفحه‌ی CMS از جدول `pages` (about, faq, rules, …)
- `/404` — صفحه یافت نشد سفارشی

Router جدید از `{param}` پشتیبانی می‌کند و **قبل از match** جدول `redirects` را چک می‌کند.

### ۲. ماژول سئو (`seo_meta`)
جدول `seo_meta` با کلید یکتای `(entity_type, entity_id)` برای پنج موجودیت:
`post`, `service`, `page`, `category`, `tag`

فیلدها: seo_title, meta_description, focus_keyword, secondary_keywords, canonical_url, robots_index, robots_follow, og_title/og_description/og_image, twitter_title/twitter_description/twitter_image, schema_type, enable_schema, include_in_sitemap, sitemap_priority, sitemap_changefreq.

تب سئو در پنل ادمین به‌صورت partial (`admin/_seo_partial.php`) به فرم‌های ویرایش مقاله، سرویس و صفحه افزوده شده است.

### ۳. تحلیل‌گر سئو (`SeoAnalyzer`)
- محاسبه **امتیاز سئو (0-100)** و **امتیاز خوانایی (0-100)**
- بررسی طول عنوان، توضیحات متا، کلمه کلیدی در عنوان/H1/H2/پاراگراف اول، طول محتوا، تعداد H1، alt تصاویر، لینک داخلی/خارجی، OG image
- نمایش **پیشنهادهای فارسی** بالای فرم ویرایش
- نمایش آمار (کلمات، جملات، میانگین طول جمله، …)

### ۴. رندر متاتگ‌ها (`Seo::renderTags`)
Layout اصلی (`views/layouts/main.php`) از داده‌ی `Seo::build($entity, $meta, $defaults)` متاتگ‌های زیر را رندر می‌کند:
title, description, canonical, robots, og:*, twitter:*

### ۵. Schema.org (JSON-LD)
هلپرهای آماده برای:
- `Organization` (همیشه)
- `Article` (در صفحه مقاله)
- `Service` (در صفحه سرویس)
- `FAQPage` (وقتی FAQ وجود دارد)
- `BreadcrumbList` (در همه صفحات داخلی)

### ۶. `/sitemap.xml` داینامیک
از کنترلر `SeoController::sitemap` رندر می‌شود. شامل: home, /services, /blog, /contact + همه‌ی posts/services/pages/categories/tags که `include_in_sitemap = 1` دارند، با `lastmod`, `changefreq`, `priority` از `seo_meta`.

### ۷. `/robots.txt` داینامیک
از `SeoController::robots`:
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /uploads/private/
Disallow: /app/

Sitemap: https://your-domain.com/sitemap.xml
```
محتوای اضافی از `site_settings.robots_extra` خوانده می‌شود.

### ۸. سیستم Redirects
جدول `redirects` با فیلدهای `old_url`, `new_url`, `status_code (301/302/307/308)`, `is_active`, `hits`. مدیریت کامل از طریق:
- `/admin/redirects/` — لیست
- `/admin/redirects/edit.php` — افزودن/ویرایش
- `/admin/redirects/delete.php` — حذف

Router در ابتدای dispatch، اگر آدرس فعلی در `redirects` فعال باشد، با کد مناسب redirect می‌کند و `hits` را افزایش می‌دهد.

---

## نصب روی cPanel (گام به گام)

### ۱) آپلود فایل‌ها
محتویات `irannetwork-php/public_html/` را در `public_html/` آپلود کنید.

### ۲) ایجاد دیتابیس
از cPanel → MySQL Databases:
- یک database و یک user بسازید
- user را با ALL PRIVILEGES به database بدهید

### ۳) Import کردن دیتابیس
از cPanel → phpMyAdmin → دیتابیس را انتخاب کرده و Import کنید:
1. ابتدا `database/schema.sql` (شامل همه‌ی جداول فاز ۱ تا ۳ — یک‌بار)
2. سپس `database/seed.sql` (داده اولیه)

> اگر فاز ۲ را قبلاً Import کرده‌اید و فقط می‌خواهید فاز ۳ را اضافه کنید، فایل `database/seo_redirects.sql` را Import کنید.

### ۴) پیکربندی
`app/config/config.example.php` را به `app/config/config.php` کپی کرده و این مقادیر را پر کنید:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `APP_URL` (مثلاً `https://irannetwork.co`)
- `APP_KEY` (یک رشته تصادفی 32+ کاراکتر)

### ۵) ساخت اولین مدیر
به `https://your-domain.com/admin/setup.php` بروید (فقط زمانی فعال است که جدول users خالی باشد). اطلاعات اولین حساب مدیر را وارد کنید.

### ۶) ورود
`https://your-domain.com/admin/login.php`

### ۷) اطمینان از .htaccess
mod_rewrite باید فعال باشد (روی cPanel معمولاً هست). در غیر این صورت در مسیرهای SEO-friendly مشکل دارید.

### ۸) بررسی sitemap و robots
- `https://your-domain.com/sitemap.xml`
- `https://your-domain.com/robots.txt`
- در Google Search Console، sitemap را Submit کنید.

---

## مسیرهای پنل مدیریت

| URL | شرح |
|---|---|
| `/admin/setup.php` | ساخت اولین admin (یک‌باره) |
| `/admin/login.php` | ورود |
| `/admin/logout.php` | خروج (POST) |
| `/admin/dashboard.php` | داشبورد |
| `/admin/posts/` | مدیریت مقالات (با تب سئو) |
| `/admin/services/` | مدیریت سرویس‌ها (با تب سئو) |
| `/admin/pages/` | مدیریت صفحات (با تب سئو) |
| `/admin/categories/` | دسته‌بندی‌ها |
| `/admin/tags/` | برچسب‌ها |
| `/admin/media/` | کتابخانه رسانه |
| `/admin/faqs/` | سوالات متداول |
| `/admin/messages/` | پیام‌های فرم تماس |
| `/admin/redirects/` | **(جدید)** مدیریت ریدایرکت‌ها |
| `/admin/settings/` | تنظیمات سایت |

---

## ساختار پوشه‌ها

```
irannetwork-php/
├── database/
│   ├── schema.sql           ← همه جداول (فاز ۱–۳)
│   ├── seed.sql             ← داده اولیه
│   └── seo_redirects.sql    ← فقط جداول فاز ۳ (برای migration جداگانه)
└── public_html/
    ├── index.php            ← front controller (Router داینامیک)
    ├── .htaccess            ← rewrite + کش + هدر امنیتی
    ├── robots.txt           ← static fallback (داینامیک نیز فعال است)
    ├── app/
    │   ├── config/
    │   ├── controllers/     ← PagesController, ServicesController, BlogController, SeoController
    │   ├── core/            ← Router, Database, Auth, Csrf, Helpers, Controller, Seo
    │   ├── models/          ← +SeoMeta, +Redirect
    │   └── views/
    │       ├── layouts/
    │       └── public/      ← +blog-index, +blog-show, +blog-list, +services-index, +service-show, +page-show
    ├── admin/
    │   ├── _bootstrap.php, _layout.php
    │   ├── _seo_partial.php ← تب سئو در فرم‌های ادمین
    │   ├── _seo_save.php    ← ذخیره‌ی seo_meta از POST
    │   ├── posts/, services/, pages/, categories/, tags/, media/, faqs/, messages/, settings/
    │   └── redirects/       ← (جدید)
    ├── assets/css/main.css, admin.css
    ├── assets/js/main.js, admin.js
    └── uploads/media/       ← آپلودها (PHP execution مسدود)
```

---

## جدول‌های دیتابیس (مجموع ۱۴)

users, categories, tags, services, pages, posts, post_tags, post_services, media, faqs, contact_messages, site_settings, **seo_meta** (جدید فاز ۳), **redirects** (جدید فاز ۳).

همه: `InnoDB` + `utf8mb4_unicode_ci`.

---

## محدودیت محیط Lovable

Lovable نمی‌تواند PHP/MySQL اجرا کند. preview زنده **در دسترس نیست**. برای تست:
- روی cPanel آپلود کنید، یا
- پوشه‌ی `public_html/` را در XAMPP/Laragon قرار دهید و دیتابیس را در phpMyAdmin لوکال Import کنید.

QA بصری و تست فانکشنال نهایی **خارج از Lovable** انجام می‌شود.

---

## آماده برای فاز ۴

- نقش‌های کاربری پیشرفته (editor/author)
- کش صفحات و object cache
- چندزبانه (en/fa)
- سیستم نظرات
- جستجوی کامل (Full-text)
- آنالیتیکس داخلی
- بکاپ خودکار

---

## آنچه هنوز ساخته نشده (در فاز ۳ خارج از scope)

- preview زنده محتوا قبل از انتشار
- ویرایشگر بلوک‌محور پیشرفته
- تصاویر چندسایزه (responsive images)
- جستجو با Full-text
- چندزبانه (i18n)
- صفحه `/search`

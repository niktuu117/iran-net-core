# IranNetwork — SEO Migration Report (Phase 5)

تحلیل مهاجرت از سایت وردپرسی فعلی `http://irannetwork.co/` به CMS جدید.

## ۱) ساختار URL سایت فعلی (WordPress)
- `/` — صفحه اصلی
- `/about-us/`, `/contact-us/`
- `/?p={id}` — Permalink پیش‌فرض پست‌ها
- `/{persian-slug}/` — اگر Pretty Permalink فعال باشد
- `/category/{slug}/`, `/tag/{slug}/`
- `/services/{slug}/` — معمول در قالب‌های وردپرس
- `/wp-content/uploads/...` — رسانه‌ها (ثابت می‌مانند تا تصاویر قدیمی ۴۰۴ نشوند)

## ۲) ساختار URL CMS جدید
- `/` — خانه
- `/about`, `/contact`, `/services`, `/blog`, `/faq`, `/rules`
- `/services/{slug}` — صفحه هر سرویس
- `/blog/{slug}` — صفحه هر مقاله
- `/category/{slug}`, `/tag/{slug}` — آرشیو
- `/{page-slug}` — صفحات استاتیک CMS (Pages)
- `/sitemap.xml`, `/robots.txt`

## ۳) جدول نگاشت

| نوع URL قدیم | معادل جدید | وضعیت |
|---|---|---|
| `/about-us/` | `/about` | 301 |
| `/contact-us/` | `/contact` | 301 |
| `/services/{slug}/` | `/services/{slug}` | 301 (حذف اسلش پایانی) |
| `/category/{slug}/` | `/category/{slug}` | 301 |
| `/tag/{slug}/` | `/tag/{slug}` | 301 |
| `/{post-slug}/` (پست) | `/blog/{slug}` | 301 |
| `/?p={id}` | `/blog/{new-slug}` | 301 (دستی) |
| `/wp-content/uploads/...` | باقی بماند یا 301 به `/uploads/media/...` | ترجیحاً انتقال فیزیکی فایل و حفظ مسیر |
| `/wp-admin/`, `/wp-login.php` | بدون redirect | باید 404 شود |

## ۴) URLهای حفظ‌شده
- ساختار `/services/...`, `/category/...`, `/tag/...` با همان اسلاگ — فقط اسلش پایانی حذف می‌شود.

## ۵) URLهای نیازمند Redirect
- همه‌ی URLهای پست‌ها (وردپرس از ریشه سرو می‌کرد، CMS جدید از `/blog/...`).
- `/about-us/` و `/contact-us/`.
- اسلاگ‌های فارسی قدیمی سرویس‌ها.

## ۶) URLهای حذف‌شده
- `/wp-login.php`, `/wp-admin/`, `/xmlrpc.php`, `/?feed=rss2`, `/wp-json/...` — نباید redirect شوند (در `robots.txt` Disallow و در حالت 404 رها شوند).

## ۷) فایل خروجی
نمونه‌ی آماده برای Import در `database/migration_redirects.sql` قرار دارد. قبل از Import:

1. لیست کامل URLهای ایندکس‌شده را از **Google Search Console → Coverage → Export** بگیرید.
2. یا از sitemap وردپرس استخراج کنید:
   ```bash
   curl -s http://irannetwork.co/sitemap.xml | grep -oE '<loc>[^<]+' | sed 's/<loc>//' > old-urls.txt
   ```
3. هر URL را به معادل جدید نگاشت کنید و در `migration_redirects.sql` به‌صورت `INSERT` اضافه کنید.
4. در phpMyAdmin → تب SQL → فایل را Import کنید.
5. در `/admin/redirects/` بررسی کنید همه ثبت شده‌اند.

## ۸) تست قبل از Go-Live
- استقرار CMS جدید روی `new.irannetwork.co`.
- برای هر URL قدیمی:
  ```bash
  curl -I https://new.irannetwork.co/about-us/
  # باید Status: 301 و Location: /about برگرداند
  ```

## ۹) Go-Live و پایش
- بکاپ کامل وردپرس + database.
- جایگزینی محتویات `public_html` با CMS جدید.
- Submit `https://irannetwork.co/sitemap.xml` در Search Console.
- پایش هفتگی Coverage → URLهای 404 و chains.

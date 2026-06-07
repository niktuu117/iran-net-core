# راهنمای استقرار روی cPanel — IranNetwork CMS v1.0

این فایل مکمل README اصلی است و فقط مراحل **deployment** را پوشش می‌دهد.

## ۱) پیش‌نیازها
- cPanel با PHP **8.1+** (پیشنهاد 8.2)
- MySQL/MariaDB
- ماژول‌های Apache: `mod_rewrite`, `mod_headers`, `mod_expires`, `mod_deflate`
- SSL فعال (Let's Encrypt از داخل cPanel)

## ۲) آپلود فایل‌ها
محتویات `irannetwork-php/public_html/` را داخل `public_html/` cPanel آپلود کنید.
ساختار باید این‌گونه باشد:

```
public_html/
├── index.php
├── .htaccess
├── admin/
├── app/         (دسترسی مستقیم به وب مسدود است)
├── assets/
├── cache/       (دسترسی مستقیم به وب مسدود است)
└── uploads/     (اجرای PHP مسدود است)
```

دیتابیس را **خارج از public_html** نگه دارید یا اطمینان حاصل کنید فولدر `database/` در سطح `public_html` نباشد (در `.htaccess` مسدود شده اما بهتر است اصلاً آنجا نباشد).

## ۳) دیتابیس
1. در cPanel → **MySQL Databases** یک database و user بسازید و user را با ALL PRIVILEGES به database وصل کنید.
2. در **phpMyAdmin** فایل‌ها را به ترتیب Import کنید:
   - `database/schema.sql`
   - `database/seed.sql`
   - `database/seo_redirects.sql` (اگر از فاز ۲ ارتقا می‌دهید)

## ۴) پیکربندی
```bash
cp app/config/config.example.php app/config/config.php
```
سپس مقادیر را پر کنید:
- `APP_ENV = 'production'`
- `APP_DEBUG = false`
- `APP_URL = 'https://irannetwork.co'`
- `APP_KEY = '...'`  (تصادفی 32+ کاراکتر)
- `DB_*`

## ۵) ساخت اولین مدیر
به `https://your-domain.com/admin/setup.php` بروید. فقط زمانی فعال است که جدول `users` خالی باشد.

## ۶) مجوزها (Permissions)
| مسیر | مجوز |
|---|---|
| `app/config/config.php` | **640** |
| `app/`, `admin/` | 755 |
| `uploads/`, `uploads/media/` | 775 |
| `cache/` | 775 |
| فایل‌ها (PHP، CSS، JS) | 644 |

## ۷) فعال‌سازی HTTPS
بعد از تأیید SSL، در `.htaccess` این بلوک را uncomment کنید:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```
و خط HSTS را در بخش Headers فعال کنید.

## ۸) چک نهایی sitemap / robots
- `https://your-domain.com/sitemap.xml`
- `https://your-domain.com/robots.txt`
- در Google Search Console، sitemap را Submit کنید.

---

## ✅ Production Checklist

- [ ] `APP_ENV=production` و `APP_DEBUG=false`
- [ ] `config.php` خارج از Git (در `.gitignore` هست)
- [ ] مجوز `config.php` = 640
- [ ] SSL فعال + Redirect HTTPS فعال
- [ ] HSTS فعال (بعد از تست SSL)
- [ ] sitemap.xml و robots.txt پاسخ می‌دهند
- [ ] رمز اولین admin قوی (12+ کاراکتر)
- [ ] `cache/` و `uploads/` قابل نوشتن
- [ ] `php_value display_errors Off` در سرور
- [ ] بکاپ خودکار شبانه فعال (در cPanel → Backup Wizard یا cron)

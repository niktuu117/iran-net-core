# ایران نتورک — IranNetwork CMS

پروژه‌ی اختصاصی سایت **ایران نتورک** ساخته‌شده با **PHP 8.1+** و **MySQL/MariaDB**، آماده‌ی نصب روی هاست **cPanel** معمولی. بدون نیاز به Node.js، Docker، Composer یا سرویس‌های ابری.

> این مخزن داخل Lovable فقط به عنوان **ادیتور کد** استفاده می‌شود. اجرای زنده‌ی PHP/MySQL داخل Lovable وجود ندارد و تست واقعی باید روی cPanel یا یک محیط لوکال PHP (XAMPP/Laragon) انجام شود.

---

## فهرست
- [وضعیت فاز ۲](#وضعیت-فاز-۲)
- [تکنولوژی‌ها](#تکنولوژیها)
- [ساختار پوشه‌ها](#ساختار-پوشهها)
- [نصب روی cPanel (گام به گام)](#نصب-روی-cpanel-گام-به-گام)
- [مسیرهای پنل مدیریت](#مسیرهای-پنل-مدیریت)
- [جدول‌های دیتابیس](#جدولهای-دیتابیس)
- [رسانه‌ها و آپلود](#رسانهها-و-آپلود)
- [بکاپ‌گیری](#بکاپگیری)
- [آماده برای فاز ۳](#آماده-برای-فاز-۳)
- [آنچه هنوز ساخته نشده](#آنچه-هنوز-ساخته-نشده)
- [محدودیت محیط Lovable](#محدودیت-محیط-lovable)

---

## وضعیت فاز ۲

فاز ۲ شامل دیتابیس واقعی، پنل ادمین فارسی RTL و CRUDهای CMS است.

**اضافه‌شده در فاز ۲:**
- ✅ Schema کامل MySQL/MariaDB (`database/schema.sql`)
- ✅ Seed اولیه (سرویس‌ها، صفحات، دسته‌ها، تنظیمات): `database/seed.sql`
- ✅ صفحه‌ی setup امن برای ساخت اولین کاربر admin
- ✅ سیستم login / logout با Bcrypt + CSRF + session regenerate
- ✅ پنل مدیریت کامل RTL (dashboard + sidebar + topbar + flash)
- ✅ CRUD برای: مقاله‌ها، سرویس‌ها، صفحات، دسته‌بندی‌ها، برچسب‌ها، رسانه‌ها، سوالات متداول، پیام‌های تماس، تنظیمات سایت
- ✅ Rich Text Editor ساده (H2/H3/Bold/Link/UL/Quote/CTA) با sanitize پایه
- ✅ آپلود امن تصویر (jpg/png/webp/svg) با بررسی mime type واقعی، نام فایل امن و جلوگیری از اجرای PHP در پوشه‌ی uploads
- ✅ ذخیره‌ی فرم تماس عمومی در دیتابیس (`contact_messages`)
- ✅ خواندن خودکار شماره‌ها و آدرس‌ها از `site_settings` در footer و صفحه تماس
- ✅ مدل‌های PDO سبک (`BaseModel`, `Post`, `Service`, `Page`, `Category`, `Tag`, `Media`, `Faq`, `ContactMessage`, `SiteSetting`, `User`)

---

## تکنولوژی‌ها

- **PHP** 8.1+ (نیاز به `pdo_mysql`, `mbstring`, `fileinfo`)
- **MySQL 5.7+ / MariaDB 10.3+**
- **PDO** با prepared statements
- HTML5 / CSS3 / Vanilla JS — بدون فریم‌ورک فرانت‌اند
- `.htaccess` + `mod_rewrite` برای URLهای تمیز
- فونت **Vazirmatn** از Google Fonts

---

## ساختار پوشه‌ها

```
public_html/
├── index.php                       # Front controller (routes static + POST /contact)
├── .htaccess                       # rewrite + cache + security headers
├── robots.txt
├── assets/
│   ├── css/main.css                # استایل سایت عمومی
│   ├── css/admin.css               # استایل پنل ادمین
│   ├── js/main.js
│   └── js/admin.js                 # editor + copy + auto-slug
├── uploads/
│   ├── .htaccess                   # PHP execution disabled
│   └── media/                      # تصاویر آپلود شده
├── admin/
│   ├── _bootstrap.php              # session + autoload + config (مشترک ادمین)
│   ├── _layout.php                 # sidebar + topbar + flash
│   ├── index.php                   # redirect → login/dashboard
│   ├── setup.php                   # ساخت اولین admin (یک بار)
│   ├── login.php  / logout.php  / dashboard.php
│   ├── posts/      (index, create, edit, delete)
│   ├── services/   (index, create, edit, delete)
│   ├── pages/      (index, create, edit, delete)
│   ├── categories/ (index, delete)
│   ├── tags/       (index, delete)
│   ├── media/      (index = آپلود + لیست، edit, delete)
│   ├── faqs/       (index, create, edit, delete)
│   ├── messages/   (index, view, delete)
│   └── settings/   (index)
├── app/
│   ├── config/
│   │   ├── config.example.php      # داخل ریپو — کپی کنید به config.php
│   │   └── config.php              # فقط روی سرور — هرگز commit نکنید
│   ├── core/  Database, Router, Controller, Auth, Csrf, Helpers
│   ├── controllers/ Pages, Services
│   ├── models/      BaseModel + 10 مدل PDO
│   └── views/
│       ├── layouts/{main,header,footer}.php
│       └── public/{home,services,service-detail,about,contact,...}.php
└── database/
    ├── schema.sql   # CREATE TABLE های فاز ۲
    └── seed.sql     # داده‌های اولیه
```

---

## نصب روی cPanel (گام به گام)

### ۱) آپلود فایل‌ها
- محتویات `irannetwork-php/public_html/` را روی `public_html/` هاست cPanel آپلود کنید.
- مطمئن شوید فایل‌های مخفی (`.htaccess` و `uploads/.htaccess`) منتقل شده‌اند.
- نسخه‌ی PHP در cPanel: **8.1 یا بالاتر**. اکستنشن‌های `pdo_mysql`, `mbstring`, `fileinfo` فعال باشند.

### ۲) ساخت دیتابیس در cPanel
1. در cPanel وارد **MySQL Databases** شوید.
2. یک دیتابیس جدید بسازید (مثلاً `myuser_irannet`).
3. یک کاربر جدید بسازید و رمز قوی بدهید.
4. کاربر را با تمام دسترسی‌ها (`ALL PRIVILEGES`) به دیتابیس متصل کنید.

### ۳) Import کردن schema و seed
- وارد **phpMyAdmin** شوید، دیتابیس را انتخاب کنید و:
  1. ابتدا `database/schema.sql` را Import کنید.
  2. سپس `database/seed.sql` را Import کنید.

### ۴) ساخت `config.php`
- در سرور، `public_html/app/config/config.example.php` را کپی کنید به `config.php`:
  ```
  cp app/config/config.example.php app/config/config.php
  ```
- مقادیر را با اطلاعات واقعی پر کنید:
  - `APP_ENV` → `production`
  - `APP_DEBUG` → `false`
  - `APP_URL` → دامنه‌ی واقعی (با https://)
  - `APP_KEY` → رشته‌ی تصادفی ۳۲+ کاراکتری
  - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` → از مرحله ۲
- فایل `config.php` در `.gitignore` است و **هرگز نباید commit شود**.

### ۵) ساخت اولین کاربر admin
- مرورگر را باز کنید و به این آدرس بروید:
  ```
  https://your-domain.tld/admin/setup.php
  ```
- اگر هیچ کاربری در دیتابیس نیست، فرم ساخت admin نمایش داده می‌شود.
- نام، ایمیل، و رمز قوی (حداقل ۸ کاراکتر) وارد کنید.
- بعد از ساخت، صفحه به `/admin/login.php` ری‌دایرکت می‌شود.
- اگر کاربری از قبل وجود داشته باشد، صفحه `setup.php` به‌صورت خودکار غیرفعال می‌شود.

### ۶) ورود به پنل
- آدرس ورود:
  ```
  https://your-domain.tld/admin/login.php
  ```

### ۷) مجوزها (Permissions)
- پوشه `public_html/uploads/media/` باید نوشتنی باشد (`0755` یا `0775`).
- در cPanel معمولاً به‌صورت پیش‌فرض درست است.

---

## مسیرهای پنل مدیریت

| مسیر | کاربرد |
|---|---|
| `/admin/setup.php` | ساخت اولین admin (فقط یک‌بار) |
| `/admin/login.php` | ورود |
| `/admin/logout.php` | خروج |
| `/admin/dashboard.php` | داشبورد + آمار |
| `/admin/posts/` | مقاله‌ها (لیست/ساخت/ویرایش/حذف) |
| `/admin/services/` | سرویس‌ها |
| `/admin/pages/` | صفحات |
| `/admin/categories/` | دسته‌بندی‌ها |
| `/admin/tags/` | برچسب‌ها |
| `/admin/media/` | کتابخانه رسانه + آپلود |
| `/admin/faqs/` | سوالات متداول |
| `/admin/messages/` | پیام‌های فرم تماس |
| `/admin/settings/` | تنظیمات سایت |

تمامی مسیرهای بالا (به‌جز `setup`, `login`, `logout`) با `Auth::requireAdmin()` محافظت می‌شوند.

---

## جدول‌های دیتابیس

`users`, `categories`, `tags`, `services`, `pages`, `posts`, `post_tags`, `post_services`, `media`, `faqs`, `contact_messages`, `site_settings`

تمام جدول‌ها با charset `utf8mb4` و collation `utf8mb4_unicode_ci` ساخته می‌شوند تا برای فارسی کاملاً ایمن باشند. Foreign keyهای حیاتی (post → user/category، post_tags، post_services، faq → ...) با `ON DELETE SET NULL` یا `CASCADE` تعریف شده‌اند.

---

## رسانه‌ها و آپلود

- **محل ذخیره فیزیکی**: `public_html/uploads/media/`
- **آدرس عمومی**: `/uploads/media/<filename>`
- **محدودیت پیش‌فرض**: ۵ مگابایت (در `config.php` متغیر `MAX_UPLOAD_SIZE`)
- **پسوندهای مجاز**: `jpg, jpeg, png, webp, svg`
- **mime type واقعی** با `finfo` بررسی می‌شود.
- **نام فایل** sanitize و با ۸ کاراکتر تصادفی پسوند‌گذاری می‌شود تا path traversal و overwrite اتفاق نیفتد.
- **اجرای PHP در پوشه uploads مسدود** است (`uploads/.htaccess`).
- در پنل مدیریت دکمه‌ی **کپی URL** برای استفاده در فیلدهای featured image وجود دارد.

---

## بکاپ‌گیری

### دیتابیس
از **phpMyAdmin** → تب **Export** → فرمت SQL → Custom → فعال‌سازی `Add DROP TABLE / IF NOT EXISTS` → دانلود.

برای CLI/SSH:
```bash
mysqldump -u USER -p DBNAME > backup_$(date +%F).sql
```

### فایل‌ها
از cPanel File Manager، پوشه `public_html/uploads/` و `public_html/app/config/config.php` را فشرده کنید و دانلود نمایید.

---

## آماده برای فاز ۳

- جدول‌ها و مدل‌های CMS کامل ساخته شده‌اند و آماده‌ی نمایش عمومی هستند.
- `SiteSetting` و `site_setting()` helper برای استفاده در همه viewها فعال است.
- `site_services()` از دیتابیس می‌خواند (در صورت موجود بودن).
- ساختار controller/router به‌راحتی قابلیت اضافه شدن مسیر داینامیک `/services/{slug}`, `/blog/{slug}`, `/blog/category/{slug}` را دارد.

---

## آنچه هنوز ساخته نشده

موارد زیر طبق درخواست برای **فاز ۳** کنار گذاشته شده‌اند:

- ❌ `sitemap.xml` داینامیک
- ❌ `robots.txt` پیشرفته
- ❌ سیستم redirects
- ❌ SEO plugin کامل (شبیه Yoast/RankMath)
- ❌ schema.org کامل برای هر صفحه
- ❌ صفحات عمومی داینامیک کامل (`/blog/{slug}`, `/services/{slug}` از DB)
- ❌ صفحات public دسته/برچسب
- ❌ جستجوی عمومی سایت
- ❌ محدودیت login attempts (rate limit)
- ❌ ارسال نوتیفیکیشن ایمیل هنگام دریافت فرم تماس

---

## محدودیت محیط Lovable

- Lovable نمی‌تواند PHP/MySQL را اجرا کند، بنابراین:
  - این مخزن صرفاً **سورس‌کد** است.
  - برای دیدن نتیجه باید روی **cPanel** یا یک محیط لوکال PHP (XAMPP/Laragon/MAMP) آپلود/اجرا کنید.
  - تست بصری ادمین، اجرای schema/seed، آپلود فایل و فرم تماس فقط در محیط واقعی قابل بررسی هستند.
- بنده schema و کد را با دقت ساخته‌ام اما هیچ‌گونه «اجرای واقعی PHP» یا «import واقعی MySQL» داخل Lovable انجام نشده است.

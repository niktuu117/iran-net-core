# ایران نتورک — IranNetwork CMS

پروژه‌ی اختصاصی سایت **ایران نتورک** ساخته‌شده با **PHP 8.1+** و آماده‌ی نصب روی هاست **cPanel** معمولی، بدون نیاز به Node.js، Docker یا سرویس‌های ابری.

> این مخزن داخل Lovable فقط به عنوان **ادیتور کد** استفاده می‌شود. سایت برای اجرا روی هاست cPanel با PHP طراحی شده و پیش‌نمایش زنده‌ی PHP داخل Lovable وجود ندارد.

---

## فهرست
- [ویژگی‌های فاز ۱](#ویژگیهای-فاز-۱)
- [تکنولوژی‌ها](#تکنولوژیها)
- [ساختار پوشه‌ها](#ساختار-پوشهها)
- [نصب روی cPanel](#نصب-روی-cpanel)
- [مسیرهای ساخته‌شده](#مسیرهای-ساختهشده)
- [آنچه آماده‌ی فاز ۲ است](#آنچه-آمادهی-فاز-۲-است)
- [آنچه هنوز ساخته نشده](#آنچه-هنوز-ساخته-نشده)

---

## ویژگی‌های فاز ۱

- ✅ ساختار MVC ساده (Router + Controller + View)
- ✅ صفحات استاتیک کامل و SEO-friendly
- ✅ طراحی ریسپانسیو، RTL، فونت Vazirmatn
- ✅ Hero قوی، کارت‌های سرویس، فرآیند همکاری، CTA
- ✅ صفحه‌ی اختصاصی برای هر ۸ سرویس
- ✅ فرم تماس با validation فرانت‌اند
- ✅ صفحه ۴۰۴ سفارشی فارسی
- ✅ Open Graph + JSON-LD (Organization)
- ✅ `.htaccess` با URL rewrite، فشرده‌سازی، کش و هدرهای امنیتی
- ✅ Auth/CSRF skeleton آماده برای فاز ۲

---

## تکنولوژی‌ها

- **PHP** 8.1+
- **MySQL/MariaDB** (در فاز ۲ فعال می‌شود)
- **PDO** برای اتصال امن دیتابیس
- **HTML5 / CSS3 / Vanilla JS** (بدون React/Vue/Tailwind build)
- **.htaccess** + `mod_rewrite` برای URLهای تمیز
- **Vazirmatn** از Google Fonts برای فارسی

---

## ساختار پوشه‌ها

```
public_html/
├── index.php                  # Front controller
├── .htaccess                  # URL rewrite + security headers
├── robots.txt
├── assets/
│   ├── css/main.css           # تمام استایل‌ها
│   ├── js/main.js             # JS سبک، بدون فریم‌ورک
│   └── images/
├── uploads/                   # محل آپلودها (فاز ۲)
├── admin/                     # پنل ادمین (فاز ۲)
├── app/
│   ├── config/
│   │   ├── config.example.php # کپی کنید به config.php
│   │   └── config.php         # ساخته نشود تا روی سرور پر شود
│   ├── core/
│   │   ├── Database.php       # PDO singleton
│   │   ├── Router.php         # روتر سبک
│   │   ├── Controller.php     # کنترلر پایه
│   │   ├── Auth.php           # skeleton
│   │   ├── Csrf.php           # skeleton
│   │   └── Helpers.php        # توابع کمکی (e, asset, icon_svg, …)
│   ├── controllers/
│   │   ├── PagesController.php
│   │   └── ServicesController.php
│   ├── models/                # خالی (فاز ۲)
│   └── views/
│       ├── layouts/{main,header,footer}.php
│       ├── public/{home,services,service-detail,about,contact,faq,rules,blog,404}.php
│       └── admin/             # خالی (فاز ۲)
└── database/
    └── schema.sql             # placeholder برای فاز ۲
```

---

## نصب روی cPanel

### ۱. آپلود فایل‌ها
- محتویات `public_html/` این پروژه را روی `public_html` هاست خود آپلود کنید.
- مطمئن شوید فایل‌های مخفی (مثل `.htaccess`) هم منتقل می‌شوند.

### ۲. تنظیم config
- در سرور، فایل `app/config/config.example.php` را به `app/config/config.php` کپی کنید.
- مقادیر زیر را تنظیم کنید:
  - `APP_ENV` → `production`
  - `APP_URL` → دامنه‌ی واقعی شما
  - `APP_KEY` → یک رشته‌ی تصادفی ۳۲+ کاراکتری
  - مقادیر `DB_*` → از روی دیتابیسی که در cPanel می‌سازید (فاز ۲)

### ۳. بررسی PHP
- در cPanel، نسخه PHP را روی **8.1 یا بالاتر** بگذارید.
- اکستنشن‌های `pdo_mysql` و `mbstring` فعال باشند.

### ۴. تست
- وارد دامنه شوید. باید صفحه‌ی اصلی فارسی و راست‌چین لود شود.
- منوی موبایل، فرم تماس و صفحه ۴۰۴ را تست کنید.

> ❗ در فاز ۱ نیازی به ساخت دیتابیس نیست. سایت کاملاً استاتیک کار می‌کند.

---

## مسیرهای ساخته‌شده

| مسیر | توضیح |
|------|-------|
| `/` | صفحه‌ی اصلی |
| `/services` | لیست خدمات |
| `/services/network-support` | پشتیبانی شبکه |
| `/services/network-installation` | نصب و راه‌اندازی شبکه |
| `/services/voip` | ویپ و سانترال |
| `/services/digital-marketing` | دیجیتال مارکتینگ |
| `/services/network-security` | امنیت شبکه و سرور |
| `/services/server-support` | پشتیبانی سرور |
| `/services/active-network` | خدمات اکتیو شبکه |
| `/services/passive-network` | خدمات پسیو شبکه |
| `/about` | درباره ما |
| `/contact` | تماس با ما |
| `/faq` | سوالات متداول |
| `/rules` | قوانین و مقررات |
| `/blog` | لیست مقالات (placeholder) |
| `/404` | صفحه یافت نشد |

---

## آنچه آماده‌ی فاز ۲ است

- اتصال PDO (کلاس `Database`)
- `Auth` و `Csrf` skeleton
- پوشه‌ی `admin/`, `app/models/`, `app/views/admin/`
- پوشه‌ی `uploads/` با حفاظت در `.htaccess`
- ساختار `database/schema.sql`

---

## آنچه هنوز ساخته نشده

- ❌ دیتابیس واقعی، schema و seed
- ❌ پنل ادمین (login, dashboard)
- ❌ CMS مقاله‌ها و سرویس‌ها
- ❌ آپلود مدیا
- ❌ ذخیره‌ی فرم تماس
- ❌ Sitemap داینامیک
- ❌ سیستم redirects
- ❌ ماژول SEO پیشرفته

این موارد در **فاز ۲ و ۳** اضافه می‌شوند.

---

## محدودیت محیط Lovable

- پیش‌نمایش زنده‌ی PHP داخل Lovable وجود ندارد.
- تست بصری و QA باید روی **cPanel** یا یک سرور PHP لوکال (مثل **Laragon** یا **XAMPP**) انجام شود.
- این مخزن فقط نقش source repository را دارد.

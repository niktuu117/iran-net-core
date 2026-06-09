# Phase 6.1 — Deep Static Audit (No Runtime Excuse)

این گزارش بر اساس بازخوانی فایل‌به‌فایل سورس‌کد نوشته شده. هر مورد یکی از سه
وضعیت زیر را دارد:

- **PASS** — کد و زنجیره‌ی منطقی درست است.
- **FAIL** — باگ واقعی در سورس پیدا شد (فایل و خط ذکر شده).
- **RISK** — کد در شرایط رایج درست کار می‌کند، اما در سناریوهای خاص ممکن است شکست بخورد.

---

## ۱) Page Creation — مسیر کامل

**زنجیره فایل‌ها وقتی کاربر "ساخت صفحه جدید" را کلیک می‌کند:**

1. مرورگر → `GET /admin/pages/create.php`
2. `admin/pages/create.php` (۱ خط) → `require __DIR__.'/_bootstrap.php'` → `Auth::requireAdmin()` → `require __DIR__.'/edit.php'`
3. `admin/pages/edit.php`:
   - خط ۴: `$isEdit = isset($_GET['id']) || (int)($_POST['id']??0)>0;` → برای create می‌شود `false`.
   - خط ۶: مقادیر پیش‌فرض `$row=['title'=>'',...,'status'=>'draft']`.
   - رندر فرم با `ob_start()` و در پایان `require __DIR__.'/../_layout.php'`.
4. کاربر فرم را Submit می‌کند → `POST /admin/pages/create.php` → باز همان مسیر تا `edit.php`.
5. در `edit.php` (خطوط ۱۷–۳۴):
   - `Csrf::check()` ← اگر توکن منقضی شده باشد، استثنا پرتاب می‌شود.
   - `sanitize_html($_POST['content'])` ← `<script>/<style>/<iframe>` و `on*` حذف.
   - `slugify()` + `unique_slug()` تضمین یکتایی.
   - `Page::create($data)` (از `BaseModel::create`) → `INSERT` + `lastInsertId()`.
   - `seo_save_from_post('page', $id)` ← متاهای SEO ذخیره می‌شود.
   - `flash('success', ...)` + `redirect('/admin/pages/edit.php?id='.$id)`.
6. صفحه عمومی توسط `PagesController::dynamicPage()` و route پویا در `Router.php` با شرط `status='published'` رندر می‌شود.

**نتیجه:** **PASS** در سطح کد (هیچ نقطه‌ی شکست منطقی پیدا نشد).

**نقاط شکست محتمل (RISK):**

- **R1 (مهم):** `is_reserved_slug()` در `Helpers.php` خط ۱۲۴ تعریف شده **اما در
  `admin/pages/edit.php` فراخوانی نمی‌شود.** یعنی کاربر می‌تواند صفحه‌ای با
  `slug='contact'`, `'blog'`, `'services'`, `'admin'` بسازد. در دیتابیس ذخیره
  می‌شود (با اضافه‌شدن `-2` به‌خاطر unique_slug؟ نه چون لیست reserved جدا از
  unique است). در فرانت‌اند هم اولویت با routeهای ثابت `Router` است؛ پس صفحه
  ذخیره‌شده ولی **نامرئی** می‌ماند → کاربر گزارش می‌دهد «صفحه ساخته نمی‌شود».
- **R2:** اگر `Page::create($data)` صفر برگرداند (مثلاً همه‌ی فیلدها خارج
  `fillable` بودند)، `redirect('/admin/pages/edit.php?id=0')` صادر می‌شود و
  edit.php پاسخ «صفحه یافت نشد» و redirect به `/admin/pages/` می‌دهد →
  ظاهر باگ «هیچ اتفاقی نمی‌افتد». احتمال وقوع کم است (همه‌ی فیلدها fillable
  هستند) ولی اگر در آینده کسی `fillable` را اصلاح کند، silent fail می‌شود.
- **R3:** `Auth::requireAdmin()` در عمل `isEditor()` را چک می‌کند (خط
  Auth.php:101). یعنی نقش `editor` هم می‌تواند صفحه بسازد. این مطابق RBAC
  داکیومنت‌شده است اما اگر مالک انتظار داشته باشد فقط ادمین صفحه بسازد، باید
  به `requirePermission('manage_content')` ارتقا داده شود.

---

## ۲) Category Creation — مسیر کامل

**زنجیره:** `admin/categories/index.php` → form POST به همان صفحه →
`Csrf::check()` → ولیدیشن نام (≥۲ کاراکتر) → `unique_slug(slugify(...))` →
`Category::create(['name','slug','description'])` → `flash` + redirect.

`Category` مدل (`models/Category.php`) `fillable=['name','slug','description']`
دارد و جدول `categories` ستون‌های متناظر را دارد (`schema.sql` خط ۲۲ به بعد).

**نتیجه:** **PASS** در سطح کد.

**نقاط شکست محتمل (RISK):**

- **R4:** اگر کاربر فقط slug را پر کند ولی name خالی باشد، خطای «نام الزامی است»
  نمایش داده می‌شود. **اما** قبل از نمایش خطا، `unique_slug()` فراخوانی می‌شود
  (خط 17 از index.php). این فقط یک کوئری اضافی است نه باگ، ولی اگر slug خالی
  و name خالی باشد، `slugify('')` → `'item'` برمی‌گرداند که در DB سرریز می‌کند.
- **R5:** ستون `slug` در جدول `categories` (schema خط ~۲۲ به بعد) با `UNIQUE`
  محدود است. تابع `unique_slug` این را با append `-2,-3,...` پوشش می‌دهد، اما
  بسته به collation (`utf8mb4_unicode_ci`) دو slug فارسی مشابه ممکن است در
  سطح MySQL تکراری شمرده شوند → `Database::execute` استثنا پرتاب می‌کند →
  fatal error در ادمین. به‌ندرت رخ می‌دهد.

---

## ۳) Edit Pages Styling

برای همه‌ی edit.php‌های زیر بررسی شد:

| فایل | render layout | path admin.css | path style |
|------|---------------|----------------|------------|
| admin/pages/edit.php | `require __DIR__.'/../_layout.php';` | `_layout.php` خط ۳۰: `<link rel="stylesheet" href="/assets/css/admin.css">` | absolute |
| admin/posts/edit.php | همان | همان | absolute |
| admin/services/edit.php | همان | همان | absolute |
| admin/categories/index.php | همان | همان | absolute |
| admin/faqs/edit.php | همان | همان | absolute |
| admin/media/edit.php | همان | همان | absolute |
| admin/users/edit.php | همان | همان | absolute |
| admin/redirects/edit.php | همان | همان | absolute |

**نتیجه:** **PASS** در سطح کد. هیچ path نسبی استفاده نشده و تمام
`<link>`‌ها با `/` شروع می‌شوند.

**نقاط شکست محتمل (RISK):**

- **R6 (محتمل‌ترین علت گزارش کاربر):** اگر document root cPanel **روی
  `public_html/irannetwork-php/public_html/`** ست نشده باشد و فایل‌ها داخل
  subfolder رفته باشند (مثلاً `public_html/cms/`)، آن‌گاه `/assets/css/admin.css`
  در آدرس `https://domain.com/assets/css/admin.css` می‌نشیند که وجود ندارد →
  صفحه ادمین بدون استایل. این یک باگ deployment است نه کد، اما با تعریف یک
  ثابت `BASE_URL` و تابع `asset()` (که اتفاقاً در `Helpers.php` خط ۵۱
  هست ولی استفاده نمی‌شود!) قابل برطرف‌سازی است.
- **R7 (واقعی):** `_layout.php` فونت `Vazirmatn` را از `fonts.googleapis.com`
  بارگذاری می‌کند. اگر سرور cPanel firewall خروجی به CDN گوگل ندارد، صفحه
  بدون فونت رندر می‌شود (نه بدون استایل، اما UI خراب جلوه می‌کند).

---

## ۴) Social Links — کل زنجیره

**Save path:**
1. `admin/settings/index.php` فرم را با `name="social_instagram"` و … submit می‌کند.
2. حلقه‌ی خط ۵۱–۵۴: برای هر key در `$allKeys`، `$s->set($k, $val)` صدا زده می‌شود.
3. `SiteSetting::set()` (خط ۱۵–۲۳): اگر key وجود دارد UPDATE، در غیر این صورت INSERT.

**Read path:**
1. `social_links()` در `Helpers.php` خط ۲۸۳ → برای هر پلتفرم
   `site_setting('social_'.$key, '')` صدا زده می‌شود.
2. `site_setting()` خط ۲۰۶ تمام جدول را **در یک static cache** بار می‌کند
   (`SELECT setting_key, setting_value FROM site_settings`).
3. اگر مقدار خالی بود، fallback به `site_setting($key, '')` (legacy keys).

**Render path:**
- `footer.php` خط ۶۱–۷۰ خروجی `social_links()` را داخل `<div class="footer-socials">` رندر می‌کند.

**نتیجه:** **PASS** در سطح کد. زنجیره‌ی save → read → render کامل و درست است.

**نقاط شکست محتمل:**

- **R8 (FAIL در عمل، PASS در کد):** اگر کاربر phase5.sql را اجرا نکرده باشد،
  هیچ ردیف `social_*` در جدول وجود ندارد. صفحه‌ی settings فرم خالی نشان
  می‌دهد و save کار می‌کند. اما اگر بعداً sql ای از قبل وجود داشته با مقدار خالی
  (`INSERT IGNORE` در `phase5.sql` خطوط ۲۲–۲۸) و کاربر هرگز submit نکرده،
  مقدار `''` در DB می‌ماند → `social_links()` آن را با `continue` رد می‌کند
  → فوتر خالی. این رفتار درست است اما توضیح نمی‌دهد چرا کاربر فکر می‌کند ذخیره
  می‌کند ولی نمایش داده نمی‌شود. باید در ادمین یک کوئری اجرا شود:
  `SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'social_%';`
- **R9 (FAIL کوچک):** static cache در `site_setting()` در طول همان request
  بعد از `SiteSetting::set()` invalidate نمی‌شود. اگر در یک script تنظیمی
  ذخیره شود و بعد بلافاصله socials خوانده شود، مقدار قبلی برمی‌گردد. در flow
  فعلی (POST → redirect → GET) مشکلی نیست، اما RISK باقی است.

---

## ۵) Office Locations

**Save:** `admin/settings/index.php` خطوط ۲۰–۳۳ → ۱۰ فیلد برای هر دفتر
(`office_tehran_title|address|phone|lat|lng`) با همان keyها در DB ذخیره می‌شود.

**Read helper:** `office_locations()` (`Helpers.php` خط ۳۱۶–۳۴۲):
- `office_tehran_lat` و `office_tehran_lng` خوانده می‌شوند.
- اگر هر دو غیرخالی → `map_url = google.com/maps?q=lat,lng`.
- در غیر این صورت اگر `address` غیرخالی → `map_url = google.com/maps/search/?api=1&query=…`.
- اگر هر دو خالی → `map_url = null`.

**نتیجه:** **PASS** در سطح کد برای helper.

**نقاط شکست واقعی (FAIL):**

- **F1 (Critical):** `app/views/layouts/footer.php` خطوط ۴۲–۵۸ از کلیدهای
  **قدیمی** `address_tehran`, `phone_tehran`, `address_isfahan`, `phone_isfahan`
  استفاده می‌کند، **نه** از کلیدهای Phase 5 یعنی `office_tehran_address`,
  `office_tehran_phone`. در نتیجه:
  - کاربر در «تنظیمات» فیلدهای جدید را پر می‌کند → در DB با کلید
    `office_tehran_address` ذخیره می‌شود.
  - فوتر همچنان به دنبال `address_tehran` می‌گردد → خالی پیدا می‌کند →
    fallback به ثابت `CONTACT_ADDRESS_TEHRAN` که در `config.example.php` تعریف
    شده یا خالی است → فوتر آدرس را خالی یا قدیمی نشان می‌دهد.
  - **این دقیقاً همان «دفترها/آدرس کار نمی‌کنند» است.**
  - فایل: `irannetwork-php/public_html/app/views/layouts/footer.php` خطوط ۴۵–۴۹.
- **F2 (مرتبط):** صفحه `contact.php` از `office_locations()` استفاده می‌کند
  (خوب)، اما footer نه. ناهماهنگی بین دو ویو.

---

## Top 10 Most Likely Production Bugs

| # | Bug | فایل | سطح |
|---|-----|------|-----|
| 1 | فوتر کلیدهای legacy می‌خواند، نه `office_*` Phase 5 → آدرس/تلفن از تنظیمات هرگز در فوتر دیده نمی‌شود. | `app/views/layouts/footer.php:45-56` | **FAIL** |
| 2 | `is_reserved_slug()` در ذخیره‌ی Page/Post/Service فراخوانی نمی‌شود → امکان ساخت صفحه‌ای با slug=`contact`/`blog` که توسط route ثابت گرفته می‌شود و نامرئی می‌ماند. | `admin/pages/edit.php`, `admin/services/edit.php`, `admin/posts/edit.php` | FAIL |
| 3 | اگر cPanel docroot دقیقاً روی `public_html/` پروژه نباشد، تمام آدرس‌های `/assets/...` و `/admin/...` می‌شکنند → صفحات edit بدون استایل + 404. | `_layout.php:30`, `index.php router` | RISK (deployment) |
| 4 | static cache `site_setting()` بعد از `SiteSetting::set()` در همان request invalidate نمی‌شود. | `Helpers.php:206-227` | RISK |
| 5 | کلید `social_*` با مقدار `''` در DB می‌ماند تا کاربر submit نکرده → فوتر خالی، بدون پیام راهنما. | `phase5.sql:22-28` + `Helpers.php:283-313` | RISK |
| 6 | `Auth::requireAdmin()` در عمل `isEditor()` چک می‌کند → نقش `editor` می‌تواند به دلخواه تنظیمات (با `can('manage_settings')`)، صفحه و دسته بسازد. اگر انتظار جداسازی سخت‌گیرانه‌تر است، نقض می‌شود. | `Auth.php:99-105` | RISK |
| 7 | `Page::create` اگر صفر برگرداند، redirect به `edit.php?id=0` → پیام «صفحه یافت نشد». فعلاً وقوع ندارد ولی silent-fail است. | `admin/pages/edit.php:29-34` | RISK |
| 8 | فونت `Vazirmatn` فقط از CDN گوگل بارگذاری می‌شود — در سرورهایی که خروجی به googleapis ندارند UI ادمین/سایت بدون فونت رندر می‌شود. | `_layout.php:27-29`, `views/layouts/main.php` | RISK |
| 9 | `Database::execute` در صورت خطای UNIQUE (slug فارسی همسان در collation `utf8mb4_unicode_ci`) استثنا پرتاب می‌کند که در ادمین به‌صورت 500 ظاهر می‌شود (try/catch ندارد). | `BaseModel.php:56-65`, `Helpers.php unique_slug` | RISK |
| 10 | `social_links()` بعد از `whatsapp`/`telegram` URL می‌سازد، اما اگر کاربر ورودی نامعتبر (مثلاً `@mychannel ` با whitespace) داده باشد، URL خراب تولید می‌شود (whitespace trim شده ولی unicode space نه). | `Helpers.php:302-310` | RISK کوچک |

---

## نتیجه‌گیری فنی

- **پروژه در سطح کد عمدتاً سالم است.** هیچ از مسیرهای CRUD اصلی منطق
  شکسته‌ای ندارد.
- **اما یک باگ واقعی و قابل اثبات وجود دارد**: ناهماهنگی کلید بین
  «تنظیمات Phase 5» و «footer» (FAIL #1). این به‌تنهایی توضیح می‌دهد چرا
  کاربر فکر می‌کند «office locations کار نمی‌کند».
- **یک باگ منطقی متوسط**: عدم استفاده از `is_reserved_slug()` در ذخیره‌ی
  محتوا (FAIL #2). این می‌تواند ظاهر «صفحه ساخته نمی‌شود» را در سناریوهای
  خاص توضیح دهد.
- **بقیه‌ی موارد گزارش‌شده (social, edit styling, page/category create)** در
  سطح سورس درست‌اند. علت بسیار محتمل دیده نشدنشان در پروداکشن یکی از این
  سه است:
  1. دیتابیس کامل seed نشده (phase5.sql اجرا نشده).
  2. docroot روی پوشه‌ی نادرست تنظیم شده.
  3. کاربر مقدار را در فرم پر نکرده یا CSRF منقضی شده submit ناموفق بوده.

**پیشنهاد قاطع برای Phase 7:** قبل از هر بازطراحی UI، حداقل
FAIL #1 و FAIL #2 را در سورس fix کنیم.

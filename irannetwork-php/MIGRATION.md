# راهنمای مهاجرت از وردپرس فعلی irannetwork.co به CMS جدید

سایت جدید جایگزین نسخه‌ی وردپرسی موجود می‌شود. برای حفظ سئو و جلوگیری از 404، باید **همه‌ی URLهای قبلی به URLهای جدید 301-redirect شوند.**

## ۱) استخراج URLهای فعلی وردپرس
گزینه‌ها (یکی را انتخاب کنید):

**الف) از Google Search Console**
- Coverage → Pages → Export → CSV (همه‌ی URLهای ایندکس‌شده)

**ب) از سایت زنده با crawler ساده**
```bash
# لیست URLها از sitemap وردپرس
curl -s https://irannetwork.co/sitemap.xml | grep -oE '<loc>[^<]+' | sed 's/<loc>//' > old-urls.txt
curl -s https://irannetwork.co/sitemap_index.xml | grep -oE '<loc>[^<]+' | sed 's/<loc>//' >> old-urls.txt
```

**ج) از داخل وردپرس**
- پلاگین "Export All URLs"
- یا از phpMyAdmin: `SELECT post_name, post_type FROM wp_posts WHERE post_status='publish';`

## ۲) نگاشت URLهای قدیم به جدید
جدول نگاشت در فایل CSV درست کنید (`migration-map.csv`):

```csv
old_url,new_url,status_code
/خدمات-شبکه/,/services/network-support,301
/?p=123,/blog/voip-installation-guide,301
/about-us/,/about,301
/خدمات-ویپ/,/services/voip,301
```

**قواعد پیشنهادی:**
| الگوی قدیم وردپرس | معادل جدید CMS |
|---|---|
| `/category/network/` | `/category/network` |
| `/tag/voip/` | `/tag/voip` |
| `/{post-slug}/` | `/blog/{slug}` |
| `/services/{slug}/` | `/services/{slug}` |
| `/contact-us/` | `/contact` |
| `/about-us/` | `/about` |

## ۳) ثبت Redirect در پنل
از `/admin/redirects/` می‌توانید تک‌به‌تک یا با Import دسته‌ای (در نسخه‌های آینده) Redirect 301 ثبت کنید. هر Redirect در جدول `redirects` با `old_url`, `new_url`, `status_code=301`, `is_active=1` ذخیره می‌شود.

روتر سایت در ابتدای هر درخواست این جدول را چک می‌کند و قبل از match کردن route عادی، Redirect را اعمال می‌کند.

### Bulk Import (SQL)
از phpMyAdmin، در تب SQL:
```sql
INSERT INTO redirects (old_url, new_url, status_code, is_active) VALUES
('/خدمات-شبکه/',       '/services/network-support', 301, 1),
('/خدمات-ویپ/',        '/services/voip',            301, 1),
('/about-us/',         '/about',                    301, 1),
('/contact-us/',       '/contact',                  301, 1);
```

## ۴) تست قبل از Go-Live
1. سایت جدید را روی subdomain (`new.irannetwork.co`) مستقر کنید.
2. هر URL قدیمی را در مرورگر باز کنید و اطمینان حاصل کنید 301 می‌شود.
3. در Chrome DevTools → Network → ستون Status را بررسی کنید.
4. `curl -I https://new.irannetwork.co/خدمات-ویپ/` باید `301` و `Location: /services/voip` بدهد.

## ۵) Go-Live
1. بکاپ کامل وردپرس فعلی (`wp-content/` + database).
2. تغییر DNS / یا تعویض پوشه `public_html`.
3. در Search Console → Sitemap جدید (`/sitemap.xml`) را Submit کنید.
4. بعد از ۱ تا ۲ هفته، در Search Console → "Coverage" بررسی کنید URLهای قدیم به جدید ایندکس می‌شوند.

## ۶) جلوگیری از افت سئو
- ✅ 301 (نه 302) برای تمام نگاشت‌ها
- ✅ canonical تک‌قطعی روی URL جدید
- ✅ `sitemap.xml` فقط URLهای **جدید** را داشته باشد
- ✅ متادیتای سئو (title/description) از وردپرس را برای صفحات معادل کپی کنید
- ✅ از URL جدید لینک‌های داخلی بسازید (در محتوا)
- ❌ Redirect chain (A→B→C) نسازید
- ❌ از Redirect 302 به جای 301 استفاده نکنید

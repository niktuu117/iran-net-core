# بکاپ و بازیابی — IranNetwork CMS

## بکاپ

### ۱) فایل‌ها
```bash
tar -czf irannetwork-files-$(date +%F).tar.gz \
  -C /home/USER public_html \
  --exclude='public_html/cache/*' \
  --exclude='public_html/app/config/config.php'   # secrets جداگانه نگه‌داری شود
```
- پوشه `public_html/uploads/` حتماً در بکاپ باشد (همه‌ی رسانه‌های کاربر).
- `cache/` لازم نیست بکاپ شود.
- `config.php` را **جداگانه** و در محل امن نگه دارید (شامل پسوردها).

### ۲) دیتابیس
از cPanel → **phpMyAdmin** → دیتابیس → Export → Quick → SQL.
یا از طریق SSH:
```bash
mysqldump -u DB_USER -p DB_NAME \
  --default-character-set=utf8mb4 \
  --single-transaction --quick \
  > irannetwork-db-$(date +%F).sql
gzip irannetwork-db-$(date +%F).sql
```

### ۳) خودکارسازی (cron)
نمونه cron شبانه ساعت ۳ بامداد:
```cron
0 3 * * * cd /home/USER && tar -czf backups/files-$(date +\%F).tar.gz public_html --exclude='public_html/cache/*' >/dev/null 2>&1
15 3 * * * mysqldump -u USER -pPASS DBNAME | gzip > /home/USER/backups/db-$(date +\%F).sql.gz
```
نگه‌داری ۳۰ روز:
```cron
0 4 * * * find /home/USER/backups -mtime +30 -delete
```

## بازیابی

### ۱) فایل‌ها
```bash
tar -xzf irannetwork-files-YYYY-MM-DD.tar.gz -C /home/USER/
```

### ۲) دیتابیس
```bash
gunzip < irannetwork-db-YYYY-MM-DD.sql.gz | mysql -u DB_USER -p DB_NAME
```
یا در phpMyAdmin → Import.

### ۳) config
`app/config/config.php` را در محل امن نگه‌داری کرده و دوباره در سرور قرار دهید، یا از `config.example.php` بازسازی و مقادیر را دوباره وارد کنید.

### ۴) پاکسازی cache
```bash
rm -rf public_html/cache/*.cache public_html/cache/throttle/*
```

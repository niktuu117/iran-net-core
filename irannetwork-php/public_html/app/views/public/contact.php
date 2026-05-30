<section class="page-header">
    <div class="container">
        <nav class="breadcrumbs" aria-label="مسیر"><a href="/">خانه</a> <span>/</span> <span>تماس با ما</span></nav>
        <h1>تماس با ایران نتورک</h1>
        <p>برای دریافت مشاوره، تماس بگیرید یا فرم زیر را پر کنید. در سریع‌ترین زمان پاسخ شما را می‌دهیم.</p>
    </div>
</section>

<section class="section">
    <div class="container contact-grid">
        <div class="contact-info">
            <h2>راه‌های ارتباطی</h2>

            <div class="contact-block">
                <h4><?= icon_svg('pin', 20) ?> دفتر تهران</h4>
                <p><?= e(defined('CONTACT_ADDRESS_TEHRAN') ? CONTACT_ADDRESS_TEHRAN : 'تهران پارس، فلکه اول، خیابان بابا یوسفی، پلاک ۳') ?></p>
                <p><a dir="ltr" href="tel:<?= e(defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664') ?>">021-91014664</a></p>
            </div>

            <div class="contact-block">
                <h4><?= icon_svg('pin', 20) ?> دفتر اصفهان</h4>
                <p><?= e(defined('CONTACT_ADDRESS_ISFAHAN') ? CONTACT_ADDRESS_ISFAHAN : 'اصفهان، شاهین شهر، خیابان امام علی، فرعی ۲ شرقی، پلاک ۲۷') ?></p>
                <p><a dir="ltr" href="tel:<?= e(defined('CONTACT_PHONE_ISFAHAN') ? CONTACT_PHONE_ISFAHAN : '03191011239') ?>">031-91011239</a></p>
            </div>

            <div class="contact-block">
                <h4><?= icon_svg('mail', 20) ?> ایمیل</h4>
                <p><a href="mailto:<?= e(defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@irannetwork.co') ?>"><?= e(defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@irannetwork.co') ?></a></p>
            </div>

            <div class="contact-block">
                <h4><?= icon_svg('clock', 20) ?> ساعات کاری</h4>
                <p>شنبه تا چهارشنبه: ۹:۰۰ تا ۱۸:۰۰</p>
                <p>پنجشنبه: ۹:۰۰ تا ۱۳:۰۰</p>
            </div>
        </div>

        <form class="contact-form" id="contactForm" novalidate>
            <h2>ارسال پیام</h2>
            <p class="form-note">فعلاً فرم تنها فرانت‌اند است؛ ذخیره‌سازی در فاز ۲ فعال می‌شود.</p>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">نام و نام خانوادگی <span>*</span></label>
                    <input type="text" id="name" name="name" required minlength="2" maxlength="80">
                </div>
                <div class="form-group">
                    <label for="phone">شماره تماس <span>*</span></label>
                    <input type="tel" id="phone" name="phone" required pattern="^[0-9+\-\s]{7,20}$" dir="ltr">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">ایمیل (اختیاری)</label>
                    <input type="email" id="email" name="email" maxlength="120" dir="ltr">
                </div>
                <div class="form-group">
                    <label for="service">نوع سرویس</label>
                    <select id="service" name="service">
                        <option value="">انتخاب کنید…</option>
                        <?php foreach (site_services() as $s): ?>
                        <option value="<?= e($s['slug']) ?>"><?= e($s['title']) ?></option>
                        <?php endforeach; ?>
                        <option value="other">سایر</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="message">پیام شما <span>*</span></label>
                <textarea id="message" name="message" rows="5" required minlength="10" maxlength="2000"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">ارسال پیام</button>
            <p class="form-feedback" id="contactFeedback" role="status" aria-live="polite"></p>
        </form>
    </div>
</section>

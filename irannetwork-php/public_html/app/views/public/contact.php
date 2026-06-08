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

            <?php
            $phoneT = site_setting('phone_tehran',  defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664');
            $phoneI = site_setting('phone_isfahan', defined('CONTACT_PHONE_ISFAHAN') ? CONTACT_PHONE_ISFAHAN : '03191011239');
            $email  = site_setting('email',         defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@irannetwork.co');
            $offices = office_locations();
            $phoneMap = ['tehran'=>$phoneT, 'isfahan'=>$phoneI];
            ?>

            <?php foreach ($offices as $office): ?>
            <div class="contact-block">
                <h4><?= icon_svg('pin', 20) ?> <?= e($office['title']) ?></h4>
                <?php if ($office['address']): ?><p><?= e($office['address']) ?></p><?php endif; ?>
                <?php $ph = $phoneMap[$office['key']] ?? ''; if ($ph): ?>
                    <p><a dir="ltr" href="tel:<?= e($ph) ?>"><?= icon_svg('phone',16) ?> <?= e($ph) ?></a></p>
                <?php endif; ?>
                <?php if ($office['map_url']): ?>
                    <p><a class="btn btn-secondary btn-sm" href="<?= e($office['map_url']) ?>" target="_blank" rel="noopener noreferrer">
                        <?= icon_svg('pin',16) ?> مشاهده روی نقشه
                    </a></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <div class="contact-block">
                <h4><?= icon_svg('mail', 20) ?> ایمیل</h4>
                <p><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
            </div>

            <div class="contact-block">
                <h4><?= icon_svg('clock', 20) ?> ساعات کاری</h4>
                <p>شنبه تا چهارشنبه: ۹:۰۰ تا ۱۸:۰۰</p>
                <p>پنجشنبه: ۹:۰۰ تا ۱۳:۰۰</p>
            </div>

            <?php $socials = social_links(); if ($socials): ?>
            <div class="contact-block">
                <h4>شبکه‌های اجتماعی</h4>
                <div class="footer-socials" style="justify-content:flex-start;margin-top:8px">
                    <?php foreach ($socials as $key => $sl): ?>
                        <a href="<?= e($sl['url']) ?>" target="_blank" rel="noopener noreferrer"
                           aria-label="<?= e($sl['label']) ?>" title="<?= e($sl['label']) ?>" class="social-link social-<?= e($key) ?>">
                            <?= icon_svg($sl['icon'], 20) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <form class="contact-form" id="contactForm" method="post" action="/contact" novalidate>
            <?= Csrf::field() ?>
            <h2>ارسال پیام</h2>

            <?php $ok = flash('contact_success'); $err = flash('contact_error'); ?>
            <?php if ($ok): ?>
                <p class="form-feedback success" style="color:#16a34a"><?= e($ok) ?></p>
            <?php endif; ?>
            <?php if ($err): ?>
                <p class="form-feedback error" style="color:#dc2626"><?= e($err) ?></p>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="name">نام و نام خانوادگی <span>*</span></label>
                    <input type="text" id="name" name="name" required minlength="2" maxlength="80" value="<?= e(old('name')) ?>">
                </div>
                <div class="form-group">
                    <label for="phone">شماره تماس <span>*</span></label>
                    <input type="tel" id="phone" name="phone" required pattern="^[0-9+\-\s]{7,20}$" dir="ltr" value="<?= e(old('phone')) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">ایمیل (اختیاری)</label>
                    <input type="email" id="email" name="email" maxlength="120" dir="ltr" value="<?= e(old('email')) ?>">
                </div>
                <div class="form-group">
                    <label for="service">نوع سرویس</label>
                    <select id="service" name="service">
                        <option value="">انتخاب کنید…</option>
                        <?php foreach (site_services() as $s): ?>
                        <option value="<?= e($s['slug']) ?>" <?= old('service')===$s['slug']?'selected':'' ?>><?= e($s['title']) ?></option>
                        <?php endforeach; ?>
                        <option value="other" <?= old('service')==='other'?'selected':'' ?>>سایر</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="message">پیام شما <span>*</span></label>
                <textarea id="message" name="message" rows="5" required minlength="10" maxlength="2000"><?= e(old('message')) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">ارسال پیام</button>
        </form>
    </div>
</section>
<?php clear_old(); ?>

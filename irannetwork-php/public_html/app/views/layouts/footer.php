<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col footer-brand">
                <div class="brand">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 32 32" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 22V10l12 8 12-8v12"/>
                            <circle cx="16" cy="16" r="2" fill="currentColor" stroke="none"/>
                        </svg>
                    </span>
                    <span class="brand-text">
                        <span class="brand-fa">ایران نتورک</span>
                        <span class="brand-en">IranNetwork</span>
                    </span>
                </div>
                <p class="footer-about">
                    ارائه‌دهنده خدمات تخصصی شبکه، سرور، امنیت و ویپ برای کسب‌وکارها و سازمان‌های ایرانی.
                </p>
            </div>

            <div class="footer-col">
                <h4>خدمات</h4>
                <ul>
                    <?php foreach (site_services() as $s): ?>
                    <li><a href="/services/<?= e($s['slug']) ?>"><?= e($s['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h4>دسترسی سریع</h4>
                <ul>
                    <li><a href="/about">درباره ما</a></li>
                    <li><a href="/blog">مقالات</a></li>
                    <li><a href="/faq">سوالات متداول</a></li>
                    <li><a href="/rules">قوانین و مقررات</a></li>
                    <li><a href="/contact">تماس با ما</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>تماس با ما</h4>
                <?php
                $fPhoneT = site_setting('phone_tehran',  defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664');
                $fPhoneI = site_setting('phone_isfahan', defined('CONTACT_PHONE_ISFAHAN') ? CONTACT_PHONE_ISFAHAN : '03191011239');
                $fEmail  = site_setting('email',         defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@irannetwork.co');
                $fAddrT  = site_setting('address_tehran',  defined('CONTACT_ADDRESS_TEHRAN') ? CONTACT_ADDRESS_TEHRAN : '');
                $fAddrI  = site_setting('address_isfahan', defined('CONTACT_ADDRESS_ISFAHAN') ? CONTACT_ADDRESS_ISFAHAN : '');
                ?>
                <ul class="footer-contact">
                    <li><?= icon_svg('pin', 18) ?><span><?= e($fAddrT) ?></span></li>
                    <li><?= icon_svg('pin', 18) ?><span><?= e($fAddrI) ?></span></li>
                    <li><?= icon_svg('phone', 18) ?><a dir="ltr" href="tel:<?= e($fPhoneT) ?>"><?= e($fPhoneT) ?></a></li>
                    <li><?= icon_svg('phone', 18) ?><a dir="ltr" href="tel:<?= e($fPhoneI) ?>"><?= e($fPhoneI) ?></a></li>
                    <li><?= icon_svg('mail', 18) ?><a href="mailto:<?= e($fEmail) ?>"><?= e($fEmail) ?></a></li>
                </ul>
            </div>
        </div>

        <?php $socials = social_links(); if ($socials): ?>
        <div class="footer-socials" aria-label="شبکه‌های اجتماعی">
            <?php foreach ($socials as $key => $s): ?>
                <a href="<?= e($s['url']) ?>" target="_blank" rel="noopener noreferrer"
                   aria-label="<?= e($s['label']) ?>" title="<?= e($s['label']) ?>" class="social-link social-<?= e($key) ?>">
                    <?= icon_svg($s['icon'], 20) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="footer-bottom">
            <p>© <?= e((string)date('Y')) ?> ایران نتورک — تمامی حقوق محفوظ است.</p>
        </div>
    </div>
</footer>

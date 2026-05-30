/* IranNetwork - Main JS (vanilla, no framework) */
(function () {
    'use strict';

    // ---------- Mobile nav toggle ----------
    var toggle = document.getElementById('navToggle');
    var nav    = document.getElementById('primaryNav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'بستن منو' : 'باز کردن منو');
        });

        // Close on link click (mobile)
        nav.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                if (window.innerWidth <= 960) {
                    nav.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }

    // ---------- Reveal-on-scroll ----------
    if ('IntersectionObserver' in window) {
        var revealTargets = document.querySelectorAll(
            '.service-card, .feature-card, .process-list li, .location-card, .blog-card, .about-teaser, .section-head'
        );
        revealTargets.forEach(function (el) { el.classList.add('reveal'); });

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        revealTargets.forEach(function (el) { io.observe(el); });
    }

    // ---------- Contact form (frontend validation only — Phase 1) ----------
    var form     = document.getElementById('contactForm');
    var feedback = document.getElementById('contactFeedback');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            feedback.className = 'form-feedback';
            feedback.textContent = '';

            var name    = form.name.value.trim();
            var phone   = form.phone.value.trim();
            var email   = form.email.value.trim();
            var message = form.message.value.trim();

            if (name.length < 2) {
                showError('لطفاً نام و نام خانوادگی را وارد کنید.');
                return;
            }
            if (!/^[0-9+\-\s]{7,20}$/.test(phone)) {
                showError('شماره تماس معتبر وارد کنید.');
                return;
            }
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError('ایمیل وارد شده معتبر نیست.');
                return;
            }
            if (message.length < 10) {
                showError('پیام شما باید حداقل ۱۰ کاراکتر باشد.');
                return;
            }

            feedback.classList.add('ok');
            feedback.textContent = 'پیام شما آماده ارسال است. ذخیره‌سازی پیام‌ها در فاز ۲ فعال خواهد شد.';
            form.reset();
        });

        function showError(msg) {
            feedback.classList.add('error');
            feedback.textContent = msg;
        }
    }
})();

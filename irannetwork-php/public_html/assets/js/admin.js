// IranNetwork admin: small UX helpers
(function () {
    // Confirm delete forms
    document.querySelectorAll('form[data-confirm]').forEach(f => {
        f.addEventListener('submit', e => {
            if (!confirm(f.dataset.confirm || 'مطمئنید؟')) e.preventDefault();
        });
    });

    // Copy-to-clipboard buttons
    document.querySelectorAll('[data-copy]').forEach(btn => {
        btn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(btn.dataset.copy);
                const old = btn.textContent;
                btn.textContent = 'کپی شد ✓';
                setTimeout(() => btn.textContent = old, 1200);
            } catch (e) {}
        });
    });

    // Auto-slug
    document.querySelectorAll('[data-slug-from]').forEach(slugField => {
        const src = document.getElementById(slugField.dataset.slugFrom);
        if (!src) return;
        let touched = slugField.value.length > 0;
        slugField.addEventListener('input', () => touched = slugField.value.length > 0);
        src.addEventListener('input', () => {
            if (touched) return;
            slugField.value = src.value
                .toString().trim()
                .replace(/[\s_\/\\]+/g, '-')
                .replace(/[^\p{L}\p{N}\-]+/gu, '')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '')
                .toLowerCase();
        });
    });

    // Mini rich editor: wraps a textarea with .js-editor
    document.querySelectorAll('textarea.js-editor').forEach(ta => {
        const bar = document.createElement('div');
        bar.className = 'editor-toolbar';
        const buttons = [
            ['H2',   t => `<h2>${t || 'عنوان'}</h2>\n`],
            ['H3',   t => `<h3>${t || 'زیر‌عنوان'}</h3>\n`],
            ['Bold', t => `<strong>${t || 'متن مهم'}</strong>`],
            ['Link', t => { const u = prompt('آدرس لینک:', 'https://'); return u ? `<a href="${u}">${t || u}</a>` : t; }],
            ['UL',   t => `<ul>\n  <li>${t || 'مورد'}</li>\n</ul>\n`],
            ['Quote',t => `<blockquote>${t || 'نقل قول'}</blockquote>\n`],
            ['CTA',  t => `<div class="cta-box"><h4>${t || 'عنوان CTA'}</h4><p>توضیح…</p><a class="btn btn-primary" href="/contact">تماس بگیرید</a></div>\n`],
        ];
        buttons.forEach(([label, fn]) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = label;
            b.addEventListener('click', () => {
                const start = ta.selectionStart, end = ta.selectionEnd;
                const sel = ta.value.substring(start, end);
                const ins = fn(sel);
                ta.setRangeText(ins, start, end, 'end');
                ta.focus();
            });
            bar.appendChild(b);
        });
        ta.parentNode.insertBefore(bar, ta);
        ta.classList.add('editor-area');
    });
})();

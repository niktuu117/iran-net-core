<?php
/**
 * Admin SEO partial — include inside an edit form.
 * Required vars in scope:
 *   $entityType (string)  'post'|'service'|'page'|'category'|'tag'
 *   $entityId   (int)     existing ID or 0
 *   $entityRow  (array)   the entity row (for live analysis)
 *
 * On POST, the calling page must call seo_save_from_post($entityType, $entityId).
 */
$seoMeta = (new SeoMeta())->findFor($entityType, (int)$entityId) ?? [];
$analysis = SeoAnalyzer::analyze($entityRow ?? [], $seoMeta);
$g = fn(string $k, $d = '') => $seoMeta[$k] ?? $d;
?>
<details class="card" open style="margin-top:18px">
  <summary style="cursor:pointer;font-weight:700;font-size:18px;margin-bottom:14px">⚙️ تنظیمات سئو و سوشال</summary>

  <div class="seo-score-box" style="display:flex;gap:16px;margin:14px 0 22px">
    <div class="seo-score" style="flex:1;padding:14px;border:1px solid rgba(255,255,255,.1);border-radius:10px">
      <div style="font-size:13px;color:var(--muted)">امتیاز سئو</div>
      <div style="font-size:28px;font-weight:800;color:<?= $analysis['seo_score']>=70?'#16a34a':($analysis['seo_score']>=40?'#eab308':'#dc2626') ?>"><?= (int)$analysis['seo_score'] ?>/100</div>
    </div>
    <div class="seo-score" style="flex:1;padding:14px;border:1px solid rgba(255,255,255,.1);border-radius:10px">
      <div style="font-size:13px;color:var(--muted)">خوانایی</div>
      <div style="font-size:28px;font-weight:800;color:<?= $analysis['readability']>=70?'#16a34a':($analysis['readability']>=40?'#eab308':'#dc2626') ?>"><?= (int)$analysis['readability'] ?>/100</div>
    </div>
    <div class="seo-stats" style="flex:2;padding:14px;border:1px solid rgba(255,255,255,.1);border-radius:10px;font-size:13px;color:var(--muted)">
      <strong>آمار:</strong> کلمات <?= $analysis['stats']['words'] ?> · جملات <?= $analysis['stats']['sentences'] ?> · لینک داخلی <?= $analysis['stats']['internal_links'] ?> · لینک خروجی <?= $analysis['stats']['external_links'] ?> · تصاویر <?= $analysis['stats']['images'] ?>/<?= $analysis['stats']['images_with_alt'] ?> با alt · H1: <?= $analysis['stats']['h1_count'] ?>
    </div>
  </div>

  <?php if (!empty($analysis['tips'])): ?>
    <div class="seo-tips" style="padding:12px;background:rgba(234,179,8,.1);border:1px solid rgba(234,179,8,.3);border-radius:8px;margin-bottom:18px">
      <strong>پیشنهادها:</strong>
      <ul style="margin:8px 0 0 18px">
        <?php foreach ($analysis['tips'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="form-grid">
    <div class="form-group full">
      <label>عنوان سئو (SEO Title)</label>
      <input name="seo[seo_title]" maxlength="190" value="<?= e((string)$g('seo_title')) ?>">
      <div class="form-help">ایده‌آل: ۳۰ تا ۶۰ کاراکتر.</div>
    </div>
    <div class="form-group full">
      <label>توضیحات متا (Meta Description)</label>
      <textarea name="seo[meta_description]" rows="3" maxlength="320"><?= e((string)$g('meta_description')) ?></textarea>
      <div class="form-help">ایده‌آل: ۱۲۰ تا ۱۶۰ کاراکتر.</div>
    </div>
    <div class="form-group">
      <label>کلمه کلیدی هدف</label>
      <input name="seo[focus_keyword]" maxlength="190" value="<?= e((string)$g('focus_keyword')) ?>">
    </div>
    <div class="form-group">
      <label>کلمات کلیدی ثانویه (با کاما)</label>
      <input name="seo[secondary_keywords]" maxlength="500" value="<?= e((string)$g('secondary_keywords')) ?>">
    </div>
    <div class="form-group full">
      <label>Canonical URL</label>
      <input name="seo[canonical_url]" dir="ltr" maxlength="500" value="<?= e((string)$g('canonical_url')) ?>">
    </div>
    <div class="form-group">
      <label>Robots</label>
      <div>
        <label><input type="checkbox" name="seo[robots_index]" value="1" <?= (int)$g('robots_index',1)?'checked':'' ?>> index</label>
        &nbsp;
        <label><input type="checkbox" name="seo[robots_follow]" value="1" <?= (int)$g('robots_follow',1)?'checked':'' ?>> follow</label>
      </div>
    </div>
    <div class="form-group">
      <label>نوع Schema</label>
      <select name="seo[schema_type]">
        <?php $st = (string)$g('schema_type',''); foreach (['','Article','Service','FAQPage','Organization','BreadcrumbList'] as $opt): ?>
          <option value="<?= e($opt) ?>" <?= $st===$opt?'selected':'' ?>><?= $opt===''?'پیش‌فرض':$opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label><input type="checkbox" name="seo[enable_schema]" value="1" <?= (int)$g('enable_schema',1)?'checked':'' ?>> فعال‌سازی Schema.org</label>
    </div>

    <div class="form-group full"><hr></div>

    <div class="form-group"><label>OG Title</label><input name="seo[og_title]" maxlength="190" value="<?= e((string)$g('og_title')) ?>"></div>
    <div class="form-group"><label>OG Image (URL)</label><input name="seo[og_image]" dir="ltr" maxlength="500" value="<?= e((string)$g('og_image')) ?>"></div>
    <div class="form-group full"><label>OG Description</label><textarea name="seo[og_description]" rows="2" maxlength="320"><?= e((string)$g('og_description')) ?></textarea></div>

    <div class="form-group"><label>Twitter Title</label><input name="seo[twitter_title]" maxlength="190" value="<?= e((string)$g('twitter_title')) ?>"></div>
    <div class="form-group"><label>Twitter Image (URL)</label><input name="seo[twitter_image]" dir="ltr" maxlength="500" value="<?= e((string)$g('twitter_image')) ?>"></div>
    <div class="form-group full"><label>Twitter Description</label><textarea name="seo[twitter_description]" rows="2" maxlength="320"><?= e((string)$g('twitter_description')) ?></textarea></div>

    <div class="form-group full"><hr></div>

    <div class="form-group">
      <label><input type="checkbox" name="seo[include_in_sitemap]" value="1" <?= (int)$g('include_in_sitemap',1)?'checked':'' ?>> در sitemap نمایش داده شود</label>
    </div>
    <div class="form-group">
      <label>Sitemap Priority (0.0 - 1.0)</label>
      <input name="seo[sitemap_priority]" type="number" step="0.1" min="0" max="1" value="<?= e((string)$g('sitemap_priority','0.5')) ?>">
    </div>
    <div class="form-group">
      <label>Sitemap Changefreq</label>
      <select name="seo[sitemap_changefreq]">
        <?php $cf=(string)$g('sitemap_changefreq','weekly'); foreach(['always','hourly','daily','weekly','monthly','yearly','never'] as $opt): ?>
          <option value="<?= $opt ?>" <?= $cf===$opt?'selected':'' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
</details>
<?php
/**
 * Helper used by edit handlers after main save.
 */
if (!function_exists('seo_save_from_post')) {
function seo_save_from_post(string $entityType, int $entityId): void
{
    if ($entityId <= 0) return;
    $raw = $_POST['seo'] ?? [];
    if (!is_array($raw)) return;
    $data = [
        'seo_title'           => trim((string)($raw['seo_title'] ?? '')) ?: null,
        'meta_description'    => trim((string)($raw['meta_description'] ?? '')) ?: null,
        'focus_keyword'       => trim((string)($raw['focus_keyword'] ?? '')) ?: null,
        'secondary_keywords'  => trim((string)($raw['secondary_keywords'] ?? '')) ?: null,
        'canonical_url'       => trim((string)($raw['canonical_url'] ?? '')) ?: null,
        'robots_index'        => !empty($raw['robots_index']) ? 1 : 0,
        'robots_follow'       => !empty($raw['robots_follow']) ? 1 : 0,
        'og_title'            => trim((string)($raw['og_title'] ?? '')) ?: null,
        'og_description'      => trim((string)($raw['og_description'] ?? '')) ?: null,
        'og_image'            => trim((string)($raw['og_image'] ?? '')) ?: null,
        'twitter_title'       => trim((string)($raw['twitter_title'] ?? '')) ?: null,
        'twitter_description' => trim((string)($raw['twitter_description'] ?? '')) ?: null,
        'twitter_image'       => trim((string)($raw['twitter_image'] ?? '')) ?: null,
        'schema_type'         => trim((string)($raw['schema_type'] ?? '')) ?: null,
        'enable_schema'       => !empty($raw['enable_schema']) ? 1 : 0,
        'include_in_sitemap'  => !empty($raw['include_in_sitemap']) ? 1 : 0,
        'sitemap_priority'    => is_numeric($raw['sitemap_priority'] ?? null) ? (float)$raw['sitemap_priority'] : 0.5,
        'sitemap_changefreq'  => in_array($raw['sitemap_changefreq'] ?? '', ['always','hourly','daily','weekly','monthly','yearly','never'], true) ? $raw['sitemap_changefreq'] : 'weekly',
    ];
    (new SeoMeta())->upsert($entityType, $entityId, $data);
}
}


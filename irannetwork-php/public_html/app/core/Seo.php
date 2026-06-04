<?php
declare(strict_types=1);

/**
 * SEO helpers — render head tags from seo_meta + entity row.
 *
 * Usage (in a view via $this->view with $seo passed):
 *   echo Seo::render([...])
 *
 * Or merge a entity SEO into the layout via build_seo_data().
 */

class Seo
{
    /**
     * Build a normalized SEO data array from an entity row + optional seo_meta row + defaults.
     *
     * @param array<string,mixed>      $entity   raw entity row (post/service/page/category/tag)
     * @param ?array<string,mixed>     $meta     row from seo_meta or null
     * @param array<string,mixed>      $defaults overrides ['title','description','canonical','image']
     * @return array<string,mixed>
     */
    public static function build(array $entity, ?array $meta, array $defaults = []): array
    {
        $title       = $meta['seo_title']        ?? $defaults['title']       ?? ($entity['title']   ?? 'ایران نتورک');
        $description = $meta['meta_description'] ?? $defaults['description'] ?? excerpt($entity['excerpt'] ?? $entity['content'] ?? '', 30);
        $canonical   = $meta['canonical_url']    ?? $defaults['canonical']   ?? current_path();
        $robots      = ((($meta['robots_index']  ?? 1) ? 'index' : 'noindex')) . ',' . ((($meta['robots_follow'] ?? 1) ? 'follow' : 'nofollow'));

        $ogTitle = $meta['og_title']       ?? $title;
        $ogDesc  = $meta['og_description'] ?? $description;
        $ogImage = $meta['og_image']       ?? $defaults['image'] ?? ($entity['featured_image'] ?? site_setting('default_og_image', ''));

        $twTitle = $meta['twitter_title']       ?? $ogTitle;
        $twDesc  = $meta['twitter_description'] ?? $ogDesc;
        $twImage = $meta['twitter_image']       ?? $ogImage;

        return [
            'title'         => $title,
            'description'   => $description,
            'canonical'     => $canonical,
            'robots'        => $robots,
            'og_title'      => $ogTitle,
            'og_description'=> $ogDesc,
            'og_image'      => $ogImage,
            'tw_title'      => $twTitle,
            'tw_description'=> $twDesc,
            'tw_image'      => $twImage,
            'schema_type'   => $meta['schema_type']   ?? null,
            'enable_schema' => (int)($meta['enable_schema'] ?? 1) === 1,
            'focus_keyword' => $meta['focus_keyword'] ?? null,
        ];
    }

    /** Render <meta> tags from build() output (use inside layout head). */
    public static function renderTags(array $seo): string
    {
        $h  = '';
        $h .= '<title>' . e((string)$seo['title']) . '</title>' . "\n";
        $h .= '<meta name="description" content="' . e((string)$seo['description']) . '">' . "\n";
        $h .= '<link rel="canonical" href="' . e((string)$seo['canonical']) . '">' . "\n";
        $h .= '<meta name="robots" content="' . e((string)$seo['robots']) . '">' . "\n";
        $h .= '<meta property="og:type" content="' . e($seo['og_type'] ?? 'website') . '">' . "\n";
        $h .= '<meta property="og:site_name" content="' . e(site_setting('site_name','ایران نتورک') ?? 'ایران نتورک') . '">' . "\n";
        $h .= '<meta property="og:title" content="' . e((string)$seo['og_title']) . '">' . "\n";
        $h .= '<meta property="og:description" content="' . e((string)$seo['og_description']) . '">' . "\n";
        $h .= '<meta property="og:url" content="' . e((string)$seo['canonical']) . '">' . "\n";
        $h .= '<meta property="og:locale" content="fa_IR">' . "\n";
        if (!empty($seo['og_image'])) {
            $h .= '<meta property="og:image" content="' . e((string)$seo['og_image']) . '">' . "\n";
        }
        $h .= '<meta name="twitter:card" content="' . (empty($seo['tw_image']) ? 'summary' : 'summary_large_image') . '">' . "\n";
        $h .= '<meta name="twitter:title" content="' . e((string)$seo['tw_title']) . '">' . "\n";
        $h .= '<meta name="twitter:description" content="' . e((string)$seo['tw_description']) . '">' . "\n";
        if (!empty($seo['tw_image'])) {
            $h .= '<meta name="twitter:image" content="' . e((string)$seo['tw_image']) . '">' . "\n";
        }
        return $h;
    }

    /** Schema.org JSON-LD render. $schemas is an array of associative arrays. */
    public static function renderJsonLd(array $schemas): string
    {
        $out = '';
        foreach ($schemas as $s) {
            if (!$s) continue;
            $out .= '<script type="application/ld+json">' . json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
        }
        return $out;
    }

    public static function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => site_setting('site_name','ایران نتورک'),
            'url'      => site_setting('site_url', defined('APP_URL') ? APP_URL : ''),
            'email'    => site_setting('email', defined('CONTACT_EMAIL') ? CONTACT_EMAIL : ''),
            'telephone'=> array_values(array_filter([
                site_setting('phone_tehran',  defined('CONTACT_PHONE_TEHRAN')  ? CONTACT_PHONE_TEHRAN  : ''),
                site_setting('phone_isfahan', defined('CONTACT_PHONE_ISFAHAN') ? CONTACT_PHONE_ISFAHAN : ''),
            ])),
        ];
    }

    public static function breadcrumbs(array $items): array
    {
        $list = [];
        foreach ($items as $i => $it) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $it['name'],
                'item' => $it['url'],
            ];
        }
        return ['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>$list];
    }

    public static function article(array $post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Article',
            'headline' => $post['title'] ?? '',
            'image'    => $post['featured_image'] ?? null,
            'datePublished' => $post['published_at'] ?? $post['created_at'] ?? null,
            'dateModified'  => $post['updated_at'] ?? null,
            'author'   => ['@type'=>'Person','name'=>$post['author_name'] ?? 'ایران نتورک'],
            'publisher'=> ['@type'=>'Organization','name'=>site_setting('site_name','ایران نتورک')],
        ];
    }

    public static function service(array $svc): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Service',
            'name'     => $svc['title'] ?? '',
            'description' => $svc['excerpt'] ?? null,
            'provider' => ['@type'=>'Organization','name'=>site_setting('site_name','ایران نتورک')],
        ];
    }

    public static function faqPage(array $faqs): array
    {
        $main = [];
        foreach ($faqs as $f) {
            $main[] = [
                '@type' => 'Question',
                'name'  => $f['question'] ?? ($f['q'] ?? ''),
                'acceptedAnswer' => ['@type'=>'Answer','text'=>strip_tags((string)($f['answer'] ?? ($f['a'] ?? '')))],
            ];
        }
        return ['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$main];
    }
}

/**
 * SEO analyzer — returns score + readability + suggestions in Persian.
 * Lightweight, heuristic, content-only.
 */
class SeoAnalyzer
{
    public static function analyze(array $entity, ?array $meta = null): array
    {
        $title = (string)($meta['seo_title'] ?? $entity['title'] ?? $entity['h1'] ?? '');
        $desc  = (string)($meta['meta_description'] ?? $entity['excerpt'] ?? '');
        $kw    = trim((string)($meta['focus_keyword'] ?? ''));
        $html  = (string)($entity['content'] ?? '');
        $text  = trim(strip_tags($html));
        $h1    = (string)($entity['h1'] ?? $entity['title'] ?? '');
        $words = $text === '' ? [] : preg_split('/\s+/u', $text) ?: [];
        $wc    = count($words);

        $score = 0; $maxScore = 0; $tips = [];
        $check = function (bool $ok, int $weight, string $okMsg, string $badMsg) use (&$score, &$maxScore, &$tips) {
            $maxScore += $weight;
            if ($ok) $score += $weight; else $tips[] = $badMsg;
        };

        // Title length
        $tl = mb_strlen($title);
        $check($tl >= 30 && $tl <= 60, 10, '', 'طول عنوان سئو باید بین ۳۰ تا ۶۰ کاراکتر باشد (فعلی: ' . $tl . ').');
        // Description length
        $dl = mb_strlen($desc);
        $check($dl >= 120 && $dl <= 160, 10, '', 'طول توضیحات متا باید بین ۱۲۰ تا ۱۶۰ کاراکتر باشد (فعلی: ' . $dl . ').');
        // Focus keyword presence
        $hasKw = $kw !== '';
        $check($hasKw, 5, '', 'یک کلمه کلیدی هدف (focus keyword) تعریف کنید.');
        if ($hasKw) {
            $kwL = mb_strtolower($kw, 'UTF-8');
            $check(mb_stripos($title, $kw) !== false, 8, '', 'کلمه کلیدی هدف در عنوان سئو نیست.');
            $check(mb_stripos($desc, $kw)  !== false, 6, '', 'کلمه کلیدی هدف در توضیحات متا نیست.');
            $check(mb_stripos($h1, $kw)    !== false, 6, '', 'کلمه کلیدی هدف در H1 صفحه نیست.');
            $check(mb_stripos($text, $kw)  !== false, 6, '', 'کلمه کلیدی هدف در متن مقاله یافت نشد.');
            // First paragraph
            $firstP = '';
            if (preg_match('#<p[^>]*>(.*?)</p>#siu', $html, $m)) $firstP = strip_tags($m[1]);
            $check($firstP !== '' && mb_stripos($firstP, $kw) !== false, 4, '', 'کلمه کلیدی در پاراگراف اول نیست.');
            // H2 occurrence
            $hasH2WithKw = false;
            if (preg_match_all('#<h2[^>]*>(.*?)</h2>#siu', $html, $mm)) {
                foreach ($mm[1] as $h2) if (mb_stripos(strip_tags($h2), $kw) !== false) { $hasH2WithKw = true; break; }
            }
            $check($hasH2WithKw, 4, '', 'هیچ H2 شامل کلمه کلیدی نیست.');
        }
        // Content length
        $check($wc >= 300, 10, '', 'محتوا کوتاه است؛ حداقل ۳۰۰ کلمه توصیه می‌شود (فعلی: ' . $wc . ').');
        // H1 count
        $h1Count = preg_match_all('#<h1[^>]*>#i', $html, $m) ?: 0;
        $check($h1Count <= 1, 5, '', 'بیش از یک تگ H1 در محتوا وجود دارد.');
        // Images alt
        $imgCount = preg_match_all('#<img\b[^>]*>#i', $html, $im) ?: 0;
        $imgAlt   = preg_match_all('#<img\b[^>]*\salt=("|\')[^"\']+\1[^>]*>#i', $html, $ia) ?: 0;
        $check($imgCount === 0 || $imgAlt === $imgCount, 5, '', 'برخی تصاویر فاقد متن جایگزین (alt) هستند.');
        // Internal/external links
        $intLinks = preg_match_all('#<a\b[^>]*href=("|\')(?!https?:)[^"\']+\1#i', $html) ?: 0;
        $extLinks = preg_match_all('#<a\b[^>]*href=("|\')https?://[^"\']+\1#i', $html) ?: 0;
        $check($intLinks >= 1, 4, '', 'حداقل یک لینک داخلی اضافه کنید.');
        $check($extLinks >= 1, 3, '', 'یک لینک خروجی به منبع معتبر مفید است.');
        // Canonical
        $check(!empty($meta['canonical_url'] ?? null) || true, 0, '', '');
        // OG image
        $check(!empty($meta['og_image'] ?? $entity['featured_image'] ?? null), 4, '', 'تصویر OG برای اشتراک‌گذاری شبکه‌های اجتماعی تنظیم نشده.');

        $seoScore = $maxScore > 0 ? (int) round(($score / $maxScore) * 100) : 0;

        // Readability — simple: average sentence length + word length
        $sentences = preg_split('/[.!؟?\n]+/u', $text) ?: [];
        $sentences = array_values(array_filter(array_map('trim', $sentences), fn($s) => $s !== ''));
        $avgWordsPerSentence = $sentences ? $wc / count($sentences) : 0;
        $avgWordLen = $wc ? array_sum(array_map(fn($w) => mb_strlen($w, 'UTF-8'), $words)) / $wc : 0;
        // Lower is better; ideal ~15 words/sentence, ~5 char/word in Persian
        $readScore = 100;
        if ($avgWordsPerSentence > 20) $readScore -= min(30, (int)(($avgWordsPerSentence - 20) * 2));
        if ($avgWordLen > 6)           $readScore -= min(20, (int)(($avgWordLen - 6) * 8));
        if ($wc < 100)                 $readScore -= 25;
        $readScore = max(0, min(100, $readScore));

        if ($readScore < 60) $tips[] = 'جملات را کوتاه‌تر و ساده‌تر کنید تا خوانایی بهبود یابد.';

        return [
            'seo_score'  => $seoScore,
            'readability'=> $readScore,
            'tips'       => $tips,
            'stats'      => [
                'words' => $wc, 'sentences' => count($sentences),
                'avg_words_per_sentence' => round($avgWordsPerSentence, 1),
                'avg_word_length' => round($avgWordLen, 1),
                'images' => $imgCount, 'images_with_alt' => $imgAlt,
                'internal_links' => $intLinks, 'external_links' => $extLinks,
                'h1_count' => $h1Count,
            ],
        ];
    }
}

<?php
/**
 * Main public layout.
 * Variables (any can be passed via Controller::view()):
 *   $content, $pageTitle, $pageDescription, $canonical, $noindex
 *   $seo  (full Seo::build() array — preferred for dynamic pages)
 *   $schemas (array of JSON-LD arrays to embed)
 */
if (!isset($seo) || !is_array($seo)) {
    $seo = [
        'title'         => $pageTitle       ?? 'ایران نتورک',
        'description'   => $pageDescription ?? 'ایران نتورک — خدمات تخصصی شبکه، سرور و امنیت.',
        'canonical'     => isset($canonical) ? site_url($canonical) : site_url(current_path()),
        'robots'        => (!empty($noindex) ? 'noindex' : 'index') . ',follow',
        'og_title'      => $pageTitle ?? 'ایران نتورک',
        'og_description'=> $pageDescription ?? '',
        'og_image'      => site_setting('default_og_image',''),
        'tw_title'      => $pageTitle ?? 'ایران نتورک',
        'tw_description'=> $pageDescription ?? '',
        'tw_image'      => site_setting('default_og_image',''),
        'enable_schema' => true,
    ];
}
$schemas = $schemas ?? [];
// Always include Organization
array_unshift($schemas, Seo::organization());
?><!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Seo::renderTags($seo) ?>
    <meta name="theme-color" content="#0b1530">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">

    <?= Seo::renderJsonLd($schemas) ?>
</head>
<body>
    <?php require __DIR__ . '/header.php'; ?>
    <main id="main">
        <?= $content ?>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
    <script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>

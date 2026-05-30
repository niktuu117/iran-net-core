<?php
/**
 * Main public layout.
 * $content, $pageTitle, $pageDescription, $canonical, $noindex are passed via Controller::view().
 */
$pageTitle       = $pageTitle       ?? 'ایران نتورک';
$pageDescription = $pageDescription ?? 'ایران نتورک — خدمات تخصصی شبکه، سرور و امنیت.';
$canonical       = $canonical       ?? current_path();
$noindex         = $noindex         ?? false;
?><!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <?php if ($noindex): ?>
    <meta name="robots" content="noindex, follow">
    <?php else: ?>
    <meta name="robots" content="index, follow">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="ایران نتورک">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:locale" content="fa_IR">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($pageDescription) ?>">

    <meta name="theme-color" content="#0b1530">

    <!-- Fonts: Vazirmatn via CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">

    <!-- Organization JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "ایران نتورک",
      "alternateName": "IranNetwork",
      "url": "https://irannetwork.co",
      "email": "<?= e(defined('CONTACT_EMAIL') ? CONTACT_EMAIL : 'info@irannetwork.co') ?>",
      "telephone": [
        "<?= e(defined('CONTACT_PHONE_TEHRAN') ? CONTACT_PHONE_TEHRAN : '02191014664') ?>",
        "<?= e(defined('CONTACT_PHONE_ISFAHAN') ? CONTACT_PHONE_ISFAHAN : '03191011239') ?>"
      ],
      "address": [
        { "@type": "PostalAddress", "addressLocality": "تهران", "streetAddress": "تهران پارس، فلکه اول، خیابان بابا یوسفی، پلاک ۳" },
        { "@type": "PostalAddress", "addressLocality": "اصفهان", "streetAddress": "اصفهان، شاهین شهر، خیابان امام علی، فرعی ۲ شرقی، پلاک ۲۷" }
      ]
    }
    </script>
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

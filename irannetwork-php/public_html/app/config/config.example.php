<?php
/**
 * IranNetwork - Configuration Example
 * Copy this file to config.php on the server and fill real values.
 * Do NOT commit config.php (it is gitignored).
 */
declare(strict_types=1);

// --- App ---
define('APP_NAME',  'ایران نتورک');
define('APP_ENV',   'production');   // 'development' | 'production'
define('APP_DEBUG', false);
define('APP_URL',   'https://irannetwork.co');
define('APP_KEY',   'CHANGE_ME_TO_A_LONG_RANDOM_STRING_32_CHARS_MIN');

// --- Database (MySQL/MariaDB) ---
define('DB_HOST',    'localhost');
define('DB_NAME',    'your_database_name');
define('DB_USER',    'your_database_user');
define('DB_PASS',    'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// --- Uploads ---
define('UPLOAD_DIR',      __DIR__ . '/../../uploads/media');     // filesystem path
define('UPLOAD_URL',      '/uploads/media');                     // public URL prefix
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);                      // 5 MB

// --- Cache (Phase 4) ---
define('CACHE_DIR',       __DIR__ . '/../../cache');             // file cache + throttle
define('CACHE_ENABLED',   true);                                 // master switch
define('CACHE_TTL',       600);                                  // default seconds

// --- Session / CSRF ---
define('SESSION_NAME',    'IRNETSESS');
define('CSRF_TOKEN_NAME', '_csrf');

// --- Public contact display defaults (used when site_settings not yet seeded) ---
define('CONTACT_PHONE_TEHRAN',    '02191014664');
define('CONTACT_PHONE_ISFAHAN',   '03191011239');
define('CONTACT_EMAIL',           'info@irannetwork.co');
define('CONTACT_ADDRESS_TEHRAN',  'تهران پارس، فلکه اول، خیابان بابا یوسفی، پلاک ۳');
define('CONTACT_ADDRESS_ISFAHAN', 'اصفهان، شاهین شهر، خیابان امام علی، فرعی ۲ شرقی، پلاک ۲۷');

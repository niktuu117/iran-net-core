<?php
/**
 * IranNetwork - Configuration Example
 *
 * Copy this file to config.php and fill in your real values.
 * NEVER commit config.php with real secrets to git.
 */

declare(strict_types=1);

// Environment: 'development' or 'production'
define('APP_ENV',  'production');

// Site basics
define('APP_NAME', 'ایران نتورک');
define('APP_URL',  'https://irannetwork.co');

// Database (used in Phase 2)
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// Security
define('APP_KEY', 'CHANGE_ME_TO_A_LONG_RANDOM_STRING_32_CHARS_MIN');

// Contact info (display)
define('CONTACT_PHONE_TEHRAN',   '02191014664');
define('CONTACT_PHONE_ISFAHAN',  '03191011239');
define('CONTACT_EMAIL',          'info@irannetwork.co');
define('CONTACT_ADDRESS_TEHRAN', 'تهران پارس، فلکه اول، خیابان بابا یوسفی، پلاک ۳');
define('CONTACT_ADDRESS_ISFAHAN','اصفهان، شاهین شهر، خیابان امام علی، فرعی ۲ شرقی، پلاک ۲۷');

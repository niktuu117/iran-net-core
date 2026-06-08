-- IranNetwork — Phase 5 migration: User roles + Social/Office settings
-- Run AFTER schema.sql, seed.sql, and seo_redirects.sql.
-- Idempotent: safe to re-run.

SET NAMES utf8mb4;

-- ---------- 1) Expand users.role ENUM with super_admin ----------
ALTER TABLE `users`
  MODIFY COLUMN `role`
    ENUM('super_admin','admin','editor','user')
    NOT NULL DEFAULT 'editor';

-- Promote the first existing admin to super_admin (only if none yet)
UPDATE `users`
   SET `role` = 'super_admin'
 WHERE `id` = (SELECT id FROM (SELECT id FROM users WHERE role='admin' ORDER BY id ASC LIMIT 1) AS t)
   AND NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM users WHERE role='super_admin' LIMIT 1) AS u);

-- ---------- 2) Site settings seeds: social + offices ----------
INSERT IGNORE INTO `site_settings` (`setting_key`,`setting_value`,`setting_type`) VALUES
-- Social
('social_instagram','','text'),
('social_telegram','','text'),
('social_whatsapp','','text'),
('social_linkedin','','text'),
('social_aparat','','text'),
('social_youtube','','text'),
('social_twitter','','text'),
-- Offices (Tehran)
('office_tehran_title','دفتر تهران','text'),
('office_tehran_address','تهران پارس، فلکه اول، خیابان بابا یوسفی، پلاک ۳','text'),
('office_tehran_lat','35.7448','text'),
('office_tehran_lng','51.5036','text'),
-- Offices (Isfahan)
('office_isfahan_title','دفتر اصفهان','text'),
('office_isfahan_address','اصفهان، شاهین شهر، خیابان امام علی، فرعی ۲ شرقی، پلاک ۲۷','text'),
('office_isfahan_lat','32.6549','text'),
('office_isfahan_lng','51.5520','text');

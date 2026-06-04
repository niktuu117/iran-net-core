-- IranNetwork — Phase 3 migration: SEO + Redirects
-- Run AFTER schema.sql (Phase 2). Idempotent via IF NOT EXISTS / INSERT IGNORE.

SET NAMES utf8mb4;

-- ---------- seo_meta ----------
CREATE TABLE IF NOT EXISTS `seo_meta` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `entity_type` ENUM('post','service','page','category','tag') NOT NULL,
  `entity_id` INT UNSIGNED NOT NULL,
  `seo_title` VARCHAR(190) NULL,
  `meta_description` VARCHAR(320) NULL,
  `focus_keyword` VARCHAR(190) NULL,
  `secondary_keywords` VARCHAR(500) NULL,
  `canonical_url` VARCHAR(500) NULL,
  `robots_index` TINYINT(1) NOT NULL DEFAULT 1,
  `robots_follow` TINYINT(1) NOT NULL DEFAULT 1,
  `og_title` VARCHAR(190) NULL,
  `og_description` VARCHAR(320) NULL,
  `og_image` VARCHAR(500) NULL,
  `twitter_title` VARCHAR(190) NULL,
  `twitter_description` VARCHAR(320) NULL,
  `twitter_image` VARCHAR(500) NULL,
  `schema_type` VARCHAR(50) NULL,
  `enable_schema` TINYINT(1) NOT NULL DEFAULT 1,
  `include_in_sitemap` TINYINT(1) NOT NULL DEFAULT 1,
  `sitemap_priority` DECIMAL(2,1) NOT NULL DEFAULT 0.5,
  `sitemap_changefreq` ENUM('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'weekly',
  `seo_score` TINYINT UNSIGNED NULL,
  `readability_score` TINYINT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_entity` (`entity_type`,`entity_id`),
  KEY `idx_sitemap` (`include_in_sitemap`,`entity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- redirects ----------
CREATE TABLE IF NOT EXISTS `redirects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `old_url` VARCHAR(500) NOT NULL,
  `new_url` VARCHAR(500) NOT NULL,
  `status_code` SMALLINT UNSIGNED NOT NULL DEFAULT 301,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `hits` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_old_url` (`old_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add a few common SEO-related site_settings
INSERT IGNORE INTO `site_settings` (`setting_key`,`setting_value`,`setting_type`) VALUES
('robots_extra','','text'),
('site_url','https://irannetwork.co','text'),
('default_og_image','','text');

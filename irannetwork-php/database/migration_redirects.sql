-- IranNetwork — Migration redirects template
-- Run AFTER phase5.sql. Edit/extend before importing.
-- Each row 301-redirects an old WordPress URL to the new CMS URL.

SET NAMES utf8mb4;

INSERT IGNORE INTO `redirects` (`old_url`,`new_url`,`status_code`,`is_active`) VALUES
-- Generic pages
('/about-us',        '/about',    301, 1),
('/about-us/',       '/about',    301, 1),
('/contact-us',      '/contact',  301, 1),
('/contact-us/',     '/contact',  301, 1),
('/blog/',           '/blog',     301, 1),
('/services/',       '/services', 301, 1),

-- Old service slugs (Persian)
('/خدمات-شبکه',           '/services/network-support',     301, 1),
('/خدمات-شبکه/',          '/services/network-support',     301, 1),
('/نصب-و-راه-اندازی-شبکه','/services/network-installation',301, 1),
('/خدمات-ویپ',            '/services/voip',                301, 1),
('/خدمات-ویپ/',           '/services/voip',                301, 1),
('/امنیت-شبکه',           '/services/network-security',    301, 1),
('/پشتیبانی-سرور',        '/services/server-support',      301, 1),
('/دیجیتال-مارکتینگ',     '/services/digital-marketing',   301, 1),
('/اکتیو-شبکه',           '/services/active-network',      301, 1),
('/پسیو-شبکه',            '/services/passive-network',     301, 1);

-- Add per-post and per-category rows below after exporting URLs from
-- the live WordPress site. See MIGRATION_REPORT.md for guidance.

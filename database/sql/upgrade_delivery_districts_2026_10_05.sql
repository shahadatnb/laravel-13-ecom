-- =====================================================
-- Delivery Districts Master Table Upgrade
-- Migration: 2026_10_05_000001_create_districts_table
--
-- Run this ONCE on production (take a backup first).
--
-- What it does:
--   1. Creates the `districts` master table
--      (name, division, zone FK, per-district charge
--      override, status)
--   2. Moves every row from `delivery_zone_districts`
--      into `districts` (zone, status and renames kept)
--   3. Fills the division for every canonical district
--      (renamed districts keep division = NULL)
--   4. Drops the legacy `delivery_zone_districts` table
--   5. Records the migration so `php artisan migrate`
--      will not try to run it again
--
-- After importing: upload the new code + public/build.
-- =====================================================

-- 1. Create districts master table
CREATE TABLE `districts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `division` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_zone_id` bigint UNSIGNED DEFAULT NULL,
  `charge` decimal(10,2) DEFAULT NULL COMMENT 'Per-district charge override; null falls back to zone/default',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `districts_name_unique` (`name`),
  KEY `districts_status_index` (`status`),
  KEY `districts_delivery_zone_id_index` (`delivery_zone_id`),
  CONSTRAINT `districts_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Move legacy rows into districts (zone + status + edits preserved)
INSERT INTO `districts` (`name`, `division`, `delivery_zone_id`, `charge`, `status`, `created_at`, `updated_at`)
SELECT `name`, NULL, `delivery_zone_id`, NULL, `status`, `created_at`, `updated_at`
FROM `delivery_zone_districts`;

-- 3. Fill division for canonical district names
UPDATE `districts` SET `division` = 'Dhaka Division' WHERE `division` IS NULL AND `name` IN (
  'Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Kishoreganj', 'Madaripur', 'Manikganj',
  'Munshiganj', 'Narayanganj', 'Narsingdi', 'Rajbari', 'Shariatpur', 'Tangail'
);

UPDATE `districts` SET `division` = 'Mymensingh Division' WHERE `division` IS NULL AND `name` IN (
  'Jamalpur', 'Mymensingh', 'Netrokona', 'Sherpur'
);

UPDATE `districts` SET `division` = 'Chittagong Division' WHERE `division` IS NULL AND `name` IN (
  'Bandarban', 'Brahmanbaria', 'Chandpur', 'Chittagong', 'Comilla', 'Cox''s Bazar', 'Feni',
  'Khagrachhari', 'Lakshmipur', 'Noakhali', 'Rangamati'
);

UPDATE `districts` SET `division` = 'Khulna Division' WHERE `division` IS NULL AND `name` IN (
  'Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah', 'Khulna', 'Kushtia', 'Magura',
  'Meherpur', 'Narail', 'Satkhira'
);

UPDATE `districts` SET `division` = 'Rajshahi Division' WHERE `division` IS NULL AND `name` IN (
  'Bogra', 'Joypurhat', 'Naogaon', 'Natore', 'Nawabganj', 'Pabna', 'Rajshahi', 'Sirajganj'
);

UPDATE `districts` SET `division` = 'Rangpur Division' WHERE `division` IS NULL AND `name` IN (
  'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh',
  'Rangpur', 'Thakurgaon'
);

UPDATE `districts` SET `division` = 'Sylhet Division' WHERE `division` IS NULL AND `name` IN (
  'Habiganj', 'Maulvibazar', 'Sunamganj', 'Sylhet'
);

UPDATE `districts` SET `division` = 'Barisal Division' WHERE `division` IS NULL AND `name` IN (
  'Barguna', 'Barisal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur'
);

-- 4. Drop the legacy table
DROP TABLE `delivery_zone_districts`;

-- 5. Mark the migration as already run
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_05_000001_create_districts_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;

-- =====================================================
-- Verification (optional):
--   SELECT COUNT(*) AS total,
--          SUM(delivery_zone_id IS NOT NULL) AS zoned,
--          SUM(division IS NOT NULL) AS with_division
--   FROM districts;
-- Expected: total = 64, zoned = 64, with_division = 64
-- =====================================================

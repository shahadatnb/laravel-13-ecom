-- ============================================================================
-- Enterprise Ecommerce ERP - Production (cPanel) database update
-- Migration range : 2026-10-06 -> 2026-10-17   (5 migration files)
--
--   2026_10_06_074848_add_stock_unit_to_products_table
--   2026_10_06_074849_add_unit_weight_to_product_variants_table
--   2026_10_06_074850_add_stock_tracking_to_orders_table
--   2026_10_06_080447_convert_inventory_quantities_to_decimal
--   2026_10_17_000001_add_allow_oversell_setting_to_site_settings
--
-- HOW TO RUN (cPanel):
--   1. cPanel -> phpMyAdmin -> select the production database
--      (e.g. laravel_ecommerce) in the left sidebar
--   2. Import tab -> choose this file -> Go
--
-- The script is idempotent: every statement is either a no-op when the
-- change is already applied, or guarded by an information_schema check.
-- Safe to run more than once. MySQL 5.7+/8.x and MariaDB 10.2+ supported.
-- ============================================================================


-- ----------------------------------------------------------------------------
-- 1) 2026_10_06_074848 - products: stock -> decimal(10,3), add stock_unit
-- ----------------------------------------------------------------------------
ALTER TABLE `products`
    MODIFY `stock` DECIMAL(10,3) NOT NULL DEFAULT 0;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `products` ADD COLUMN `stock_unit` VARCHAR(8) NOT NULL DEFAULT ''unit'' AFTER `stock`',
        'SELECT ''SKIP: products.stock_unit already exists'' AS note')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'products'
      AND COLUMN_NAME = 'stock_unit'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ----------------------------------------------------------------------------
-- 2) 2026_10_06_074849 - product_variants: add unit_weight
-- ----------------------------------------------------------------------------
SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `product_variants` ADD COLUMN `unit_weight` DECIMAL(8,3) DEFAULT NULL AFTER `stock`',
        'SELECT ''SKIP: product_variants.unit_weight already exists'' AS note')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'product_variants'
      AND COLUMN_NAME = 'unit_weight'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ----------------------------------------------------------------------------
-- 3) 2026_10_06_074850 - orders: add stock_deducted_at / stock_restocked_at
-- ----------------------------------------------------------------------------
SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `orders` ADD COLUMN `stock_deducted_at` TIMESTAMP NULL DEFAULT NULL AFTER `shipping_status`',
        'SELECT ''SKIP: orders.stock_deducted_at already exists'' AS note')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND COLUMN_NAME = 'stock_deducted_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE `orders` ADD COLUMN `stock_restocked_at` TIMESTAMP NULL DEFAULT NULL AFTER `stock_deducted_at`',
        'SELECT ''SKIP: orders.stock_restocked_at already exists'' AS note')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'orders'
      AND COLUMN_NAME = 'stock_restocked_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ----------------------------------------------------------------------------
-- 4) 2026_10_06_080447 - inventory tables: integers -> decimal(10,3)
--    available_stock is a STORED generated column: MySQL cannot change its
--    type in place, so it is dropped first and recreated afterwards.
-- ----------------------------------------------------------------------------

-- 4a) drop the old generated column only when it exists with the wrong type
SET @has_available = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inventory'
      AND COLUMN_NAME = 'available_stock'
);
SET @available_ok = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inventory'
      AND COLUMN_NAME = 'available_stock'
      AND COLUMN_TYPE LIKE 'decimal(10,3)%'
      AND EXTRA LIKE '%STORED GENERATED%'
);
SET @sql = IF(@has_available = 1 AND @available_ok = 0,
    'ALTER TABLE `inventory` DROP COLUMN `available_stock`',
    'SELECT ''SKIP: inventory.available_stock drop not needed'' AS note');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4b) base stock columns -> decimal(10,3)
ALTER TABLE `inventory`
    MODIFY `current_stock`  DECIMAL(10,3) NOT NULL DEFAULT 0,
    MODIFY `reserved_stock` DECIMAL(10,3) NOT NULL DEFAULT 0,
    MODIFY `minimum_stock`  DECIMAL(10,3) NOT NULL DEFAULT 0,
    MODIFY `maximum_stock`  DECIMAL(10,3) DEFAULT NULL,
    MODIFY `reorder_level`  DECIMAL(10,3) NOT NULL DEFAULT 0;

ALTER TABLE `inventory_transactions`
    MODIFY `quantity_before` DECIMAL(10,3) NOT NULL DEFAULT 0,
    MODIFY `quantity_change` DECIMAL(10,3) NOT NULL,
    MODIFY `quantity_after`  DECIMAL(10,3) NOT NULL DEFAULT 0;

ALTER TABLE `inventory_adjustments`
    MODIFY `quantity_before` DECIMAL(10,3) NOT NULL,
    MODIFY `quantity_change` DECIMAL(10,3) NOT NULL,
    MODIFY `quantity_after`  DECIMAL(10,3) NOT NULL;

-- 4c) recreate the generated column with the new decimal type
SET @sql = IF(@available_ok = 0,
    'ALTER TABLE `inventory` ADD COLUMN `available_stock` DECIMAL(10,3) GENERATED ALWAYS AS (`current_stock` - `reserved_stock`) STORED',
    'SELECT ''SKIP: inventory.available_stock already decimal(10,3)'' AS note');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- ----------------------------------------------------------------------------
-- 5) 2026_10_17_000001 - site_settings: allow_oversell switch
-- ----------------------------------------------------------------------------
INSERT INTO `site_settings` (`key`, `value`, `group`, `label`, `type`, `created_at`, `updated_at`)
SELECT 'allow_oversell', '0', 'checkout', 'Allow Overselling (sell beyond stock)', 'boolean', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM (SELECT `key` FROM `site_settings`) AS `existing`
    WHERE `existing`.`key` = 'allow_oversell'
);


-- ----------------------------------------------------------------------------
-- Bookkeeping: mark the five migrations as applied so a future
-- `php artisan migrate` on production will not run them again.
-- ----------------------------------------------------------------------------
SET @batch = (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT `m`.`migration`, @batch
FROM (
    SELECT '2026_10_06_074848_add_stock_unit_to_products_table' AS `migration`
    UNION ALL
    SELECT '2026_10_06_074849_add_unit_weight_to_product_variants_table'
    UNION ALL
    SELECT '2026_10_06_074850_add_stock_tracking_to_orders_table'
    UNION ALL
    SELECT '2026_10_06_080447_convert_inventory_quantities_to_decimal'
    UNION ALL
    SELECT '2026_10_17_000001_add_allow_oversell_setting_to_site_settings'
) AS `m`
WHERE NOT EXISTS (
    SELECT 1
    FROM (SELECT `migration` FROM `migrations`) AS `applied`
    WHERE `applied`.`migration` = `m`.`migration`
);


-- ============================================================================
-- Verification queries (expected results noted above each query)
-- ============================================================================
-- Expected: 5 rows (products.stock_unit, product_variants.unit_weight,
--                   orders.stock_deducted_at, orders.stock_restocked_at,
--                   inventory.available_stock)
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND (
    (TABLE_NAME = 'products' AND COLUMN_NAME = 'stock_unit')
    OR (TABLE_NAME = 'product_variants' AND COLUMN_NAME = 'unit_weight')
    OR (TABLE_NAME = 'orders' AND COLUMN_NAME IN ('stock_deducted_at', 'stock_restocked_at'))
    OR (TABLE_NAME = 'inventory' AND COLUMN_NAME = 'available_stock')
  )
ORDER BY TABLE_NAME, COLUMN_NAME;

-- Expected: 1 row with value 0 and type boolean
SELECT `key`, `value`, `type`
FROM `site_settings`
WHERE `key` = 'allow_oversell';

-- Expected: 5 rows (one per migration in this range)
SELECT `migration`, `batch`
FROM `migrations`
WHERE `migration` IN (
    '2026_10_06_074848_add_stock_unit_to_products_table',
    '2026_10_06_074849_add_unit_weight_to_product_variants_table',
    '2026_10_06_074850_add_stock_tracking_to_orders_table',
    '2026_10_06_080447_convert_inventory_quantities_to_decimal',
    '2026_10_17_000001_add_allow_oversell_setting_to_site_settings'
);

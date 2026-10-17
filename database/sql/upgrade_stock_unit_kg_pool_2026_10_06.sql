-- =====================================================
-- Kilogram Bulk Stock Pool (Option B) Upgrade
-- Migrations:
--   2026_10_06_074848_add_stock_unit_to_products_table
--   2026_10_06_074849_add_unit_weight_to_product_variants_table
--   2026_10_06_074850_add_stock_tracking_to_orders_table
--   2026_10_06_080447_convert_inventory_quantities_to_decimal
--
-- Run this ONCE on production (take a backup first).
--
-- What it does:
--   1. products.stock becomes DECIMAL(10,3) (kg pool) and
--      gains stock_unit ('unit' | 'kg')
--   2. product_variants gains unit_weight DECIMAL(8,3)
--      (kg consumed per variant sold)
--   3. orders gains stock_deducted_at / stock_restocked_at
--      (null marks legacy orders that must never restock)
--   4. inventory quantity columns convert INT -> DECIMAL(10,3)
--      (generated available_stock column is dropped and
--      re-added with the new type; its old index goes away
--      with the drop, matching `php artisan migrate`)
--   5. inventory_transactions / inventory_adjustments
--      quantity columns convert INT -> DECIMAL(10,3)
--   6. Records the migrations so `php artisan migrate`
--      will not try to run them again
--
-- After importing: upload the new code + public/build.
-- =====================================================

-- 1. products: kilogram stock pool + stock unit
ALTER TABLE `products`
  MODIFY `stock` DECIMAL(10,3) NOT NULL DEFAULT 0,
  ADD COLUMN `stock_unit` VARCHAR(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit' AFTER `stock`;

-- 2. product_variants: kilograms consumed per variant
ALTER TABLE `product_variants`
  ADD COLUMN `unit_weight` DECIMAL(8,3) NULL AFTER `stock`;

-- 3. orders: stock tracking timestamps
ALTER TABLE `orders`
  ADD COLUMN `stock_deducted_at` TIMESTAMP NULL DEFAULT NULL AFTER `shipping_status`,
  ADD COLUMN `stock_restocked_at` TIMESTAMP NULL DEFAULT NULL AFTER `stock_deducted_at`;

-- 4. inventory: quantities become decimal (kg-friendly)
ALTER TABLE `inventory`
  MODIFY `current_stock` DECIMAL(10,3) NOT NULL DEFAULT 0,
  MODIFY `reserved_stock` DECIMAL(10,3) NOT NULL DEFAULT 0,
  MODIFY `minimum_stock` DECIMAL(10,3) NOT NULL DEFAULT 0,
  MODIFY `maximum_stock` DECIMAL(10,3) NULL DEFAULT NULL,
  MODIFY `reorder_level` DECIMAL(10,3) NOT NULL DEFAULT 0;

-- Generated column cannot change type in place: drop + re-add.
-- Must be two separate statements (merge raises error 1089).
-- (DROP COLUMN also drops its index; migrate behaves the same.)
ALTER TABLE `inventory`
  DROP COLUMN `available_stock`;

ALTER TABLE `inventory`
  ADD COLUMN `available_stock` DECIMAL(10,3) GENERATED ALWAYS AS (`current_stock` - `reserved_stock`) STORED;

-- 5. inventory_transactions / inventory_adjustments
ALTER TABLE `inventory_transactions`
  MODIFY `quantity_before` DECIMAL(10,3) NOT NULL DEFAULT 0,
  MODIFY `quantity_change` DECIMAL(10,3) NOT NULL,
  MODIFY `quantity_after` DECIMAL(10,3) NOT NULL DEFAULT 0;

ALTER TABLE `inventory_adjustments`
  MODIFY `quantity_before` DECIMAL(10,3) NOT NULL,
  MODIFY `quantity_change` DECIMAL(10,3) NOT NULL,
  MODIFY `quantity_after` DECIMAL(10,3) NOT NULL;

-- 6. Mark the migrations as already run
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_06_074848_add_stock_unit_to_products_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_06_074849_add_unit_weight_to_product_variants_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_06_074850_add_stock_tracking_to_orders_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_06_080447_convert_inventory_quantities_to_decimal', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;

-- =====================================================
-- Verification (optional):
--   SELECT COLUMN_TYPE FROM information_schema.COLUMNS
--   WHERE TABLE_SCHEMA = DATABASE()
--     AND ((TABLE_NAME = 'products' AND COLUMN_NAME IN ('stock','stock_unit'))
--       OR (TABLE_NAME = 'product_variants' AND COLUMN_NAME = 'unit_weight')
--       OR (TABLE_NAME = 'orders' AND COLUMN_NAME IN ('stock_deducted_at','stock_restocked_at'))
--       OR (TABLE_NAME = 'inventory' AND COLUMN_NAME IN ('current_stock','available_stock')));
-- Expected: stock / *_stock / quantity_* = decimal(10,3),
--           stock_unit = varchar(8), unit_weight = decimal(8,3)
--
--   SELECT migration FROM migrations
--   WHERE migration LIKE '2026_10_06_0748%' OR migration LIKE '2026_10_06_0804%';
-- Expected: 4 rows
-- =====================================================

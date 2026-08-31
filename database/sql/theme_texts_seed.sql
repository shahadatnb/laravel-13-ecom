-- =====================================================
-- Theme Texts / Marketing Copy Seed
-- Generated from migration: 2026_08_31_012014
-- Run this SQL to insert default theme texts into
-- the site_settings table.
-- =====================================================

-- Common Elements
INSERT INTO site_settings (`key`, `value`, `type`, `group`, `created_at`, `updated_at`)
VALUES
('theme_text_secure_checkout', 'Secure Checkout', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_secure_checkout_desc', '100% secure checkout', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_easy_returns', 'Easy Returns', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_easy_returns_desc', '7-day return policy', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_support_247', '24/7 Support', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_support_247_desc', 'Dedicated support', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_cash_on_delivery', 'Cash on Delivery', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_original_product', 'Original Product', 'text', 'theme_texts', NOW(), NOW()),

-- Section Titles & Subtitles
('theme_text_shop_by_category', 'Shop by Category', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_shop_by_category_subtitle', 'Browse our top categories', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_featured_products', 'Featured Products', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_featured_products_subtitle', 'Handpicked items just for you', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_new_arrivals', 'New Arrivals', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_new_arrivals_subtitle', 'Latest products for you', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_featured_deals', 'Featured Deals', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_featured_deals_subtitle', 'Handpicked bargains for you', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_shop_the_collection', 'Shop the Collection', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_collection_subtitle', 'Every product is handpicked, tested, and guaranteed.', 'text', 'theme_texts', NOW(), NOW()),

-- Hero Fallback (when no slides in DB)
('theme_text_hero_title', 'Discover the Best Deals Online', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_hero_subtitle', 'Shop the latest trends with amazing prices. Quality products, fast delivery, and exceptional customer service.', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_hero_cta', 'Shop Now', 'text', 'theme_texts', NOW(), NOW()),

-- Urgency Strip (Deals Theme)
('theme_text_urgency_flash_deals', '⚡ Flash deals dropping daily', 'text', 'theme_texts', NOW(), NOW()),
('theme_text_urgency_easy_returns', '🔄 Easy 7-day returns', 'text', 'theme_texts', NOW(), NOW())

ON DUPLICATE KEY UPDATE
  `value` = VALUES(`value`),
  `type` = VALUES(`type`),
  `group` = VALUES(`group`),
  `updated_at` = NOW();

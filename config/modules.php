<?php

return [
    'coupons' => [
        'label' => 'Coupons & Promotions',
        'group' => 'Marketing',
        'description' => 'Discount coupons with product, category, and customer targeting.',
        'icon' => 'fa-ticket-alt',
        'enabled' => env('MODULE_COUPONS', true),
    ],
    'wishlist' => [
        'label' => 'Wishlist',
        'group' => 'Storefront',
        'description' => 'Let customers save products to a wishlist.',
        'icon' => 'fa-heart',
        'enabled' => env('MODULE_WISHLIST', true),
    ],
    'inventory' => [
        'label' => 'Inventory & Warehouses',
        'group' => 'Operations',
        'description' => 'Stock levels, warehouses, transfers, and stock adjustments.',
        'icon' => 'fa-warehouse',
        'enabled' => env('MODULE_INVENTORY', true),
    ],
    'delivery_zones' => [
        'label' => 'Delivery Zones',
        'group' => 'Operations',
        'description' => 'Shipping areas with district-based delivery charges.',
        'icon' => 'fa-truck',
        'enabled' => env('MODULE_DELIVERY_ZONES', true),
    ],
    'pages' => [
        'label' => 'Custom Pages',
        'group' => 'Content',
        'description' => 'Static pages for about, privacy, terms, and other content.',
        'icon' => 'fa-file-alt',
        'enabled' => env('MODULE_PAGES', true),
    ],
    'media' => [
        'label' => 'Media Library',
        'group' => 'Content',
        'description' => 'Central library for images and uploads used across the site.',
        'icon' => 'fa-photo-video',
        'enabled' => env('MODULE_MEDIA', true),
    ],
];

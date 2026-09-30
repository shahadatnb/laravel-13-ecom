<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

uses()->group('api', 'product', 'category-sort');

beforeEach(function () {
    $this->brand = Brand::factory()->create();

    $this->parent = Category::factory()->create([
        'slug' => 'parent-category',
        'parent_id' => null,
        'sort_order' => 0,
        'status' => 'active',
    ]);

    // Subcategory with lower sort_order appears first
    $this->childA = Category::factory()->create([
        'slug' => 'child-a',
        'parent_id' => $this->parent->id,
        'sort_order' => 1,
        'status' => 'active',
    ]);

    $this->childB = Category::factory()->create([
        'slug' => 'child-b',
        'parent_id' => $this->parent->id,
        'sort_order' => 2,
        'status' => 'active',
    ]);
});

test('category listing groups products by subcategory sort_order, not created_at', function () {
    // Newest product belongs to child-b (sort_order 2)
    $productInB = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->childB->id,
        'slug' => 'product-in-child-b',
        'status' => 'published',
        'created_at' => now(),
    ]);

    // Oldest product belongs to child-a (sort_order 1)
    $productInA = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->childA->id,
        'slug' => 'product-in-child-a',
        'status' => 'published',
        'created_at' => now()->subYear(),
    ]);

    $response = $this->getJson('/api/products?category=parent-category');

    $response->assertStatus(200)->assertJson(['success' => true]);

    $slugs = collect($response->json('data.data'))->pluck('slug');

    expect($slugs->all())->toBe(['product-in-child-a', 'product-in-child-b']);
});

test('explicit sort option still overrides subcategory grouping', function () {
    $productInB = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->childB->id,
        'slug' => 'expensive-product',
        'status' => 'published',
        'regular_price' => 500,
        'sale_price' => null,
        'created_at' => now()->subYear(),
    ]);

    $productInA = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->childA->id,
        'slug' => 'cheap-product',
        'status' => 'published',
        'regular_price' => 50,
        'sale_price' => null,
        'created_at' => now(),
    ]);

    $response = $this->getJson('/api/products?category=parent-category&sort=price_high');

    $response->assertStatus(200);

    $slugs = collect($response->json('data.data'))->pluck('slug');

    expect($slugs->first())->toBe('expensive-product');
});

test('parent category products come before subcategory products', function () {
    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->childB->id,
        'slug' => 'sub-product',
        'status' => 'published',
        'created_at' => now(),
    ]);

    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->parent->id,
        'slug' => 'parent-product',
        'status' => 'published',
        'created_at' => now()->subYear(),
    ]);

    $response = $this->getJson('/api/products?category=parent-category');

    $slugs = collect($response->json('data.data'))->pluck('slug');

    expect($slugs->all())->toBe(['parent-product', 'sub-product']);
});

<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

uses()->group('api', 'product', 'visibility');

beforeEach(function () {
    $this->brand = Brand::factory()->create();
    $this->category = Category::factory()->create(['status' => 'active']);
});

test('index only returns published products', function () {
    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'visible-product',
        'status' => 'published',
    ]);

    foreach (['hidden', 'draft', 'pending', 'archived'] as $status) {
        Product::factory()->create([
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'slug' => "not-visible-{$status}",
            'status' => $status,
        ]);
    }

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)->assertJson(['success' => true]);

    $slugs = collect($response->json('data.data'))->pluck('slug');
    expect($slugs)->toContain('visible-product');
    expect($slugs)->not->toContain('not-visible-hidden');
    expect($slugs)->not->toContain('not-visible-draft');
    expect($slugs)->not->toContain('not-visible-pending');
    expect($slugs)->not->toContain('not-visible-archived');
});

test('index cannot bypass visibility with status query parameter', function () {
    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'hidden-product',
        'status' => 'hidden',
    ]);

    $response = $this->getJson('/api/products?status=hidden');

    $response->assertStatus(200);

    $slugs = collect($response->json('data.data'))->pluck('slug');
    expect($slugs)->not->toContain('hidden-product');
});

test('hidden product is not accessible via show endpoint', function () {
    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'hidden-product',
        'status' => 'hidden',
    ]);

    $this->getJson('/api/products/hidden-product')->assertStatus(404);
});

test('published product is accessible via show endpoint', function () {
    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'published-product',
        'status' => 'published',
    ]);

    $this->getJson('/api/products/published-product')
        ->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('category show excludes non-published products', function () {
    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'cat-visible',
        'status' => 'published',
    ]);

    Product::factory()->create([
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'slug' => 'cat-hidden',
        'status' => 'hidden',
    ]);

    $response = $this->getJson("/api/categories/{$this->category->slug}");

    $response->assertStatus(200)->assertJson(['success' => true]);

    $slugs = collect($response->json('data.products'))->pluck('slug');
    expect($slugs)->toContain('cat-visible');
    expect($slugs)->not->toContain('cat-hidden');
});

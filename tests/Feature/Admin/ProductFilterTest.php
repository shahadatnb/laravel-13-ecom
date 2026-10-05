<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $this->brand = Brand::factory()->create(['name' => 'FilterTest Brand']);
    $this->category = Category::factory()->create(['name' => 'FilterTest Category']);

    $this->laptop = Product::factory()->create([
        'name' => 'Gaming Laptop Pro',
        'sku' => 'SKU-LAPTOP-PRO',
        'brand_id' => $this->brand->id,
        'category_id' => $this->category->id,
        'status' => 'published',
        'featured' => true,
        'stock' => 50,
        'minimum_stock' => 5,
    ]);

    $this->phone = Product::factory()->create([
        'name' => 'Mobile Phone Basic',
        'sku' => 'SKU-PHONE-BASIC',
        'status' => 'draft',
        'featured' => false,
        'stock' => 0,
        'minimum_stock' => 5,
    ]);
});

test('admin can filter products by name search', function () {
    $response = $this->get(route('admin.product.index', ['q' => 'Gaming Laptop']));

    $response->assertOk();
    $response->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->laptop->id];
    });
});

test('admin can filter products by sku search', function () {
    $response = $this->get(route('admin.product.index', ['q' => 'SKU-PHONE-BASIC']));

    $response->assertOk();
    $response->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->phone->id];
    });
});

test('admin can filter products by status', function () {
    $response = $this->get(route('admin.product.index', ['status' => 'draft']));

    $response->assertOk();
    $response->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->phone->id];
    });
});

test('admin can filter products by brand', function () {
    $response = $this->get(route('admin.product.index', ['brand_id' => $this->brand->id]));

    $response->assertOk();
    $response->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->laptop->id];
    });
});

test('admin can filter products by category', function () {
    $response = $this->get(route('admin.product.index', ['category_id' => $this->category->id]));

    $response->assertOk();
    $response->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->laptop->id];
    });
});

test('admin can filter products by featured flag', function () {
    $response = $this->get(route('admin.product.index', ['featured' => '1']));

    $response->assertOk();
    $response->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->laptop->id];
    });
});

test('admin can filter products by stock status', function () {
    $outResponse = $this->get(route('admin.product.index', ['stock_status' => 'out']));

    $outResponse->assertOk();
    $outResponse->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->phone->id];
    });

    $inResponse = $this->get(route('admin.product.index', ['stock_status' => 'in']));

    $inResponse->assertOk();
    $inResponse->assertViewHas('filters', ['stock_status' => 'in']);
    $inResponse->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->laptop->id];
    });
});

test('admin can combine product filters', function () {
    $matchResponse = $this->get(route('admin.product.index', [
        'q' => 'Gaming',
        'status' => 'published',
    ]));

    $matchResponse->assertOk();
    $matchResponse->assertViewHas('products', function ($products) {
        return $products->pluck('id')->all() === [$this->laptop->id];
    });

    $emptyResponse = $this->get(route('admin.product.index', [
        'q' => 'Gaming',
        'status' => 'draft',
    ]));

    $emptyResponse->assertOk();
    $emptyResponse->assertViewHas('products', function ($products) {
        return $products->isEmpty();
    });
});

test('empty product filter values are ignored', function () {
    $response = $this->get(route('admin.product.index', [
        'q' => '',
        'status' => '',
        'stock_status' => '',
        'brand_id' => '',
        'category_id' => '',
        'featured' => '',
    ]));

    $response->assertOk();
    $response->assertViewHas('products', function ($products) {
        return $products->count() === 2;
    });
});

test('invalid product filter values are rejected', function () {
    $statusResponse = $this->get(route('admin.product.index', ['status' => 'unknown']));

    $statusResponse->assertStatus(302);
    $statusResponse->assertSessionHasErrors('status');

    $stockResponse = $this->get(route('admin.product.index', ['stock_status' => 'bogus']));

    $stockResponse->assertStatus(302);
    $stockResponse->assertSessionHasErrors('stock_status');

    $brandResponse = $this->get(route('admin.product.index', ['brand_id' => 999999]));

    $brandResponse->assertStatus(302);
    $brandResponse->assertSessionHasErrors('brand_id');
});

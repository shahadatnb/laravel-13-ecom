<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Validation\ValidationException;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs($user = User::factory()->create());
});

test('admin can view product list', function () {
    $response = $this->get(route('admin.product.index'));

    $response->assertOk();
    $response->assertViewIs('admin.product.index');
    $response->assertViewHas('products');
});

test('admin can create a product', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();

    $response = $this->post(route('admin.product.store'), [
        'name' => 'Test Product',
        'slug' => 'test-product',
        'status' => 'published',
        'visibility' => 'public',
        'brand_id' => $brand->id,
        'category_id' => $category->id,
    ]);

    $response->assertRedirect(route('admin.product.index'));
    $response->assertSessionHas('success', 'Product created successfully.');

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'slug' => 'test-product',
    ]);
});

test('admin can edit a product', function () {
    $product = Product::factory()->create();

    $response = $this->get(route('admin.product.edit', $product->id));

    $response->assertOk();
    $response->assertViewIs('admin.product.edit');
    $response->assertViewHas('product');
});

test('admin can update a product', function () {
    $product = Product::factory()->create();

    $response = $this->put(route('admin.product.update', $product->id), [
        'name' => 'Updated Product',
        'slug' => 'updated-product',
        'status' => 'published',
        'visibility' => 'public',
    ]);

    $response->assertRedirect(route('admin.product.index'));
    $response->assertSessionHas('success', 'Product updated successfully.');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product',
        'slug' => 'updated-product',
    ]);
});

test('admin can delete a variant via ajax', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'sku' => 'VARIANT-SKU',
    ]);

    $response = $this->delete(route('admin.product.variant-delete', $product->id), [
        'variant_id' => $variant->id,
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseMissing('product_variants', [
        'product_id' => $product->id,
        'sku' => 'VARIANT-SKU',
    ]);
});

test('admin can update variant sku via ajax', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'sku' => 'OLD-SKU',
    ]);

    $response = $this->post(route('admin.product.variant-update', $product->id), [
        'variant_id' => $variant->id,
        'field' => 'sku',
        'value' => 'NEW-SKU',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('product_variants', [
        'id' => $variant->id,
        'sku' => 'NEW-SKU',
    ]);
});

test('admin can update variant price via ajax', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => 100,
        'stock' => 50,
    ]);

    $response = $this->post(route('admin.product.variant-update', $product->id), [
        'variant_id' => $variant->id,
        'field' => 'price',
        'value' => 199.99,
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('product_variants', [
        'id' => $variant->id,
        'price' => 199.99,
    ]);
});

test('stock field cannot be updated via variant-update endpoint', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'stock' => 50,
    ]);

    // Use regular post with JSON accept header — when validation fails on
    // a JSON-expecting request, Laravel returns a 422 response.
    // The key assertion: stock value must remain unchanged in the database.
    try {
        $this->post(route('admin.product.variant-update', $product->id), [
            'variant_id' => $variant->id,
            'field' => 'stock',
            'value' => 999,
        ]);
    } catch (ValidationException $e) {
        // Expected — validation should reject 'stock' as an invalid field
        expect($e->errors())->toHaveKey('field');
    }

    $this->assertDatabaseHas('product_variants', [
        'id' => $variant->id,
        'stock' => 50, // unchanged — stock cannot be edited from the edit page
    ]);
});

test('generated variants autofill sku and price from product', function () {
    $product = Product::factory()->create([
        'sku' => 'PROD-001',
        'regular_price' => 999.99,
        'sale_price' => null,
    ]);

    $response = $this->post(route('admin.product.variants-generate', $product->id), [
        'variants' => [
            [
                'attributes' => json_encode(['Size' => 'S', 'Color' => 'Red']),
                'name' => 'S - Red',
            ],
        ],
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('product_variants', [
        'product_id' => $product->id,
        'sku' => 'PROD-001',
        'price' => 999.99,
    ]);
});

test('admin can delete a product', function () {
    $product = Product::factory()->create();

    $response = $this->delete(route('admin.product.destroy', $product->id));

    $response->assertRedirect(route('admin.product.index'));
    $response->assertSessionHas('success', 'Product deleted successfully.');

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});

test('product name is required', function () {
    $response = $this->post(route('admin.product.store'), [
        'slug' => 'test-product',
        'status' => 'draft',
        'visibility' => 'private',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('product slug must be unique', function () {
    Product::factory()->create(['slug' => 'existing-slug']);

    $response = $this->post(route('admin.product.store'), [
        'name' => 'New Product',
        'slug' => 'existing-slug',
        'status' => 'draft',
        'visibility' => 'private',
    ]);

    $response->assertSessionHasErrors(['slug']);
});

test('product sku must be unique', function () {
    Product::factory()->create(['sku' => 'EXISTING-SKU']);

    $response = $this->post(route('admin.product.store'), [
        'name' => 'New Product',
        'slug' => 'new-product',
        'sku' => 'EXISTING-SKU',
        'status' => 'draft',
        'visibility' => 'private',
    ]);

    $response->assertSessionHasErrors(['sku']);
});

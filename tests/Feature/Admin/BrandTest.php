<?php

use App\Models\Brand;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs($user = User::factory()->create());
});

test('admin can view brand list', function () {
    $response = $this->get(route('admin.brand.index'));

    $response->assertOk();
    $response->assertViewIs('admin.brand.index');
    $response->assertViewHas('brands');
});

test('admin can create a brand', function () {
    $response = $this->post(route('admin.brand.store'), [
        'name' => 'Test Brand',
        'slug' => 'test-brand',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertRedirect(route('admin.brand.index'));
    $response->assertSessionHas('success', 'Brand created successfully.');

    $this->assertDatabaseHas('brands', [
        'name' => 'Test Brand',
        'slug' => 'test-brand',
    ]);
});

test('admin can edit a brand', function () {
    $brand = Brand::factory()->create();

    $response = $this->get(route('admin.brand.edit', $brand->id));

    $response->assertOk();
    $response->assertViewIs('admin.brand.createOrEdit');
    $response->assertViewHas('brand');
});

test('admin can update a brand', function () {
    $brand = Brand::factory()->create();

    $response = $this->put(route('admin.brand.update', $brand->id), [
        'name' => 'Updated Brand',
        'slug' => 'updated-brand',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertRedirect(route('admin.brand.index'));
    $response->assertSessionHas('success', 'Brand updated successfully.');

    $this->assertDatabaseHas('brands', [
        'id' => $brand->id,
        'name' => 'Updated Brand',
        'slug' => 'updated-brand',
    ]);
});

test('admin can delete a brand', function () {
    $brand = Brand::factory()->create();

    $response = $this->delete(route('admin.brand.destroy', $brand->id));

    $response->assertRedirect(route('admin.brand.index'));
    $response->assertSessionHas('success', 'Brand deleted successfully.');

    $this->assertSoftDeleted('brands', [
        'id' => $brand->id,
    ]);
});

test('brand name is required', function () {
    $response = $this->post(route('admin.brand.store'), [
        'slug' => 'test-brand',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('brand slug must be unique', function () {
    Brand::factory()->create(['slug' => 'existing-slug']);

    $response = $this->post(route('admin.brand.store'), [
        'name' => 'New Brand',
        'slug' => 'existing-slug',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertSessionHasErrors(['slug']);
});

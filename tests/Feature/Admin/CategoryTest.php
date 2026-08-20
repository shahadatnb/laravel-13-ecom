<?php

use App\Models\Category;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs($user = User::factory()->create());
});

test('admin can view category list', function () {
    $response = $this->get(route('admin.category.index'));

    $response->assertOk();
    $response->assertViewIs('admin.category.index');
    $response->assertViewHas('categories');
});

test('admin can create a category', function () {
    $response = $this->post(route('admin.category.store'), [
        'name' => 'Electronics',
        'slug' => 'electronics',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertRedirect(route('admin.category.index'));
    $response->assertSessionHas('success', 'Category created successfully.');

    $this->assertDatabaseHas('categories', [
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);
});

test('admin can create a category with parent', function () {
    $parent = Category::factory()->create();

    $response = $this->post(route('admin.category.store'), [
        'name' => 'Laptop',
        'slug' => 'laptop',
        'parent_id' => $parent->id,
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertRedirect(route('admin.category.index'));

    $this->assertDatabaseHas('categories', [
        'name' => 'Laptop',
        'parent_id' => $parent->id,
    ]);
});

test('admin can edit a category', function () {
    $category = Category::factory()->create();

    $response = $this->get(route('admin.category.edit', $category->id));

    $response->assertOk();
    $response->assertViewIs('admin.category.createOrEdit');
    $response->assertViewHas('category');
});

test('admin can update a category', function () {
    $category = Category::factory()->create();

    $response = $this->put(route('admin.category.update', $category->id), [
        'name' => 'Updated Category',
        'slug' => 'updated-category',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertRedirect(route('admin.category.index'));
    $response->assertSessionHas('success', 'Category updated successfully.');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category',
        'slug' => 'updated-category',
    ]);
});

test('admin can delete a category', function () {
    $category = Category::factory()->create();

    $response = $this->delete(route('admin.category.destroy', $category->id));

    $response->assertRedirect(route('admin.category.index'));
    $response->assertSessionHas('success', 'Category deleted successfully.');

    $this->assertSoftDeleted('categories', [
        'id' => $category->id,
    ]);
});

test('category name is required', function () {
    $response = $this->post(route('admin.category.store'), [
        'slug' => 'test-category',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('category slug must be unique', function () {
    Category::factory()->create(['slug' => 'existing-slug']);

    $response = $this->post(route('admin.category.store'), [
        'name' => 'New Category',
        'slug' => 'existing-slug',
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertSessionHasErrors(['slug']);
});

test('category cannot be parent of itself', function () {
    $category = Category::factory()->create();

    $response = $this->put(route('admin.category.update', $category->id), [
        'name' => $category->name,
        'slug' => $category->slug,
        'parent_id' => $category->id,
        'status' => 'active',
        'visibility' => 'public',
    ]);

    $response->assertSessionHasErrors(['parent_id']);
});

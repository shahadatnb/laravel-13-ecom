<?php

use App\Models\Category;
use App\Models\User;

uses()->in('Feature\\Admin');

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $this->root = Category::factory()->create([
        'name' => 'Electronics',
        'slug' => 'electronics-filter-test',
        'status' => 'active',
        'featured' => true,
        'parent_id' => null,
    ]);

    $this->child = Category::factory()->create([
        'name' => 'Phones',
        'slug' => 'phones-filter-test',
        'status' => 'inactive',
        'featured' => false,
        'parent_id' => $this->root->id,
    ]);
});

test('admin can filter categories by search', function () {
    $response = $this->get(route('admin.category.index', ['q' => 'Electronics']));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->pluck('id')->all() === [$this->root->id];
    });
});

test('admin can filter categories by slug search', function () {
    $response = $this->get(route('admin.category.index', ['q' => 'phones-filter-test']));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->pluck('id')->all() === [$this->child->id];
    });
});

test('admin can filter categories by status', function () {
    $response = $this->get(route('admin.category.index', ['status' => 'inactive']));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->pluck('id')->all() === [$this->child->id];
    });
});

test('admin can filter categories by featured flag', function () {
    $response = $this->get(route('admin.category.index', ['featured' => '1']));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->pluck('id')->all() === [$this->root->id];
    });
});

test('admin can filter categories by parent category', function () {
    $response = $this->get(route('admin.category.index', ['parent_id' => $this->root->id]));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->pluck('id')->all() === [$this->child->id];
    });
});

test('filtering by a category without children returns no results', function () {
    $response = $this->get(route('admin.category.index', ['parent_id' => $this->child->id]));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->isEmpty();
    });
});

test('admin can combine category search and status filters', function () {
    $response = $this->get(route('admin.category.index', [
        'q' => 'Electronics',
        'status' => 'inactive',
    ]));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->isEmpty();
    });
});

test('empty category filter values are ignored', function () {
    $response = $this->get(route('admin.category.index', [
        'q' => '',
        'status' => '',
        'featured' => '',
        'parent_id' => '',
    ]));

    $response->assertOk();
    $response->assertViewHas('categories', function ($categories) {
        return $categories->count() === 2;
    });
});

test('invalid category filter values are rejected', function () {
    $response = $this->get(route('admin.category.index', ['status' => 'unknown']));

    $response->assertStatus(302);
    $response->assertSessionHasErrors('status');

    $missingParentResponse = $this->get(route('admin.category.index', ['parent_id' => 999999]));

    $missingParentResponse->assertStatus(302);
    $missingParentResponse->assertSessionHasErrors('parent_id');
});

<?php

use App\Models\User;

test('admin registration is not available', function () {
    $response = $this->get('/admin/register');

    $response->assertNotFound();
});

test('admin registration posts are rejected', function () {
    $this->post('/admin/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    // No admin user was created
    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});

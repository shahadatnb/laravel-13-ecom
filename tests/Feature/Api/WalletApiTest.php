<?php

use App\Models\Customer;
use App\Models\Wallet;

uses()->group('api', 'wallet');

test('unauthenticated user cannot access wallet', function () {
    $this->getJson('/api/wallet')->assertUnauthorized();
});

test('wallet endpoint auto creates a wallet when the customer has none', function () {
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'customer')->getJson('/api/wallet');

    $response->assertOk()->assertJson(['success' => true]);

    expect(Wallet::where('customer_id', $customer->id)->exists())->toBeTrue();
    expect((float) $response->json('data.balance'))->toBe(0.0);
    expect((float) $response->json('data.total_credits'))->toBe(0.0);
    expect((float) $response->json('data.total_debits'))->toBe(0.0);
});

test('wallet returns balance, totals and transaction history', function () {
    $customer = Customer::factory()->create();
    $wallet = $customer->wallet()->create([
        'balance' => 400,
        'locked_balance' => 50,
        'status' => 'active',
    ]);

    $wallet->transactions()->create([
        'customer_id' => $customer->id,
        'transaction_code' => 'TXN-CREDIT-001',
        'type' => 'credit',
        'category' => 'bonus',
        'amount' => 500,
        'balance_before' => 0,
        'balance_after' => 500,
        'description' => 'Welcome bonus',
    ]);

    $wallet->transactions()->create([
        'customer_id' => $customer->id,
        'transaction_code' => 'TXN-DEBIT-001',
        'type' => 'debit',
        'category' => 'purchase',
        'amount' => 100,
        'balance_before' => 500,
        'balance_after' => 400,
        'description' => 'Order payment',
    ]);

    $response = $this->actingAs($customer, 'customer')->getJson('/api/wallet');

    $response->assertOk()->assertJsonStructure([
        'success',
        'data' => [
            'id',
            'status',
            'balance',
            'locked_balance',
            'available_balance',
            'total_credits',
            'total_debits',
            'transactions',
        ],
    ]);

    expect((float) $response->json('data.balance'))->toBe(400.0);
    expect((float) $response->json('data.available_balance'))->toBe(350.0);
    expect((float) $response->json('data.total_credits'))->toBe(500.0);
    expect((float) $response->json('data.total_debits'))->toBe(100.0);
    expect($response->json('data.transactions.data'))->toHaveCount(2);
});

test('wallet does not include other customers transactions', function () {
    $customer = Customer::factory()->create();
    $customer->wallet()->create([
        'balance' => 100,
        'locked_balance' => 0,
        'status' => 'active',
    ]);

    $other = Customer::factory()->create();
    $otherWallet = $other->wallet()->create([
        'balance' => 999,
        'locked_balance' => 0,
        'status' => 'active',
    ]);
    $otherWallet->transactions()->create([
        'customer_id' => $other->id,
        'transaction_code' => 'TXN-OTHER-001',
        'type' => 'credit',
        'category' => 'bonus',
        'amount' => 999,
        'balance_before' => 0,
        'balance_after' => 999,
    ]);

    $response = $this->actingAs($customer, 'customer')->getJson('/api/wallet');

    expect((float) $response->json('data.balance'))->toBe(100.0);
    expect((float) $response->json('data.total_credits'))->toBe(0.0);
    expect($response->json('data.transactions.data'))->toHaveCount(0);
});

test('wallet transactions are paginated', function () {
    $customer = Customer::factory()->create();
    $wallet = $customer->wallet()->create([
        'balance' => 0,
        'locked_balance' => 0,
        'status' => 'active',
    ]);

    foreach (range(1, 3) as $i) {
        $wallet->transactions()->create([
            'customer_id' => $customer->id,
            'transaction_code' => 'TXN-PAGE-00'.$i,
            'type' => 'credit',
            'category' => 'bonus',
            'amount' => 10,
            'balance_before' => 0,
            'balance_after' => 10,
        ]);
    }

    $response = $this->actingAs($customer, 'customer')
        ->getJson('/api/wallet?page=1&per_page=2');

    $response->assertOk();
    expect($response->json('data.transactions.data'))->toHaveCount(2);
    expect($response->json('data.transactions.total'))->toBe(3);
    expect($response->json('data.transactions.last_page'))->toBe(2);
});

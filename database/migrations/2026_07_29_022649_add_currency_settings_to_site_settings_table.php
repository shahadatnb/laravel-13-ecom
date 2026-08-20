<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert currency settings into site_settings table
        DB::table('site_settings')->insertOrIgnore([
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'group' => 'currency',
                'label' => 'Currency Symbol',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_code',
                'value' => 'USD',
                'group' => 'currency',
                'label' => 'Currency Code',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_position',
                'value' => 'before',
                'group' => 'currency',
                'label' => 'Currency Position',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_decimals',
                'value' => '2',
                'group' => 'currency',
                'label' => 'Decimal Places',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_thousand_separator',
                'value' => ',',
                'group' => 'currency',
                'label' => 'Thousand Separator',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_decimal_separator',
                'value' => '.',
                'group' => 'currency',
                'label' => 'Decimal Separator',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', [
            'currency_symbol',
            'currency_code',
            'currency_position',
            'currency_decimals',
            'currency_thousand_separator',
            'currency_decimal_separator',
        ])->delete();
    }
};

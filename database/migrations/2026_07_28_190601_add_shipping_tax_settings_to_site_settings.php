<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $settings = [
        [
            'key' => 'tax_rate',
            'value' => '5',
            'group' => 'checkout',
            'label' => 'Tax Rate (%)',
            'type' => 'text',
        ],
        [
            'key' => 'free_shipping_threshold',
            'value' => '50',
            'group' => 'checkout',
            'label' => 'Free Shipping Threshold ($)',
            'type' => 'text',
        ],
        [
            'key' => 'shipping_rate',
            'value' => '5',
            'group' => 'checkout',
            'label' => 'Standard Shipping Rate ($)',
            'type' => 'text',
        ],
    ];

    public function up(): void
    {
        foreach ($this->settings as $setting) {
            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        SiteSetting::whereIn('key', array_column($this->settings, 'key'))->delete();
    }
};

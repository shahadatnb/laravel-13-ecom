<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SiteSetting::firstOrCreate(
            ['key' => 'allow_oversell'],
            [
                'value' => '0',
                'group' => 'checkout',
                'label' => 'Allow Overselling (sell beyond stock)',
                'type' => 'boolean',
            ]
        );
    }

    public function down(): void
    {
        SiteSetting::where('key', 'allow_oversell')->delete();
    }
};

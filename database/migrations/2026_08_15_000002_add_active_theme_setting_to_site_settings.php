<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->insertOrIgnore([
            'key' => 'active_theme',
            'value' => 'classic',
            'group' => 'appearance',
            'label' => 'Homepage Theme',
            'type' => 'select',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'active_theme')->delete();
    }
};

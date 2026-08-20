<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $imageSettings = [
            ['key' => 'logo', 'value' => '', 'group' => 'branding', 'label' => 'Site Logo', 'type' => 'image', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favicon', 'value' => '', 'group' => 'branding', 'label' => 'Favicon', 'type' => 'image', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'og_image', 'value' => '', 'group' => 'branding', 'label' => 'OG Image (Social Share)', 'type' => 'image', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('site_settings')->insert($imageSettings);
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', ['logo', 'favicon', 'og_image'])->delete();
    }
};

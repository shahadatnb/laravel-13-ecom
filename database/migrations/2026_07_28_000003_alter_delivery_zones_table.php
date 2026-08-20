<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make type column nullable (was NOT NULL enum)
        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->change();
        });

        // Drop composite unique index + FK on delivery_zone_districts,
        // then add a regular index for the FK + a global unique on name
        Schema::table('delivery_zone_districts', function (Blueprint $table) {
            // Drop foreign key first, then the composite unique index
            $table->dropForeign(['delivery_zone_id']);
            $table->dropUnique(['delivery_zone_id', 'name']);

            // Add index for the foreign key (InnoDB requirement)
            $table->index('delivery_zone_id');

            // Global unique — a district name can only belong to one zone
            $table->unique('name');

            // Re-add foreign key
            $table->foreign('delivery_zone_id')
                ->references('id')
                ->on('delivery_zones')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->string('type', 50)->nullable(false)->change();
        });

        Schema::table('delivery_zone_districts', function (Blueprint $table) {
            $table->dropForeign(['delivery_zone_id']);
            $table->dropIndex(['delivery_zone_id']);
            $table->dropUnique(['name']);
            $table->unique(['delivery_zone_id', 'name']);
            $table->foreign('delivery_zone_id')
                ->references('id')
                ->on('delivery_zones')
                ->onDelete('cascade');
        });
    }
};

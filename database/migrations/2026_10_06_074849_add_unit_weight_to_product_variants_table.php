<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Kilograms consumed by selling one unit of this variant. Only
            // meaningful when the parent product uses the kg stock pool.
            $table->decimal('unit_weight', 8, 3)->nullable()->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('unit_weight');
        });
    }
};

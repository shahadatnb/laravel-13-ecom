<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Bulk stock pool. Unit-mode products keep integer counts, kg-mode
            // products store kilogram amounts, hence decimal precision.
            $table->decimal('stock', 10, 3)->default(0)->change();
            $table->string('stock_unit', 8)->default('unit')->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->change();
            $table->dropColumn('stock_unit');
        });
    }
};

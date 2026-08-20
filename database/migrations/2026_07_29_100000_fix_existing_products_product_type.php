<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix existing products that have variant records but product_type
     * was never set to 'variable' due to an admin form bug.
     */
    public function up(): void
    {
        $affected = DB::affectingStatement("
            UPDATE products
            SET product_type = 'variable'
            WHERE (
                product_type IS NULL
                OR product_type = ''
                OR product_type NOT IN ('simple', 'variable', 'digital', 'service', 'bundle')
            )
            AND (
                SELECT COUNT(*)
                FROM product_variants
                WHERE product_variants.product_id = products.id
            ) > 0
        ");

        echo "✅ Fixed {$affected} products: product_type set to 'variable'.\n";
    }

    /**
     * Reverse the migration — optional, not critical.
     */
    public function down(): void
    {
        // No rollback — this is a data fix, not a schema change.
        // Reversing would break products that were correctly fixed.
    }
};

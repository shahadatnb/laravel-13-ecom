<?php

use App\Helpers\BdDistricts;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('division', 100)->nullable();
            $table->foreignId('delivery_zone_id')->nullable()
                ->constrained('delivery_zones')->nullOnDelete();
            $table->decimal('charge', 10, 2)->nullable()
                ->comment('Per-district charge override; null falls back to zone/default');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('delivery_zone_id');
        });

        $this->transferLegacyDistricts();
    }

    public function down(): void
    {
        if (Schema::hasTable('delivery_zone_districts')) {
            DB::table('districts')->whereNotNull('delivery_zone_id')->get()
                ->each(function ($district) {
                    DB::table('delivery_zone_districts')->insert([
                        'delivery_zone_id' => $district->delivery_zone_id,
                        'name' => $district->name,
                        'status' => $district->status,
                        'created_at' => $district->created_at,
                        'updated_at' => $district->updated_at,
                    ]);
                });
        }

        Schema::dropIfExists('districts');
    }

    /**
     * Move rows from the legacy free-text delivery_zone_districts table
     * into the canonical districts table. Division is resolved from the
     * canonical list where the name still matches; renamed districts are
     * carried over as-is (division left null for the admin to fix).
     */
    private function transferLegacyDistricts(): void
    {
        if (! Schema::hasTable('delivery_zone_districts')) {
            return;
        }

        $canonical = BdDistricts::all();
        $now = now();

        DB::table('delivery_zone_districts')->orderBy('id')->get()
            ->each(function ($legacy) use ($canonical, $now) {
                DB::table('districts')->updateOrInsert(
                    ['name' => $legacy->name],
                    [
                        'division' => $canonical[$legacy->name] ?? null,
                        'delivery_zone_id' => $legacy->delivery_zone_id,
                        'status' => $legacy->status,
                        'created_at' => $legacy->created_at ?? $now,
                        'updated_at' => $now,
                    ]
                );
            });

        Schema::dropIfExists('delivery_zone_districts');
    }
};

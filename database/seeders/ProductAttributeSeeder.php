<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Size ──
        $size = ProductAttribute::firstOrCreate(
            ['slug' => 'size'],
            [
                'name' => 'Size',
                'type' => 'select',
                'description' => 'Product size variant',
                'is_required' => true,
                'is_filterable' => true,
                'sort_order' => 1,
            ]
        );

        if ($size->wasRecentlyCreated) {
            collect(['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'])->each(fn ($v, $i) => ProductAttributeValue::firstOrCreate([
                'attribute_id' => $size->id,
                'value' => $v,
            ], ['sort_order' => $i + 1])
            );
        }

        // ── Color ──
        $color = ProductAttribute::firstOrCreate(
            ['slug' => 'color'],
            [
                'name' => 'Color',
                'type' => 'color',
                'description' => 'Product color variant',
                'is_required' => true,
                'is_filterable' => true,
                'sort_order' => 2,
            ]
        );

        if ($color->wasRecentlyCreated) {
            $colors = [
                ['Red', '#FF0000'],
                ['Blue', '#0000FF'],
                ['Green', '#00AA00'],
                ['Black', '#000000'],
                ['White', '#FFFFFF'],
                ['Yellow', '#FFD700'],
                ['Purple', '#800080'],
                ['Orange', '#FFA500'],
                ['Pink', '#FFC0CB'],
                ['Gray', '#808080'],
            ];
            collect($colors)->each(fn ($c, $i) => ProductAttributeValue::firstOrCreate([
                'attribute_id' => $color->id,
                'value' => $c[0],
            ], [
                'color_code' => $c[1],
                'sort_order' => $i + 1,
            ])
            );
        }

        // ── Material ──
        $material = ProductAttribute::firstOrCreate(
            ['slug' => 'material'],
            [
                'name' => 'Material',
                'type' => 'select',
                'description' => 'Product material variant',
                'is_required' => false,
                'is_filterable' => true,
                'sort_order' => 3,
            ]
        );

        if ($material->wasRecentlyCreated) {
            collect(['Cotton', 'Polyester', 'Wool', 'Silk', 'Linen', 'Denim', 'Leather', 'Nylon'])->each(fn ($v, $i) => ProductAttributeValue::firstOrCreate([
                'attribute_id' => $material->id,
                'value' => $v,
            ], ['sort_order' => $i + 1])
            );
        }

        $totalAttrs = ProductAttribute::count();
        $totalValues = ProductAttributeValue::count();
        $this->command->info("✅ Product attributes: {$totalAttrs} attributes, {$totalValues} values");
    }
}

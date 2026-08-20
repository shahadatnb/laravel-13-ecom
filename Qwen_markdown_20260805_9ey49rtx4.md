# Laravel + MySQL Product Variant Refactor Plan

> তারিখ: ৫ আগস্ট, ২০২৬  
> Stack: Laravel + MySQL  
> লক্ষ্য: Product edit, delete, variant management এবং stock maintenance professional করা।

---

## 1. Current Situation

Current tables:

```text
products
product_variants
```

Current problems:

1. `product_variants.product_id` has `cascadeOnDelete()`.
   - Product delete করলে variants automatically delete হয়ে যায়।
   - Stock/order history থাকলে data হারিয়ে যাওয়ার risk আছে।

2. No stock ledger table.
   - Stock directly update হয়।
   - Stock history track করা যায় না।

3. Variant edit/delete stock break করতে পারে।
   - Variant delete করলে stock history হারিয়ে যায়।
   - Variant edit করলে stock randomly overwrite হয়।

4. Variation generation unsafe হতে পারে।
   - Duplicate variant create হতে পারে।
   - Existing variant accidentally delete হতে পারে।

5. Order dependency check নেই।
   - Order history থাকা variant delete/edit করা dangerous।

---

## 2. Target Architecture

Target tables:

```text
products
product_variants
stock_movements
order_items, with variant reference
```

Target behavior:

```text
Product edit → safe, transaction-based
Product delete → soft delete/archive if history exists
Variant edit → same variant ID, stock ledger entry
Variant delete → deactivate if history exists, force delete if no history
Variation generate → hash-based duplicate protection
Stock change → always through stock_movements
Order placement → transaction + stock check + stock movement
```

---

## 3. Mandatory Backup Before Any Change

Before touching anything:

```bash
cd /path/to/your-laravel-project

php artisan down

mkdir -p ../backup/2026-08-05-product-variant-refactor

mysqldump -u DB_USER -p \
  --single-transaction \
  --routines \
  --triggers \
  --events \
  DB_NAME > ../backup/2026-08-05-product-variant-refactor/DB_NAME.sql

git add -A
git commit -m "Backup before product variant refactor"
git tag backup-before-variant-refactor

php artisan up
```

Restore backup in local/staging and verify before production migration.

---

## 4. Database Changes

### 4.1 Add professional fields to product_variants

Add:

```text
is_active
attribute_hash
deleted_at
```

Change foreign key:

```text
product_variants.product_id
from cascadeOnDelete
to restrictOnDelete
```

Why:

- Product hard delete accidentally variants delete করবে না।
- Variant soft delete possible হবে।
- Variation duplicate prevent করার জন্য `attribute_hash` use করা যাবে।

---

### 4.2 Create stock_movements table

Purpose:

```text
Every stock change must have a ledger entry.
```

Important fields:

```text
product_id
variant_id
type
quantity_change
reference_type
reference_id
note
created_by
created_at
```

Types:

```text
opening
purchase
purchase_return
sale
sale_return
adjustment
transfer_in
transfer_out
```

Rules:

```text
quantity_change must not be zero.
Positive quantity = stock in.
Negative quantity = stock out.
```

---

### 4.3 Backfill attribute_hash

Existing `product_variants.attributes` JSON থেকে hash generate করতে হবে।

Example attributes:

```json
{
  "Color": "Red",
  "Size": "M"
}
```

Hash purpose:

```text
Same product + same options = same hash
Prevent duplicate variant combinations
```

---

## 5. Migration Files

### 5.1 Migration: add professional fields to product_variants

File:

```text
database/migrations/2026_08_05_000001_add_professional_fields_to_product_variants.php
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('stock');
            $table->char('attribute_hash', 64)->nullable()->after('attributes');
            $table->softDeletes();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            try {
                $table->dropForeign(['product_id']);
            } catch (\Throwable $e) {
                // Ignore if FK name differs.
            }
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreign('product_id', 'product_variants_product_id_fk')
                ->references('id')
                ->on('products')
                ->restrictOnDelete();

            $table->index(
                ['product_id', 'attribute_hash'],
                'product_variants_product_attribute_hash_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign('product_variants_product_id_fk');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'attribute_hash', 'deleted_at']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }
};
```

---

### 5.2 Migration: create stock_movements

File:

```text
database/migrations/2026_08_05_000002_create_stock_movements_table.php
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->string('type', 30);
            $table->integer('quantity_change');

            $table->nullableMorphs('reference');

            $table->string('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['variant_id', 'created_at']);
            $table->index('type');
        });

        DB::statement('
            ALTER TABLE stock_movements
            ADD CONSTRAINT stock_movements_quantity_change_not_zero
            CHECK (quantity_change <> 0)
        ');

        $now = now();

        DB::table('products')
            ->whereNull('deleted_at')
            ->where('stock', '>', 0)
            ->where('product_type', '!=', 'variable')
            ->orderBy('id')
            ->chunkById(500, function ($products) use ($now) {
                $rows = [];

                foreach ($products as $product) {
                    $rows[] = [
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'type' => 'opening',
                        'quantity_change' => (int) $product->stock,
                        'note' => 'Opening product stock migrated from products.stock',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('stock_movements')->insert($rows);
            });

        DB::table('product_variants')
            ->where('stock', '>', 0)
            ->orderBy('id')
            ->chunkById(500, function ($variants) use ($now) {
                $rows = [];

                foreach ($variants as $variant) {
                    $rows[] = [
                        'product_id' => $variant->product_id,
                        'variant_id' => $variant->id,
                        'type' => 'opening',
                        'quantity_change' => (int) $variant->stock,
                        'note' => 'Opening variant stock migrated from product_variants.stock',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('stock_movements')->insert($rows);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
```

---

### 5.3 Migration: backfill attribute_hash

File:

```text
database/migrations/2026_08_05_000003_backfill_product_variant_attribute_hash.php
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_variants')
            ->whereNotNull('attributes')
            ->whereNull('attribute_hash')
            ->orderBy('id')
            ->chunkById(500, function ($variants) {
                foreach ($variants as $variant) {
                    $attributes = json_decode($variant->attributes, true);

                    if (! is_array($attributes)) {
                        continue;
                    }

                    $normalized = $this->normalizeAttributes($attributes);

                    if (empty($normalized)) {
                        continue;
                    }

                    DB::table('product_variants')
                        ->where('id', $variant->id)
                        ->update([
                            'attribute_hash' => $this->hashAttributes($normalized),
                        ]);
                }
            });

        $hasDuplicates = DB::select('
            SELECT product_id, attribute_hash, COUNT(*) AS total
            FROM product_variants
            WHERE attribute_hash IS NOT NULL
            GROUP BY product_id, attribute_hash
            HAVING total > 1
            LIMIT 1
        ');

        if (empty($hasDuplicates)) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->unique(
                    ['product_id', 'attribute_hash'],
                    'product_variants_product_attribute_hash_unique'
                );
            });
        }
    }

    public function down(): void
    {
        try {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropUnique('product_variants_product_attribute_hash_unique');
            });
        } catch (\Throwable $e) {
            // Ignore if unique index was not created.
        }

        DB::table('product_variants')->update([
            'attribute_hash' => null,
        ]);
    }

    private function normalizeAttributes(array $attributes): array
    {
        $normalized = [];

        foreach ($attributes as $key => $value) {
            if (is_array($value) && isset($value['name'], $value['value'])) {
                $normalized[(string) $value['name']] = (string) $value['value'];
            } else {
                $normalized[(string) $key] = (string) $value;
            }
        }

        ksort($normalized);

        return $normalized;
    }

    private function hashAttributes(array $attributes): string
    {
        return hash('sha256', json_encode($attributes, JSON_UNESCAPED_UNICODE));
    }
};
```

If duplicate combinations exist, clean them first:

```sql
SELECT product_id, attribute_hash, COUNT(*) AS total
FROM product_variants
WHERE attribute_hash IS NOT NULL
GROUP BY product_id, attribute_hash
HAVING total > 1;
```

---

## 6. Models

### 6.1 Product Model

File:

```text
app/Models/Product.php
```

Required relations:

```php
public function variants()
{
    return $this->hasMany(ProductVariant::class);
}

public function activeVariants()
{
    return $this->variants()->where('is_active', true);
}

public function stockMovements()
{
    return $this->hasMany(StockMovement::class);
}
```

Product should use:

```php
use SoftDeletes;
```

---

### 6.2 ProductVariant Model

File:

```text
app/Models/ProductVariant.php
```

Required fields:

```php
protected $fillable = [
    'product_id',
    'name',
    'sku',
    'barcode',
    'price',
    'stock',
    'attributes',
    'attribute_hash',
    'sort_order',
    'is_active',
];

protected $casts = [
    'attributes' => 'array',
    'price' => 'decimal:2',
    'stock' => 'integer',
    'sort_order' => 'integer',
    'is_active' => 'boolean',
];
```

Required traits:

```php
use SoftDeletes;
```

Required relations:

```php
public function product()
{
    return $this->belongsTo(Product::class);
}

public function stockMovements()
{
    return $this->hasMany(StockMovement::class, 'variant_id');
}

public function scopeActive($query)
{
    return $query->where('is_active', true);
}
```

---

### 6.3 StockMovement Model

File:

```text
app/Models/StockMovement.php
```

Constants:

```php
public const TYPE_OPENING = 'opening';
public const TYPE_PURCHASE = 'purchase';
public const TYPE_PURCHASE_RETURN = 'purchase_return';
public const TYPE_SALE = 'sale';
public const TYPE_SALE_RETURN = 'sale_return';
public const TYPE_ADJUSTMENT = 'adjustment';
public const TYPE_TRANSFER_IN = 'transfer_in';
public const TYPE_TRANSFER_OUT = 'transfer_out';
```

Fillable:

```php
protected $fillable = [
    'product_id',
    'variant_id',
    'type',
    'quantity_change',
    'reference_type',
    'reference_id',
    'note',
    'created_by',
];
```

Relations:

```php
public function product()
{
    return $this->belongsTo(Product::class);
}

public function variant()
{
    return $this->belongsTo(ProductVariant::class, 'variant_id');
}

public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}
```

---

## 7. Service Layer

Do not put business logic directly inside controllers.

Use:

```text
app/Services/Inventory/StockService.php
app/Services/Product/VariantService.php
app/Services/Product/VariationService.php
app/Services/Product/ProductDeletionService.php
```

---

## 8. StockService Responsibilities

File:

```text
app/Services/Inventory/StockService.php
```

Should handle:

```text
adjustProduct()
adjustVariant()
deductProduct()
deductVariant()
syncProductStockFromVariants()
recordMovement()
```

Rules:

```text
Use DB transaction.
Use lockForUpdate().
Never allow negative stock.
Always create stock_movements row.
For variable product, stock should be managed by variants.
Product stock should be synced from variant stock sum.
```

Important behavior:

```text
Old stock: 30
New stock: 50
Difference: +20

Insert into stock_movements:
type = adjustment
quantity_change = +20
```

For sale:

```text
type = sale
quantity_change = -ordered quantity
```

For purchase:

```text
type = purchase
quantity_change = +received quantity
```

---

## 9. VariantService Responsibilities

File:

```text
app/Services/Product/VariantService.php
```

Should handle:

```text
create()
update()
delete()
deactivateOrDelete()
hasHistory()
hasOrderHistory()
```

Rules:

```text
Variant ID must remain same during edit.
Do not delete variant if it has:
- stock
- stock movement history
- order history
If history exists, set is_active = false.
If no history exists, forceDelete is allowed.
Stock changes must go through StockService.
Do not allow option change if variant has order history.
```

Delete decision:

```text
if stock != 0 → deactivate
if stock_movements exists → deactivate
if order_items exists → deactivate
else → forceDelete
```

---

## 10. VariationService Responsibilities

File:

```text
app/Services/Product/VariationService.php
```

Should handle:

```text
generate()
buildCombinations()
hashAttributes()
variantName()
generateSku()
```

Input format:

```json
{
  "attributes": [
    {
      "name": "Color",
      "values": ["Red", "Blue"]
    },
    {
      "name": "Size",
      "values": ["S", "M"]
    }
  ]
}
```

Generated combinations:

```text
Color: Red, Size: S
Color: Red, Size: M
Color: Blue, Size: S
Color: Blue, Size: M
```

Rules:

```text
Use attribute_hash to identify existing variant.
If hash exists:
  restore if trashed
  set is_active = true
If hash missing:
  create new variant
If existing variant hash not in new combinations:
  deactivate if history exists
  force delete if no history
Never randomly delete variants with history.
```

---

## 11. ProductDeletionService Responsibilities

File:

```text
app/Services/Product/ProductDeletionService.php
```

Should handle:

```text
delete(Product $product)
```

Rules:

```text
Lock product.
Process all variants safely.
If variants remain because of history:
  product status = archived
  soft delete product
If no history and no remaining variants:
  force delete product
```

Product delete decision:

```text
if product has stock movement history → soft delete/archive
if product has variants with history → soft delete/archive
else → force delete
```

---

## 12. Form Requests

### 12.1 StoreVariantRequest

File:

```text
app/Http/Requests/ProductVariant/StoreVariantRequest.php
```

Validation:

```text
name: required
sku: nullable, unique per product
barcode: nullable
price: nullable, numeric, min:0
stock: nullable, integer, min:0
attributes: nullable, array
sort_order: nullable, integer
```

---

### 12.2 UpdateVariantRequest

File:

```text
app/Http/Requests/ProductVariant/UpdateVariantRequest.php
```

Validation:

```text
name: sometimes required
sku: nullable, unique per product except current variant
barcode: nullable
price: nullable, numeric, min:0
stock: nullable, integer, min:0
attributes: nullable, array
sort_order: nullable, integer
is_active: nullable, boolean
```

---

### 12.3 GenerateVariationsRequest

File:

```text
app/Http/Requests/ProductVariant/GenerateVariationsRequest.php
```

Validation:

```text
attributes: required, array, min:1
attributes.*.name: required, string, max:100
attributes.*.values: required, array, min:1
attributes.*.values.*: required, string, max:191
```

---

## 13. Controllers

### 13.1 ProductVariantController

File:

```text
app/Http/Controllers/Admin/ProductVariantController.php
```

Methods:

```text
store()
update()
destroy()
```

Flow:

```text
Validate request using FormRequest
Ensure variant belongs to product
Call service
Return success message
```

---

### 13.2 ProductVariationController

File:

```text
app/Http/Controllers/Admin/ProductVariationController.php
```

Method:

```text
generate()
```

Flow:

```text
Validate attributes
Call VariationService
Return success message
```

---

### 13.3 ProductController destroy

Existing product delete should not be:

```php
$product->delete();
```

It should be:

```php
$productDeletionService->delete($product);
```

---

## 14. Routes

Example admin routes:

```php
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProductVariationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::post('products/{product}/variations/generate', [
            ProductVariationController::class,
            'generate',
        ])->name('products.variations.generate');

        Route::post('products/{product}/variants', [
            ProductVariantController::class,
            'store',
        ])->name('products.variants.store');

        Route::put('products/{product}/variants/{variant}', [
            ProductVariantController::class,
            'update',
        ])->name('products.variants.update');

        Route::delete('products/{product}/variants/{variant}', [
            ProductVariantController::class,
            'destroy',
        ])->name('products.variants.destroy');
    });
```

---

## 15. Order Integration Requirement

Order stock deduction requires variant reference in order items.

If your `order_items` table does not have `variant_id`, add it carefully.

Example:

```php
Schema::table('order_items', function (Blueprint $table) {
    $table->foreignId('variant_id')
        ->nullable()
        ->after('product_id')
        ->constrained('product_variants')
        ->restrictOnDelete();
});
```

If table/column name differs, adjust accordingly.

Order placement should use:

```php
app(\App\Services\Inventory\StockService::class)->deductVariant(
    variant: $variant,
    quantity: $quantity,
    reference: $order,
    userId: auth()->id()
);
```

For simple product:

```php
app(\App\Services\Inventory\StockService::class)->deductProduct(
    product: $product,
    quantity: $quantity,
    reference: $order,
    userId: auth()->id()
);
```

---

## 16. Admin UI Behavior

### Variant list should show

```text
SKU
Options
Price
Stock
Status
Actions
```

Actions:

```text
Edit
Stock Adjust
Deactivate
Delete
```

Delete button logic:

```text
If variant has history:
  show warning and deactivate instead of delete
If variant has no history:
  allow permanent delete with confirmation
```

Variation generator should show preview before saving:

```text
Color: Red | Size: S
Color: Red | Size: M
Color: Blue | Size: S
Color: Blue | Size: M
```

---

## 17. Migration Execution

Run migrations:

```bash
php artisan migrate
```

Or run individually:

```bash
php artisan migrate --path=database/migrations/2026_08_05_000001_add_professional_fields_to_product_variants.php

php artisan migrate --path=database/migrations/2026_08_05_000002_create_stock_movements_table.php

php artisan migrate --path=database/migrations/2026_08_05_000003_backfill_product_variant_attribute_hash.php
```

---

## 18. Testing Checklist

### Product tests

```text
[ ] Product create works
[ ] Product edit works
[ ] Product delete with history becomes archived/soft delete
[ ] Product delete without history can force delete
```

### Variant tests

```text
[ ] Variant create works
[ ] Variant edit keeps same variant ID
[ ] Variant stock edit creates stock movement
[ ] Variant delete with history deactivates
[ ] Variant delete without history force deletes
[ ] Duplicate SKU is blocked
[ ] Duplicate attribute combination is blocked
```

### Variation tests

```text
[ ] Generate creates missing variants
[ ] Generate activates existing matching variants
[ ] Generate does not duplicate variants
[ ] Generate deactivates unused variants with history
[ ] Generate deletes unused variants without history
```

### Stock tests

```text
[ ] Manual stock adjustment creates movement
[ ] Sale deducts stock
[ ] Sale return increases stock
[ ] Purchase increases stock
[ ] Negative stock is blocked
[ ] Variable product stock is synced from variants
[ ] Concurrent orders do not oversell
```

---

## 19. Important Rules

```text
Never hard delete product if variants/history exist.
Never hard delete variant if stock/order/movement history exists.
Never update stock without creating stock movement.
Never allow variant ID to change during edit.
Never allow SKU to change freely after history exists.
Never use cascadeOnDelete for product_variants.product_id.
Never generate variations without hash-based matching.
Never run production migration without backup.
```

---

## 20. Next Information Needed

To merge with existing product-related files, need:

```text
ProductController.php
ProductVariantController.php, if exists
Product.php
ProductVariant.php
orders table migration
order_items table migration
product edit blade/form
variant edit blade/form
existing attributes JSON example
```

Especially need exact `order_items` structure to finalize:

```text
order stock deduction
variant order history check
return stock handling
```

---

## 21. Final Outcome

After implementation:

```text
Product edit/delete becomes safe.
Variant edit/delete becomes stock-safe.
Variation generation becomes duplicate-safe.
Stock becomes ledger-based.
Order history becomes protected.
Product/variant deletion becomes professional.
```
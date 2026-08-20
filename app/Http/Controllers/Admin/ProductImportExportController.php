<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductImportExportController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();
        $brands = Brand::orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.product.import-export', compact('totalProducts', 'brands', 'categories'));
    }

    public function export(Request $request)
    {
        $query = Product::with(['brand', 'category']);

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->orderBy('id')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_export_' . now()->format('Y-m-d_His') . '.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Brand', 'Category', 'Name', 'Name (BN)', 'SKU', 'Barcode',
                'Short Description', 'Description', 'Regular Price', 'Sale Price',
                'Wholesale Price', 'Cost Price', 'Stock', 'Minimum Stock',
                'Maximum Order', 'Weight (kg)', 'Length (cm)', 'Width (cm)',
                'Height (cm)', 'Tax Class', 'Shipping Class', 'Status',
                'Product Type', 'Featured', 'Visibility', 'Meta Title',
                'Meta Description', 'Meta Keywords',
            ]);

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->id, $p->brand?->name ?? '', $p->category?->name ?? '',
                    $p->name, $p->name_bn ?? '', $p->sku ?? '', $p->barcode ?? '',
                    $p->short_description ?? '', $p->description ?? '',
                    $p->regular_price ?? '', $p->sale_price ?? '',
                    $p->wholesale_price ?? '', $p->cost_price ?? '',
                    $p->stock, $p->minimum_stock, $p->maximum_order ?? '',
                    $p->weight ?? '', $p->length ?? '', $p->width ?? '',
                    $p->height ?? '', $p->tax_class ?? '', $p->shipping_class ?? '',
                    $p->status, $p->product_type, $p->featured ? 'Yes' : 'No',
                    $p->visibility, $p->meta_title ?? '', $p->meta_description ?? '',
                    $p->meta_keywords ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Brand', 'Category', 'Name', 'Name (BN)', 'SKU', 'Barcode',
                'Short Description', 'Description', 'Regular Price', 'Sale Price',
                'Wholesale Price', 'Cost Price', 'Stock', 'Minimum Stock',
                'Maximum Order', 'Weight', 'Length', 'Width', 'Height',
                'Tax Class', 'Shipping Class', 'Status', 'Product Type',
                'Featured', 'Visibility',
            ]);

            fputcsv($file, [
                'Samsung', 'Electronics', 'Galaxy S24 Ultra', '',
                'SAM-S24U-256', '8806091234567', 'Premium flagship smartphone',
                'Samsung Galaxy S24 Ultra with 256GB storage', '139999', '129999',
                '', '110000', '50', '10', '5', '0.23', '16.2', '7.9', '0.8',
                'standard', 'standard', 'published', 'simple', 'Yes', 'public',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'csv_file.required' => 'Please select a CSV file to import.',
            'csv_file.mimes' => 'The file must be a CSV or TXT file.',
            'csv_file.max' => 'The file size must not exceed 10MB.',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'Could not read the file.']);
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'The CSV file is empty.']);
        }

        $headerMap = $this->mapHeaders($header);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $rowNum = 1;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (count($row) < 3) {
                    $skipped++;
                    $errors[] = "Row {$rowNum}: Not enough columns — skipped.";
                    continue;
                }

                $data = $this->mapRowToData($headerMap, $row);

                $brandId = null;
                if (!empty($data['brand'])) {
                    $brand = Brand::where('name', 'like', $data['brand'])->first();
                    if (!$brand) {
                        $brand = Brand::create([
                            'name' => $data['brand'],
                            'slug' => Str::slug($data['brand']),
                            'status' => 'active',
                        ]);
                    }
                    $brandId = $brand->id;
                }

                $categoryId = null;
                if (!empty($data['category'])) {
                    $category = Category::where('name', 'like', $data['category'])->first();
                    if (!$category) {
                        $category = Category::create([
                            'name' => $data['category'],
                            'slug' => Str::slug($data['category']),
                            'status' => 'active',
                        ]);
                    }
                    $categoryId = $category->id;
                }

                $slug = Str::slug($data['name'] ?? '');
                if (empty($slug)) {
                    $skipped++;
                    $errors[] = "Row {$rowNum}: Empty name — skipped.";
                    continue;
                }

                $existing = null;
                if (!empty($data['sku'])) {
                    $existing = Product::where('sku', $data['sku'])->first();
                }
                if (!$existing && !empty($data['name'])) {
                    $existing = Product::where('name', $data['name'])->first();
                }

                $productData = [
                    'brand_id' => $brandId,
                    'category_id' => $categoryId,
                    'name' => $data['name'] ?? '',
                    'name_bn' => $data['name_bn'] ?? null,
                    'slug' => $existing ? $existing->slug : $slug,
                    'short_description' => $data['short_description'] ?? null,
                    'description' => $data['description'] ?? null,
                    'sku' => $data['sku'] ?? null,
                    'barcode' => $data['barcode'] ?? null,
                    'regular_price' => $data['regular_price'] ?: null,
                    'sale_price' => $data['sale_price'] ?: null,
                    'wholesale_price' => $data['wholesale_price'] ?: null,
                    'cost_price' => $data['cost_price'] ?: null,
                    'stock' => (int) ($data['stock'] ?? 0),
                    'minimum_stock' => (int) ($data['minimum_stock'] ?? 0),
                    'maximum_order' => $data['maximum_order'] ?: null,
                    'weight' => $data['weight'] ?: null,
                    'length' => $data['length'] ?: null,
                    'width' => $data['width'] ?: null,
                    'height' => $data['height'] ?: null,
                    'tax_class' => $data['tax_class'] ?? null,
                    'shipping_class' => $data['shipping_class'] ?? null,
                    'status' => $data['status'] ?? 'draft',
                    'product_type' => $data['product_type'] ?? 'simple',
                    'featured' => strtolower($data['featured'] ?? 'no') === 'yes',
                    'visibility' => $data['visibility'] ?? 'public',
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                    'meta_keywords' => $data['meta_keywords'] ?? null,
                ];

                if ($existing) {
                    $existing->update($productData);
                    $updated++;
                } else {
                    if (Product::where('slug', $productData['slug'])->exists()) {
                        $productData['slug'] = $productData['slug'] . '-' . Str::random(5);
                    }
                    Product::create($productData);
                    $created++;
                }
            }

            fclose($handle);
            DB::commit();

            $summary = "Import complete: {$created} created, {$updated} updated, {$skipped} skipped.";
            if (!empty($errors)) {
                $summary .= ' Errors: ' . implode(' | ', array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $summary .= ' ... and ' . (count($errors) - 10) . ' more.';
                }
            }

            return back()->with('import_success', $summary);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['csv_file' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    private function mapHeaders(array $header): array
    {
        $map = [];
        foreach ($header as $index => $name) {
            $n = strtolower(trim($name));
            $map[$index] = match (true) {
                str_contains($n, 'brand') => 'brand',
                str_contains($n, 'category') && !str_contains($n, 'product') => 'category',
                str_contains($n, 'name') && str_contains($n, 'bn') => 'name_bn',
                str_contains($n, 'name') => 'name',
                str_contains($n, 'sku') => 'sku',
                str_contains($n, 'barcode') => 'barcode',
                str_contains($n, 'short') && str_contains($n, 'desc') => 'short_description',
                str_contains($n, 'description') => 'description',
                str_contains($n, 'regular') || str_contains($n, 'normal') => 'regular_price',
                str_contains($n, 'sale') || str_contains($n, 'discount') => 'sale_price',
                str_contains($n, 'wholesale') => 'wholesale_price',
                str_contains($n, 'cost') => 'cost_price',
                str_contains($n, 'min') && str_contains($n, 'stock') => 'minimum_stock',
                str_contains($n, 'max') && str_contains($n, 'order') => 'maximum_order',
                str_contains($n, 'stock') => 'stock',
                str_contains($n, 'weight') => 'weight',
                str_contains($n, 'length') => 'length',
                str_contains($n, 'width') => 'width',
                str_contains($n, 'height') => 'height',
                str_contains($n, 'tax_class') => 'tax_class',
                str_contains($n, 'tax') => 'tax_class',
                str_contains($n, 'shipping_class') => 'shipping_class',
                str_contains($n, 'shipping') => 'shipping_class',
                str_contains($n, 'status') => 'status',
                str_contains($n, 'type') => 'product_type',
                str_contains($n, 'featured') => 'featured',
                str_contains($n, 'visibility') || str_contains($n, 'visible') => 'visibility',
                str_contains($n, 'meta_title') => 'meta_title',
                str_contains($n, 'meta_desc') => 'meta_description',
                str_contains($n, 'meta_key') || str_contains($n, 'keyword') => 'meta_keywords',
                default => $n,
            };
        }
        return $map;
    }

    private function mapRowToData(array $headerMap, array $row): array
    {
        $data = [];
        foreach ($headerMap as $index => $key) {
            $data[$key] = $row[$index] ?? '';
        }
        return $data;
    }
}

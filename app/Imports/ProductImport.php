<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow
{
    private int $created = 0;
    private int $updated = 0;
    private int $skipped = 0;
    private array $errors = [];
    private array $brandCache = [];
    private array $categoryCache = [];

    public function __construct()
    {
        $this->brandCache = Brand::pluck("id", "name")
            ->mapWithKeys(fn ($id, $name) => [Str::lower($name) => $id])
            ->toArray();
        $this->categoryCache = Category::pluck("id", "name")
            ->mapWithKeys(fn ($id, $name) => [Str::lower($name) => $id])
            ->toArray();
    }

    public function collection(Collection $rows): void
    {
        $rowNum = 1;
        foreach ($rows as $row) {
            $rowNum++;
            $name = trim($row["name"] ?? $row["Name"] ?? "");
            if ($name === "") {
                $this->errors[] = "Row {$rowNum}: Empty name — skipped.";
                continue;
            }

            $sku = trim($row["sku"] ?? $row["SKU"] ?? "");

            $brandId = $this->resolveBrand(trim($row["brand"] ?? $row["Brand"] ?? ""));
            $categoryId = $this->resolveCategory(trim($row["category"] ?? $row["Category"] ?? ""));

            $slug = Str::slug($name);

            $existing = null;
            if ($sku !== "") $existing = Product::where("sku", $sku)->first();
            if (!$existing && $name !== "") $existing = Product::where("name", $name)->first();

            $data = [
                "brand_id" => $brandId,
                "category_id" => $categoryId,
                "name" => $name,
                "name_bn" => $row["name_bn"] ?? $row["Name (BN)"] ?? null,
                "slug" => $existing ? $existing->slug : $slug,
                "short_description" => $row["short_description"] ?? $row["Short Description"] ?? null,
                "description" => $row["description"] ?? $row["Description"] ?? null,
                "sku" => $sku ?: null,
                "barcode" => $row["barcode"] ?? $row["Barcode"] ?? null,
                "regular_price" => $row["regular_price"] ?? $row["Regular Price"] ?? null,
                "sale_price" => $row["sale_price"] ?? $row["Sale Price"] ?? null,
                "wholesale_price" => $row["wholesale_price"] ?? $row["Wholesale Price"] ?? null,
                "cost_price" => $row["cost_price"] ?? $row["Cost Price"] ?? null,
                "stock" => (int)($row["stock"] ?? $row["Stock"] ?? 0),
                "minimum_stock" => (int)($row["minimum_stock"] ?? $row["Minimum Stock"] ?? 0),
                "maximum_order" => $row["maximum_order"] ?? $row["Maximum Order"] ?? null,
                "weight" => $row["weight"] ?? $row["Weight (kg)"] ?? $row["Weight"] ?? null,
                "length" => $row["length"] ?? $row["Length (cm)"] ?? $row["Length"] ?? null,
                "width" => $row["width"] ?? $row["Width (cm)"] ?? $row["Width"] ?? null,
                "height" => $row["height"] ?? $row["Height (cm)"] ?? $row["Height"] ?? null,
                "tax_class" => $row["tax_class"] ?? $row["Tax Class"] ?? null,
                "shipping_class" => $row["shipping_class"] ?? $row["Shipping Class"] ?? null,
                "status" => $row["status"] ?? $row["Status"] ?? "draft",
                "product_type" => $row["product_type"] ?? $row["Product Type"] ?? "simple",
                "featured" => in_array(strtolower(trim($row["featured"] ?? $row["Featured"] ?? "no")), ["yes", "1", "true"], true),
                "visibility" => $row["visibility"] ?? $row["Visibility"] ?? "public",
                "meta_title" => $row["meta_title"] ?? $row["Meta Title"] ?? null,
                "meta_description" => $row["meta_description"] ?? $row["Meta Description"] ?? null,
                "meta_keywords" => $row["meta_keywords"] ?? $row["Meta Keywords"] ?? null,
            ];

            if ($existing) {
                $existing->update($data);
                $this->updated++;
            } else {
                if (Product::where("slug", $data["slug"])->exists()) {
                    $data["slug"] = $data["slug"] . "-" . Str::random(5);
                }
                Product::create($data);
                $this->created++;
            }
        }
    }

    public function getResults(): array
    {
        return [
            "created" => $this->created,
            "updated" => $this->updated,
            "skipped" => $this->skipped,
            "errors" => $this->errors,
        ];
    }

    private function resolveBrand(string $name): ?int
    {
        if ($name === "") return null;
        $key = Str::lower($name);
        if (isset($this->brandCache[$key])) return $this->brandCache[$key];
        $brand = Brand::create(["name" => $name, "slug" => Str::slug($name), "status" => "active"]);
        $this->brandCache[$key] = $brand->id;
        return $brand->id;
    }

    private function resolveCategory(string $name): ?int
    {
        if ($name === "") return null;
        $key = Str::lower($name);
        if (isset($this->categoryCache[$key])) return $this->categoryCache[$key];
        $category = Category::create(["name" => $name, "slug" => Str::slug($name), "status" => "active"]);
        $this->categoryCache[$key] = $category->id;
        return $category->id;
    }
}

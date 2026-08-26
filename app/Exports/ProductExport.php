<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    public const COLUMNS = [
        "Brand", "Category", "Name", "Name (BN)", "SKU", "Barcode",
        "Short Description", "Description", "Regular Price", "Sale Price",
        "Wholesale Price", "Cost Price", "Stock", "Minimum Stock",
        "Maximum Order", "Weight (kg)", "Length (cm)", "Width (cm)",
        "Height (cm)", "Tax Class", "Shipping Class", "Status",
        "Product Type", "Featured", "Visibility", "Meta Title",
        "Meta Description", "Meta Keywords",
    ];

    private ?int $brandId;
    private ?int $categoryId;
    private string $status;

    public function __construct(?int $brandId = null, ?int $categoryId = null, string $status = '')
    {
        $this->brandId = $brandId;
        $this->categoryId = $categoryId;
        $this->status = $status;
    }

    public function collection(): Collection
    {
        $query = Product::with(["brand", "category"]);
        if ($this->brandId) $query->where("brand_id", $this->brandId);
        if ($this->categoryId) $query->where("category_id", $this->categoryId);
        if ($this->status !== "") $query->where("status", $this->status);
        return $query->orderBy("id")->get();
    }

    public function headings(): array
    {
        return self::COLUMNS;
    }

    public function map($p): array
    {
        return [
            $p->brand?->name ?? "",
            $p->category?->name ?? "",
            $p->name,
            $p->name_bn ?? "",
            $p->sku ?? "",
            $p->barcode ?? "",
            $p->short_description ?? "",
            $p->description ?? "",
            $p->regular_price !== null ? (float) $p->regular_price : "",
            $p->sale_price !== null ? (float) $p->sale_price : "",
            $p->wholesale_price !== null ? (float) $p->wholesale_price : "",
            $p->cost_price !== null ? (float) $p->cost_price : "",
            (int) $p->stock,
            (int) $p->minimum_stock,
            $p->maximum_order !== null ? (int) $p->maximum_order : "",
            $p->weight !== null ? (float) $p->weight : "",
            $p->length !== null ? (float) $p->length : "",
            $p->width !== null ? (float) $p->width : "",
            $p->height !== null ? (float) $p->height : "",
            $p->tax_class ?? "",
            $p->shipping_class ?? "",
            $p->status,
            $p->product_type,
            $p->featured ? "Yes" : "No",
            $p->visibility,
            $p->meta_title ?? "",
            $p->meta_description ?? "",
            $p->meta_keywords ?? "",
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                "font" => ["bold" => true, "size" => 11],
                "fill" => ["fillType" => "solid", "color" => ["rgb" => "D6EAF8"]],
            ],
        ];
    }

    public function columnFormats(): array
    {
        $cols = [];
        foreach (["I", "J", "K", "L"] as $col) $cols[$col] = "#,##0.00";
        foreach (["P", "Q", "R", "S"] as $col) $cols[$col] = "#,##0.00";
        return $cols;
    }
}

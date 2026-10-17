<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_bn' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['nullable', 'integer', 'exists:product_galleries,id'],
            'variant_images' => ['nullable', 'array'],
            'variant_images.*' => ['nullable', 'array'],
            'variant_images.*.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variant_existing_images' => ['nullable', 'array'],
            'variant_existing_images.*' => ['nullable', 'array'],
            'variant_existing_images.*.*' => ['nullable', 'integer', 'exists:product_galleries,id'],
            'variants' => ['nullable', 'array'],
            'variants.*.unit_weight' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'stock_unit' => [
                'nullable',
                'in:unit,kg',
                function ($attribute, $value, $fail) {
                    if ($value !== 'kg') {
                        $stock = $this->input('stock');
                        if ($stock !== null && (float) $stock !== floor((float) $stock)) {
                            $fail('Count stock must be a whole number. Switch Stock Unit to Kilogram (kg) for decimals.');
                        }

                        return;
                    }

                    if (empty($this->input('variants'))) {
                        $weight = $this->input('weight');
                        if ($weight === null || $weight === '' || (float) $weight <= 0) {
                            $fail('Weight (kg) is required for a kilogram-stock product without variants.');
                        }

                        return;
                    }

                    foreach ($this->input('variants') as $variant) {
                        $explicit = $variant['unit_weight'] ?? null;

                        if (is_numeric($explicit) && (float) $explicit > 0) {
                            continue;
                        }

                        $name = (string) ($variant['name'] ?? '');
                        $attributes = json_decode($variant['attributes'] ?? '[]', true) ?: [];

                        if (ProductVariant::deriveUnitWeight(array_merge(['name' => $name], $attributes)) === null) {
                            $fail("Variant \"{$name}\" needs a weight like \"5kg\" (or fill the Weight column) so kilogram stock can be tracked.");
                        }
                    }
                },
            ],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'maximum_order' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'tax_class' => ['nullable', 'string', 'max:100'],
            'shipping_class' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:draft,pending,published,hidden,archived'],
            'product_type' => ['nullable', 'in:simple,variable,digital,service,bundle'],
            'featured' => ['nullable', 'boolean'],
            'visibility' => ['required', 'in:public,private,hidden'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'slug.required' => 'The slug is required.',
            'slug.unique' => 'The slug has already been taken.',
            'sku.unique' => 'The SKU has already been taken.',
            'barcode.unique' => 'The barcode has already been taken.',
            'status.required' => 'The status is required.',
            'visibility.required' => 'The visibility is required.',
            'stock_unit.in' => 'The stock unit must be Count or Kilogram (kg).',
        ];
    }
}

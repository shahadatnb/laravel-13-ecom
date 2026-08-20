<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product') instanceof Product ? $this->route('product') : Product::findOrFail($this->route('product'));

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_bn' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($product->id)],
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
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
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
        ];
    }
}

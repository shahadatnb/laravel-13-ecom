<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductAttributeRequest;
use App\Http\Requests\Admin\UpdateProductAttributeRequest;
use App\Models\ProductAttribute;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductAttributeController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(): View
    {
        $attributes = ProductAttribute::with('values')->orderBy('sort_order')->get();

        return view('admin.product.attribute.index', compact('attributes'));
    }

    public function create(): View
    {
        return view('admin.product.attribute.createOrEdit');
    }

    public function store(StoreProductAttributeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $attribute = ProductAttribute::create($data);

        if ($request->filled('values')) {
            foreach ($request->input('values', []) as $index => $value) {
                if (empty($value['value'])) {
                    continue;
                }

                $attribute->values()->create([
                    'value' => $value['value'],
                    'color_code' => $value['color_code'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.attribute.index')->with('success', 'Attribute created successfully.');
    }

    public function edit(ProductAttribute $attribute): View
    {
        return view('admin.product.attribute.createOrEdit', compact('attribute'));
    }

    public function update(UpdateProductAttributeRequest $request, ProductAttribute $attribute)// : RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $attribute->update($data);

        if ($request->has('values')) {
            $attribute->values()->delete();
            foreach ($request->input('values', []) as $index => $value) {
                if (empty($value['value'])) {
                    continue;
                }

                $attribute->values()->create([
                    'value' => $value['value'],
                    'color_code' => $value['color_code'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.attribute.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy(ProductAttribute $attribute): RedirectResponse
    {
        $attribute->values()->delete();
        $attribute->delete();

        return redirect()->route('admin.attribute.index')->with('success', 'Attribute deleted successfully.');
    }
}

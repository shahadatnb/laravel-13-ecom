<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(private BrandService $brandService) {}

    public function index(): View
    {
        $brands = $this->brandService->list();

        return view('admin.brand.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brand.createOrEdit');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['logo'] = $this->uploadFile($request->file('logo'), 'brands/logo');
        $data['banner'] = $this->uploadFile($request->file('banner'), 'brands/banner');

        $this->brandService->create($data);

        return redirect()->route('admin.brand.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brand.createOrEdit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $this->deleteFile($brand->logo);
            $data['logo'] = $this->uploadFile($request->file('logo'), 'brands/logo');
        }

        if ($request->hasFile('banner')) {
            $this->deleteFile($brand->banner);
            $data['banner'] = $this->uploadFile($request->file('banner'), 'brands/banner');
        }

        $this->brandService->update($brand, $data);

        return redirect()->route('admin.brand.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->deleteFile($brand->logo);
        $this->deleteFile($brand->banner);
        $this->brandService->delete($brand);

        return redirect()->route('admin.brand.index')->with('success', 'Brand deleted successfully.');
    }

    private function uploadFile($file, string $folder): ?string
    {
        if (! $file) {
            return null;
        }

        $filename = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
        $path = 'upload/'.$folder.'/'.$filename;
        Storage::disk('public')->put($path, file_get_contents($file));

        return $path;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

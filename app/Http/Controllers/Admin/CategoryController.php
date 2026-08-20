<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index(): View
    {
        $categories = $this->categoryService->list();

        return view('admin.category.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = $this->categoryService->tree();

        return view('admin.category.createOrEdit', compact('parents'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['icon'] = $this->uploadFile($request->file('icon'), 'categories/icon');
        $data['thumbnail'] = $this->uploadFile($request->file('thumbnail'), 'categories/thumbnail');
        $data['banner'] = $this->uploadFile($request->file('banner'), 'categories/banner');

        $this->categoryService->create($data);

        return redirect()->route('admin.category.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $parents = $this->categoryService->tree()->where('id', '!=', $category->id);

        return view('admin.category.createOrEdit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('icon')) {
            $this->deleteFile($category->icon);
            $data['icon'] = $this->uploadFile($request->file('icon'), 'categories/icon');
        }

        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($category->thumbnail);
            $data['thumbnail'] = $this->uploadFile($request->file('thumbnail'), 'categories/thumbnail');
        }

        if ($request->hasFile('banner')) {
            $this->deleteFile($category->banner);
            $data['banner'] = $this->uploadFile($request->file('banner'), 'categories/banner');
        }

        $this->categoryService->update($category, $data);

        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->deleteFile($category->icon);
        $this->deleteFile($category->thumbnail);
        $this->deleteFile($category->banner);
        $this->categoryService->delete($category);

        return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully.');
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

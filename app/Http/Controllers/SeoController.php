<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /**
     * Handle SPA routes and inject dynamic SEO meta tags into the Blade view.
     * Vue takes over client-side after the initial HTML load.
     */
    public function handle(Request $request)
    {
        $path = $request->path();
        $siteName = SiteSetting::getValue('site_name', config('app.name', 'E-Commerce'));
        $siteDescription = SiteSetting::getValue('site_description', 'E-Commerce Platform');
        $ogImage = SiteSetting::getValue('og_image');

        $seo = [
            'title' => $siteName,
            'description' => $siteDescription,
            'image' => $ogImage,
            'url' => url()->current(),
            'type' => 'website',
        ];

        // ── Product page ──
        if (preg_match('#^product/(.+)$#', $path, $m)) {
            $product = Product::where('slug', $m[1])->select('id', 'name', 'slug', 'short_description', 'description', 'thumbnail', 'meta_title', 'meta_description', 'meta_keywords')->first();

            if ($product) {
                $productImage = $product->thumbnail ? asset('storage/' . $product->thumbnail) : $ogImage;
                $seo['title'] = $product->meta_title ?: "{$product->name} - {$siteName}";
                $seo['description'] = $product->meta_description ?: strip_tags($product->short_description ?: $product->description);
                $seo['image'] = $productImage;
                $seo['url'] = url("/product/{$product->slug}");
                $seo['type'] = 'product';
            }
        }

        // ── Category page ──
        elseif (preg_match('#^category/(.+)$#', $path, $m)) {
            $category = Category::where('slug', $m[1])->select('id', 'name', 'slug', 'description', 'thumbnail', 'meta_title', 'meta_description', 'meta_keywords')->first();

            if ($category) {
                $catImage = $category->thumbnail ? asset('storage/' . $category->thumbnail) : $ogImage;
                $seo['title'] = $category->meta_title ?: "{$category->name} - {$siteName}";
                $seo['description'] = $category->meta_description ?: strip_tags($category->description);
                $seo['image'] = $catImage;
                $seo['url'] = url("/category/{$category->slug}");
                $seo['type'] = 'website';
            }
        }

        // ── Dynamic page ──
        elseif (preg_match('#^page/(.+)$#', $path, $m)) {
            $page = Page::where('slug', $m[1])->select('id', 'title', 'slug', 'meta_title', 'meta_description', 'meta_keywords')->first();

            if ($page) {
                $seo['title'] = $page->meta_title ?: "{$page->title} - {$siteName}";
                $seo['description'] = $page->meta_description ?: '';
                $seo['url'] = url("/page/{$page->slug}");
                $seo['type'] = 'website';
            }
        }

        // ── Other SPA routes keep default site SEO ──

        return view('app', compact('seo'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = config('app.url', url('/'));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $xml .= $this->urlEntry($baseUrl, '1.0', 'daily');

        // Categories
        Category::where('status', 'active')
            ->whereNotNull('slug')
            ->orderBy('name')
            ->chunk(100, function ($categories) use (&$xml, $baseUrl) {
                foreach ($categories as $category) {
                    $xml .= $this->urlEntry("{$baseUrl}/category/{$category->slug}", '0.8', 'weekly');
                }
            });

        // Products
        Product::whereIn('status', ['published', 'active'])
            ->whereNotNull('slug')
            ->orderBy('updated_at', 'desc')
            ->chunk(100, function ($products) use (&$xml, $baseUrl) {
                foreach ($products as $product) {
                    $lastMod = $product->updated_at?->format('Y-m-d') ?? $product->created_at?->format('Y-m-d');
                    $xml .= $this->urlEntry("{$baseUrl}/product/{$product->slug}", '0.7', 'weekly', $lastMod);
                }
            });

        // Static pages
        Page::where('status', 'active')
            ->whereNotNull('slug')
            ->orderBy('title')
            ->chunk(50, function ($pages) use (&$xml, $baseUrl) {
                foreach ($pages as $page) {
                    $xml .= $this->urlEntry("{$baseUrl}/page/{$page->slug}", '0.5', 'monthly');
                }
            });

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function urlEntry(string $url, string $priority = '0.5', string $changefreq = 'monthly', ?string $lastmod = null): string
    {
        $entry = "  <url>\n";
        $entry .= "    <loc>{$url}</loc>\n";
        if ($lastmod) {
            $entry .= "    <lastmod>{$lastmod}</lastmod>\n";
        }
        $entry .= "    <changefreq>{$changefreq}</changefreq>\n";
        $entry .= "    <priority>{$priority}</priority>\n";
        $entry .= "  </url>\n";
        return $entry;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::orderBy('sort_order')->orderBy('title')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,'.$page->id,
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Link preview endpoint for Editor.js Link Tool.
     * Fetches URL metadata (title, description, og:image).
     */
    public function linkPreview(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        $url = $request->input('url');

        try {
            // Try fetching the page to extract meta tags
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Laravel; Editor.js LinkTool)'])
                ->get($url);

            if (! $response->successful()) {
                // Fallback: just return the URL
                return response()->json([
                    'success' => 1,
                    'link' => [
                        'url' => $url,
                        'title' => $url,
                        'description' => '',
                        'image' => null,
                    ],
                ]);
            }

            $html = $response->body();

            // Extract title
            $title = $url;
            if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
                $title = trim($matches[1]);
            }

            // Extract meta description
            $description = '';
            if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                $description = trim($matches[1]);
            } elseif (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                $description = trim($matches[1]);
            }

            // Extract og:image
            $image = null;
            if (preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                $image = trim($matches[1]);
            }

            return response()->json([
                'success' => 1,
                'link' => [
                    'url' => $url,
                    'title' => $title,
                    'description' => $description,
                    'image' => $image ? ['url' => $image] : null,
                ],
            ]);
        } catch (\Exception $e) {
            // Fallback: return URL only
            return response()->json([
                'success' => 1,
                'link' => [
                    'url' => $url,
                    'title' => $url,
                    'description' => '',
                    'image' => null,
                ],
            ]);
        }
    }
}

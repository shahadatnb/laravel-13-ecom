<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * List all published pages (for navigation/footer).
     */
    public function index(): JsonResponse
    {
        $pages = Page::published()->orderBy('sort_order')->get(['id', 'title', 'slug']);

        return response()->json([
            'success' => true,
            'data' => $pages,
        ]);
    }

    /**
     * Get a single page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $page = Page::published()->where('slug', $slug)->first();

        if (! $page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
            ],
        ]);
    }
}

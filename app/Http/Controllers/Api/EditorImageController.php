<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditorImageController extends Controller
{
    /**
     * Upload an image for Editor.js Image Tool.
     * Editor.js expects: { success: 1, file: { url: "..." } }
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        $path = $request->file('image')->store('editor-images', 'public');

        $url = asset('storage/'.$path);

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => $url,
            ],
        ]);
    }

    /**
     * Fetch an image by URL (for by-url uploads in Editor.js).
     * Editor.js Image Tool has a "paste URL" feature.
     */
    public function fetchUrl(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        $url = $request->input('url');

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => $url,
            ],
        ]);
    }
}

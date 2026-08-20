<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Display the media library grid.
     */
    public function index(Request $request): View
    {
        $query = Media::orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by name/original_name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(24);

        return view('admin.media.index', compact('media'));
    }

    /**
     * Upload new media file(s).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
            'alt_text' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Determine type
        $type = 'document';
        if (str_starts_with($mimeType, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $type = 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            $type = 'audio';
        }

        // Generate unique name
        $name = time().'_'.Str::random(10).'.'.$extension;
        $folder = $type === 'image' ? 'media/images' : 'media/files';
        $path = $file->storeAs($folder, $name, 'public');
        $url = asset('storage/'.$path);

        Media::create([
            'name' => pathinfo($originalName, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'path' => $path,
            'url' => $url,
            'type' => $type,
            'mime_type' => $mimeType,
            'size' => $size,
            'alt_text' => $request->alt_text,
            'description' => $request->description,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'File uploaded successfully.']);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'File uploaded successfully.');
    }

    /**
     * AJAX upload endpoint (for dropzone/modal).
     */
    public function uploadAjax(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        $type = 'document';
        if (str_starts_with($mimeType, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $type = 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            $type = 'audio';
        }

        $name = time().'_'.Str::random(10).'.'.$extension;
        $folder = $type === 'image' ? 'media/images' : 'media/files';
        $path = $file->storeAs($folder, $name, 'public');
        $url = asset('storage/'.$path);

        $media = Media::create([
            'name' => pathinfo($originalName, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'path' => $path,
            'url' => $url,
            'type' => $type,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);

        return response()->json([
            'success' => true,
            'media' => [
                'id' => $media->id,
                'url' => $media->url,
                'name' => $media->name,
                'type' => $media->type,
                'thumb' => $type === 'image' ? $url : null,
            ],
        ]);
    }

    /**
     * Delete media item.
     */
    public function destroy(Media $medium): RedirectResponse|JsonResponse
    {
        $medium->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'File deleted.']);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'File deleted successfully.');
    }

    /**
     * API: List media for Editor.js Browse Media integration.
     * Returns paginated JSON for the browse modal.
     */
    public function browse(Request $request): JsonResponse
    {
        $query = Media::orderBy('created_at', 'desc');

        // Only images by default
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        } else {
            $query->where('type', 'image');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 24);
        $media = $query->paginate($perPage);

        $items = $media->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => $item->url,
                'name' => $item->name,
                'original_name' => $item->original_name,
                'type' => $item->type,
                'mime_type' => $item->mime_type,
                'size' => $item->formatted_size,
                'alt_text' => $item->alt_text,
                'created_at' => $item->created_at?->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'per_page' => $media->perPage(),
                'total' => $media->total(),
            ],
        ]);
    }
}

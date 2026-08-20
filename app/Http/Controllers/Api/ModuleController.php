<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    public function index(): JsonResponse
    {
        $modules = collect(config('modules', []))->map(function ($meta, $key) {
            return [
                'key' => $key,
                'label' => $meta['label'] ?? ucfirst($key),
                'enabled' => module_enabled($key),
            ];
        })->values();

        return response()->json([
            'modules' => $modules,
        ]);
    }
}

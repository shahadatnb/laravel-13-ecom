<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    /**
     * Get all public site settings and hero slides for the SPA frontend.
     */
    public function index(): JsonResponse
    {
        $slides = HeroSlide::active()->get()->map(function ($slide) {
            return [
                'id' => $slide->id,
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'cta_text' => $slide->cta_text,
                'cta_link' => $slide->cta_link,
                'bg_gradient' => $slide->bg_gradient,
                'image_emoji' => $slide->image_emoji,
                'badge_text' => $slide->badge_text,
            ];
        });

        $settings = SiteSetting::getAllAsMap();

        $themes = collect(config('themes', []))->map(function ($meta, $key) {
            return [
                'key' => $key,
                'label' => $meta['label'] ?? ucfirst($key),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'slides' => $slides,
                'settings' => $settings,
                'themes' => $themes,
                'active_theme' => $settings['active_theme'] ?? 'classic',
            ],
        ]);
    }
}

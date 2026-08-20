<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    // ===== HERO SLIDES =====

    public function heroSlides(): View
    {
        $slides = HeroSlide::orderBy('sort_order')->get();

        return view('admin.settings.hero-slides', compact('slides'));
    }

    public function storeHeroSlide(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'bg_gradient' => 'nullable|string|max:255',
            'image_emoji' => 'nullable|string|max:10',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        HeroSlide::create($validated);

        return redirect()->route('admin.settings.hero-slides')
            ->with('success', 'Hero slide created successfully.');
    }

    public function updateHeroSlide(Request $request, HeroSlide $slide): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'bg_gradient' => 'nullable|string|max:255',
            'image_emoji' => 'nullable|string|max:10',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $slide->update($validated);

        return redirect()->route('admin.settings.hero-slides')
            ->with('success', 'Hero slide updated successfully.');
    }

    public function destroyHeroSlide(HeroSlide $slide): RedirectResponse
    {
        $slide->delete();

        return redirect()->route('admin.settings.hero-slides')
            ->with('success', 'Hero slide deleted successfully.');
    }

    // ===== SITE SETTINGS =====

    public function siteSettings(): View
    {
        $settings = SiteSetting::orderBy('group')->orderBy('id')->get();

        return view('admin.settings.site-settings', compact('settings'));
    }

    public function updateSiteSettings(Request $request): RedirectResponse
    {
        $settings = SiteSetting::all();

        foreach ($settings as $setting) {
            $key = $setting->key;

            // Handle image-type settings (file upload)
            if ($setting->type === 'image') {
                if ($request->hasFile($key)) {
                    // Delete old file
                    if (! empty($setting->value) && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }

                    // Upload new file
                    $path = $request->file($key)->store('settings', 'public');
                    $setting->update(['value' => $path]);
                }
                // If no file uploaded and clear checkbox is set, delete the image
                elseif ($request->has($key.'_clear') && ! empty($setting->value)) {
                    if (Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    $setting->update(['value' => '']);
                }

                continue;
            }

            // Handle select settings — value must be one of the registered themes
            if ($setting->type === 'select') {
                if ($request->has($key)) {
                    $validated = $request->validate([
                        $key => ['required', Rule::in(array_keys(config('themes', [])))],
                    ]);

                    $setting->update(['value' => $validated[$key]]);
                }

                continue;
            }

            // Handle text/textarea/json settings
            if ($request->has($key)) {
                $value = $request->input($key);

                if ($setting->type === 'json') {
                    if (empty($value)) {
                        $value = '[]';
                    } else {
                        $decoded = json_decode($value, true);
                        $value = ($decoded !== null) ? json_encode($decoded) : '[]';
                    }
                }

                $setting->update(['value' => $value]);
            }
        }

        return redirect()->route('admin.settings.site-settings')
            ->with('success', 'Site settings updated successfully.');
    }
}

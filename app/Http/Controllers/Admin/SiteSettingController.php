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
            'bg_image' => 'nullable|file|image|max:2048',
            'image_emoji' => 'nullable|string|max:10',
            'feature_image' => 'nullable|file|image|max:2048',
            'image_position' => 'nullable|string|in:left,right',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('bg_image')) {
            $validated['bg_image'] = $request->file('bg_image')->store('hero-slides', 'public');
        }
        if ($request->hasFile('feature_image')) {
            $validated['feature_image'] = $request->file('feature_image')->store('hero-slides', 'public');
        }
        unset($validated['bg_image'], $validated['feature_image']);

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
            'bg_image' => 'nullable|file|image|max:2048',
            'image_emoji' => 'nullable|string|max:10',
            'feature_image' => 'nullable|file|image|max:2048',
            'image_position' => 'nullable|string|in:left,right',
            'badge_text' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('bg_image')) {
            if ($slide->bg_image && Storage::disk('public')->exists($slide->bg_image)) {
                Storage::disk('public')->delete($slide->bg_image);
            }
            $validated['bg_image'] = $request->file('bg_image')->store('hero-slides', 'public');
        }
        if ($request->hasFile('feature_image')) {
            if ($slide->feature_image && Storage::disk('public')->exists($slide->feature_image)) {
                Storage::disk('public')->delete($slide->feature_image);
            }
            $validated['feature_image'] = $request->file('feature_image')->store('hero-slides', 'public');
        }

        $slide->update($validated);

        return redirect()->route('admin.settings.hero-slides')
            ->with('success', 'Hero slide updated successfully.');
    }

    public function destroyHeroSlide(HeroSlide $slide): RedirectResponse
    {
        if ($slide->bg_image && Storage::disk('public')->exists($slide->bg_image)) {
            Storage::disk('public')->delete($slide->bg_image);
        }
        if ($slide->feature_image && Storage::disk('public')->exists($slide->feature_image)) {
            Storage::disk('public')->delete($slide->feature_image);
        }

        $slide->delete();

        return redirect()->route('admin.settings.hero-slides')
            ->with('success', 'Hero slide deleted successfully.');
    }

    // ===== FEATURE ITEMS =====

    public function featureItems(): View
    {
        $items = SiteSetting::getValue('trust_features', []);

        return view('admin.settings.feature-items', compact('items'));
    }

    public function storeFeatureItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'icon' => 'required|string|max:10',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:255',
        ]);

        $items = SiteSetting::getValue('trust_features', []);
        $items[] = $validated;

        SiteSetting::where('key', 'trust_features')->update(['value' => json_encode($items)]);

        return redirect()->route('admin.settings.feature-items')
            ->with('success', 'Feature item added successfully.');
    }

    public function updateFeatureItem(Request $request, int $index): RedirectResponse
    {
        $validated = $request->validate([
            'icon' => 'required|string|max:10',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:255',
        ]);

        $items = SiteSetting::getValue('trust_features', []);

        if (! isset($items[$index])) {
            return redirect()->route('admin.settings.feature-items')
                ->with('error', 'Feature item not found.');
        }

        $items[$index] = $validated;

        SiteSetting::where('key', 'trust_features')->update(['value' => json_encode($items)]);

        return redirect()->route('admin.settings.feature-items')
            ->with('success', 'Feature item updated successfully.');
    }

    public function destroyFeatureItem(int $index): RedirectResponse
    {
        $items = SiteSetting::getValue('trust_features', []);

        if (! isset($items[$index])) {
            return redirect()->route('admin.settings.feature-items')
                ->with('error', 'Feature item not found.');
        }

        array_splice($items, $index, 1);

        SiteSetting::where('key', 'trust_features')->update(['value' => json_encode($items)]);

        return redirect()->route('admin.settings.feature-items')
            ->with('success', 'Feature item deleted successfully.');
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

    /**
     * Show theme texts management page.
     */
    public function themeTexts(): View
    {
        $settings = SiteSetting::where('group', 'theme_texts')->pluck('value', 'key')->toArray();
        return view('admin.settings.theme-texts', compact('settings'));
    }

    /**
     * Update theme texts.
     */
    public function updateThemeTexts(Request $request): RedirectResponse
    {
        $texts = $request->input('texts', []);
        foreach ($texts as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text', 'group' => 'theme_texts']
            );
        }
        return redirect()->route('admin.settings.theme-texts')
            ->with('success', 'Theme texts updated successfully.');
    }
}

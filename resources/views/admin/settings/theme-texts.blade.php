@extends('admin.layouts.app')
@section('title', 'Theme Texts')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-font"></i> Theme Texts / Marketing Copy</h3>
            </div>
            <div class="card-body">
                @include('admin.layouts._message')
                <form action="{{ route('admin.settings.theme-texts.update') }}" method="POST">
                    @csrf

                    {{-- Section: Common --}}
                    <h5 class="mb-3 mt-4"><i class="fas fa-star text-warning"></i> Common Elements</h5>
                    <div class="row">
                        @foreach(['secure_checkout' => 'Secure Checkout Title', 'secure_checkout_desc' => 'Secure Checkout Description', 'easy_returns' => 'Easy Returns Title', 'easy_returns_desc' => 'Easy Returns Description', 'support_247' => '24/7 Support Title', 'support_247_desc' => '24/7 Support Description'] as $key => $label)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $label }}</label>
                                <input type="text" name="texts[theme_text_{{ $key }}]" value="{{ $settings["theme_text_{$key}"] ?? '' }}" class="form-control">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr>

                    {{-- Section: Section Titles --}}
                    <h5 class="mb-3"><i class="fas fa-heading text-primary"></i> Section Titles & Subtitles</h5>
                    <div class="row">
                        @foreach(['shop_by_category' => 'Shop by Category', 'shop_by_category_subtitle' => 'Category Subtitle', 'featured_products' => 'Featured Products', 'featured_products_subtitle' => 'Featured Products Subtitle', 'new_arrivals' => 'New Arrivals', 'new_arrivals_subtitle' => 'New Arrivals Subtitle', 'featured_deals' => 'Featured Deals', 'featured_deals_subtitle' => 'Featured Deals Subtitle', 'shop_the_collection' => 'Shop the Collection', 'collection_subtitle' => 'Collection Subtitle'] as $key => $label)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $label }}</label>
                                <input type="text" name="texts[theme_text_{{ $key }}]" value="{{ $settings["theme_text_{$key}"] ?? '' }}" class="form-control">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr>

                    {{-- Section: Hero Fallback --}}
                    <h5 class="mb-3"><i class="fas fa-image text-info"></i> Hero Fallback (when no slides in DB)</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Hero Title</label>
                                <input type="text" name="texts[theme_text_hero_title]" value="{{ $settings['theme_text_hero_title'] ?? '' }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Hero Subtitle</label>
                                <textarea name="texts[theme_text_hero_subtitle]" class="form-control" rows="2">{{ $settings['theme_text_hero_subtitle'] ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hero CTA Button Text</label>
                                <input type="text" name="texts[theme_text_hero_cta]" value="{{ $settings['theme_text_hero_cta'] ?? 'Shop Now' }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Section: Urgency Strip --}}
                    <h5 class="mb-3"><i class="fas fa-bolt text-danger"></i> Urgency Strip (Deals Theme)</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Flash Deals Text</label>
                                <input type="text" name="texts[theme_text_urgency_flash_deals]" value="{{ $settings['theme_text_urgency_flash_deals'] ?? '' }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Easy Returns Text</label>
                                <input type="text" name="texts[theme_text_urgency_easy_returns]" value="{{ $settings['theme_text_urgency_easy_returns'] ?? '' }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Section: Bengali / Localized --}}
                    <h5 class="mb-3"><i class="fas fa-language text-success"></i> Bengali / Localized Texts (Showroom Theme)</h5>
                    <div class="row">
                        @foreach(['cash_on_delivery' => 'Cash on Delivery', 'original_product' => 'Original Product'] as $key => $label)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $label }}</label>
                                <input type="text" name="texts[theme_text_{{ $key }}]" value="{{ $settings["theme_text_{$key}"] ?? '' }}" class="form-control">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg mt-3">
                        <i class="fas fa-save"></i> Save All Texts
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Site Settings')

@push('styles')
<style>
.nav-tabs .nav-link.active { font-weight: 600; border-bottom: 2px solid #007bff; }
</style>
@endpush

@push('scripts')
<script>
$(function() {
    // Initialize custom file inputs (Bootstrap 4 plugin)
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
});
</script>
@endpush

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Site Settings</h3>
            </div>
            <form action="{{ route('admin.settings.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        @php
                            $groups = $settings->pluck('group')->unique();
                            $active = true;
                        @endphp
                        @foreach($groups as $group)
                        <li class="nav-item">
                            <a class="nav-link {{ $active ? 'active' : '' }}" id="{{ $group }}-tab" data-toggle="tab"
                                href="#{{ $group }}" role="tab">
                                {{ ucfirst($group) }}
                            </a>
                            @php $active = false; @endphp
                        </li>
                        @endforeach
                    </ul>
                    <div class="tab-content mt-4" id="settingsTabsContent">
                        @php $active = true; @endphp
                        @foreach($groups as $group)
                        <div class="tab-pane {{ $active ? 'show active' : '' }}" id="{{ $group }}" role="tabpanel">
                            @php $active = false; @endphp
                            <div class="row">
                                @foreach($settings->where('group', $group) as $setting)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="{{ $setting->key }}">{{ $setting->label ?? $setting->key }}</label>

                                        @if($setting->type === 'image')
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    @if(!empty($setting->value))
                                                        <img src="{{ asset('storage/' . $setting->value) }}"
                                                            alt="{{ $setting->label }}"
                                                            class="img-thumbnail"
                                                            style="max-width:120px; max-height:120px; object-fit:contain;">
                                                        <div class="mt-2">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="{{ $setting->key }}_clear" name="{{ $setting->key }}_clear" value="1">
                                                                <label class="custom-control-label text-danger" for="{{ $setting->key }}_clear">
                                                                    <small>Remove image</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="d-flex align-items-center justify-content-center"
                                                            style="width:120px; height:80px; background:#f8f9fa; border:1px dashed #ddd; border-radius:4px;">
                                                            <small class="text-muted">No image</small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="{{ $setting->key }}"
                                                            name="{{ $setting->key }}" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                                        <label class="custom-file-label" for="{{ $setting->key }}">Choose file</label>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        PNG, JPG, WebP or SVG. Max 2MB.
                                                    </small>
                                                </div>
                                            </div>

                                        @elseif($setting->type === 'textarea')
                                            <textarea name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="form-control @error($setting->key) is-invalid @enderror"
                                                rows="3">{{ old($setting->key, $setting->value) }}</textarea>

                                        @elseif($setting->type === 'json')
                                            <textarea name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="form-control @error($setting->key) is-invalid @enderror font-monospace"
                                                rows="5" style="font-family: monospace; font-size: 12px;"
                                                placeholder="Enter JSON data...">{{ old($setting->key, $setting->value) }}</textarea>
                                            <small class="text-muted">JSON format. Use <code>[]</code> for arrays.</small>

                                        @elseif($setting->type === 'select')
                                            <select name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="form-control @error($setting->key) is-invalid @enderror">
                                                @foreach(config('themes', []) as $themeKey => $themeMeta)
                                                    <option value="{{ $themeKey }}"
                                                        {{ old($setting->key, $setting->value) === $themeKey ? 'selected' : '' }}>
                                                        {{ $themeMeta['label'] ?? ucfirst($themeKey) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @php
                                                $currentTheme = config("themes." . old($setting->key, $setting->value), null);
                                            @endphp
                                            @if($currentTheme)
                                                <small class="text-muted d-block mt-1">{{ $currentTheme['description'] }}</small>
                                            @endif

                                        @else
                                            <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}"
                                                class="form-control @error($setting->key) is-invalid @enderror"
                                                value="{{ old($setting->key, $setting->value) }}">

                                        @endif
                                        @error($setting->key)
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save All Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

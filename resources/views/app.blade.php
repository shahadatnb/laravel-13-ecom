<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO: Title --}}
    <title>{{ $seo['title'] ?? \App\Models\SiteSetting::getValue('site_name', config('app.name', 'E-Commerce')) }}</title>

    {{-- SEO: Description --}}
    <meta name="description" content="{{ $seo['description'] ?? \App\Models\SiteSetting::getValue('site_description', 'E-Commerce Platform') }}">

    {{-- SEO: Keywords (if available) --}}
    @isset($seo['keywords'])
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endisset

    {{-- Favicon --}}
    @php $favicon = \App\Models\SiteSetting::getValue('favicon'); @endphp
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
        <link rel="shortcut icon" href="{{ $favicon }}">
        <link rel="apple-touch-icon" href="{{ $favicon }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- SEO: Open Graph / Social Share --}}
    <meta property="og:title" content="{{ $seo['title'] ?? \App\Models\SiteSetting::getValue('site_name', config('app.name', 'E-Commerce')) }}">
    <meta property="og:description" content="{{ $seo['description'] ?? \App\Models\SiteSetting::getValue('site_description', 'E-Commerce Platform') }}">
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $seo['url'] ?? url()->current() }}">
    <meta property="og:site_name" content="{{ \App\Models\SiteSetting::getValue('site_name', config('app.name', 'E-Commerce')) }}">
    @if(!empty($seo['image']))
        <meta property="og:image" content="{{ $seo['image'] }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif

    {{-- SEO: Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] ?? \App\Models\SiteSetting::getValue('site_name', config('app.name', 'E-Commerce')) }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? \App\Models\SiteSetting::getValue('site_description', 'E-Commerce Platform') }}">
    @if(!empty($seo['image']))
        <meta name="twitter:image" content="{{ $seo['image'] }}">
    @endif

    {{-- SEO: Canonical URL --}}
    <link rel="canonical" href="{{ $seo['url'] ?? url()->current() }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Vite SPA --}}
    @vite(['src/main.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div id="app"></div>

    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            apiUrl: '{{ url("/api") }}',
            appName: '{{ config("app.name") }}'
        }
    </script>
</body>
</html>

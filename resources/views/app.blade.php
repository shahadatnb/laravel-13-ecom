<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\SiteSetting::getValue('site_name', config('app.name', 'E-Commerce')) }}</title>

    <!-- Meta tags -->
    <meta name="description" content="{{ \App\Models\SiteSetting::getValue('site_description', 'E-Commerce Platform') }}">

    <!-- Favicon -->
    @php $favicon = \App\Models\SiteSetting::getValue('favicon'); @endphp
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
        <link rel="shortcut icon" href="{{ $favicon }}">
        <link rel="apple-touch-icon" href="{{ $favicon }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Open Graph / Social Share -->
    @php $ogImage = \App\Models\SiteSetting::getValue('og_image'); @endphp
    <meta property="og:title" content="{{ \App\Models\SiteSetting::getValue('site_name', config('app.name', 'E-Commerce')) }}">
    <meta property="og:description" content="{{ \App\Models\SiteSetting::getValue('site_description', 'E-Commerce Platform') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Vite SPA via Laravel Vite helper -->
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

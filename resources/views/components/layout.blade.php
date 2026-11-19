@props(['title' => null, 'description' => null])
@php
    $site = \App\Support\SiteContent::group('general');
    $scripts = \App\Support\SiteContent::group('scripts');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($site['name'].' — '.$site['tagline']) }}</title>
    <meta name="description" content="{{ $description ?? $site['meta_description'] }}">
    <meta name="theme-color" content="#0A2459">
    <link rel="icon" type="image/png" href="{{ asset('images/logo/mark-160.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <script>document.documentElement.classList.add('js');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (! empty($scripts['head_scripts']))
        {!! $scripts['head_scripts'] !!}
    @endif
</head>
<body>
    {{ $slot }}
    @if (! empty($scripts['body_scripts']))
        {!! $scripts['body_scripts'] !!}
    @endif
</body>
</html>

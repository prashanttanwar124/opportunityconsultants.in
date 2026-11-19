@props(['active' => null])

@php
    $site = \App\Support\SiteContent::group('general');
    $home = request()->routeIs('home') ? '' : route('home');
    $links = [
        ['href' => $home.'#programs', 'label' => 'Programs', 'key' => 'programs'],
        ['href' => $home.'#destinations', 'label' => 'Destinations', 'key' => 'destinations'],
        ['href' => $home.'#process', 'label' => 'How it works', 'key' => 'process'],
        ['href' => route('about'), 'label' => 'About us', 'key' => 'about'],
        ['href' => route('blog.index'), 'label' => 'Blog', 'key' => 'blog'],
        ['href' => $home.'#faq', 'label' => 'FAQ', 'key' => 'faq'],
    ];

    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
            foreach (\App\Models\Page::forHeader()->get() as $customPage) {
                $links[] = [
                    'href' => route('pages.show', $customPage),
                    'label' => $customPage->title,
                    'key' => 'page:'.$customPage->slug,
                ];
            }
        }
    } catch (\Throwable) {
        // Ignore when migrations have not run yet.
    }
@endphp

<div class="utility-bar">
    <div class="container">
        <p><span class="dot" aria-hidden="true"></span>{{ $site['utility_text'] }}</p>
        <div class="utility-bar__links">
            <a href="tel:{{ $site['phone'] }}">{{ $site['phone_display'] }}</a>
            <a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ $site['name'] }} home">
            <x-logo variant="mark" class="brand__mark hide-sm" />
            <x-logo variant="word" class="brand__word" :alt="$site['name'].' — Study Abroad Consultant'" />
        </a>

        <nav class="main-nav" aria-label="Main">
            @foreach ($links as $link)
                <a class="nav-link" href="{{ $link['href'] }}" @if ($active === $link['key']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="header-actions">
            <a href="https://wa.me/{{ $site['whatsapp'] }}" class="btn btn--sm btn--outline hide-sm" style="font-weight: 700">
                <x-icon name="chat" size="18" />
                WhatsApp
            </a>
            <a href="{{ route('apply') }}" class="btn btn--sm btn--accent">Free counselling</a>

            <details class="mobile-nav">
                <summary aria-label="Open menu"><x-icon name="menu" size="20" /></summary>
                <nav class="mobile-nav__panel" aria-label="Mobile">
                    @foreach ($links as $link)
                        <a href="{{ $link['href'] }}" @if ($active === $link['key']) aria-current="page" @endif>{{ $link['label'] }}</a>
                    @endforeach
                    <a href="https://wa.me/{{ $site['whatsapp'] }}">WhatsApp us</a>
                </nav>
            </details>
        </div>
    </div>
</header>

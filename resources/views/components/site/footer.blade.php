@php
    $home = request()->routeIs('home') ? '' : route('home');
@endphp

<footer class="site-footer">
    <div class="container">
        <div class="grid-auto footer-grid">
            <div class="footer-brand">
                <span class="footer-brand__logo">
                    <x-logo variant="full" :alt="config('site.name').' — Study Abroad Consultant'" />
                </span>
                <span class="footer-brand__tag">{{ config('site.tagline') }}</span>
                <p>Free counselling for students and parents.</p>
            </div>

            <nav class="footer-nav" aria-label="Programs">
                <strong>Programs</strong>
                <a href="{{ $home }}#programs">Study abroad</a>
                <a href="{{ $home }}#programs">Internships abroad</a>
                <a href="{{ $home }}#services">Scholarships &amp; loans</a>
                <a href="{{ $home }}#services">Visas</a>
            </nav>

            <nav class="footer-nav" aria-label="Destinations">
                <strong>Destinations</strong>
                <a href="{{ $home }}#destinations">UK &amp; Ireland</a>
                <a href="{{ $home }}#destinations">USA &amp; Canada</a>
                <a href="{{ $home }}#destinations">Australia</a>
                <a href="{{ $home }}#destinations">Germany</a>
                <a href="{{ $home }}#destinations">Dubai &amp; Mauritius</a>
            </nav>

            <nav class="footer-nav" aria-label="Company">
                <strong>Company</strong>
                <a href="{{ route('about') }}">About us</a>
                <a href="{{ $home }}#faq">FAQ</a>
                <a href="{{ route('apply') }}">Free counselling</a>
                @foreach (config('site.socials') as $label => $url)
                    <a href="{{ $url }}">{{ $label }}</a>
                @endforeach
            </nav>
        </div>

        <div class="footer-bottom">
            <span>© {{ date('Y') }} {{ config('site.name') }}. All rights reserved.</span>
            <span><a href="#">Privacy policy</a><a href="#">Terms</a></span>
        </div>
    </div>
</footer>

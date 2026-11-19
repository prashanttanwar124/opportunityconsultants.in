<x-layout :title="'About us — '.$site['name']">
<x-site.header active="about" />

<main>

{{-- Hero --}}
<section class="hero about-hero bg-hero">
    <div class="container hero__inner">
        <div class="hero__copy" style="gap: 24px">
            <span class="eyebrow eyebrow--light rise">About us</span>
            <h1 class="display rise d1">{{ $about['hero_title'] }} <span class="text-accent">{{ $about['hero_title_accent'] }}</span></h1>
            <p class="hero__text rise d2" style="max-width: 560px">{{ $about['hero_text'] }}</p>
        </div>
        <dl class="glance rise d3">
            <div class="glance__bar">
                <span>AT A GLANCE</span>
                <x-logo variant="mark" sizes="42px" />
            </div>
            <div class="glance__grid">
                @foreach ($about['glance'] ?? [] as $item)
                    <div><dt class="label-mono">{{ $item['label'] ?? '' }}</dt><dd>{{ $item['value'] ?? '' }}</dd></div>
                @endforeach
            </div>
        </dl>
    </div>
</section>

{{-- Story, vision & mission --}}
<section class="section">
    <div class="bg-layer bg-stamps" aria-hidden="true"></div>
    <div class="container story">
        <div class="story__panel reveal">
            <span class="eyebrow eyebrow--muted">{{ $about['story_eyebrow'] }}</span>
            <h2 class="display h-section-sm">{{ $about['story_title'] }}</h2>
            <p>{{ $about['story_text'] }}</p>
            <ul class="services-list">
                <li><x-icon name="search" size="18" stroke="2.2" />University shortlisting</li>
                <li><x-icon name="language" size="18" stroke="2.2" />IELTS, TOEFL &amp; PTE training</li>
                <li><x-icon name="passport" size="18" stroke="2.2" />Visa processing</li>
                <li><x-icon name="cap" size="18" stroke="2.2" />Post-admission guidance</li>
            </ul>
        </div>
        <div class="stack" style="gap: 16px">
            <article class="card card--navy vm-card reveal lift">
                <span class="vm-card__title">
                    <span class="icon-box icon-box--sm icon-box--accent"><x-icon name="eye" size="22" /></span>
                    <h3>{{ $about['vision_title'] }}</h3>
                </span>
                <p>{{ $about['vision_text'] }}</p>
            </article>
            <article class="card vm-card reveal s2 lift" style="border-width: 1.5px">
                <span class="vm-card__title">
                    <span class="icon-box icon-box--sm"><x-icon name="target" size="22" /></span>
                    <h3>{{ $about['mission_title'] }}</h3>
                </span>
                <p>{{ $about['mission_text'] }}</p>
            </article>
        </div>
    </div>
</section>

{{-- Leadership --}}
<section class="section bg-mist">
    <div class="container stack" style="gap: 44px">
        <div class="section-head__title reveal">
            <span class="eyebrow eyebrow--muted">Leadership</span>
            <h2 class="display h-section">The people behind your plan.</h2>
        </div>
        <div class="grid-auto leaders">
            @foreach ($leaders as $person)
                <article class="leader reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }}">
                    @if (! empty($person['photo']))
                        <img src="{{ asset('storage/'.$person['photo']) }}" alt="{{ $person['name'] ?? '' }}" class="photo-ph photo-ph--portrait photo-img" loading="lazy">
                    @else
                        <div class="photo-ph photo-ph--portrait" aria-hidden="true">{{ $person['initials'] ?? '' }}</div>
                    @endif
                    <div class="leader__body">
                        <span class="leader__role {{ ! empty($person['accent']) ? 'leader__role--accent' : '' }}">{{ $person['role'] ?? '' }}</span>
                        <h3>{{ $person['name'] ?? '' }}</h3>
                        <p class="leader__meta">{{ $person['meta'] ?? '' }}</p>
                        <p class="leader__bio">{{ $person['bio'] ?? '' }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Team --}}
<section class="section team-section">
    <div class="bg-layer bg-skyline" aria-hidden="true"></div>
    <div class="container stack" style="gap: 44px">
        <div class="section-head reveal">
            <div class="section-head__title" style="max-width: 640px">
                <span class="eyebrow">Our team</span>
                <h2 class="display h-section">The team that keeps your journey on track.</h2>
            </div>
            <a href="{{ route('apply') }}" class="btn btn--navy" style="height: 52px; padding: 0 22px">Talk to a counsellor <x-icon name="arrow" size="18" stroke="2.4" /></a>
        </div>
        <div class="team-grid">
            @foreach ($team as $member)
                <article class="card member reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }}">
                    @if (! empty($member['photo']))
                        <img src="{{ asset('storage/'.$member['photo']) }}" alt="{{ $member['name'] ?? '' }}" class="photo-ph photo-ph--square photo-img" loading="lazy">
                    @else
                        <div class="photo-ph photo-ph--square" aria-hidden="true">{{ $member['initials'] ?? '' }}</div>
                    @endif
                    <div class="member__body">
                        <h3>{{ $member['name'] ?? '' }}</h3>
                        <span class="member__role">{{ $member['role'] ?? '' }}</span>
                        <p>{{ $member['bio'] ?? '' }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Visit --}}
<section class="section destinations bg-map-light">
    <div class="container visit">
        <div class="map-ph reveal">
            <span class="map-ph__pin" aria-hidden="true"><x-icon name="pin" size="28" color="#0A2459" /></span>
            <span class="map-ph__label">{{ $site['address_short'] }}</span>
        </div>
        <div class="visit__copy reveal s2">
            <div class="stack" style="gap: 14px">
                <span class="eyebrow eyebrow--muted">Visit us</span>
                <h2 class="display h-section-sm">{{ $about['visit_title'] }}</h2>
            </div>
            <address>
                <span><x-icon name="pin" size="22" stroke="1.8" /><span>{{ $site['address'] }}</span></span>
                <span><x-icon name="clock" size="22" stroke="1.8" /><span>{{ $site['hours'] }}</span></span>
                <span><x-icon name="phone" size="22" stroke="1.8" /><a href="tel:{{ $site['phone'] }}">{{ $site['phone_display'] }}</a></span>
                <span><x-icon name="mail" size="22" stroke="1.8" /><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></span>
            </address>
            <div class="btn-row">
                <a href="{{ route('apply') }}" class="btn btn--accent">Book a free session <x-icon name="arrow" size="18" stroke="2.4" /></a>
                <a href="{{ $site['directions_url'] }}" class="btn btn--outline" target="_blank" rel="noopener">Get directions</a>
            </div>
        </div>
    </div>
</section>

{{-- CTA band --}}
<section class="section cta-band" style="padding-block: clamp(56px, 7vw, 88px)">
    <span class="cta-band__ghost" aria-hidden="true" style="font-size: 240px; bottom: -70px">FLY</span>
    <div class="container cta-strip reveal">
        <h2 class="display">{{ $about['cta_title'] }}</h2>
        <div class="btn-row">
            <a href="{{ route('apply') }}" class="btn btn--lg btn--navy">Check my eligibility <x-icon name="arrow" size="20" stroke="2.4" /></a>
            <a href="https://wa.me/{{ $site['whatsapp'] }}" class="btn btn--lg btn--outline-2">Chat on WhatsApp</a>
        </div>
    </div>
</section>

</main>

<x-site.footer />
</x-layout>

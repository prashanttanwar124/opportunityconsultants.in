<x-layout>
<x-site.header />

<main>

{{-- Hero --}}
<section class="hero bg-hero">
    <div class="container hero__inner">
        <div class="hero__copy">
            <span class="pill rise"><span class="dot" aria-hidden="true"></span>{{ $home['hero_pill'] }}</span>
            <h1 class="display h-hero rise d1">{{ $home['hero_title'] }} <span class="text-accent">{{ $home['hero_title_accent'] }}</span></h1>
            <p class="hero__text rise d2">{{ $home['hero_text'] }}</p>
            <div class="btn-row rise d3" style="gap: 14px">
                <a href="{{ route('apply') }}" class="btn btn--lg btn--accent btn--glow">
                    {{ $home['hero_primary_cta'] }}
                    <x-icon name="arrow" size="20" stroke="2.4" />
                </a>
                <a href="https://wa.me/{{ $site['whatsapp'] }}" class="btn btn--lg btn--outline-light">
                    <x-icon name="chat" size="20" />
                    {{ $home['hero_secondary_cta'] }}
                </a>
            </div>
            <div class="social-proof rise d4">
                <span class="avatars" aria-hidden="true">
                    <span class="avatar avatar--accent">RD</span>
                    <span class="avatar avatar--blue">AK</span>
                    <span class="avatar avatar--white">SP</span>
                </span>
                <p><strong>{{ $home['hero_proof_line1'] }}</strong><br>{{ $home['hero_proof_line2'] }}</p>
            </div>
        </div>

        {{-- Departure board --}}
        <div class="board-wrap rise d5">
            <div class="board">
                <div class="board__head">
                    <span class="board__title"><x-icon name="plane" size="18" />DEPARTURES</span>
                    <span class="board__sub">INDIA → THE WORLD</span>
                </div>
                <div class="board__grid">
                    <span class="board__col hide-sm">FLIGHT</span>
                    <span class="board__col">DESTINATION</span>
                    <span class="board__col" style="text-align: right">STATUS</span>
                    @foreach ($departures as $row)
                        <span class="board__code hide-sm">{{ $row['code'] }}</span>
                        <span class="board__tiles" role="img" aria-label="{{ $row['label'] }}">
                            @foreach ($row['tiles'] as $i => $char)
                                <span class="tile" aria-hidden="true" style="animation-delay: {{ 0.6 + $loop->parent->index * 0.12 + $i * 0.04 }}s">{{ $char === ' ' ? "\u{00A0}" : $char }}</span>
                            @endforeach
                        </span>
                        <a href="{{ route('apply') }}" class="board__enquire">ENQUIRE <x-icon name="arrow" size="14" stroke="2.6" /></a>
                    @endforeach
                </div>
                <div class="board__foot">
                    <span>Dream country not listed?</span>
                    <a href="{{ route('apply') }}">Ask a counsellor</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Callback form --}}
<section id="callback" class="callback" aria-labelledby="cb-title">
    <div class="container">
        <form class="callback__card rise d5" method="POST" action="{{ route('callback.store') }}" novalidate>
            @csrf
            <div class="callback__intro">
                <h2 id="cb-title" class="display">{{ $home['callback_title'] }}</h2>
                <p>{{ $home['callback_subtitle'] }}</p>
            </div>
            <div class="field">
                <label for="cb-name">Full name</label>
                <input id="cb-name" name="name" type="text" class="input @error('name', 'callback') is-invalid @enderror" autocomplete="name" placeholder="e.g. Priya Sharma" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label for="cb-phone">WhatsApp number</label>
                <input id="cb-phone" name="phone" type="tel" class="input @error('phone', 'callback') is-invalid @enderror" autocomplete="tel" placeholder="+91 98XXX XXXXX" value="{{ old('phone') }}" required>
            </div>
            <div class="field">
                <label for="cb-interest">I'm interested in</label>
                <select id="cb-interest" name="interest" class="input">
                    @foreach (config('site.counselling.interests') as $value => $label)
                        <option value="{{ $value }}" @selected(old('interest') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-submit">{{ $home['callback_button'] }}</button>

            @if (session('callback_sent'))
                <p class="alert alert--success callback__note" role="status"><x-icon name="check" size="18" stroke="2.6" />Thanks! A counsellor will call you back within 24 hours.</p>
            @elseif ($errors->callback->any())
                <div class="alert alert--error callback__note" role="alert">
                    <ul>
                        @foreach ($errors->callback->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="callback__direct">
                <x-icon name="chat" size="16" />
                <span>Or WhatsApp us directly:</span>
                <span class="callback__numbers">
                    @foreach ($site['whatsapp_numbers'] ?? [] as $number)
                        <a href="https://wa.me/{{ $number['wa'] }}" target="_blank" rel="noopener">{{ $number['display'] }}</a>@if (! $loop->last)<span class="callback__sep" aria-hidden="true">/</span>@endif
                    @endforeach
                </span>
            </div>
        </form>
    </div>
</section>

{{-- Crossed marquee bands --}}
<section class="bands" aria-hidden="true">
    @php
        $marqueeServices = $home['marquee_services'] ?? [];
        $marqueeCountries = $home['marquee_countries'] ?? [];
    @endphp
    <div class="band band--navy">
        <div class="band__track">
            @foreach ([1, 2] as $copy)
                @foreach ($marqueeServices as $item)
                    <span>{{ is_array($item) ? ($item['label'] ?? '') : $item }}</span><span>✦</span>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="band band--accent">
        <div class="band__track">
            @foreach ([1, 2] as $copy)
                @foreach ($marqueeCountries as $item)
                    <span>{{ is_array($item) ? ($item['label'] ?? '') : $item }}</span><x-icon name="plane" size="24" stroke="2.2" />
                @endforeach
            @endforeach
        </div>
    </div>
</section>

{{-- Proof --}}
<section class="stats" aria-label="{{ $site['name'] }} in numbers">
    <div class="container grid-auto" style="--min: 230px; --gap: 32px">
        @foreach ($home['stats'] ?? [] as $stat)
            <div class="stat {{ ! empty($stat['accent']) ? 'stat--accent' : '' }} reveal {{ ['', 's2', 's3', 's3'][$loop->index % 4] }}">
                <span class="stat__value">{{ $stat['value'] ?? '' }}</span>
                <span class="stat__label">{{ $stat['label'] ?? '' }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- Programs --}}
<section id="programs" class="section programs">
    <div class="bg-layer bg-skyline" aria-hidden="true"></div>
    <div class="container stack" style="gap: 44px">
        <div class="section-head reveal">
            <div class="section-head__title">
                <span class="eyebrow">{{ $home['programs_eyebrow'] }}</span>
                <h2 class="display h-section">{{ $home['programs_title'] }}</h2>
            </div>
            <p class="lead">{{ $home['programs_lead'] }}</p>
        </div>

        <div class="grid-auto" style="--min: 460px; --gap: 22px">
            <article class="track track--navy reveal lift">
                <div class="track__top">
                    <span class="track__badge" style="background: var(--accent)">{{ $home['study_badge'] }}</span>
                    <span class="track__num" style="color: var(--pale-2)">01</span>
                </div>
                <h3>{{ $home['study_title'] }}</h3>
                <p>{{ $home['study_text'] }}</p>
                <ul class="ticks">
                    @foreach ($home['study_points'] ?? [] as $point)
                        <li><x-icon name="check" stroke="2.4" /><span>{{ is_array($point) ? ($point['text'] ?? '') : $point }}</span></li>
                    @endforeach
                </ul>
                <a href="{{ route('apply') }}" class="btn btn--accent">Check study eligibility <x-icon name="arrow" size="18" stroke="2.4" /></a>
            </article>

            <article class="track track--blue reveal s2 lift">
                <div class="track__top">
                    <span class="track__badge" style="background: #FFFFFF">{{ $home['internship_badge'] }}</span>
                    <span class="track__num" style="color: #DCE6FF">02</span>
                </div>
                <h3>{{ $home['internship_title'] }}</h3>
                <p>{{ $home['internship_text'] }}</p>
                <dl class="facts">
                    @foreach ($home['internship_facts'] ?? [] as $fact)
                        <div><dt>{{ $fact['label'] ?? '' }}</dt><dd>{{ $fact['value'] ?? '' }}</dd></div>
                    @endforeach
                </dl>
                <a href="{{ route('apply') }}" class="btn btn--white">Check internship eligibility <x-icon name="arrow" size="18" stroke="2.4" /></a>
            </article>
        </div>
    </div>
</section>

{{-- Services bento --}}
<section id="services" class="section" style="border-top: 1px solid var(--line)">
    <div class="bg-layer bg-stamps" aria-hidden="true"></div>
    <div class="container stack" style="gap: 44px">
        <div class="section-head reveal">
            <div class="section-head__title" style="max-width: 700px">
                <span class="eyebrow">{{ $home['services_eyebrow'] }}</span>
                <h2 class="display h-section">{{ $home['services_title'] }}</h2>
            </div>
            <p class="lead" style="max-width: 360px">{{ $home['services_lead'] }}</p>
        </div>

        <div class="grid-auto bento">
            <article class="card card--accent bento__feature reveal lift">
                <span class="icon-box icon-box--lg icon-box--navy"><x-icon name="user-check" size="28" stroke="1.9" /></span>
                <h3>Free profile assessment</h3>
                <p>We review your marks, experience, budget and goals to show you where you can realistically get in — before you spend a rupee on applications.</p>
                <a href="{{ route('apply') }}" class="btn btn--navy">Start my assessment <x-icon name="arrow" size="18" stroke="2.4" /></a>
                <span class="bento__ghost" aria-hidden="true">01</span>
            </article>
            <article class="card reveal s2 lift">
                <span class="icon-box"><x-icon name="search" size="26" stroke="1.9" /></span>
                <h3>University &amp; internship shortlist</h3>
                <p>An honest shortlist of programs that fit your profile, budget and the career you're aiming for.</p>
            </article>
            <article class="card card--blue reveal s3 lift">
                <span class="icon-box icon-box--glass"><x-icon name="cap" size="26" stroke="1.9" /></span>
                <h3>Scholarships &amp; education loans</h3>
                <p>We find scholarships you qualify for and help you compare education loan options.</p>
            </article>
            <article class="card reveal s2 lift">
                <span class="icon-box"><x-icon name="document" size="26" stroke="1.9" /></span>
                <h3>Applications, SOPs &amp; CVs</h3>
                <p>Forms, statements of purpose, CVs and recommendation letters — prepared well and submitted on time.</p>
            </article>
            <article class="card reveal s3 lift">
                <span class="icon-box"><x-icon name="passport" size="26" stroke="1.9" /></span>
                <h3>Student &amp; internship visas</h3>
                <p>Visa file preparation, financial documents and mock interviews, so you walk in confident.</p>
            </article>
            <article class="card bento__wide reveal lift">
                <svg class="bento__path" aria-hidden="true" width="620" height="140" viewBox="0 0 620 140" fill="none"><path d="M0 130 C 200 120, 380 30, 600 18" stroke="#DCE3EF" stroke-width="2" stroke-dasharray="6 10"/></svg>
                <div class="bento__wide-body">
                    <span class="icon-box icon-box--accent" style="width: 64px; height: 64px; border-radius: 18px"><x-icon name="plane" size="30" stroke="1.9" /></span>
                    <div class="stack" style="gap: 6px">
                        <h3>Pre-departure &amp; arrival</h3>
                        <p>Accommodation, forex, flights and a briefing on what to expect, so you land ready.</p>
                    </div>
                </div>
                <a href="{{ route('apply') }}" class="btn btn--outline" style="height: 52px; padding: 0 22px; font-size: 15px">Plan my journey <x-icon name="arrow" size="18" stroke="2.4" /></a>
            </article>
        </div>
    </div>
</section>

{{-- Destinations --}}
<section id="destinations" class="section destinations bg-map-light">
    <div class="container stack" style="gap: 44px">
        <div class="section-head reveal">
            <div class="section-head__title" style="max-width: 640px">
                <span class="eyebrow eyebrow--muted">{{ $home['destinations_eyebrow'] }}</span>
                <h2 class="display h-section">{{ $home['destinations_title'] }}</h2>
            </div>
            <p class="lead" style="color: var(--muted); font-weight: 400">{{ $home['destinations_lead'] }}</p>
        </div>

        <div class="grid-auto" style="--min: 320px; --gap: 22px">
            @foreach ($destinations as $pass)
                <article class="pass reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }}">
                    <div class="pass__head">
                        <span class="pass__meta"><span>BOARDING PASS</span><span>{{ $pass['route'] ?? '' }}</span></span>
                        <span class="pass__code">{{ $pass['code'] ?? '' }}</span>
                    </div>
                    <div class="pass__body">
                        <h3>{{ $pass['name'] ?? '' }}</h3>
                        <p>{{ $pass['blurb'] ?? '' }}</p>
                    </div>
                    <div class="pass__tear" aria-hidden="true"><span></span></div>
                    <a href="{{ route('apply', ['country' => $pass['code'] ?? '']) }}" class="pass__cta">{{ $pass['cta'] ?? 'Check eligibility' }} <x-icon name="arrow" size="18" stroke="2.4" /></a>
                </article>
            @endforeach
        </div>
        @if (! empty($home['destinations_note']))
            <p class="note-strip reveal"><strong>Interning abroad?</strong><span>{{ $home['destinations_note'] }}</span></p>
        @endif
    </div>
</section>

{{-- Process --}}
<section id="process" class="section">
    <div class="bg-layer bg-sky" aria-hidden="true"></div>
    <div class="container stack" style="gap: 48px">
        <div class="section-head__title reveal">
            <span class="eyebrow">{{ $home['steps_eyebrow'] }}</span>
            <h2 class="display h-section">{{ $home['steps_title'] }}</h2>
        </div>
        <ol class="grid-auto steps">
            @foreach ($steps as $step)
                <li class="card step-card reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }} {{ $loop->last ? 'card--accent' : '' }}">
                    <span class="step-card__num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $step['title'] ?? '' }}</h3>
                    <p>{{ $step['text'] ?? '' }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- Why us --}}
<section class="section bg-navy">
    <div class="container why">
        <div class="ticket ticket--tilt reveal" aria-hidden="true">
            <div class="ticket__bar"><span>BOARDING PASS</span><span style="letter-spacing: 0.1em">OC {{ date('Y') + 1 }}</span></div>
            <div class="ticket__body">
                <div class="ticket__route">
                    <span class="ticket__end"><span class="label-mono">FROM</span><span class="ticket__iata">IND</span><span class="ticket__place">Your hometown</span></span>
                    <x-icon name="plane" size="44" stroke="1.8" color="#1557B0" />
                    <span class="ticket__end ticket__end--right"><span class="label-mono">TO</span><span class="ticket__iata">UNI</span><span class="ticket__place">University or internship</span></span>
                </div>
                <div class="ticket__row" style="grid-template-columns: repeat(3, minmax(0, 1fr))">
                    <span><span class="label-mono">PASSENGER</span><span class="ticket__val">You</span></span>
                    <span><span class="label-mono">GATE</span><span class="ticket__val">Opportunity</span></span>
                    <span><span class="label-mono">STATUS</span><span class="ticket__val" style="color: var(--blue)">Confirmed</span></span>
                </div>
                <div class="ticket__foot">
                    <span class="barcode"></span>
                    <x-logo variant="mark" sizes="79px" style="height: 64px; width: auto; display: block" />
                </div>
            </div>
        </div>

        <div class="stack" style="gap: 36px">
            <div class="stack reveal" style="gap: 18px">
                <span class="eyebrow eyebrow--light">Why {{ $site['name'] }}</span>
                <h2 class="display" style="font-size: clamp(32px, 3.6vw, 48px); line-height: 1.06">{{ $home['why_title'] }}</h2>
            </div>
            <div class="grid-auto why__features">
                @foreach ($home['why_features'] ?? [] as $feature)
                    <div class="why__feature reveal {{ $loop->even ? 's2' : '' }}">
                        <x-icon :name="$feature['icon'] ?? 'star'" size="28" stroke="1.8" />
                        <h3>{{ $feature['title'] ?? '' }}</h3>
                        <p>{{ $feature['text'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('about') }}" class="link-arrow">Meet the team <x-icon name="arrow" size="18" stroke="2.4" /></a>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="section">
    <div class="bg-layer bg-sky" aria-hidden="true" style="transform: scaleX(-1)"></div>
    <div class="container stack" style="gap: 44px">
        <div class="section-head__title reveal">
            <span class="eyebrow">{{ $home['testimonials_eyebrow'] }}</span>
            <h2 class="display h-section">{{ $home['testimonials_title'] }}</h2>
        </div>
        <div class="grid-auto" style="--min: 300px">
            @foreach ($testimonials as $t)
                <figure class="card quote reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }}">
                    <blockquote>{{ $t['quote'] ?? '' }}</blockquote>
                    <figcaption>
                        @if (! empty($t['photo']))
                            <img src="{{ asset('storage/'.$t['photo']) }}" alt="{{ $t['name'] ?? '' }}" class="avatar avatar--photo" loading="lazy">
                        @else
                            <span class="avatar avatar--{{ $t['tone'] ?? 'accent' }}" aria-hidden="true">{{ $t['initials'] ?? 'ST' }}</span>
                        @endif
                        <span><strong>{{ $t['name'] ?? '' }}</strong><small>{{ $t['meta'] ?? '' }}</small></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section id="faq" class="section bg-mist">
    <div class="container faq">
        <div class="faq__intro reveal">
            <span class="eyebrow eyebrow--muted">FAQ</span>
            <h2 class="display h-section-sm">{{ $home['faq_title'] }}</h2>
            <p>{{ $home['faq_subtitle'] }}</p>
            <a href="https://wa.me/{{ $site['whatsapp'] }}" class="btn btn--outline" style="height: 52px; padding: 0 22px">
                <x-icon name="chat" size="20" />
                Ask on WhatsApp
            </a>
        </div>
        <div class="faq__list">
            @foreach ($faqs as $faq)
                <details class="reveal" @if ($loop->first) open @endif>
                    <summary>{{ $faq['q'] ?? '' }}<span class="faq__plus" aria-hidden="true"><x-icon name="plus" size="16" stroke="2.6" color="#0A2459" /></span></summary>
                    <p>{{ $faq['a'] ?? '' }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section class="section cta-band">
    <span class="cta-band__ghost" aria-hidden="true">FLY</span>
    <div class="container cta-grid">
        <div class="stack reveal" style="align-items: flex-start; gap: 22px">
            <h2 class="display" style="font-size: clamp(38px, 5vw, 64px); line-height: 1.02; letter-spacing: -0.025em">{{ $home['cta_title'] }}</h2>
            <p>{{ $home['cta_text'] }}</p>
            <div class="btn-row">
                <a href="{{ route('apply') }}" class="btn btn--lg btn--navy">Check my eligibility <x-icon name="arrow" size="20" stroke="2.4" /></a>
                <a href="https://wa.me/{{ $site['whatsapp'] }}" class="btn btn--lg btn--outline-2">Chat on WhatsApp</a>
            </div>
        </div>
        <address class="contact-card reveal s2">
            <div class="contact-card__row">
                <x-icon name="pin" size="22" stroke="1.8" />
                <span><strong>Visit our office</strong><span>{{ $site['address'] }}</span></span>
            </div>
            <div class="contact-card__row">
                <x-icon name="phone" size="22" stroke="1.8" />
                <span><strong>Call or WhatsApp</strong><a href="tel:{{ $site['phone'] }}">{{ $site['phone_display'] }}</a></span>
            </div>
            <div class="contact-card__row">
                <x-icon name="mail" size="22" stroke="1.8" />
                <span><strong>Email</strong><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></span>
            </div>
            <div class="contact-card__row">
                <x-icon name="clock" size="22" stroke="1.8" />
                <span><strong>Office hours</strong><span>{{ $site['hours'] }}</span></span>
            </div>
        </address>
    </div>
</section>

</main>

<x-site.footer />
</x-layout>

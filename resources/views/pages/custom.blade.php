@php
    $blocks = $page->blocks ?? [];
    $hasConversionBlock = collect($blocks)->contains(
        fn (array $block): bool => in_array($block['type'] ?? '', ['cta_banner', 'callback_form'], true)
    );
@endphp

<x-layout
    :title="($page->meta_title ?: $page->title).' — '.$site['name']"
    :description="$page->meta_description ?: $page->subtitle"
>
<x-site.header :active="'page:'.$page->slug" />

<main>
    @if (! $page->is_published && auth()->check())
        <div class="draft-banner" role="status">
            <div class="container draft-banner__inner">
                <span><strong>Draft preview</strong> — This page is not published yet and is only visible to logged-in admins.</span>
                <a href="{{ \App\Filament\Resources\PageResource::getUrl('edit', ['record' => $page]) }}" class="draft-banner__link">Edit page &rarr;</a>
            </div>
        </div>
    @endif

    {{-- Page Hero --}}
    <section class="hero custom-hero bg-hero">
        <div class="container hero__inner custom-hero__inner">
            <div class="hero__copy" style="gap: 20px">
                <nav class="breadcrumb rise" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">{{ $page->title }}</span>
                </nav>

                @if ($page->eyebrow)
                    <span class="pill rise d1"><span class="dot" aria-hidden="true"></span>{{ $page->eyebrow }}</span>
                @endif

                <h1 class="display h-hero rise d1">{{ $page->title }}</h1>

                @if ($page->subtitle)
                    <p class="hero__text rise d2">{{ $page->subtitle }}</p>
                @endif

                <div class="btn-row rise d3" style="gap: 14px">
                    <a href="{{ $page->hero_cta_url ?: route('apply') }}" class="btn btn--lg btn--accent btn--glow">
                        {{ $page->hero_cta_label ?: 'Check my eligibility' }}
                        <x-icon name="arrow" size="20" stroke="2.4" />
                    </a>
                    <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn--lg btn--outline-light">
                        <x-icon name="chat" size="20" />
                        Chat on WhatsApp
                    </a>
                </div>
            </div>

            {{-- Side quick-guidance card --}}
            <aside class="custom-hero__card rise d4" aria-label="Free counselling contact">
                <div class="custom-hero__card-head">
                    <span>FREE COUNSELLING</span>
                    <span class="custom-hero__card-badge">24h callback</span>
                </div>
                <p class="custom-hero__card-title">Need honest advice on {{ $page->title }}?</p>
                <p class="custom-hero__card-text">Speak directly with a counsellor — profile check, university shortlist, scholarships, and visa guidance.</p>

                <div class="custom-hero__card-contacts">
                    <div class="custom-hero__card-row">
                        <x-icon name="chat" size="18" />
                        <div>
                            <small>WhatsApp directly</small>
                            <span class="custom-hero__card-nums">
                                @foreach ($site['whatsapp_numbers'] ?? [] as $number)
                                    <a href="https://wa.me/{{ $number['wa'] }}" target="_blank" rel="noopener">{{ $number['display'] }}</a>@if (! $loop->last)<span> / </span>@endif
                                @endforeach
                            </span>
                        </div>
                    </div>
                    <div class="custom-hero__card-row">
                        <x-icon name="clock" size="18" />
                        <div>
                            <small>Office hours</small>
                            <span>{{ $site['hours'] }}</span>
                        </div>
                    </div>
                    <div class="custom-hero__card-row">
                        <x-icon name="pin" size="18" />
                        <div>
                            <small>Visit us</small>
                            <span>{{ $site['address_short'] }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('apply') }}" class="custom-hero__card-cta">
                    Book free eligibility call
                    <x-icon name="arrow" size="16" stroke="2.4" />
                </a>
            </aside>
        </div>
    </section>

    {{-- Dynamic Content Blocks --}}
    @foreach ($blocks as $block)
        @php
            $type = $block['type'] ?? '';
            $data = $block['data'] ?? [];
        @endphp

        @if ($type === 'rich_text')
            <section class="section cms-section">
                <div class="bg-layer bg-stamps" aria-hidden="true"></div>
                <div class="container">
                    <article class="cms-prose-card reveal">
                        @if (! empty($data['heading']))
                            <h2 class="display h-section-sm cms-prose-card__title">{{ $data['heading'] }}</h2>
                        @endif
                        <div class="cms-prose__body">{!! $data['content'] ?? '' !!}</div>
                    </article>
                </div>
            </section>
        @elseif ($type === 'feature_cards')
            <section class="section bg-mist cms-section">
                <div class="bg-layer bg-skyline" aria-hidden="true"></div>
                <div class="container stack" style="gap: 40px">
                    @if (! empty($data['heading']) || ! empty($data['eyebrow']))
                        <div class="section-head__title reveal">
                            @if (! empty($data['eyebrow']))
                                <span class="eyebrow eyebrow--muted">{{ $data['eyebrow'] }}</span>
                            @endif
                            @if (! empty($data['heading']))
                                <h2 class="display h-section-sm">{{ $data['heading'] }}</h2>
                            @endif
                        </div>
                    @endif
                    <div class="grid-auto" style="--min: 280px; --gap: 22px">
                        @foreach ($data['cards'] ?? [] as $card)
                            <article class="card cms-feature-card reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }}">
                                <span class="cms-feature-card__num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3>{{ $card['title'] ?? '' }}</h3>
                                <p>{{ $card['text'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @elseif ($type === 'stats_row')
            <section class="stats">
                <div class="container grid-auto" style="--min: 210px; --gap: 32px">
                    @foreach ($data['stats'] ?? [] as $stat)
                        <div class="stat {{ ! empty($stat['accent']) ? 'stat--accent' : '' }} reveal {{ ['', 's2', 's3', 's3'][$loop->index % 4] }}">
                            <span class="stat__value">{{ $stat['value'] ?? '' }}</span>
                            <span class="stat__label">{{ $stat['label'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @elseif ($type === 'faq')
            <section class="section bg-mist">
                <div class="container faq">
                    <div class="faq__intro reveal">
                        <span class="eyebrow eyebrow--muted">FAQ</span>
                        <h2 class="display h-section-sm">{{ $data['heading'] ?? 'Frequently asked questions' }}</h2>
                        <p>Have a specific question? Message our team directly on WhatsApp.</p>
                        <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn--outline" style="height: 52px; padding: 0 22px">
                            <x-icon name="chat" size="20" />
                            Ask on WhatsApp
                        </a>
                    </div>
                    <div class="faq__list">
                        @foreach ($data['items'] ?? [] as $faq)
                            <details class="reveal" @if ($loop->first) open @endif>
                                <summary>{{ $faq['question'] ?? '' }}<span class="faq__plus" aria-hidden="true"><x-icon name="plus" size="16" stroke="2.6" color="#0A2459" /></span></summary>
                                <p>{{ $faq['answer'] ?? '' }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>
        @elseif ($type === 'callback_form')
            <section id="callback" class="section bg-map-light cms-callback-section">
                <div class="container">
                    <form class="callback__card reveal" method="POST" action="{{ route('callback.store') }}" novalidate>
                        @csrf
                        <input type="hidden" name="page_slug" value="{{ $page->slug }}">
                        <div class="callback__intro">
                            <h2 class="display">{{ $data['heading'] ?? 'Talk to a counsellor, free' }}</h2>
                            <p>{{ $data['subtitle'] ?? 'We call back within 24 hours' }}</p>
                        </div>
                        <div class="field">
                            <label for="cb-name-{{ $loop->index }}">Full name</label>
                            <input id="cb-name-{{ $loop->index }}" name="name" type="text" class="input @error('name', 'callback') is-invalid @enderror" autocomplete="name" placeholder="e.g. Priya Sharma" value="{{ old('name') }}" required>
                        </div>
                        <div class="field">
                            <label for="cb-phone-{{ $loop->index }}">WhatsApp number</label>
                            <input id="cb-phone-{{ $loop->index }}" name="phone" type="tel" class="input @error('phone', 'callback') is-invalid @enderror" autocomplete="tel" placeholder="+91 98XXX XXXXX" value="{{ old('phone') }}" required>
                        </div>
                        <div class="field">
                            <label for="cb-interest-{{ $loop->index }}">I'm interested in</label>
                            <select id="cb-interest-{{ $loop->index }}" name="interest" class="input">
                                @foreach (config('site.counselling.interests') as $value => $label)
                                    <option value="{{ $value }}" @selected(old('interest') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-submit">{{ $data['button_label'] ?? 'Call me back' }}</button>

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
        @elseif ($type === 'cta_banner')
            <section class="section cta-band" style="padding-block: clamp(56px, 7vw, 88px)">
                <span class="cta-band__ghost" aria-hidden="true" style="font-size: 240px; bottom: -70px">FLY</span>
                <div class="container cta-strip reveal">
                    <h2 class="display">{{ $data['heading'] ?? 'Ready to take the next step?' }}</h2>
                    <div class="btn-row">
                        <a href="{{ $data['button_url'] ?? route('apply') }}" class="btn btn--lg btn--navy">{{ $data['button_label'] ?? 'Check my eligibility' }} <x-icon name="arrow" size="20" stroke="2.4" /></a>
                        <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn--lg btn--outline-2">Chat on WhatsApp</a>
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    @if (! $hasConversionBlock)
        <section class="section cta-band" style="padding-block: clamp(56px, 7vw, 84px)">
            <span class="cta-band__ghost" aria-hidden="true" style="font-size: 240px; bottom: -70px">FLY</span>
            <div class="container cta-strip reveal">
                <h2 class="display">Your boarding pass starts with one free call.</h2>
                <div class="btn-row">
                    <a href="{{ route('apply') }}" class="btn btn--lg btn--navy">Check my eligibility <x-icon name="arrow" size="20" stroke="2.4" /></a>
                    <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn--lg btn--outline-2">Chat on WhatsApp</a>
                </div>
            </div>
        </section>
    @endif
</main>

<x-site.footer />
</x-layout>

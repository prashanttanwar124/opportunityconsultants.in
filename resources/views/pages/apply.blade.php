@php
    $selected = [
        'level' => old('level'),
        'country' => old('country', strtoupper((string) request('country')) ?: null),
        'intake' => old('intake'),
    ];
    if ($selected['country'] && ! isset($countries[$selected['country']])) {
        $selected['country'] = null;
    }

    // Which step to open first: the first unanswered one, or the contact step after a failed submit.
    $startStep = match (true) {
        $errors->hasAny(['name', 'phone', 'email', 'city', 'consent']) => 4,
        ! $selected['level'] => 1,
        ! $selected['country'] => 2,
        ! $selected['intake'] => 3,
        default => 4,
    };

    $level = $lead ? $levels[$lead->level] : ($selected['level'] ? $levels[$selected['level']] : null);
    $country = $lead ? $countries[$lead->country] : ($selected['country'] ? $countries[$selected['country']] : null);
    $intake = $lead ? $intakes[$lead->intake] : ($selected['intake'] ? $intakes[$selected['intake']] : null);
    $isUnsureCountry = ($lead?->country ?? $selected['country']) === 'XX';
@endphp

<x-layout :title="'Free counselling — '.config('site.name')">
<div class="apply-page">
    <div class="bg-layer bg-sky" aria-hidden="true"></div>

    <header class="apply-header">
        <div class="container">
            <a href="{{ route('home') }}" class="brand" aria-label="{{ config('site.name') }} home">
                <x-logo variant="mark" class="brand__mark hide-sm" />
                <x-logo variant="word" class="brand__word" :alt="config('site.name').' — Study Abroad Consultant'" />
            </a>
            <div class="apply-header__links">
                <a href="tel:{{ config('site.phone') }}" class="plain hide-sm"><x-icon name="phone" size="18" />{{ config('site.phone_display') }}</a>
                <a href="{{ route('home') }}" style="text-underline-offset: 5px">Back to home</a>
            </div>
        </div>
    </header>

    <main class="apply-main">

        {{-- Live boarding pass --}}
        <aside class="apply-aside rise" aria-label="Your counselling booking summary">
            <div class="apply-aside__intro">
                <span class="kicker">FREE STUDY ABROAD &amp; INTERNSHIP COUNSELLING</span>
                <h1 class="display">Check in for your <span class="text-accent">free counselling call.</span></h1>
                <p>Four quick questions. A counsellor calls you back with study and internship options that fit your profile.</p>
            </div>

            <div class="ticket">
                <div class="ticket__bar"><span>BOARDING PASS</span><span style="letter-spacing: 0.12em" data-pass="status">{{ $lead ? 'CHECKED IN' : 'BOARDING' }}</span></div>
                <div class="ticket__body">
                    <div class="ticket__route">
                        <span class="ticket__end"><span class="label-mono">FROM</span><span class="ticket__iata">IND</span><span class="ticket__place" data-pass="city">{{ $lead?->city ?: (old('city') ?: 'Your city') }}</span></span>
                        <x-icon name="plane" size="36" stroke="1.8" color="#1557B0" />
                        <span class="ticket__end ticket__end--right"><span class="label-mono">TO</span><span class="ticket__iata" data-pass="code">{{ $country['code'] ?? '—' }}</span><span class="ticket__place" data-pass="country">{{ $country ? ($isUnsureCountry ? 'Help me choose' : $country['label']) : 'Destination' }}</span></span>
                    </div>
                    <div class="ticket__row" style="gap: 14px; padding-top: 18px">
                        <span><span class="label-mono">PASSENGER</span><span class="ticket__val" data-pass="name">{{ $lead?->name ?: (old('name') ?: 'Your name') }}</span></span>
                        <span><span class="label-mono">PROGRAM</span><span class="ticket__val" data-pass="level">{{ $level['short'] ?? '—' }}</span></span>
                        <span><span class="label-mono">DEPARTS</span><span class="ticket__val" data-pass="intake">{{ $intake['short'] ?? '—' }}</span></span>
                    </div>
                    <div class="ticket__foot" style="gap: 16px">
                        <span class="barcode" aria-hidden="true"></span>
                        <x-logo variant="mark" sizes="59px" style="height: 48px; width: auto; display: block" />
                    </div>
                </div>
            </div>

            <ul class="check-list">
                <li><x-icon name="check" stroke="2.4" /><span>A counsellor calls you within 24 hours</span></li>
                <li><x-icon name="check" stroke="2.4" /><span>Free profile assessment and personal shortlist</span></li>
                <li><x-icon name="check" stroke="2.4" /><span>No obligation — parents welcome on the call</span></li>
            </ul>
        </aside>

        {{-- Form card --}}
        <section class="apply-card rise d2" aria-live="polite">
            @if ($lead)
                <div class="done">
                    <span class="done__badge" aria-hidden="true"><x-icon name="check" size="36" stroke="2.6" color="#0A2459" /></span>
                    <h2 class="display">You're checked in, {{ \Illuminate\Support\Str::of($lead->name)->trim()->explode(' ')->first() }}!</h2>
                    <p>A counsellor will call or WhatsApp you on <strong>+91 {{ $lead->phone }}</strong> within 24 hours to talk through {{ $level['phrase'] }} options in {{ $isUnsureCountry ? 'the countries that suit you best' : $country['label'] }}.</p>
                    <div class="done__tip">
                        <strong>Before the call, keep these handy</strong>
                        <span>Your latest marksheets, any English test scores, your CV and a rough budget.</span>
                    </div>
                    <div class="btn-row">
                        <a href="https://wa.me/{{ config('site.whatsapp') }}" class="btn btn--navy"><x-icon name="chat" size="20" />Chat on WhatsApp now</a>
                        <a href="{{ route('home') }}" class="btn btn--outline" style="padding: 0 22px">Back to home</a>
                    </div>
                    <a href="{{ route('apply') }}" class="done__restart">Start over</a>
                </div>
            @else
                <div class="apply-progress" data-progress hidden>
                    <div class="apply-progress__top">
                        <span class="apply-progress__label">STEP <span data-step-number>{{ $startStep }}</span> OF 4</span>
                        <button type="button" class="apply-back" data-back hidden><x-icon name="arrow-left" size="18" stroke="2.4" />Back</button>
                    </div>
                    <div class="segments" aria-hidden="true">
                        @for ($i = 1; $i <= 4; $i++)
                            <span data-segment="{{ $i }}"></span>
                        @endfor
                    </div>
                </div>

                <form method="POST" action="{{ route('apply.store') }}" id="apply-form" data-start-step="{{ $startStep }}" novalidate>
                    @csrf

                    @error('level') <p class="alert alert--error" role="alert" style="margin-bottom: 20px">{{ $message }}</p> @enderror
                    @error('country') <p class="alert alert--error" role="alert" style="margin-bottom: 20px">{{ $message }}</p> @enderror
                    @error('intake') <p class="alert alert--error" role="alert" style="margin-bottom: 20px">{{ $message }}</p> @enderror

                    {{-- Step 1 --}}
                    <fieldset class="apply-step" data-step="1">
                        <legend class="display">What are you looking for?</legend>
                        <div class="options">
                            @foreach ($levels as $id => $option)
                                <label class="option">
                                    <input type="radio" name="level" value="{{ $id }}" data-short="{{ $option['short'] }}" @checked($selected['level'] === $id) required>
                                    <span class="option__box">
                                        <span class="option__label">{{ $option['label'] }}</span>
                                        <span class="option__sub">{{ $option['sub'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- Step 2 --}}
                    <fieldset class="apply-step" data-step="2">
                        <legend class="display">Where would you like to go?</legend>
                        <div class="options options--countries">
                            @foreach ($countries as $id => $option)
                                <label class="option">
                                    <input type="radio" name="country" value="{{ $id }}" data-code="{{ $option['code'] }}" data-label="{{ $id === 'XX' ? 'Help me choose' : $option['label'] }}" @checked($selected['country'] === $id) required>
                                    <span class="option__box">
                                        <span class="option__code">{{ $option['code'] }}</span>
                                        <span class="option__label">{{ $option['label'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- Step 3 --}}
                    <fieldset class="apply-step" data-step="3">
                        <legend class="display">When do you want to start?</legend>
                        <div class="options">
                            @foreach ($intakes as $id => $option)
                                <label class="option">
                                    <input type="radio" name="intake" value="{{ $id }}" data-short="{{ $option['short'] }}" @checked($selected['intake'] === $id) required>
                                    <span class="option__box">
                                        <span class="option__label">{{ $option['label'] }}</span>
                                        <span class="option__sub">{{ $option['sub'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- Step 4 --}}
                    <fieldset class="apply-step" data-step="4">
                        <legend class="display">Where should your counsellor reach you?</legend>
                        <div class="contact-fields">
                            <div class="field">
                                <label for="ap-name">Full name</label>
                                <input id="ap-name" name="name" type="text" class="input @error('name') is-invalid @enderror" autocomplete="name" placeholder="e.g. Priya Sharma" value="{{ old('name') }}" data-pass-input="name" required>
                            </div>
                            <div class="field">
                                <label for="ap-phone">WhatsApp number</label>
                                <div class="input-group @error('phone') is-invalid @enderror">
                                    <span class="input-group__prefix">+91</span>
                                    <input id="ap-phone" name="phone" type="tel" inputmode="numeric" autocomplete="tel-national" placeholder="98XXX XXXXX" value="{{ old('phone') }}" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="ap-email">Email <span class="optional">(optional)</span></label>
                                <input id="ap-email" name="email" type="email" class="input @error('email') is-invalid @enderror" autocomplete="email" placeholder="you@example.com" value="{{ old('email') }}">
                            </div>
                            <div class="field">
                                <label for="ap-city">Your city</label>
                                <input id="ap-city" name="city" type="text" class="input" autocomplete="address-level2" placeholder="e.g. Mumbai" value="{{ old('city') }}" data-pass-input="city">
                            </div>
                        </div>

                        <label for="ap-consent" class="consent">
                            <input id="ap-consent" name="consent" type="checkbox" value="1" @checked(old('consent')) required>
                            <span>I agree to be contacted by {{ config('site.name') }} by phone, WhatsApp and email about studying or interning abroad.</span>
                        </label>

                        <p class="alert alert--error" role="alert" data-client-error hidden>Please add your name, a 10-digit WhatsApp number and tick the consent box.</p>
                        @if ($errors->hasAny(['name', 'phone', 'email', 'city', 'consent']))
                            <div class="alert alert--error" role="alert">
                                <ul>
                                    @foreach (['name', 'phone', 'email', 'city', 'consent'] as $field)
                                        @error($field) <li>{{ $message }}</li> @enderror
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <button type="submit" class="btn btn--accent btn-book">
                            Book my free counselling call
                            <x-icon name="arrow" size="20" stroke="2.4" />
                        </button>
                        <p class="privacy-note"><x-icon name="lock" size="16" />We never share your details. <a href="#">Privacy policy</a></p>
                    </fieldset>
                </form>
            @endif
        </section>
    </main>
</div>
</x-layout>

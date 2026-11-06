<x-layout>
<x-site.header />

<main>

{{-- Hero --}}
<section class="hero bg-hero">
    <div class="container hero__inner">
        <div class="hero__copy">
            <span class="pill rise"><span class="dot" aria-hidden="true"></span>Study abroad &amp; internship consultants · Free counselling</span>
            <h1 class="display h-hero rise d1">Your journey abroad is <span class="text-accent">now boarding.</span></h1>
            <p class="hero__text rise d2">Study at the right university or intern with an international company. We help you choose the country and program, then handle applications, funding and visas — right up to the day you fly.</p>
            <div class="btn-row rise d3" style="gap: 14px">
                <a href="{{ route('apply') }}" class="btn btn--lg btn--accent btn--glow">
                    Check my eligibility
                    <x-icon name="arrow" size="20" stroke="2.4" />
                </a>
                <a href="https://wa.me/{{ config('site.whatsapp') }}" class="btn btn--lg btn--outline-light">
                    <x-icon name="chat" size="20" />
                    Chat on WhatsApp
                </a>
            </div>
            <div class="social-proof rise d4">
                <span class="avatars" aria-hidden="true">
                    <span class="avatar avatar--accent">RD</span>
                    <span class="avatar avatar--blue">AK</span>
                    <span class="avatar avatar--white">SP</span>
                </span>
                <p><strong>2,500+ students</strong> counselled since 2016<br>4.9 ★ on Google from 380+ reviews</p>
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
                <h2 id="cb-title" class="display">Talk to a counsellor, free</h2>
                <p>We call back within <strong style="color: var(--navy)">24 hours</strong></p>
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
            <button type="submit" class="btn-submit">Call me back</button>

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
        </form>
    </div>
</section>

{{-- Crossed marquee bands --}}
<section class="bands" aria-hidden="true">
    @php
        $services = ['FREE COUNSELLING', 'INTERNSHIPS ABROAD', 'UNIVERSITY SHORTLIST', 'APPLICATIONS & SOP', 'SCHOLARSHIPS', 'EDUCATION LOANS', 'VISAS', 'PRE-DEPARTURE'];
        $countries = ['UNITED KINGDOM', 'USA', 'CANADA', 'AUSTRALIA', 'GERMANY', 'IRELAND', 'DUBAI', 'MAURITIUS'];
    @endphp
    <div class="band band--navy">
        <div class="band__track">
            @foreach ([1, 2] as $copy)
                @foreach ($services as $item)
                    <span>{{ $item }}</span><span>✦</span>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="band band--accent">
        <div class="band__track">
            @foreach ([1, 2] as $copy)
                @foreach ($countries as $item)
                    <span>{{ $item }}</span><x-icon name="plane" size="24" stroke="2.2" />
                @endforeach
            @endforeach
        </div>
    </div>
</section>

{{-- Proof --}}
<section class="stats" aria-label="{{ config('site.name') }} in numbers">
    <div class="container grid-auto" style="--min: 230px; --gap: 32px">
        <div class="stat reveal"><span class="stat__value">2,500+</span><span class="stat__label">Students counselled</span></div>
        <div class="stat reveal s2"><span class="stat__value">150+</span><span class="stat__label">Partner universities and companies</span></div>
        <div class="stat reveal s3"><span class="stat__value">96%</span><span class="stat__label">Student visa success rate</span></div>
        <div class="stat stat--accent reveal s3"><span class="stat__value">10</span><span class="stat__label">Countries for study and internships</span></div>
    </div>
</section>

{{-- Programs --}}
<section id="programs" class="section programs">
    <div class="bg-layer bg-skyline" aria-hidden="true"></div>
    <div class="container stack" style="gap: 44px">
        <div class="section-head reveal">
            <div class="section-head__title">
                <span class="eyebrow">Two ways to go global</span>
                <h2 class="display h-section">Study abroad. Intern abroad. Or both.</h2>
            </div>
            <p class="lead">Not sure which suits you? Your counsellor will help you weigh both on the free call.</p>
        </div>

        <div class="grid-auto" style="--min: 460px; --gap: 22px">
            <article class="track track--navy reveal lift">
                <div class="track__top">
                    <span class="track__badge" style="background: var(--accent)">For Class 12 students &amp; graduates</span>
                    <span class="track__num" style="color: var(--pale-2)">01</span>
                </div>
                <h3>Study Abroad</h3>
                <p>Bachelor's, master's, diploma and PhD programs at universities in the UK, USA, Canada, Australia, Germany and Ireland.</p>
                <ul class="ticks">
                    <li><x-icon name="check" stroke="2.4" /><span>Profile assessment &amp; shortlist</span></li>
                    <li><x-icon name="check" stroke="2.4" /><span>Applications, SOPs &amp; LORs</span></li>
                    <li><x-icon name="check" stroke="2.4" /><span>Scholarships &amp; education loans</span></li>
                    <li><x-icon name="check" stroke="2.4" /><span>Student visa &amp; pre-departure</span></li>
                </ul>
                <a href="{{ route('apply') }}" class="btn btn--accent">Check study eligibility <x-icon name="arrow" size="18" stroke="2.4" /></a>
            </article>

            <article class="track track--blue reveal s2 lift">
                <div class="track__top">
                    <span class="track__badge" style="background: #FFFFFF">For students &amp; recent graduates</span>
                    <span class="track__num" style="color: #DCE6FF">02</span>
                </div>
                <h3>Internships Abroad</h3>
                <p>International internships that give you real work experience — and a CV that stands out at home and abroad.</p>
                <dl class="facts">
                    <div><dt>INDUSTRIES</dt><dd>Hospitality, Business &amp; IT</dd></div>
                    <div><dt>DURATION</dt><dd>3–12 months</dd></div>
                    <div><dt>STIPEND</dt><dd>Paid, up to US$1,500 / month</dd></div>
                    <div><dt>DESTINATIONS</dt><dd>Dubai, Mauritius, France, USA</dd></div>
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
                <span class="eyebrow">What we do</span>
                <h2 class="display h-section">Everything between “I want to go abroad” and landing there.</h2>
            </div>
            <p class="lead" style="max-width: 360px">One counsellor guides you through every step, so you and your parents always know what comes next.</p>
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
                <span class="eyebrow eyebrow--muted">Destinations</span>
                <h2 class="display h-section">Pick your destination. We'll plan the route.</h2>
            </div>
            <p class="lead" style="color: var(--muted); font-weight: 400">Not sure which country suits you? That's exactly what the free counselling call is for.</p>
        </div>

        <div class="grid-auto" style="--min: 320px; --gap: 22px">
            @foreach ($destinations as $pass)
                <article class="pass reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }}">
                    <div class="pass__head">
                        <span class="pass__meta"><span>BOARDING PASS</span><span>{{ $pass['route'] }}</span></span>
                        <span class="pass__code">{{ $pass['code'] }}</span>
                    </div>
                    <div class="pass__body">
                        <h3>{{ $pass['name'] }}</h3>
                        <p>{{ $pass['blurb'] }}</p>
                    </div>
                    <div class="pass__tear" aria-hidden="true"><span></span></div>
                    <a href="{{ route('apply', ['country' => $pass['code']]) }}" class="pass__cta">{{ $pass['cta'] }} <x-icon name="arrow" size="18" stroke="2.4" /></a>
                </article>
            @endforeach
        </div>
        <p class="note-strip reveal"><strong>Interning abroad?</strong><span>Internship placements are also available in Dubai, Mauritius, France and the Maldives.</span></p>
    </div>
</section>

{{-- Process --}}
<section id="process" class="section">
    <div class="bg-layer bg-sky" aria-hidden="true"></div>
    <div class="container stack" style="gap: 48px">
        <div class="section-head__title reveal">
            <span class="eyebrow">How it works</span>
            <h2 class="display h-section">From first call to first day abroad.</h2>
        </div>
        <ol class="grid-auto steps">
            @foreach ($steps as $step)
                <li class="card step-card reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }} {{ $loop->last ? 'card--accent' : '' }}">
                    <span class="step-card__num" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] }}</p>
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
                <span class="eyebrow eyebrow--light">Why {{ config('site.name') }}</span>
                <h2 class="display" style="font-size: clamp(32px, 3.6vw, 48px); line-height: 1.06">Honest advice first. Applications second.</h2>
            </div>
            <div class="grid-auto why__features">
                <div class="why__feature reveal"><x-icon name="chat" size="28" stroke="1.8" /><h3>Counselling is free</h3><p>Ask as many questions as you need before you decide anything.</p></div>
                <div class="why__feature reveal s2"><x-icon name="route" size="28" stroke="1.8" /><h3>One counsellor, start to finish</h3><p>The person who shortlists your options is the one who files your visa.</p></div>
                <div class="why__feature reveal"><x-icon name="star" size="28" stroke="1.8" /><h3>Shortlists that fit you</h3><p>Universities and internships matched to your profile and budget — explained, not just listed.</p></div>
                <div class="why__feature reveal s2"><x-icon name="users" size="28" stroke="1.8" /><h3>Parents in the loop</h3><p>Family sessions and regular updates, so everyone knows what's happening.</p></div>
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
            <span class="eyebrow">Student stories</span>
            <h2 class="display h-section">Hear it from students who've already flown.</h2>
        </div>
        <div class="grid-auto" style="--min: 300px">
            @foreach ($testimonials as $t)
                <figure class="card quote reveal lift {{ ['', 's2', 's3'][$loop->index % 3] }}">
                    <blockquote>{{ $t['quote'] }}</blockquote>
                    <figcaption>
                        <span class="avatar avatar--{{ $t['tone'] }}" aria-hidden="true">{{ $t['initials'] }}</span>
                        <span><strong>{{ $t['name'] }}</strong><small>{{ $t['meta'] }}</small></span>
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
            <h2 class="display h-section-sm">What students and parents ask us.</h2>
            <p>Question not here? Message us — a counsellor replies on WhatsApp.</p>
            <a href="https://wa.me/{{ config('site.whatsapp') }}" class="btn btn--outline" style="height: 52px; padding: 0 22px">
                <x-icon name="chat" size="20" />
                Ask on WhatsApp
            </a>
        </div>
        <div class="faq__list">
            @foreach ($faqs as $faq)
                <details class="reveal" @if ($loop->first) open @endif>
                    <summary>{{ $faq['q'] }}<span class="faq__plus" aria-hidden="true"><x-icon name="plus" size="16" stroke="2.6" color="#0A2459" /></span></summary>
                    <p>{{ $faq['a'] }}</p>
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
            <h2 class="display" style="font-size: clamp(38px, 5vw, 64px); line-height: 1.02; letter-spacing: -0.025em">Your boarding pass starts with one free call.</h2>
            <p>Answer four quick questions and a counsellor will call you with the study and internship options that fit your profile.</p>
            <div class="btn-row">
                <a href="{{ route('apply') }}" class="btn btn--lg btn--navy">Check my eligibility <x-icon name="arrow" size="20" stroke="2.4" /></a>
                <a href="https://wa.me/{{ config('site.whatsapp') }}" class="btn btn--lg btn--outline-2">Chat on WhatsApp</a>
            </div>
        </div>
        <address class="contact-card reveal s2">
            <div class="contact-card__row">
                <x-icon name="pin" size="22" stroke="1.8" />
                <span><strong>Visit our office</strong><span>{{ config('site.address') }}</span></span>
            </div>
            <div class="contact-card__row">
                <x-icon name="phone" size="22" stroke="1.8" />
                <span><strong>Call or WhatsApp</strong><a href="tel:{{ config('site.phone') }}">{{ config('site.phone_display') }}</a></span>
            </div>
            <div class="contact-card__row">
                <x-icon name="mail" size="22" stroke="1.8" />
                <span><strong>Email</strong><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></span>
            </div>
            <div class="contact-card__row">
                <x-icon name="clock" size="22" stroke="1.8" />
                <span><strong>Office hours</strong><span>{{ config('site.hours') }}</span></span>
            </div>
        </address>
    </div>
</section>

</main>

<x-site.footer />
</x-layout>

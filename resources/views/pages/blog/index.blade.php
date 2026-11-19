<x-layout
    :title="'Blog & Study Guides — '.$site['name']"
    :description="'Guides on studying abroad, international internships, scholarships, IELTS and student visas from '.$site['name'].'.'"
>
<x-site.header active="blog" />

<main>
    <section class="hero about-hero bg-hero">
        <div class="container hero__inner" style="grid-template-columns: 1fr">
            <div class="hero__copy" style="gap: 18px; max-width: 720px">
                <span class="eyebrow eyebrow--light rise">Blog &amp; Study Guides</span>
                <h1 class="display h-hero rise d1">Practical guides for your <span class="text-accent">journey abroad.</span></h1>
                <p class="hero__text rise d2">Deadlines, scholarship tips, country comparisons, and paid internship insights — written by our counsellors.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container stack" style="gap: 36px">
            <div class="blog-cats" role="navigation" aria-label="Blog categories">
                <a href="{{ route('blog.index') }}" class="blog-cat {{ $activeCategory === '' ? 'is-active' : '' }}">All</a>
                @foreach ($categories as $cat)
                    <a href="{{ route('blog.index', ['category' => $cat]) }}" class="blog-cat {{ $activeCategory === $cat ? 'is-active' : '' }}">{{ $cat }}</a>
                @endforeach
            </div>

            @if ($posts->isEmpty())
                <div class="blog-empty">
                    <span class="icon-box icon-box--lg"><x-icon name="document" size="28" stroke="1.8" /></span>
                    <h2 class="display">Guides coming soon</h2>
                    <p>Our counsellors are preparing new study abroad and internship articles. Meanwhile, feel free to reach out for personalised advice.</p>
                    <div class="btn-row" style="justify-content: center">
                        <a href="{{ route('apply') }}" class="btn btn--accent">Check my eligibility <x-icon name="arrow" size="18" stroke="2.4" /></a>
                        <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn--outline">Chat on WhatsApp</a>
                    </div>
                </div>
            @else
                <div class="grid-auto" style="--min: 310px; --gap: 24px">
                    @foreach ($posts as $post)
                        <article class="blog-card lift">
                            @if ($post->cover_image)
                                <a href="{{ route('blog.show', $post) }}" class="blog-card__media">
                                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" loading="lazy">
                                </a>
                            @endif
                            <div class="blog-card__body">
                                <div class="blog-card__meta">
                                    @if ($post->category)
                                        <span class="blog-card__cat">{{ $post->category }}</span>
                                    @endif
                                    <span>{{ ($post->published_at ?? $post->created_at)->format('d M Y') }} · {{ $post->readingMinutes() }} min read</span>
                                </div>
                                <h3><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                                @if ($post->excerpt)
                                    <p>{{ $post->excerpt }}</p>
                                @endif
                                <a href="{{ route('blog.show', $post) }}" class="blog-card__more">Read guide <x-icon name="arrow" size="16" stroke="2.4" /></a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($posts->hasPages())
                    <div class="blog-pagination">
                        @if ($posts->onFirstPage())
                            <span class="btn btn--sm btn--outline" style="opacity: 0.45; pointer-events: none">Previous</span>
                        @else
                            <a href="{{ $posts->previousPageUrl() }}" class="btn btn--sm btn--outline">Previous</a>
                        @endif
                        <span class="blog-pagination__info">Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
                        @if ($posts->hasMorePages())
                            <a href="{{ $posts->nextPageUrl() }}" class="btn btn--sm btn--navy">Next</a>
                        @else
                            <span class="btn btn--sm btn--outline" style="opacity: 0.45; pointer-events: none">Next</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>

    <section class="section cta-band" style="padding-block: clamp(56px, 7vw, 84px)">
        <span class="cta-band__ghost" aria-hidden="true" style="font-size: 240px; bottom: -70px">FLY</span>
        <div class="container cta-strip reveal">
            <h2 class="display">Want personalised advice for your profile?</h2>
            <div class="btn-row">
                <a href="{{ route('apply') }}" class="btn btn--lg btn--navy">Check my eligibility <x-icon name="arrow" size="20" stroke="2.4" /></a>
                <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn--lg btn--outline-2">Chat on WhatsApp</a>
            </div>
        </div>
    </section>
</main>

<x-site.footer />
</x-layout>

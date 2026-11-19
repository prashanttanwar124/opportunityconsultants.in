<x-layout
    :title="($post->meta_title ?: $post->title).' — '.$site['name']"
    :description="$post->meta_description ?: $post->excerpt"
>
<x-site.header active="blog" />

<main>
    @if (! $post->is_published && auth()->check())
        <div class="draft-banner" role="status">
            <div class="container draft-banner__inner">
                <span><strong>Draft preview</strong> — This article is not published yet.</span>
                <a href="{{ \App\Filament\Resources\PostResource::getUrl('edit', ['record' => $post]) }}" class="draft-banner__link">Edit article &rarr;</a>
            </div>
        </div>
    @endif

    <section class="hero custom-hero bg-hero">
        <div class="container" style="max-width: 860px">
            <div class="hero__copy" style="gap: 18px">
                <nav class="breadcrumb rise" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('blog.index') }}">Blog</a>
                    @if ($post->category)
                        <span aria-hidden="true">/</span>
                        <span>{{ $post->category }}</span>
                    @endif
                </nav>
                <h1 class="display h-hero rise d1" style="font-size: clamp(32px, 4.5vw, 52px)">{{ $post->title }}</h1>
                <p class="blog-post__meta rise d2">
                    <span>{{ ($post->published_at ?? $post->created_at)->format('d M Y') }}</span>
                    <span>·</span>
                    <span>{{ $post->readingMinutes() }} min read</span>
                    @if ($post->author)
                        <span>·</span>
                        <span>By {{ $post->author->name }}</span>
                    @endif
                </p>
            </div>
        </div>
    </section>

    <section class="section cms-section">
        <div class="bg-layer bg-stamps" aria-hidden="true"></div>
        <div class="container">
            <article class="cms-prose-card">
                @if ($post->cover_image)
                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="blog-post__cover">
                @endif
                <div class="cms-prose__body">{!! $post->content !!}</div>
            </article>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="section bg-mist">
            <div class="container stack" style="gap: 32px">
                <h2 class="display h-section-sm">More study guides</h2>
                <div class="grid-auto" style="--min: 280px; --gap: 22px">
                    @foreach ($related as $item)
                        <article class="blog-card lift">
                            <div class="blog-card__body">
                                <div class="blog-card__meta">
                                    @if ($item->category)
                                        <span class="blog-card__cat">{{ $item->category }}</span>
                                    @endif
                                    <span>{{ ($item->published_at ?? $item->created_at)->format('d M Y') }}</span>
                                </div>
                                <h3><a href="{{ route('blog.show', $item) }}">{{ $item->title }}</a></h3>
                                <a href="{{ route('blog.show', $item) }}" class="blog-card__more">Read guide <x-icon name="arrow" size="16" stroke="2.4" /></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section cta-band" style="padding-block: clamp(56px, 7vw, 84px)">
        <span class="cta-band__ghost" aria-hidden="true" style="font-size: 240px; bottom: -70px">FLY</span>
        <div class="container cta-strip">
            <h2 class="display">Have questions about this guide? Talk to a counsellor.</h2>
            <div class="btn-row">
                <a href="{{ route('apply') }}" class="btn btn--lg btn--navy">Check my eligibility <x-icon name="arrow" size="20" stroke="2.4" /></a>
                <a href="https://wa.me/{{ $site['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn--lg btn--outline-2">Chat on WhatsApp</a>
            </div>
        </div>
    </section>
</main>

<x-site.footer />
</x-layout>

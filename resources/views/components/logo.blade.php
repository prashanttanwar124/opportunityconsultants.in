{{--
    Logo image with pre-resized 1x/2x/3x sources, so the browser never has to
    shrink the huge originals (which made the small text and stars blurry).
    `sizes` is the rendered CSS width of the image.
--}}
@props(['variant' => 'word', 'alt' => '', 'sizes' => null])

@php
    $sources = [
        'word' => ['file' => 'wordmark', 'widths' => [160, 320, 480], 'sizes' => '(max-width: 640px) 124px, 155px'],
        'mark' => ['file' => 'mark', 'widths' => [80, 160, 240], 'sizes' => '76px'],
        'full' => ['file' => 'full', 'widths' => [140, 280, 420], 'sizes' => '132px'],
    ][$variant];

    $srcset = collect($sources['widths'])
        ->map(fn ($w) => asset("images/logo/{$sources['file']}-{$w}.png")." {$w}w")
        ->implode(', ');
    $fallback = asset("images/logo/{$sources['file']}-{$sources['widths'][1]}.png");
@endphp

<img src="{{ $fallback }}" srcset="{{ $srcset }}" sizes="{{ $sizes ?? $sources['sizes'] }}" alt="{{ $alt }}" decoding="async" {{ $attributes }}>

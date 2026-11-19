<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\SiteContent;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * The home page.
     */
    public function home(): View
    {
        $home = SiteContent::group('home');
        $site = SiteContent::group('general');

        $departures = collect($home['departures'] ?? [])
            ->map(function (array $row): array {
                $dest = strtoupper((string) ($row['dest'] ?? ''));

                return $row + [
                    'tiles' => str_split(str_pad(substr($dest, 0, 9), 9)),
                ];
            });

        return view('pages.home', [
            'home' => $home,
            'site' => $site,
            'departures' => $departures,
            'destinations' => $home['destinations'] ?? [],
            'steps' => $home['steps'] ?? [],
            'testimonials' => $home['testimonials'] ?? [],
            'faqs' => $home['faqs'] ?? [],
        ]);
    }

    /**
     * The about us page.
     */
    public function about(): View
    {
        $about = SiteContent::group('about');
        $site = SiteContent::group('general');

        return view('pages.about', [
            'about' => $about,
            'site' => $site,
            'leaders' => $about['leaders'] ?? [],
            'team' => $about['team'] ?? [],
        ]);
    }

    /**
     * Render a custom CMS page created from the admin panel.
     */
    public function show(Page $page): View
    {
        abort_unless($page->is_published || auth()->check(), 404);

        return view('pages.custom', [
            'page' => $page,
            'site' => SiteContent::group('general'),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('category')->trim()->toString();

        $query = Post::published()->with('author')->latest('published_at')->latest('id');

        if ($category !== '') {
            $query->where('category', $category);
        }

        $posts = $query->paginate(9)->withQueryString();

        return view('pages.blog.index', [
            'posts' => $posts,
            'activeCategory' => $category,
            'categories' => Post::CATEGORIES,
            'site' => SiteContent::group('general'),
        ]);
    }

    public function show(Post $post): View
    {
        $isPublished = $post->is_published && ($post->published_at === null || $post->published_at->lte(now()));
        abort_unless($isPublished || auth()->check(), 404);

        $related = Post::published()
            ->whereKeyNot($post->getKey())
            ->when($post->category, fn ($q) => $q->where('category', $post->category))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('pages.blog.show', [
            'post' => $post,
            'related' => $related,
            'site' => SiteContent::group('general'),
        ]);
    }
}

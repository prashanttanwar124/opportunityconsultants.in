<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'slug',
    'eyebrow',
    'subtitle',
    'hero_cta_label',
    'hero_cta_url',
    'blocks',
    'meta_title',
    'meta_description',
    'is_published',
    'show_in_header',
    'show_in_footer',
    'nav_order',
])]
class Page extends Model
{
    use LogsActivity;

    /**
     * Slugs reserved by built-in routes and system endpoints.
     *
     * @var list<string>
     */
    public const RESERVED_SLUGS = [
        'about',
        'admin',
        'apply',
        'blog',
        'callback',
        'free-counselling',
        'livewire',
        'login',
        'logout',
        'register',
        'storage',
        'up',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'is_published' => 'boolean',
            'show_in_header' => 'boolean',
            'show_in_footer' => 'boolean',
            'nav_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopeForHeader(Builder $query): Builder
    {
        return $query->published()->where('show_in_header', true)->orderBy('nav_order')->orderBy('title');
    }

    /**
     * @param  Builder<Page>  $query
     * @return Builder<Page>
     */
    public function scopeForFooter(Builder $query): Builder
    {
        return $query->published()->where('show_in_footer', true)->orderBy('nav_order')->orderBy('title');
    }

    public function activityLabel(): string
    {
        return "Custom page \"{$this->title}\"";
    }
}

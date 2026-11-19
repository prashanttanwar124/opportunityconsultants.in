<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'slug',
    'category',
    'excerpt',
    'content',
    'cover_image',
    'author_id',
    'meta_title',
    'meta_description',
    'is_published',
    'published_at',
])]
class Post extends Model
{
    use LogsActivity;

    /**
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'Study Abroad' => 'Study Abroad',
        'Internships' => 'Internships',
        'Scholarships & Loans' => 'Scholarships & Loans',
        'Visas & IELTS' => 'Visas & IELTS',
        'Country Guides' => 'Country Guides',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function readingMinutes(): int
    {
        $words = str_word_count(strip_tags((string) $this->content));

        return max(1, (int) ceil($words / 200));
    }

    public function activityLabel(): string
    {
        return "Blog post \"{$this->title}\"";
    }
}

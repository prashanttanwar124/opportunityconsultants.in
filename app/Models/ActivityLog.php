<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Throwable;

#[Fillable(['user_id', 'action', 'subject_type', 'subject_id', 'description', 'properties', 'created_at'])]
class ActivityLog extends Model
{
    public $timestamps = false;

    /**
     * @var array<string, string>
     */
    public const ACTION_COLORS = [
        'created' => 'success',
        'updated' => 'info',
        'saved' => 'primary',
        'deleted' => 'danger',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public static function record(
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = [],
    ): void {
        try {
            if (! Schema::hasTable('activity_logs')) {
                return;
            }

            static::query()->create([
                'user_id' => auth()->id(),
                'action' => $action,
                'subject_type' => $subject ? class_basename($subject) : null,
                'subject_id' => $subject?->getKey(),
                'description' => $description,
                'properties' => $properties !== [] ? $properties : null,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Never interrupt user actions if logging fails.
        }
    }
}

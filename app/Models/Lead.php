<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Support\SiteContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'source',
    'page_slug',
    'name',
    'phone',
    'email',
    'city',
    'interest',
    'level',
    'country',
    'intake',
    'consent',
    'status',
    'notes',
    'assigned_to',
    'follow_up_at',
])]
class Lead extends Model
{
    use LogsActivity;

    public const SOURCE_CALLBACK = 'callback';

    public const SOURCE_COUNSELLING = 'counselling';

    public const SOURCE_PAGE = 'page';

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_CLOSED = 'closed';

    /**
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        self::STATUS_NEW => 'New',
        self::STATUS_CONTACTED => 'Contacted',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_CONVERTED => 'Converted',
        self::STATUS_CLOSED => 'Closed',
    ];

    /**
     * @var array<string, string>
     */
    public const STATUS_COLORS = [
        self::STATUS_NEW => 'info',
        self::STATUS_CONTACTED => 'warning',
        self::STATUS_IN_PROGRESS => 'primary',
        self::STATUS_CONVERTED => 'success',
        self::STATUS_CLOSED => 'gray',
    ];

    /**
     * @var array<string, string>
     */
    public const SOURCE_LABELS = [
        self::SOURCE_CALLBACK => 'Callback Bar',
        self::SOURCE_COUNSELLING => 'Free Counselling',
        self::SOURCE_PAGE => 'Custom Page',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'consent' => 'boolean',
            'follow_up_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function whatsappUrl(): string
    {
        $siteName = SiteContent::get('general.name', config('site.name'));

        return $this->whatsappUrlWithMessage(
            "Hi {$this->name}, thank you for contacting {$siteName}. How can we help you with your study or internship plans?"
        );
    }

    public function whatsappUrlWithMessage(string $message): string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone) ?? '';
        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        }

        return "https://wa.me/{$digits}?text=".rawurlencode($message);
    }

    public function isFollowUpDue(): bool
    {
        return $this->follow_up_at !== null
            && $this->follow_up_at->lte(now()->endOfDay())
            && ! in_array($this->status, [self::STATUS_CONVERTED, self::STATUS_CLOSED], true);
    }

    public function activityLabel(): string
    {
        return "Lead \"{$this->name}\"";
    }
}

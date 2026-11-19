<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Support\SiteContent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'body', 'is_active', 'sort_order'])]
class WhatsappTemplate extends Model
{
    use LogsActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<WhatsappTemplate>  $query
     * @return Builder<WhatsappTemplate>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function renderForLead(Lead $lead): string
    {
        $countries = config('site.counselling.countries', []);
        $countryName = $lead->country ? ($countries[$lead->country]['label'] ?? $lead->country) : 'your destination';
        $siteName = (string) SiteContent::get('general.name', config('site.name'));

        return strtr((string) $this->body, [
            '{name}' => $lead->name,
            '{country}' => $countryName,
            '{site_name}' => $siteName,
        ]);
    }

    public function whatsappUrlForLead(Lead $lead): string
    {
        return $lead->whatsappUrlWithMessage($this->renderForLead($lead));
    }

    public function activityLabel(): string
    {
        return "WhatsApp template \"{$this->name}\"";
    }
}

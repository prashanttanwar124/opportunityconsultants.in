<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadsStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Lead::query()->count();
        $newCount = Lead::query()->where('status', Lead::STATUS_NEW)->count();
        $thisWeek = Lead::query()->where('created_at', '>=', now()->subDays(7))->count();
        $converted = Lead::query()->where('status', Lead::STATUS_CONVERTED)->count();
        $publishedPages = Page::query()->where('is_published', true)->count();

        return [
            Stat::make('New Leads', $newCount)
                ->description('Waiting for first callback')
                ->descriptionIcon(Heroicon::OutlinedBellAlert)
                ->color($newCount > 0 ? 'warning' : 'success'),
            Stat::make('Total Leads', $total)
                ->description("{$thisWeek} in the last 7 days")
                ->descriptionIcon(Heroicon::OutlinedInboxStack)
                ->color('primary'),
            Stat::make('Converted', $converted)
                ->description('Students enrolled / placed')
                ->descriptionIcon(Heroicon::OutlinedCheckBadge)
                ->color('success'),
            Stat::make('Custom Pages', $publishedPages)
                ->description('Published CMS pages')
                ->descriptionIcon(Heroicon::OutlinedDocumentDuplicate)
                ->color('info'),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class LeadsTrendChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Leads (Last 14 Days)';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn (int $offset): Carbon => now()->subDays($offset)->startOfDay());

        $counts = Lead::query()
            ->where('created_at', '>=', $days->first())
            ->get(['created_at'])
            ->groupBy(fn (Lead $lead): string => $lead->created_at->format('Y-m-d'))
            ->map->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Leads',
                    'data' => $days->map(fn (Carbon $day): int => (int) ($counts[$day->format('Y-m-d')] ?? 0))->all(),
                    'borderColor' => '#1557B0',
                    'backgroundColor' => 'rgba(21, 87, 176, 0.14)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $days->map(fn (Carbon $day): string => $day->format('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

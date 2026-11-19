<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadsBySourceChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Leads by Source';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $counts = Lead::query()
            ->selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source');

        $sources = [
            Lead::SOURCE_CALLBACK => '#0A2459',
            Lead::SOURCE_COUNSELLING => '#1557B0',
            Lead::SOURCE_PAGE => '#F4B731',
        ];

        return [
            'datasets' => [
                [
                    'data' => collect($sources)->keys()->map(fn (string $key): int => (int) ($counts[$key] ?? 0))->all(),
                    'backgroundColor' => array_values($sources),
                ],
            ],
            'labels' => collect($sources)->keys()->map(fn (string $key): string => Lead::SOURCE_LABELS[$key] ?? $key)->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

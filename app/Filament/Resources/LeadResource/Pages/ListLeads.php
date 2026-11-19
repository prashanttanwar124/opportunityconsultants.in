<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Lead'),
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(function (): StreamedResponse {
                    $filename = 'leads-'.now()->format('Y-m-d-His').'.csv';

                    return response()->streamDownload(function (): void {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['ID', 'Date', 'Source', 'Page', 'Name', 'Phone', 'Email', 'City', 'Interest', 'Level', 'Country', 'Intake', 'Status', 'Assigned To', 'Notes']);

                        Lead::query()
                            ->with('assignee')
                            ->latest()
                            ->chunk(200, function ($leads) use ($out): void {
                                foreach ($leads as $lead) {
                                    fputcsv($out, [
                                        $lead->id,
                                        $lead->created_at?->format('Y-m-d H:i:s'),
                                        Lead::SOURCE_LABELS[$lead->source] ?? $lead->source,
                                        $lead->page_slug,
                                        $lead->name,
                                        $lead->phone,
                                        $lead->email,
                                        $lead->city,
                                        $lead->interest,
                                        $lead->level,
                                        $lead->country,
                                        $lead->intake,
                                        Lead::STATUS_LABELS[$lead->status] ?? $lead->status,
                                        $lead->assignee?->name,
                                        $lead->notes,
                                    ]);
                                }
                            });

                        fclose($out);
                    }, $filename, ['Content-Type' => 'text/csv']);
                }),
        ];
    }
}

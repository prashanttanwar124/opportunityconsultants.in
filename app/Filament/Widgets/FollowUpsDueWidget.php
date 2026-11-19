<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class FollowUpsDueWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Follow-ups Due Today & Overdue';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()
                    ->whereNotNull('follow_up_at')
                    ->where('follow_up_at', '<=', now()->endOfDay())
                    ->whereNotIn('status', [Lead::STATUS_CONVERTED, Lead::STATUS_CLOSED])
                    ->orderBy('follow_up_at')
            )
            ->emptyStateHeading('All caught up!')
            ->emptyStateDescription('No student follow-ups are overdue or due today.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle)
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->weight('bold'),
                TextColumn::make('phone')->label('WhatsApp')->copyable(),
                TextColumn::make('follow_up_at')
                    ->label('Due')
                    ->dateTime('d M, h:i A')
                    ->badge()
                    ->color(fn (Lead $record): string => $record->follow_up_at?->isPast() ? 'danger' : 'warning'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Lead::STATUS_LABELS[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => Lead::STATUS_COLORS[$state] ?? 'gray'),
                TextColumn::make('assignee.name')->label('Counsellor')->placeholder('Unassigned'),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (Lead $record): string => $record->whatsappUrl(), shouldOpenInNewTab: true),
                Action::make('clearFollowUp')
                    ->label('Done')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('primary')
                    ->action(function (Lead $record): void {
                        $record->update([
                            'follow_up_at' => null,
                            'status' => $record->status === Lead::STATUS_NEW ? Lead::STATUS_CONTACTED : $record->status,
                        ]);
                    }),
                Action::make('open')
                    ->label('Edit')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Lead $record): string => LeadResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}

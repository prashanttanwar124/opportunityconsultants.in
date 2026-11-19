<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLeadsWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Enquiries & Callback Requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->weight('bold'),
                TextColumn::make('phone')->label('WhatsApp')->copyable(),
                TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Lead::SOURCE_LABELS[$state] ?? ucfirst($state)),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Lead::STATUS_LABELS[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => Lead::STATUS_COLORS[$state] ?? 'gray'),
                TextColumn::make('created_at')->label('Received')->since(),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (Lead $record): string => $record->whatsappUrl(), shouldOpenInNewTab: true),
                Action::make('open')
                    ->label('Open')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->url(fn (Lead $record): string => LeadResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}

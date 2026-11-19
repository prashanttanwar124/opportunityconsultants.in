<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages\CreateLead;
use App\Filament\Resources\LeadResource\Pages\EditLead;
use App\Filament\Resources\LeadResource\Pages\ListLeads;
use App\Models\Lead;
use App\Models\User;
use App\Models\WhatsappTemplate;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static string | UnitEnum | null $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = 'Leads & Messages';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'email', 'city'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Phone' => $record->phone,
            'Status' => Lead::STATUS_LABELS[$record->status] ?? $record->status,
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Lead::query()->where('status', Lead::STATUS_NEW)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lead Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('phone')
                        ->label('WhatsApp / Phone')
                        ->required()
                        ->maxLength(20),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(160),
                    TextInput::make('city')
                        ->maxLength(120),
                    Select::make('source')
                        ->options(Lead::SOURCE_LABELS)
                        ->required(),
                    TextInput::make('page_slug')
                        ->label('Custom Page Slug')
                        ->placeholder('N/A'),
                    Select::make('interest')
                        ->options(config('site.counselling.interests', [])),
                    Select::make('level')
                        ->label('Study Level')
                        ->options(collect(config('site.counselling.levels', []))->map(fn (array $item): string => $item['label'])->all()),
                    Select::make('country')
                        ->options(collect(config('site.counselling.countries', []))->map(fn (array $item): string => $item['label'])->all()),
                    Select::make('intake')
                        ->options(collect(config('site.counselling.intakes', []))->map(fn (array $item): string => $item['label'])->all()),
                ]),

            Section::make('CRM Tracking')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options(Lead::STATUS_LABELS)
                        ->required()
                        ->default(Lead::STATUS_NEW),
                    Select::make('assigned_to')
                        ->label('Assigned Counsellor')
                        ->options(fn () => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->nullable(),
                    DateTimePicker::make('follow_up_at')
                        ->label('Next Follow-up Reminder')
                        ->seconds(false)
                        ->helperText('Leads due today or overdue appear on the Dashboard.'),
                    Textarea::make('notes')
                        ->label('Internal Counsellor Notes')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Call summary, shortlisted universities, follow-up date...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Lead::SOURCE_LABELS[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        Lead::SOURCE_COUNSELLING => 'primary',
                        Lead::SOURCE_PAGE => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('interest')
                    ->label('Interest / Country')
                    ->getStateUsing(function (Lead $record): string {
                        $interests = config('site.counselling.interests', []);
                        $countries = config('site.counselling.countries', []);

                        $parts = array_filter([
                            $record->interest ? ($interests[$record->interest] ?? $record->interest) : null,
                            $record->country ? ($countries[$record->country]['label'] ?? $record->country) : null,
                        ]);

                        return $parts !== [] ? implode(' · ', $parts) : '—';
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Lead::STATUS_LABELS[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => Lead::STATUS_COLORS[$state] ?? 'gray')
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assigned')
                    ->placeholder('Unassigned')
                    ->toggleable(),
                TextColumn::make('follow_up_at')
                    ->label('Follow-up')
                    ->dateTime('d M, h:i A')
                    ->badge()
                    ->color(fn (Lead $record): string => $record->isFollowUpDue() ? 'danger' : 'gray')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->tooltip(fn (Lead $record): string => $record->created_at?->format('d M Y, h:i A') ?? '')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('follow_up_due')
                    ->label('Follow-up Due / Overdue')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('follow_up_at')
                        ->where('follow_up_at', '<=', now()->endOfDay())
                        ->whereNotIn('status', [Lead::STATUS_CONVERTED, Lead::STATUS_CLOSED])),
                SelectFilter::make('status')
                    ->options(Lead::STATUS_LABELS),
                SelectFilter::make('source')
                    ->options(Lead::SOURCE_LABELS),
                SelectFilter::make('assigned_to')
                    ->label('Counsellor')
                    ->relationship('assignee', 'name'),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(fn (Lead $record): string => $record->whatsappUrl(), shouldOpenInNewTab: true),
                Action::make('sendTemplate')
                    ->label('Template')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('info')
                    ->modalHeading(fn (Lead $record): string => "Send WhatsApp Template to {$record->name}")
                    ->modalSubmitActionLabel('Open in WhatsApp')
                    ->schema([
                        Select::make('template_id')
                            ->label('Choose Template')
                            ->options(fn (): array => WhatsappTemplate::active()->pluck('name', 'id')->all())
                            ->live()
                            ->placeholder('Select a template or type custom message...')
                            ->afterStateUpdated(function (?string $state, Set $set, Lead $record): void {
                                if ($state && ($template = WhatsappTemplate::find($state))) {
                                    $set('message', $template->renderForLead($record));
                                }
                            }),
                        Textarea::make('message')
                            ->label('Message Preview')
                            ->required()
                            ->rows(4)
                            ->default(fn (Lead $record): string => "Hi {$record->name}, "),
                    ])
                    ->action(function (array $data, Lead $record, $livewire): void {
                        $url = $record->whatsappUrlWithMessage((string) $data['message']);
                        $livewire->js('window.open('.json_encode($url).', "_blank", "noopener")');
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markContacted')
                        ->label('Mark as Contacted')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->action(fn (Collection $records) => $records->each->update(['status' => Lead::STATUS_CONTACTED])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}

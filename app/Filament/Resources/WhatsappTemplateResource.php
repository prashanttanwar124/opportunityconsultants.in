<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsappTemplateResource\Pages\CreateWhatsappTemplate;
use App\Filament\Resources\WhatsappTemplateResource\Pages\EditWhatsappTemplate;
use App\Filament\Resources\WhatsappTemplateResource\Pages\ListWhatsappTemplates;
use App\Models\WhatsappTemplate;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class WhatsappTemplateResource extends Resource
{
    protected static ?string $model = WhatsappTemplate::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string | UnitEnum | null $navigationGroup = 'CRM';

    protected static ?string $navigationLabel = 'WhatsApp Templates';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('WhatsApp Quick-Reply Template')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(120)
                        ->placeholder('e.g. Initial Intro / Document Checklist'),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Textarea::make('body')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull()
                        ->placeholder('Hi {name}, thank you for contacting {site_name} about {country}...')
                        ->helperText('Available placeholders: {name}, {country}, {site_name} — auto-filled for each lead.'),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('body')
                    ->limit(70)
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWhatsappTemplates::route('/'),
            'create' => CreateWhatsappTemplate::route('/create'),
            'edit' => EditWhatsappTemplate::route('/{record}/edit'),
        ];
    }
}

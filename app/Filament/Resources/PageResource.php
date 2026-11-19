<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $recordRouteKeyName = 'id';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string | UnitEnum | null $navigationGroup = 'Website CMS';

    protected static ?string $navigationLabel = 'Custom Pages';

    protected static ?int $navigationSort = 3;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'eyebrow'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page Hero & Details')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, ?Page $record): void {
                            if ($record === null && filled($state)) {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(160)
                        ->prefix(url('/').'/')
                        ->unique(ignoreRecord: true)
                        ->rules([
                            'regex:/^[a-z0-9-]+$/',
                            Rule::notIn(Page::RESERVED_SLUGS),
                        ])
                        ->helperText('Lowercase letters, numbers, and hyphens only (e.g. study-in-uk).'),
                    TextInput::make('eyebrow')
                        ->maxLength(120)
                        ->placeholder('e.g. Study Abroad · United Kingdom'),
                    TextInput::make('hero_cta_label')
                        ->label('Hero Button Label')
                        ->maxLength(80)
                        ->placeholder('e.g. Check my eligibility'),
                    Textarea::make('subtitle')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Short intro shown in the hero banner.'),
                    TextInput::make('hero_cta_url')
                        ->label('Hero Button URL')
                        ->maxLength(255)
                        ->placeholder('/free-counselling (defaults to eligibility form)'),
                ]),

            Section::make('Page Content Blocks')
                ->description('Add, reorder, or remove sections on this page.')
                ->schema([
                    Builder::make('blocks')
                        ->label('')
                        ->collapsible()
                        ->cloneable()
                        ->blocks([
                            Block::make('rich_text')
                                ->label('Rich Text Section')
                                ->icon(Heroicon::OutlinedDocumentText)
                                ->schema([
                                    TextInput::make('heading')
                                        ->maxLength(200),
                                    RichEditor::make('content')
                                        ->required(),
                                ]),
                            Block::make('feature_cards')
                                ->label('Feature / Highlight Cards')
                                ->icon(Heroicon::OutlinedSquares2x2)
                                ->schema([
                                    TextInput::make('eyebrow')->maxLength(100),
                                    TextInput::make('heading')->maxLength(200),
                                    Repeater::make('cards')
                                        ->schema([
                                            TextInput::make('title')->required(),
                                            Textarea::make('text')->rows(2)->required(),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(3),
                                ]),
                            Block::make('stats_row')
                                ->label('Stats Row')
                                ->icon(Heroicon::OutlinedChartBar)
                                ->schema([
                                    Repeater::make('stats')
                                        ->schema([
                                            TextInput::make('value')->required()->placeholder('e.g. 25+'),
                                            TextInput::make('label')->required()->placeholder('e.g. Partner Universities'),
                                            Toggle::make('accent')->label('Gold highlight'),
                                        ])
                                        ->columns(3)
                                        ->defaultItems(3),
                                ]),
                            Block::make('faq')
                                ->label('FAQ Accordion')
                                ->icon(Heroicon::OutlinedQuestionMarkCircle)
                                ->schema([
                                    TextInput::make('heading')
                                        ->default('Frequently asked questions'),
                                    Repeater::make('items')
                                        ->schema([
                                            TextInput::make('question')->required(),
                                            Textarea::make('answer')->rows(2)->required(),
                                        ])
                                        ->defaultItems(2),
                                ]),
                            Block::make('callback_form')
                                ->label('Embedded Enquiry / Callback Form')
                                ->icon(Heroicon::OutlinedPhoneArrowUpRight)
                                ->schema([
                                    TextInput::make('heading')
                                        ->default('Talk to a counsellor, free'),
                                    TextInput::make('subtitle')
                                        ->default('We call back within 24 hours'),
                                    TextInput::make('button_label')
                                        ->default('Call me back'),
                                ]),
                            Block::make('cta_banner')
                                ->label('Call-to-Action Banner')
                                ->icon(Heroicon::OutlinedMegaphone)
                                ->schema([
                                    TextInput::make('heading')
                                        ->required()
                                        ->default('Your boarding pass starts with one free call.'),
                                    TextInput::make('button_label')
                                        ->default('Check my eligibility'),
                                    TextInput::make('button_url')
                                        ->default('/free-counselling'),
                                ]),
                        ]),
                ]),

            Section::make('Publishing, Navigation & SEO')
                ->columns(2)
                ->schema([
                    Toggle::make('is_published')
                        ->label('Published (visible to public)')
                        ->default(true),
                    TextInput::make('nav_order')
                        ->label('Navigation Order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('show_in_header')
                        ->label('Show link in Header navigation'),
                    Toggle::make('show_in_footer')
                        ->label('Show link in Footer navigation'),
                    TextInput::make('meta_title')
                        ->label('SEO Meta Title')
                        ->maxLength(255)
                        ->placeholder('Leave blank to use Page Title'),
                    Textarea::make('meta_description')
                        ->label('SEO Meta Description')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nav_order')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('slug')
                    ->formatStateUsing(fn (string $state): string => '/'.$state)
                    ->copyable()
                    ->color('gray'),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                IconColumn::make('show_in_header')
                    ->label('Header')
                    ->boolean(),
                IconColumn::make('show_in_footer')
                    ->label('Footer')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('View')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Page $record): string => route('pages.show', $record), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}

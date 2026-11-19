<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string | UnitEnum | null $navigationGroup = 'Website CMS';

    protected static ?string $navigationLabel = 'Blog & Guides';

    protected static ?int $navigationSort = 4;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'category', 'excerpt'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Category' => $record->category,
            'Status' => $record->is_published ? 'Published' : 'Draft',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Article Details')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, Set $set, ?Post $record): void {
                            if ($record === null && filled($state)) {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(160)
                        ->prefix(url('/blog').'/')
                        ->unique(ignoreRecord: true)
                        ->rules(['regex:/^[a-z0-9-]+$/']),
                    Select::make('category')
                        ->options(Post::CATEGORIES)
                        ->searchable(),
                    DateTimePicker::make('published_at')
                        ->label('Publish Date')
                        ->default(now()),
                    FileUpload::make('cover_image')
                        ->label('Cover Image')
                        ->disk('public')
                        ->directory('blog')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull(),
                    Textarea::make('excerpt')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Short summary shown on the blog listing card.'),
                    RichEditor::make('content')
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Publishing & SEO')
                ->columns(2)
                ->schema([
                    Toggle::make('is_published')
                        ->label('Published')
                        ->default(true),
                    TextInput::make('meta_title')
                        ->label('SEO Meta Title')
                        ->maxLength(255),
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->badge()
                    ->color('info')
                    ->placeholder('General'),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->options(Post::CATEGORIES),
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Post $record): string => route('blog.show', $record), shouldOpenInNewTab: true),
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Support\SiteContent;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageAboutContent extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static string | UnitEnum | null $navigationGroup = 'Website CMS';

    protected static ?string $navigationLabel = 'About Page';

    protected static ?string $title = 'Edit About Page Content';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.settings-page';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteContent::group('about'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('About Sections')
                    ->tabs([
                        Tab::make('Hero & Story')
                            ->schema([
                                TextInput::make('hero_title')->label('Hero Heading'),
                                TextInput::make('hero_title_accent')->label('Hero Heading (Gold Accent)'),
                                Textarea::make('hero_text')->label('Hero Subtitle')->rows(3)->columnSpanFull(),
                                Repeater::make('glance')
                                    ->label('At a Glance Stats')
                                    ->schema([
                                        TextInput::make('label')->required(),
                                        TextInput::make('value')->required(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                TextInput::make('story_eyebrow')->label('Story Eyebrow'),
                                TextInput::make('story_title')->label('Story Heading'),
                                Textarea::make('story_text')->label('Story Body')->rows(4)->columnSpanFull(),
                                TextInput::make('vision_title')->label('Vision Title'),
                                Textarea::make('vision_text')->label('Vision Text')->rows(2)->columnSpanFull(),
                                TextInput::make('mission_title')->label('Mission Title'),
                                Textarea::make('mission_text')->label('Mission Text')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Leadership & Team')
                            ->schema([
                                Repeater::make('leaders')
                                    ->label('Leadership Team')
                                    ->schema([
                                        FileUpload::make('photo')
                                            ->label('Portrait Photo (optional)')
                                            ->disk('public')
                                            ->directory('team')
                                            ->image()
                                            ->imageEditor()
                                            ->columnSpanFull(),
                                        TextInput::make('initials')->maxLength(3)->required(),
                                        TextInput::make('name')->required(),
                                        TextInput::make('role')->required(),
                                        TextInput::make('meta')->label('Credentials / Experience'),
                                        Toggle::make('accent')->label('Gold Role Highlight'),
                                        Textarea::make('bio')->rows(3)->required()->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Repeater::make('team')
                                    ->label('Core Team')
                                    ->schema([
                                        FileUpload::make('photo')
                                            ->label('Photo (optional)')
                                            ->disk('public')
                                            ->directory('team')
                                            ->image()
                                            ->avatar()
                                            ->columnSpanFull(),
                                        TextInput::make('initials')->maxLength(3)->required(),
                                        TextInput::make('name')->required(),
                                        TextInput::make('role')->required(),
                                        Textarea::make('bio')->rows(2)->required()->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        Tab::make('Visit & CTA')
                            ->schema([
                                TextInput::make('visit_title')->label('Visit Office Heading'),
                                TextInput::make('cta_title')->label('Bottom CTA Heading'),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        SiteContent::setGroup('about', $this->form->getState());
        ActivityLog::record('saved', 'Updated About Page CMS content');

        Notification::make()
            ->title('About page content updated')
            ->success()
            ->send();
    }
}

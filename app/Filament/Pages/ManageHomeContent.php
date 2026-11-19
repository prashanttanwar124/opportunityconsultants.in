<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Support\SiteContent;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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

class ManageHomeContent extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedHome;

    protected static string | UnitEnum | null $navigationGroup = 'Website CMS';

    protected static ?string $navigationLabel = 'Home Page';

    protected static ?string $title = 'Edit Home Page Content';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.settings-page';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteContent::group('home'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Home Sections')
                    ->tabs([
                        Tab::make('Hero & Board')
                            ->schema([
                                TextInput::make('hero_pill')->label('Top Pill Badge')->columnSpanFull(),
                                TextInput::make('hero_title')->label('Headline (White)'),
                                TextInput::make('hero_title_accent')->label('Headline (Gold Accent)'),
                                Textarea::make('hero_text')->label('Hero Subtitle')->rows(3)->columnSpanFull(),
                                TextInput::make('hero_primary_cta')->label('Primary Button Text'),
                                TextInput::make('hero_secondary_cta')->label('Secondary Button Text'),
                                TextInput::make('hero_proof_line1')->label('Social Proof Line 1'),
                                TextInput::make('hero_proof_line2')->label('Social Proof Line 2'),
                                Repeater::make('departures')
                                    ->label('Departure Board Rows')
                                    ->schema([
                                        TextInput::make('code')->required()->placeholder('OC 101'),
                                        TextInput::make('dest')->label('Board Text (max 9 chars)')->required()->maxLength(9),
                                        TextInput::make('label')->label('Accessible Name')->required(),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Callback & Ticker')
                            ->schema([
                                TextInput::make('callback_title')->label('Callback Bar Heading'),
                                TextInput::make('callback_subtitle')->label('Callback Bar Subtitle'),
                                TextInput::make('callback_button')->label('Callback Button Label'),
                                TagsInput::make('marquee_services')->label('Navy Ticker Items')->columnSpanFull(),
                                TagsInput::make('marquee_countries')->label('Gold Ticker Countries')->columnSpanFull(),
                            ])
                            ->columns(3),

                        Tab::make('Stats & Programs')
                            ->schema([
                                Repeater::make('stats')
                                    ->label('Proof Stats')
                                    ->schema([
                                        TextInput::make('value')->required()->placeholder('25+'),
                                        TextInput::make('label')->required(),
                                        Toggle::make('accent')->label('Gold Top Border'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                                TextInput::make('programs_eyebrow')->label('Programs Eyebrow'),
                                TextInput::make('programs_title')->label('Programs Heading'),
                                Textarea::make('programs_lead')->label('Programs Subtitle')->rows(2)->columnSpanFull(),
                                TextInput::make('study_badge')->label('Study Card Badge'),
                                TextInput::make('study_title')->label('Study Card Title'),
                                Textarea::make('study_text')->label('Study Card Description')->rows(2)->columnSpanFull(),
                                TagsInput::make('study_points')->label('Study Card Bullet Points')->columnSpanFull(),
                                TextInput::make('internship_badge')->label('Internship Card Badge'),
                                TextInput::make('internship_title')->label('Internship Card Title'),
                                Textarea::make('internship_text')->label('Internship Card Description')->rows(2)->columnSpanFull(),
                                Repeater::make('internship_facts')
                                    ->label('Internship Facts')
                                    ->schema([
                                        TextInput::make('label')->required(),
                                        TextInput::make('value')->required(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Destinations')
                            ->schema([
                                TextInput::make('destinations_eyebrow')->label('Eyebrow'),
                                TextInput::make('destinations_title')->label('Section Heading'),
                                Textarea::make('destinations_lead')->label('Subtitle')->rows(2)->columnSpanFull(),
                                Repeater::make('destinations')
                                    ->label('Destination Boarding Passes')
                                    ->schema([
                                        TextInput::make('code')->required()->maxLength(4)->placeholder('GB'),
                                        TextInput::make('route')->required()->placeholder('IND → LHR'),
                                        TextInput::make('name')->required(),
                                        TextInput::make('cta')->required()->placeholder('Check my UK eligibility'),
                                        Textarea::make('blurb')->rows(2)->required()->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                TextInput::make('destinations_note')->label('Bottom Internship Note')->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Process & Why Us')
                            ->schema([
                                TextInput::make('steps_eyebrow')->label('Process Eyebrow'),
                                TextInput::make('steps_title')->label('Process Heading'),
                                Repeater::make('steps')
                                    ->label('How It Works Steps')
                                    ->schema([
                                        TextInput::make('title')->required(),
                                        Textarea::make('text')->rows(2)->required(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                TextInput::make('why_title')->label('Why Us Heading')->columnSpanFull(),
                                Repeater::make('why_features')
                                    ->label('Why Us Features')
                                    ->schema([
                                        Select::make('icon')
                                            ->options([
                                                'chat' => 'Chat',
                                                'route' => 'Route',
                                                'star' => 'Star',
                                                'users' => 'Users',
                                                'check' => 'Check',
                                                'plane' => 'Plane',
                                            ])
                                            ->default('star'),
                                        TextInput::make('title')->required(),
                                        Textarea::make('text')->rows(2)->required()->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Stories, FAQ & CTA')
                            ->schema([
                                TextInput::make('testimonials_eyebrow')->label('Testimonials Eyebrow'),
                                TextInput::make('testimonials_title')->label('Testimonials Heading'),
                                Repeater::make('testimonials')
                                    ->label('Student Stories')
                                    ->schema([
                                        FileUpload::make('photo')
                                            ->label('Student Photo (optional)')
                                            ->disk('public')
                                            ->directory('testimonials')
                                            ->image()
                                            ->avatar()
                                            ->columnSpanFull(),
                                        TextInput::make('initials')->maxLength(3)->required(),
                                        Select::make('tone')
                                            ->options(['accent' => 'Gold', 'blue' => 'Blue', 'navy' => 'Navy'])
                                            ->default('accent'),
                                        TextInput::make('name')->required(),
                                        TextInput::make('meta')->label('Course / Location')->required(),
                                        Textarea::make('quote')->rows(2)->required()->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                TextInput::make('faq_title')->label('FAQ Heading'),
                                TextInput::make('faq_subtitle')->label('FAQ Subtitle'),
                                Repeater::make('faqs')
                                    ->label('FAQ Items')
                                    ->schema([
                                        TextInput::make('q')->label('Question')->required(),
                                        Textarea::make('a')->label('Answer')->rows(2)->required(),
                                    ])
                                    ->columnSpanFull(),
                                TextInput::make('cta_title')->label('Final CTA Heading')->columnSpanFull(),
                                Textarea::make('cta_text')->label('Final CTA Subtitle')->rows(2)->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public function save(): void
    {
        SiteContent::setGroup('home', $this->form->getState());
        ActivityLog::record('saved', 'Updated Home Page CMS content');

        Notification::make()
            ->title('Home page content updated')
            ->success()
            ->send();
    }
}

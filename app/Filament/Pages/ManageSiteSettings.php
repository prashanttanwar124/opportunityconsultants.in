<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Support\SiteContent;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSiteSettings extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string | UnitEnum | null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Site Settings & Scripts';

    protected static ?string $title = 'Site Settings & Tracking Scripts';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.settings-page';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            ...SiteContent::group('general'),
            ...SiteContent::group('scripts'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Brand & Contact')
                            ->schema([
                                TextInput::make('name')->label('Business Name')->required(),
                                TextInput::make('tagline')->label('Tagline')->required(),
                                TextInput::make('utility_text')->label('Top Bar Announcement')->columnSpanFull(),
                                TextInput::make('phone')->label('Primary Phone (tel: link)')->required()->placeholder('+917977632506'),
                                TextInput::make('phone_display')->label('Primary Phone (Display)')->required()->placeholder('+91 79776 32506'),
                                TextInput::make('whatsapp')->label('Primary WhatsApp Digits (for wa.me buttons)')->required()->placeholder('917977632506'),
                                TextInput::make('email')->label('Contact Email')->email()->required(),
                                Repeater::make('whatsapp_numbers')
                                    ->label('WhatsApp Numbers (shown in Callback Bar)')
                                    ->schema([
                                        TextInput::make('display')->label('Display Number')->required()->placeholder('+91 7977632506'),
                                        TextInput::make('wa')->label('wa.me Digits')->required()->placeholder('917977632506'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Office & Socials')
                            ->schema([
                                Textarea::make('address')->label('Full Office Address')->rows(2)->columnSpanFull(),
                                TextInput::make('address_short')->label('Short Address Label'),
                                TextInput::make('hours')->label('Office Hours'),
                                TextInput::make('directions_url')->label('Google Maps URL')->url()->columnSpanFull(),
                                TextInput::make('instagram_url')->label('Instagram URL'),
                                TextInput::make('youtube_url')->label('YouTube URL'),
                                TextInput::make('footer_tagline')->label('Footer Short Note')->columnSpanFull(),
                                Textarea::make('meta_description')->label('Default SEO Meta Description')->rows(3)->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Tracking Scripts')
                            ->schema([
                                Textarea::make('head_scripts')
                                    ->label('Custom <head> Scripts (Google Analytics, Meta Pixel, etc.)')
                                    ->rows(6)
                                    ->helperText('Injected right before </head> on all public pages. Super Admins only.'),
                                Textarea::make('body_scripts')
                                    ->label('Custom <body> Scripts (Chat widgets, Tag Manager noscript, etc.)')
                                    ->rows(6)
                                    ->helperText('Injected right before </body> on all public pages.'),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $scriptKeys = ['head_scripts', 'body_scripts'];
        $scripts = array_intersect_key($state, array_flip($scriptKeys));
        $general = array_diff_key($state, array_flip($scriptKeys));

        SiteContent::setGroup('general', $general);
        SiteContent::setGroup('scripts', $scripts);
        ActivityLog::record('saved', 'Updated Site Settings & Tracking Scripts');

        Notification::make()
            ->title('Site settings saved')
            ->success()
            ->send();
    }
}

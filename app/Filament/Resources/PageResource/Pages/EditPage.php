<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('View Page')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (Page $record): string => route('pages.show', $record), shouldOpenInNewTab: true),
            DeleteAction::make(),
        ];
    }
}

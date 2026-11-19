<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('View Post')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->url(fn (Post $record): string => route('blog.show', $record), shouldOpenInNewTab: true),
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\SektorUsahaResource\Pages;

use App\Filament\Resources\SektorUsahaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSektorUsaha extends EditRecord
{
    protected static string $resource = SektorUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ResiumumResource\Pages;

use App\Filament\Resources\ResiumumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResiumum extends EditRecord
{
    protected static string $resource = ResiumumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

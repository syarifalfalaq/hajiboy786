<?php

namespace App\Filament\Resources\RegencyResource\Pages;

use App\Filament\Resources\RegencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegency extends EditRecord
{
    protected static string $resource = RegencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

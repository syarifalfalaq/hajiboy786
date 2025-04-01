<?php

namespace App\Filament\Resources\SektorUsahaResource\Pages;

use App\Filament\Resources\SektorUsahaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSektorUsaha extends ViewRecord
{
    protected static string $resource = SektorUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ResiumumResource\Pages;

use App\Filament\Resources\ResiumumResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewResiumum extends ViewRecord
{
    protected static string $resource = ResiumumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

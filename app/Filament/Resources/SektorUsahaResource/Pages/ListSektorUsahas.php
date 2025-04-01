<?php

namespace App\Filament\Resources\SektorUsahaResource\Pages;

use App\Filament\Resources\SektorUsahaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSektorUsahas extends ListRecords
{
    protected static string $resource = SektorUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

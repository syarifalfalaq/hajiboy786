<?php

namespace App\Filament\Resources\ResiumumResource\Pages;

use App\Filament\Resources\ResiumumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResiumums extends ListRecords
{
    protected static string $resource = ResiumumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

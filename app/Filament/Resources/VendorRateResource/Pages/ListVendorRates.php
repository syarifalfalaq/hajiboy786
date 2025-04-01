<?php

namespace App\Filament\Resources\VendorRateResource\Pages;

use App\Filament\Resources\VendorRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorRates extends ListRecords
{
    protected static string $resource = VendorRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

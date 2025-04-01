<?php

namespace App\Filament\Resources\VendorRateResource\Pages;

use App\Filament\Resources\VendorRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorRate extends EditRecord
{
    protected static string $resource = VendorRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

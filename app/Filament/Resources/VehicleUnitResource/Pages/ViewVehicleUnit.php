<?php

namespace App\Filament\Resources\VehicleUnitResource\Pages;

use App\Filament\Resources\VehicleUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ViewVehicleUnit extends ViewRecord
{
    protected static string $resource = VehicleUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            // ... other fields
            \Filament\Forms\Components\FileUpload::make('foto')
                ->image()
                ->disk('public')
                ->directory('vehicle-photos')
                ->visibility('public')
                ->getStateUsing(function ($record) {
                    if ($record->foto) {
                        $url = asset('storage/' . $record->foto);
                        return $record->foto;
                    }
                    return null;
                }),
        ];
    }
}

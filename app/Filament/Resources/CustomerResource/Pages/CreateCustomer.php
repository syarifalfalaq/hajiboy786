<?php

// FILEPATH: j:/Project12 filament/1stproject_filament/app/Filament/Resources/CustomerResource/Pages/CreateCustomer.php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Regency;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['id_customer'])) {
            $regency = Regency::find($data['regency_id']);
            $YearCode = date('ny');
            $sequentialNumber = CustomerResource::getNextSequentialNumber();
            $data['id_customer'] = 'CS-' . $regency->id . '-' . $YearCode . str_pad($sequentialNumber, 3, '0', STR_PAD_LEFT);
        }
        return $data;
    }
}

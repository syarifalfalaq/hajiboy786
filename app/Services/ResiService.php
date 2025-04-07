<?php

namespace App\Services;

use App\Models\Resiumum;
use App\Enums\ResiStatusEnum;

class ResiService
{
    public function createResi(array $data)
    {
        $resi = new Resiumum($data);
        $resi->status = ResiStatusEnum::DATA_ENTRY;
        $resi->save();

        return $resi;
    }
}

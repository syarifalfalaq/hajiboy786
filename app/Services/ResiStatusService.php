<?php

namespace App\Services;

use App\Models\Resiumum;
use App\Models\ResiStatus;
use App\Enums\ResiStatusEnum;
use Illuminate\Support\Facades\Auth;

class ResiStatusService
{
    //  public static function updateStatus(Resiumum $resiumum, ResiStatusEnum $status)
    // {
    //     ResiStatus::create([
    //   'resiumum_id' => $resiumum->id,
    //         'status' => $status->value,
    //         'timestamp' => now(),
    //         'user_id' => Auth::id(),
    //    ]);
    // }

    public static function updateStatus(Resiumum $record, $status)
    {
        if ($status === null) {
            throw new \InvalidArgumentException("Status tidak boleh kosong (NULL)");
        }

        if ($status instanceof ResiStatusEnum) {
            $record->status = $status;
        } elseif (is_string($status)) {
            $enumStatus = ResiStatusEnum::tryFrom($status);
            if ($enumStatus) {
                $record->status = $enumStatus;
            } else {
                throw new \InvalidArgumentException("Status string tidak valid: {$status}");
            }
        } else {
            throw new \InvalidArgumentException("Tipe status tidak valid: " . gettype($status));
        }
        $record->save();
    }
}

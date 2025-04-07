<?php

namespace App\Observers;

use App\Models\Resiumum;

class ResiumumObserver
{
    public function creating(Resiumum $resiumum)
    {
        if (!$resiumum->last_officer_id) {
            $resiumum->last_officer_id = request()->id();
        }
    }

    public function updating(Resiumum $resiumum)
    {
        $resiumum->last_officer_id = request()->id();
    }
}

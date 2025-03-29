<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Network extends Model
{
    protected $fillable = [
        'type_id',
        'code',
        'name',
        'address',
        'kordinat',
        'postal_code',
        'phone',
        'email',
        'orig_province_id',
        'orig_regency_id',
        'orig_district_id'
    ];
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'orig_province_id');
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'orig_regency_id', 'id');
    }



    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'orig_district_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'orig_village_id');
    }

    public function jaringan(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'sektor_id',
        'network_id',
        'id_customer',
        'sektor_usaha_id',


        'kode',
        'nama',
        'npwp',
        'is_taxed',
        'is_active',
        'detail_pic',
        'start_date',
        'address',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'category_id',
    ];
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }



    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function SektorUsaha(): BelongsTo
    {
        return $this->belongsTo(SektorUsaha::class);
    }
    public function Network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }
}

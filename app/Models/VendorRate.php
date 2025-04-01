<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorRate extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'destination_district',
        'origin_city',
        'destination_province',
        'destination_city',
        'regency_id',
        'province_id',
        'harga',
        'jenis_pengiriman',
        'berat_minimum',
        'harga_per_kg_tambahan',



    ];
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }


    public function originCities(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'origin_city');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'destination_district');
    }
}

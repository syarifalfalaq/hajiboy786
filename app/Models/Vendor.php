<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis', // 'pengiriman' atau 'taksi'
        'alamat',
        'telepon',
        'email',
        'status', // 'aktif' atau 'nonaktif'
    ];
    public function vehicleUnits()
    {
        return $this->hasMany(VehicleUnit::class);
    }

    public function shippingRates(): HasMany
    {
        return $this->hasMany(VendorRate::class);
    }
}

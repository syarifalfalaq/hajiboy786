<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'kepemilikan',
        'jenis_kendaraan',
        'merk',
        'model',
        'tahun',
        'nomor_plat',
        'nomor_rangka',
        'nomor_mesin',
        'warna',
        'status',
        'foto',
    ];
    protected $casts = [
        'kepemilikan' => 'string',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return asset('storage/' . $this->foto);
        }

        return null;
    }
}

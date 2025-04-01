<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($vehicleUnit) {
            if ($vehicleUnit->foto) {
                Storage::disk('public')->delete($vehicleUnit->foto);
            }
        });
    }
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            $path = 'vehicle-photos/' . basename($this->foto);
            $fullPath = Storage::disk('public')->path($path);
            $exists = Storage::disk('public')->exists($path);
            $url = asset('storage/' . $path);

            Log::info("VehicleUnit foto debug", [
                'id' => $this->id,
                'foto' => $this->foto,
                'path' => $path,
                'fullPath' => $fullPath,
                'exists' => $exists,
                'url' => $url,
            ]);

            return $url;
        }
        return null;
        if ($this->foto) {
            $path = 'vehicle-photos/' . basename($this->foto);
            $fullPath = Storage::disk('public')->path($path);
            $exists = Storage::disk('public')->exists($path);
            $url = asset('storage/' . $path);

            Log::info("VehicleUnit foto debug", [
                'id' => $this->id,
                'foto' => $this->foto,
                'path' => $path,
                'fullPath' => $fullPath,
                'exists' => $exists,
                'url' => $url,
            ]);

            return $url;
        }

        return null;
    }
}

<?php

namespace App\Models;

use App\Enums\ResiStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Resiumum extends Model
{

    const STATUS_DATA_ENTRY = 'Data Entry';
    const STATUS_PENDING = 'Pending';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_DELIVERED = 'Delivered';
    protected $fillable = [
        'ty_cust',
        'orig_regency_id',
        'province_id',
        'regency_id',
        'district_id',
        'etd',
        'layanan_id',
        'service_name',
        'service_id',
        'weight',
        'koli',
        'pcs_count',
        'p',
        'l',
        't',
        'volume',
        'weightVolume',
        'insurance',
        'insurance_value',
        'charged_on',
        'jenis_kiriman',
        'discount',
        'biaya_admin',
        'biaya_packing',
        'biaya_tambahan',
        'tariff',
        'ongkir',
        'total',
        'noresi',
        'nofakt',
        'date_input',
        'jenis_pembayaran',
        'kurir_pickup',
        'desk_brg',
        'catatan',
        'nama_pengirim',
        'alamat_pengirim',
        'telp_pengirim',
        'nama_penerima',
        'alamat_penerima',
        'telp_penerima',
        'kode_pos',
        'kode_pos_asal',
        'last_officer_id',
        'last_location_id',
        'pickup_courier_id',
        'delivery_courier_id',
        'items_detail',
        'total_volume',
        'total_weight_volume',
        'satuan_barang',
        'status',
        'nama_customer',
        'vendor_id',
    ];
    protected $casts = [
        'layanan_id' => 'integer',
        'tariff' => 'float',
        'ongkir' => 'float',
        'total' => 'float',
        'p' => 'float',
        'l' => 'float',
        't' => 'float',
        //  'totalvolume' => 'float',
        //   'totalWeightVolume' => 'float',
        'insurance_value' => 'float',
        'discount' => 'float',
        'biaya_admin' => 'float',
        'biaya_packing' => 'float',
        'biaya_tambahan' => 'float',
        'dimensi' => 'array',
        'items_detail' => 'array',
        'total_volume' => 'float',
        'total_weight_volume' => 'float',
        'status' => ResiStatusEnum::class,


    ];



    protected static function boot()
    {
        parent::boot();

        static::creating(function ($resi) {
            $resi->status = ResiStatusEnum::DATA_ENTRY;
        });
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'orig_regency_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
    public function regencydest()
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    //  public function statuses()
    //  {
    //       return $this->hasMany(ResiStatus::class);
    //  }


    //  public function manifests(): BelongsToMany
    //  {
    //   return $this->belongsToMany(Manifest::class, 'manifest_resiumum');
    //}

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'nama_customer', 'id');
    }
    public function lastOfficer()
    {
        return $this->belongsTo(User::class, 'last_officer_id');
    }

    use HasFactory;
}

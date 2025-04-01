<?php

namespace App\Models;

use id;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class rate extends Model
{
    protected $fillable = [
        'customer_id',
        'service_id',
        'pack_type_id',
        'orig_regency_id',
        'regency_id',
        'district_id',
        'province_id',
        'rate_kg',
        'rate_pc',
        'rate_koli',
        'min_weight',
        'nama_customer',
        'etd',
        'discount',
        'add_cost',
        'notes',
        'ty_cust',
    ];
    public function originRegency()
    {
        return $this->belongsTo(Regency::class, 'orig_regency_id');
    }


    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }


    public function originCities(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'orig_regency_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'nama_customer');
    }
}

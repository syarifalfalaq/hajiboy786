<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SektorUsaha extends Model
{
    protected $fillable = [
        'nama_sektor',
        'kode_sektor',
        'deskripsi',
        'is_active',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifUjrah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_barang',
        'min_taksiran',
        'max_taksiran',
        'tarif',
    ];
}

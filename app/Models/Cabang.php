<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabang',
        'alamat',
        'telepon',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function nasabah()
    {
        return $this->hasMany(Nasabah::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    protected $table = 'nasabah';
    protected $fillable = ['nik', 'nama', 'alamat', 'telepon', 'foto_ktp'];

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }

    public function transaksiRahn()
    {
        return $this->hasMany(TransaksiRahn::class);
    }
}

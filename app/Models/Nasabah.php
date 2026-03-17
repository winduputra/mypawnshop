<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    protected $table = 'nasabah';
    protected $fillable = ['nik', 'nama', 'email', 'alamat', 'telepon', 'foto_ktp', 'foto', 'nama_bank', 'no_rekening', 'cabang_id'];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function getWhatsappNumberAttribute()
    {
        $phone = $this->telepon;
        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }
        return $phone;
    }

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }

    public function transaksiRahn()
    {
        return $this->hasMany(TransaksiRahn::class);
    }
}

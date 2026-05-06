<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $fillable = [
        'nasabah_id', 'nama_barang', 'kategori', 'merk_type',
        'nomor_seri', 'spesifikasi', 'kelengkapan', 'kondisi_fisik',
        'deskripsi', 'berat', 'taksiran',
    ];
    protected $casts = ['kelengkapan' => 'array'];
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function isSedangDigadai()
    {
        return $this->detailTransaksi()
            ->whereHas('transaksiRahn', function ($query) {
                $query->whereIn('status', ['aktif', 'diperpanjang']);
            })->exists();
    }

    public function fotoBarang()
    {
        return $this->hasMany(FotoBarang::class);
    }
}

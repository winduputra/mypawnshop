<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';
    protected $fillable = ['transaksi_rahn_id', 'barang_id', 'taksiran_item', 'pinjaman_item'];

    public function transaksiRahn()
    {
        return $this->belongsTo(TransaksiRahn::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}

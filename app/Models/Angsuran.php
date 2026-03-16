<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    protected $table = 'angsuran';
    protected $fillable = [
        'transaksi_rahn_id', 'user_id', 'tanggal_bayar',
        'jumlah_bayar', 'sisa_pinjaman', 'catatan'
    ];

    public function transaksiRahn()
    {
        return $this->belongsTo(TransaksiRahn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lelang extends Model
{
    protected $table = 'lelang';
    protected $fillable = [
        'transaksi_rahn_id', 'user_id', 'tanggal_lelang', 
        'harga_lelang', 'biaya_lelang', 'pembeli', 'telepon_pembeli', 
        'sisa_untuk_nasabah', 'kerugian', 'sisa_pinjaman', 'catatan'
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

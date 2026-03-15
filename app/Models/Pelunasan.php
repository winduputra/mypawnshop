<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelunasan extends Model
{
    protected $table = 'pelunasan';
    protected $fillable = [
        'transaksi_rahn_id', 'user_id', 'tanggal_pelunasan', 
        'total_pinjaman', 'total_ujrah', 'total_bayar', 'catatan'
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

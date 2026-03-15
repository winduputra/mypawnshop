<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiRahn extends Model
{
    protected $table = 'transaksi_rahn';
    protected $fillable = [
        'no_transaksi', 'nasabah_id', 'user_id', 'tanggal_transaksi', 
        'total_taksiran', 'total_pinjaman', 'biaya_admin', 
        'ujrah_per_30hari', 'tenor_hari', 'tanggal_jatuh_tempo', 
        'tanggal_batas_lelang', 'status'
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function perpanjangan()
    {
        return $this->hasMany(Perpanjangan::class);
    }

    public function pelunasan()
    {
        return $this->hasOne(Pelunasan::class);
    }

    public function lelang()
    {
        return $this->hasOne(Lelang::class);
    }
}

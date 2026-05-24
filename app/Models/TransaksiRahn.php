<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiRahn extends Model
{
    protected $table = 'transaksi_rahn';
    protected $fillable = [
        'no_transaksi', 'no_register_akad', 'nasabah_id', 'user_id',
        'tanggal_transaksi', 'total_taksiran', 'total_pinjaman', 'sisa_pinjaman',
        'biaya_admin', 'biaya_penitipan', 'metode_pembayaran',
        'ujrah_per_30hari', 'tenor_hari', 'tanggal_jatuh_tempo',
        'tanggal_batas_lelang', 'status', 'status_approval',
        'catatan_admin', 'taksiran_final', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class);
    }

    public function histories()
    {
        return $this->hasMany(TransaksiRahnHistory::class)->latest();
    }
}

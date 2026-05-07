<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perpanjangan extends Model
{
    protected $table = 'perpanjangan';
    protected $fillable = [
        'transaksi_rahn_id', 'user_id', 'no_nota',
        'tanggal_perpanjangan', 'tambahan_tenor_hari',
        'tanggal_jatuh_tempo_baru', 'ujrah_dibayar',
        'is_overdue_extension', 'catatan'
    ];

    protected $casts = [
        'is_overdue_extension' => 'boolean',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lelang extends Model
{
    protected $table = 'lelang';
    protected $fillable = [
        'no_lelang', 'transaksi_rahn_id', 'user_id', 'approved_by',
        'tanggal_lelang', 'status_lelang', 'tanggal_terjual',
        'harga_lelang', 'biaya_lelang', 'ijarah',
        'pembeli', 'telepon_pembeli',
        'sisa_untuk_nasabah', 'sisa_dana_kembali', 'kerugian', 'sisa_pinjaman',
        'catatan', 'catatan_owner', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function transaksiRahn()
    {
        return $this->belongsTo(TransaksiRahn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate unique auction ID: LLG-YYYYMMDD-XXXX
     */
    public static function generateNoLelang(): string
    {
        $date = now()->format('Ymd');
        $prefix = "LLG-{$date}-";

        $last = static::where('no_lelang', 'like', "{$prefix}%")
            ->orderByDesc('no_lelang')
            ->value('no_lelang');

        if ($last) {
            $seq = intval(substr($last, -4)) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

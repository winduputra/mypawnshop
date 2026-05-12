<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiRahnHistory extends Model
{
    protected $table = 'transaksi_rahn_histories';

    protected $fillable = [
        'transaksi_rahn_id', 'user_id', 'action', 'status_approval', 'note',
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

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Nasabah;
use App\Models\TransaksiRahn;
use App\Models\Lelang;
use App\Models\Pelunasan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isKasir = $user->role === 'kasir' && $user->cabang_id;

        $nasabahQuery   = Nasabah::query();
        $transaksiQuery = TransaksiRahn::query();

        if ($isKasir) {
            $nasabahQuery->where('cabang_id', $user->cabang_id);
            $transaksiQuery->whereHas('nasabah', fn($q) => $q->where('cabang_id', $user->cabang_id));
        }

        $stats = [
            'total_nasabah'       => $nasabahQuery->count(),
            'active_rahn'         => (clone $transaksiQuery)->whereIn('status', ['aktif', 'diperpanjang'])->count(),
            'total_pinjaman'      => (clone $transaksiQuery)->whereIn('status', ['aktif', 'diperpanjang'])->sum('total_pinjaman'),
            'siap_lelang'         => (clone $transaksiQuery)->where('status', '!=', 'lunas')
                                        ->where('tanggal_batas_lelang', '<=', now()->toDateString())->count(),
            'pelunasan_hari_ini'  => Pelunasan::where('tanggal_pelunasan', now()->toDateString())->sum('total_bayar'),
        ];

        $recent_transactions = (clone $transaksiQuery)->with('nasabah')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_transactions'));
    }
}

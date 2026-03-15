<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Nasabah;
use App\Models\TransaksiRahn;
use App\Models\Lelang;
use App\Models\Pelunasan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_nasabah' => Nasabah::count(),
            'active_rahn' => TransaksiRahn::whereIn('status', ['aktif', 'diperpanjang'])->count(),
            'total_pinjaman' => TransaksiRahn::whereIn('status', ['aktif', 'diperpanjang'])->sum('total_pinjaman'),
            'siap_lelang' => TransaksiRahn::where('status', '!=', 'lunas')
                ->where('tanggal_batas_lelang', '<=', now()->toDateString())
                ->count(),
            'pelunasan_hari_ini' => Pelunasan::where('tanggal_pelunasan', now()->toDateString())->sum('total_bayar'),
        ];

        $recent_transactions = TransaksiRahn::with('nasabah')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_transactions'));
    }
}

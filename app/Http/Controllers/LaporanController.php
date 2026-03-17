<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiRahn;
use App\Models\Perpanjangan;
use App\Models\Pelunasan;
use App\Models\DetailTransaksi;
use App\Models\Cabang;
use App\Models\Angsuran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user     = Auth::user();
        $period   = $request->get('period', 'harian');
        $search   = $request->get('search_jt');
        $cabangs  = Cabang::orderBy('nama_cabang')->get();

        // For admin: allow selecting a branch filter. For kasir: always use their own branch.
        $filterCabangId = null;
        if ($user->role === 'admin') {
            $filterCabangId = $request->get('cabang_id');
        } elseif ($user->role === 'kasir' && $user->cabang_id) {
            $filterCabangId = $user->cabang_id;
        }

        $selectedCabang = $filterCabangId ? Cabang::find($filterCabangId) : null;

        $now = Carbon::now();
        switch ($period) {
            case 'mingguan':
                $startDate   = $now->copy()->startOfWeek();
                $endDate     = $now->copy()->endOfWeek();
                $periodLabel = 'Minggu ini (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'bulanan':
                $startDate   = $now->copy()->startOfMonth();
                $endDate     = $now->copy()->endOfMonth();
                $periodLabel = 'Bulan ' . $now->translatedFormat('F Y');
                break;
            default:
                $startDate   = $now->copy()->startOfDay();
                $endDate     = $now->copy()->endOfDay();
                $periodLabel = 'Hari ini (' . $now->translatedFormat('d F Y') . ')';
                break;
        }

        // Scope helper: filter transaksi by cabang via nasabah
        $scopeTrx = function ($query) use ($filterCabangId) {
            if ($filterCabangId) {
                $query->whereHas('nasabah', fn($q) => $q->where('cabang_id', $filterCabangId));
            }
        };

        $laporanAdmin = TransaksiRahn::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->tap($scopeTrx)
            ->selectRaw('COUNT(*) as jumlah, SUM(biaya_admin) as total_admin')
            ->first();

        $ujrahPerpanjangan = Perpanjangan::whereBetween('tanggal_perpanjangan', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($filterCabangId, fn($q) => $q->whereHas('transaksiRahn.nasabah', fn($q2) => $q2->where('cabang_id', $filterCabangId)))
            ->selectRaw('COUNT(*) as jumlah, SUM(ujrah_dibayar) as total_ujrah')
            ->first();

        $ujrahTransaksi = TransaksiRahn::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->tap($scopeTrx)
            ->selectRaw('SUM(biaya_penitipan) as total_penitipan')
            ->first();

        $laporanUjrah = (object)[
            'jumlah'     => ($ujrahPerpanjangan->jumlah ?? 0),
            'total_ujrah'=> ($ujrahPerpanjangan->total_ujrah ?? 0) + ($ujrahTransaksi->total_penitipan ?? 0),
        ];

        $laporanPinjaman = TransaksiRahn::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->tap($scopeTrx)
            ->selectRaw('COUNT(*) as jumlah, SUM(total_pinjaman) as total_pinjaman, SUM(total_taksiran) as total_taksiran')
            ->first();

        $laporanAngsuran = Angsuran::whereBetween('tanggal_bayar', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($filterCabangId, fn($q) => $q->whereHas('transaksiRahn.nasabah', fn($q2) => $q2->where('cabang_id', $filterCabangId)))
            ->selectRaw('COUNT(*) as jumlah, SUM(jumlah_bayar) as total_angsuran')
            ->first();

        $laporanBarang = DetailTransaksi::whereHas('transaksiRahn', function ($q) use ($filterCabangId) {
                $q->whereIn('status', ['aktif', 'diperpanjang']);
                if ($filterCabangId) {
                    $q->whereHas('nasabah', fn($q2) => $q2->where('cabang_id', $filterCabangId));
                }
            })
            ->join('barang', 'detail_transaksi.barang_id', '=', 'barang.id')
            ->selectRaw('barang.kategori, COUNT(*) as jumlah, SUM(detail_transaksi.taksiran_item) as total_taksiran, SUM(detail_transaksi.pinjaman_item) as total_pinjaman')
            ->groupBy('barang.kategori')
            ->get();

        $totalBarangAktif    = $laporanBarang->sum('jumlah');
        $totalNilaiTaksiran  = $laporanBarang->sum('total_taksiran');
        $totalNilaiPinjaman  = $laporanBarang->sum('total_pinjaman');

        $perPage      = in_array($request->get('per_page', 10), [10, 20, 50, 100]) ? (int)$request->get('per_page', 10) : 10;

        $laporanJatuhTempo = TransaksiRahn::whereIn('status', ['aktif', 'diperpanjang'])
            ->with('nasabah')
            ->tap($scopeTrx)
            ->when($search, function ($query, $search) {
                return $query->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('nasabah', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            })
            ->orderBy('tanggal_jatuh_tempo')
            ->paginate($perPage, ['*'], 'page_jt')
            ->appends($request->except('page_jt'));

        // Enrich with sisa_hari
        $laporanJatuhTempo->getCollection()->transform(function ($trx) use ($now) {
            $trx->sisa_hari = Carbon::parse($trx->tanggal_jatuh_tempo)->diffInDays($now, false);
            return $trx;
        });

        $transaksiPeriod = TransaksiRahn::with('nasabah')
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->tap($scopeTrx)
            ->latest()
            ->paginate($perPage, ['*'], 'page_trx')
            ->appends($request->except('page_trx'));

        return view('laporan.index', compact(
            'period', 'periodLabel', 'search', 'perPage',
            'laporanAdmin', 'laporanUjrah', 'laporanPinjaman', 'laporanAngsuran',
            'laporanBarang', 'totalBarangAktif', 'totalNilaiTaksiran', 'totalNilaiPinjaman',
            'laporanJatuhTempo', 'transaksiPeriod',
            'cabangs', 'filterCabangId', 'selectedCabang'
        ));
    }
}

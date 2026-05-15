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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NasabahExport;
use App\Exports\StockOpnameExport;
use App\Exports\UangMasukExport;
use App\Exports\UangDipinjamExport;
use App\Exports\JatuhTempoExport;
use App\Exports\BarangLelangExport;
use App\Exports\PinjamanExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user     = Auth::user();
        $period   = $request->get('period', 'harian');
        $startInput = $request->get('start_date');
        $endInput = $request->get('end_date');
        $search   = $request->get('search_jt');
        $cabangs  = Cabang::orderBy('nama_cabang')->get();

        // For admin: allow selecting a branch filter. For kasir: always use their own branch.
        $filterCabangId = null;
        if (in_array($user->role, ['admin', 'owner', 'superadmin', 'superuser'])) {
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
            case 'custom':
                $startDate = $startInput ? Carbon::parse($startInput)->startOfDay() : $now->copy()->startOfDay();
                $endDate = $endInput ? Carbon::parse($endInput)->endOfDay() : $startDate->copy()->endOfDay();
                $periodLabel = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
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

        // Enrich with whole-day status, ignoring current time fraction.
        $laporanJatuhTempo->getCollection()->transform(function ($trx) use ($now) {
            $trx->sisa_hari = (int) Carbon::parse($trx->tanggal_jatuh_tempo)
                ->startOfDay()
                ->diffInDays($now->copy()->startOfDay(), false);
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
            'cabangs', 'filterCabangId', 'selectedCabang', 'startInput', 'endInput'
        ));
    }

    // ── Excel Export Methods ──

    public function exportNasabah()
    {
        return Excel::download(new NasabahExport, 'Laporan-Data-Nasabah-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportStockOpname(Request $request)
    {
        $kategori = $request->get('kategori'); // null = semua
        $suffix = $kategori ? ucfirst($kategori) : 'Semua';
        return Excel::download(new StockOpnameExport($kategori), "Laporan-Stock-Opname-{$suffix}-" . now()->format('Ymd') . '.xlsx');
    }

    public function exportUangMasuk()
    {
        return Excel::download(new UangMasukExport, 'Laporan-Uang-Masuk-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportUangDipinjam()
    {
        return Excel::download(new UangDipinjamExport, 'Laporan-Uang-Dipinjam-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportJatuhTempo()
    {
        return Excel::download(new JatuhTempoExport, 'Laporan-Jatuh-Tempo-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportBarangLelang(Request $request)
    {
        $status = $request->get('status'); // 'terjual', 'belum', or null
        $suffix = $status === 'terjual' ? 'Terjual' : ($status === 'belum' ? 'BelumTerjual' : 'Semua');
        return Excel::download(new BarangLelangExport($status), "Laporan-Barang-Lelang-{$suffix}-" . now()->format('Ymd') . '.xlsx');
    }

    public function exportPinjaman()
    {
        return Excel::download(new PinjamanExport, 'Laporan-Pinjaman-' . now()->format('Ymd') . '.xlsx');
    }
}

@php
    $logoPath = public_path('images/logo.jpeg');
    $logoExists = file_exists($logoPath);
    $nasabah = $transaksi->nasabah;
    $petugas = $transaksi->user->name ?? '-';
    $cabang = $nasabah->cabang->nama_cabang ?? '-';
    $nomorAkad = $transaksi->no_register_akad ?? $transaksi->no_transaksi;
    $tanggalAkad = \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y');
    $tanggalJatuhTempo = \Carbon\Carbon::parse($transaksi->tanggal_jatuh_tempo)->translatedFormat('d F Y');
    $tanggalBatasLelang = \Carbon\Carbon::parse($transaksi->tanggal_batas_lelang)->translatedFormat('d F Y');
    $detailUtama = $transaksi->detailTransaksi->first();
    $barangUtama = $detailUtama?->barang;
    $lokasiTtd = $cabang !== '-' ? $cabang : 'Bandar Lampung';

    $terbilang = function ($nilai) use (&$terbilang) {
        $nilai = abs((int) $nilai);
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($nilai < 12) {
            return $huruf[$nilai];
        }
        if ($nilai < 20) {
            return trim($terbilang($nilai - 10) . ' belas');
        }
        if ($nilai < 100) {
            return trim($terbilang(floor($nilai / 10)) . ' puluh ' . $terbilang($nilai % 10));
        }
        if ($nilai < 200) {
            return trim('seratus ' . $terbilang($nilai - 100));
        }
        if ($nilai < 1000) {
            return trim($terbilang(floor($nilai / 100)) . ' ratus ' . $terbilang($nilai % 100));
        }
        if ($nilai < 2000) {
            return trim('seribu ' . $terbilang($nilai - 1000));
        }
        if ($nilai < 1000000) {
            return trim($terbilang(floor($nilai / 1000)) . ' ribu ' . $terbilang($nilai % 1000));
        }
        if ($nilai < 1000000000) {
            return trim($terbilang(floor($nilai / 1000000)) . ' juta ' . $terbilang($nilai % 1000000));
        }
        if ($nilai < 1000000000000) {
            return trim($terbilang(floor($nilai / 1000000000)) . ' miliar ' . $terbilang($nilai % 1000000000));
        }

        return trim($terbilang(floor($nilai / 1000000000000)) . ' triliun ' . $terbilang($nilai % 1000000000000));
    };

    $formatBarang = function ($detail) {
        $barang = $detail->barang;
        $bagian = array_filter([
            $barang->nama_barang ?? null,
            $barang->merk_type ?? null,
            $barang->nomor_seri ?? null,
            $barang->spesifikasi ?? null,
            $barang->kondisi_fisik ?? null,
        ]);

        return implode(' / ', $bagian);
    };
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Akad Pembiayaan Gadai Syariah - {{ $nomorAkad }}</title>
    <style>
        @page { margin: 34mm 18mm 32mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', 'DejaVu Serif', serif; font-size: 11.5px; line-height: 1.45; color: #000; padding: 0 12mm; }
        .page-break { page-break-before: always; height: 18mm; }
        .header { text-align: center; margin-bottom: 10px; }
        .logo { display: block; width: 175px; height: auto; margin: 0 auto 8px; }
        .brand-fallback { font-size: 18px; font-weight: bold; letter-spacing: 1px; margin-bottom: 8px; }
        .title { font-size: 15px; font-weight: bold; text-align: center; text-transform: uppercase; margin-bottom: 12px; }
        .meta { width: 360px; margin: 0 auto 10px; border-collapse: collapse; }
        .meta td { padding: 1px 3px; }
        .meta-label { width: 86px; }
        .meta-sep { width: 8px; }
        .dash { text-align: center; margin: 8px 0 10px; letter-spacing: .5px; }
        .small-dash { text-align: center; margin: 9px 0; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .section-heading { text-align: center; font-weight: bold; text-transform: uppercase; margin: 9px 0 6px; }
        .sub-heading { font-weight: bold; margin: 8px 0 4px; }
        .field-table { width: 100%; border-collapse: collapse; margin: 3px 0 7px; }
        .field-table td { padding: 1px 0; vertical-align: top; }
        .field-label { width: 118px; }
        .field-sep { width: 10px; }
        .indent { margin-left: 15px; }
        .clause { display: table; width: 100%; margin-bottom: 5px; padding-left: 0; text-indent: 0; text-align: left; }
        .clause-label { display: table-cell; width: 32px; padding-right: 8px; white-space: nowrap; text-indent: 0; }
        .letter-list { margin-left: 40px; }
        .letter-list div { margin-bottom: 2px; }
        .items { width: 100%; border-collapse: collapse; margin: 5px 0 7px; }
        .items th, .items td { border: 1px solid #000; padding: 4px 5px; vertical-align: top; }
        .items th { text-align: center; font-weight: bold; }
        .right { text-align: right; }
        .signature { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .signature td { width: 50%; vertical-align: top; padding-right: 18px; }
        .sign-space { height: 72px; }
        .sign-line { display: inline-block; min-width: 190px; border-bottom: 1px dotted #000; }
        .witness { margin-top: 18px; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoExists)
            <img class="logo" src="{{ $logoPath }}" alt="Harmans Gadai Syariah">
        @else
            <div class="brand-fallback">HARMANS GADAI SYARIAH</div>
        @endif
        <div class="title">AKAD PEMBIAYAAN GADAI SYARIAH (RAHN)</div>
    </div>

    <table class="meta">
        <tr><td class="meta-label">Nomor Akad</td><td class="meta-sep">:</td><td>{{ $nomorAkad }}</td></tr>
        <tr><td class="meta-label">Tanggal</td><td class="meta-sep">:</td><td>{{ $tanggalAkad }}</td></tr>
        <tr><td class="meta-label">Cabang</td><td class="meta-sep">:</td><td>{{ $cabang }}</td></tr>
    </table>

    <div class="dash">------------------------------------------------------------------------------------------------------------------------</div>

    <div class="section-heading">PERNYATAAN DAN PERSETUJUAN NASABAH</div>

    <div class="sub-heading">I. Pihak Pertama (Rahin / Nasabah / Peminjam)</div>
    <table class="field-table indent">
        <tr><td class="field-label">Nama</td><td class="field-sep">:</td><td>{{ $nasabah->nama ?? '-' }}</td></tr>
        <tr><td class="field-label">No. Identitas</td><td class="field-sep">:</td><td>{{ $nasabah->nik ?? '-' }}</td></tr>
        <tr><td class="field-label">Alamat</td><td class="field-sep">:</td><td>{{ $nasabah->alamat ?? '-' }}</td></tr>
    </table>
    <div class="indent">Selanjutnya disebut <span class="bold">PEMINJAM</span>.</div>

    <div class="sub-heading">II. Pihak Kedua (Murtahin / Perusahaan / Penerima Gadai)</div>
    <table class="field-table indent">
        <tr><td class="field-label">Nama Perusahaan</td><td class="field-sep">:</td><td>HARMANS GADAI SYARIAH</td></tr>
        <tr><td class="field-label">Diwakili oleh</td><td class="field-sep">:</td><td>{{ $petugas }}</td></tr>
        <tr><td class="field-label">Jabatan</td><td class="field-sep">:</td><td>Kasir</td></tr>
    </table>
    <div class="indent">Selanjutnya disebut <span class="bold">PENERIMA GADAI</span>.</div>

    <p class="clause" style="margin-top: 9px;">Kedua belah pihak sepakat mengadakan akad pembiayaan dengan prinsip syariah dengan ketentuan sebagai berikut.</p>

    <div class="small-dash">---</div>

    <div class="section-heading">PASAL 1<br>DASAR AKAD</div>
    <p class="clause"><span class="clause-label">1.1</span>Akad ini menggunakan prinsip:</p>
    <div class="letter-list">
        <div>a. Qard (pinjaman kebajikan) atas pokok pinjaman.</div>
        <div>b. Rahn (gadai) atas barang jaminan yang diserahkan.</div>
        <div>c. Ijarah (sewa) atas jasa penitipan barang jaminan.</div>
    </div>
    <p class="clause"><span class="clause-label">1.2</span>Kedua belah pihak menyatakan bahwa seluruh transaksi ini bebas dari riba, gharar (ketidakpastian), dan maysir (spekulasi).</p>

    <div class="small-dash">---</div>

    <div class="section-heading">PASAL 2<br>POKOK PINJAMAN (QARD) DAN JAMINAN</div>
    <p class="clause"><span class="clause-label">2.1</span>PEMINJAM menerima pinjaman sebesar Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</p>
    <p class="clause indent">(terbilang : {{ ucfirst($terbilang($transaksi->total_pinjaman)) }} rupiah).</p>
    <p class="clause"><span class="clause-label">2.2</span>Sebagai jaminan, PEMINJAM menyerahkan barang kepada PENERIMA GADAI dengan spesifikasi:</p>
    <table class="field-table indent">
        <tr><td class="field-label">Jenis Barang</td><td class="field-sep">:</td><td>{{ $barangUtama->kategori ?? $barangUtama->nama_barang ?? '-' }}</td></tr>
        <tr><td class="field-label">Merek / Tipe</td><td class="field-sep">:</td><td>{{ $barangUtama->merk_type ?? '-' }}</td></tr>
        <tr><td class="field-label">Nomor Seri/Polisi</td><td class="field-sep">:</td><td>{{ $barangUtama->nomor_seri ?? '-' }}</td></tr>
    </table>
    <p class="clause"><span class="clause-label">2.3</span>Barang jaminan berada dalam penguasaan fisik PENERIMA GADAI selama masa akad berlangsung.</p>

    <div class="page-break"></div>

    <div class="section-heading">PASAL 3<br>BIAYA PENITIPAN (UJRAH) DAN ADMINISTRASI</div>
    <p class="clause"><span class="clause-label">3.1</span>Ujrah (biaya penitipan) disepakati sebesar Rp {{ number_format($transaksi->ujrah_per_30hari, 0, ',', '.') }} per 30 (tiga puluh) hari.</p>
    <p class="clause"><span class="clause-label">3.2</span>Besaran ujrah didasarkan pada biaya riil penitipan (tempat dan pemeliharaan) dan tidak dikaitkan dengan jumlah pinjaman, sehingga transparan dan adil.</p>
    <p class="clause"><span class="clause-label">3.3</span>Biaya administrasi di awal sebesar Rp {{ number_format($transaksi->biaya_admin, 0, ',', '.') }} dibayarkan sekali di muka.</p>

    <div class="small-dash">---</div>

    <div class="section-heading">PASAL 4<br>JANGKA WAKTU DAN PERPANJANGAN</div>
    <p class="clause"><span class="clause-label">4.1</span>Jangka waktu pinjaman selama {{ $transaksi->tenor_hari }} hari, terhitung sejak tanggal akad ini sampai dengan {{ $tanggalJatuhTempo }}.</p>
    <p class="clause"><span class="clause-label">4.2</span>Apabila PEMINJAM ingin memperpanjang waktu, wajib membayar ujrah (biaya penitipan) sebelum jatuh tempo.</p>
    <p class="clause"><span class="clause-label">4.3</span>Perpanjangan dapat dilakukan tanpa batasan jumlah perpanjangan, selama PEMINJAM membayar ujrah dan belum melewati jatuh tempo lebih dari 7 (tujuh) hari.</p>
    <p class="clause"><span class="clause-label">4.4</span>Apabila telah melewati jatuh tempo paling lama 7 (tujuh) hari, PEMINJAM masih dapat memperpanjang jangka waktu pinjaman dengan ketentuan:</p>
    <div class="letter-list">
        <div>a. Membayar ujrah untuk masa yang telah dilalui.</div>
        <div>b. Perpanjangan waktu dihitung dari tanggal jatuh tempo dan hanya menambah 20 (dua puluh) hari.</div>
    </div>
    <p class="clause"><span class="clause-label">4.5</span>Apabila telah melewati jatuh tempo lebih dari 7 (tujuh) hari, PEMINJAM tidak dapat memperpanjang atau mengangsur, tetapi tetap dapat melunasi seluruh sisa pinjaman dan ujrah yang terutang. Jika tidak melunasi, PENERIMA GADAI berhak mengeksekusi jaminan sesuai Pasal 7.</p>

    <div class="small-dash">---</div>

    <div class="section-heading">PASAL 5<br>HAK DAN KEWAJIBAN</div>
    <p class="clause"><span class="clause-label">5.1</span>Kewajiban PEMINJAM:</p>
    <div class="letter-list"><div>a. Mengembalikan pokok pinjaman (qard) tepat waktu.</div><div>b. Membayar ujrah sesuai kesepakatan.</div><div>c. Menjamin keaslian dan status kepemilikan barang jaminan.</div></div>
    <p class="clause"><span class="clause-label">5.2</span>Hak PEMINJAM:</p>
    <div class="letter-list"><div>a. Mendapatkan kembali barang jaminan setelah pelunasan.</div><div>b. Mendapatkan perlakuan adil dan transparan.</div></div>
    <p class="clause"><span class="clause-label">5.3</span>Kewajiban PENERIMA GADAI:</p>
    <div class="letter-list"><div>a. Menjaga barang jaminan dengan baik.</div><div>b. Memberikan informasi yang jelas mengenai tagihan.</div></div>
    <p class="clause"><span class="clause-label">5.4</span>Hak PENERIMA GADAI:</p>
    <div class="letter-list"><div>a. Menerima pelunasan pokok dan ujrah.</div><div>b. Menahan barang jaminan hingga pinjaman dilunasi.</div><div>c. Melelang barang jaminan jika PEMINJAM tidak melunasi kewajibannya setelah melewati batas waktu sebagaimana dimaksud dalam Pasal 4.5.</div></div>

    <div class="page-break"></div>

    <div class="section-heading">PASAL 6<br>MEKANISME ANGSURAN DAN PELUNASAN</div>
    <p class="clause"><span class="clause-label">6.1</span>PEMINJAM dapat mengangsur pokok pinjaman dengan nominal berapa pun sebelum jatuh tempo atau paling lambat 7 (tujuh) hari setelah jatuh tempo.</p>
    <p class="clause"><span class="clause-label">6.2</span>Pelunasan dapat dilakukan kapan saja, termasuk setelah melewati jatuh tempo lebih dari 7 (tujuh) hari, sebelum barang jaminan terjual.</p>
    <p class="clause"><span class="clause-label">6.3</span>Setelah pelunasan, PENERIMA GADAI wajib mengembalikan barang jaminan dalam kondisi yang sama dengan saat diterima.</p>
    <p class="clause"><span class="clause-label">6.4</span>Setiap pembayaran akan diberikan tanda bukti berupa nota.</p>

    <div class="small-dash">---</div>

    <div class="section-heading">PASAL 7<br>WANPRESTASI DAN EKSEKUSI JAMINAN (LELANG SYARIAH)</div>
    <p class="clause"><span class="clause-label">7.1</span>PEMINJAM dinyatakan wanprestasi apabila tidak melunasi pokok dan ujrah hingga 7 (tujuh) hari setelah jatuh tempo.</p>
    <p class="clause"><span class="clause-label">7.2</span>Sebelum eksekusi, PENERIMA GADAI wajib memberi peringatan tertulis sebanyak 2 (dua) kali sebelum hari ke-8 sejak jatuh tempo.</p>
    <p class="clause"><span class="clause-label">7.3</span>Apabila tetap tidak ada penyelesaian, pada hari ke-8 setelah jatuh tempo, PENERIMA GADAI berhak menjual barang jaminan melalui lelang syariah atau penjualan kepada pihak ketiga secara transparan dan dengan harga wajar.</p>
    <p class="clause"><span class="clause-label">7.4</span>Hasil penjualan akan digunakan untuk:</p>
    <div class="letter-list"><div>a. Melunasi pokok pinjaman (qard).</div><div>b. Membayar ujrah yang terutang.</div><div>c. Membayar biaya lelang.</div></div>
    <p class="clause"><span class="clause-label">7.5</span>Kelebihan hasil penjualan (bila ada) wajib diserahkan kepada PEMINJAM paling lambat 7 (tujuh) hari setelah barang terjual.</p>
    <p class="clause"><span class="clause-label">7.6</span>Kekurangan hasil penjualan (bila hasil kurang dari kewajiban) tidak dapat ditagih lebih lanjut kepada PEMINJAM, sesuai prinsip qard (pinjaman kebajikan) yang tidak boleh meminta tambahan. Namun, secara sukarela dan etika, PEMINJAM dapat melunasi kekurangan tersebut.</p>

    <div class="small-dash">---</div>

    <div class="section-heading">PASAL 8<br>KEADAAN DARURAT (FORCE MAJEURE)</div>
    <p class="clause"><span class="clause-label">8.1</span>Jika barang jaminan rusak berat atau musnah di luar kuasa PENERIMA GADAI (bencana alam, kebakaran, pencurian dengan unsur force majeure), PENERIMA GADAI tidak bertanggung jawab, tetapi wajib memberikan bukti yang sah.</p>
    <p class="clause"><span class="clause-label">8.2</span>Dalam kondisi tersebut, PEMINJAM tetap wajib melunasi pokok pinjaman, namun ujrah dihapuskan sejak kejadian.</p>

    <div class="page-break"></div>

    <div class="section-heading">PASAL 9<br>PENYELESAIAN PERSELISIHAN</div>
    <p class="clause"><span class="clause-label">9.1</span>Setiap perselisihan diselesaikan secara musyawarah untuk mufakat.</p>
    <p class="clause"><span class="clause-label">9.2</span>Apabila tidak tercapai kesepakatan, para pihak sepakat memilih penyelesaian melalui:</p>
    <div class="letter-list"><div>a. Badan Arbitrase Syariah, atau</div><div>b. Pengadilan Agama setempat.</div></div>

    <div class="small-dash">---</div>

    <div class="section-heading">PASAL 10<br>PENUTUP</div>
    <p class="clause"><span class="clause-label">10.1</span>Akad ini dibuat bermeterai dan mempunyai kekuatan hukum.</p>
    <p class="clause"><span class="clause-label">10.2</span>Akad ini dibuat dengan penuh kesadaran dan tanpa paksaan dari pihak mana pun.</p>

    <div class="small-dash">---</div>

    <div class="section-heading">RINGKASAN BARANG JAMINAN</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th>Barang Jaminan</th>
                <th style="width: 95px;">Taksiran</th>
                <th style="width: 95px;">Pinjaman</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->detailTransaksi as $index => $detail)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $formatBarang($detail) }}</td>
                    <td class="right">{{ number_format($detail->taksiran_item, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($detail->pinjaman_item, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="right bold">Total</td>
                <td class="right bold">{{ number_format($transaksi->total_taksiran, 0, ',', '.') }}</td>
                <td class="right bold">{{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 15px;">{{ $lokasiTtd }}, {{ $tanggalAkad }}</p>

    <table class="signature">
        <tr>
            <td>
                <div>Pihak Pertama (Peminjam),</div>
                <div class="sign-space"></div>
                <div class="sign-line">{{ $nasabah->nama ?? '-' }}</div>
            </td>
            <td>
                <div>Pihak Kedua (Penerima Gadai),</div>
                <div style="margin-top: 16px;">Materai Rp10.000</div>
                <div class="sign-space" style="height: 56px;"></div>
                <div class="sign-line">{{ $petugas }}</div>
            </td>
        </tr>
    </table>

    <table class="signature witness">
        <tr>
            <td>
                <div>Saksi 1,</div>
                <div class="sign-space" style="height: 62px;"></div>
                <div class="sign-line"></div>
            </td>
            <td>
                <div>Saksi 2,</div>
                <div class="sign-space" style="height: 62px;"></div>
                <div class="sign-line"></div>
            </td>
        </tr>
    </table>
</body>
</html>

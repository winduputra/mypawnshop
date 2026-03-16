<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kontrak Gadai - {{ $transaksi->no_transaksi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #1a1a1a;
            padding: 30px 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 10px;
            color: #555;
        }
        .contract-no {
            text-align: center;
            margin: 15px 0;
            font-size: 12px;
        }
        .contract-no strong {
            font-size: 13px;
        }
        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin: 15px 0 8px;
            padding: 5px 10px;
            background: #f0f0f0;
            border-left: 4px solid #333;
        }
        .party-info {
            margin-left: 20px;
            margin-bottom: 10px;
        }
        .party-info table {
            width: 100%;
        }
        .party-info td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .party-info td:first-child {
            width: 140px;
            font-weight: bold;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table.items th, table.items td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }
        table.items th {
            background: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.items td { font-size: 10px; }
        table.items .right { text-align: right; }
        .total-row {
            font-weight: bold;
            background: #f5f5f5;
        }
        .terms {
            margin: 15px 0;
            font-size: 10px;
            line-height: 1.8;
        }
        .terms ol {
            padding-left: 20px;
        }
        .terms li {
            margin-bottom: 5px;
        }
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-section table {
            width: 100%;
        }
        .signature-section td {
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }
        .sign-line {
            border-bottom: 1px solid #333;
            margin: 60px auto 5px;
            width: 180px;
        }
        .sign-name {
            font-weight: bold;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>MyPawnShop</h1>
        <h2>SURAT KONTRAK GADAI (RAHN)</h2>
        <p>Jl. Contoh Alamat No. 123, Kota, Indonesia | Telp: (021) 123-4567</p>
    </div>

    <!-- Contract Number -->
    <div class="contract-no">
        No. Kontrak: <strong>{{ $transaksi->no_transaksi }}</strong><br>
        Tanggal: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}
    </div>

    <!-- Pihak Pertama -->
    <div class="section-title">PIHAK PERTAMA (Penerima Gadai)</div>
    <div class="party-info">
        <table>
            <tr><td>Nama</td><td>: MyPawnShop</td></tr>
            <tr><td>Diwakili oleh</td><td>: {{ $transaksi->user->name }}</td></tr>
            <tr><td>Jabatan</td><td>: Kasir</td></tr>
        </table>
        <p style="margin-top: 5px; font-size: 10px; color: #555;">Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>
    </div>

    <!-- Pihak Kedua -->
    <div class="section-title">PIHAK KEDUA (Pemberi Gadai / Nasabah)</div>
    <div class="party-info">
        <table>
            <tr><td>Nama</td><td>: {{ $transaksi->nasabah->nama }}</td></tr>
            <tr><td>NIK</td><td>: {{ $transaksi->nasabah->nik }}</td></tr>
            <tr><td>Alamat</td><td>: {{ $transaksi->nasabah->alamat }}</td></tr>
            <tr><td>Telepon</td><td>: {{ $transaksi->nasabah->telepon }}</td></tr>
            @if($transaksi->nasabah->email)
            <tr><td>Email</td><td>: {{ $transaksi->nasabah->email }}</td></tr>
            @endif
        </table>
        <p style="margin-top: 5px; font-size: 10px; color: #555;">Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
    </div>

    <!-- Barang Jaminan -->
    <div class="section-title">BARANG JAMINAN (MARHUN)</div>
    <table class="items">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th class="right">Taksiran (Rp)</th>
                <th class="right">Pinjaman (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->detailTransaksi as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->barang->nama_barang }}</td>
                <td style="text-transform: capitalize;">{{ $detail->barang->kategori }}</td>
                <td class="right">{{ number_format($detail->taksiran_item, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($detail->pinjaman_item, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Total</td>
                <td class="right">{{ number_format($transaksi->total_taksiran, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Detail Transaksi -->
    <div class="section-title">RINCIAN TRANSAKSI</div>
    <div class="party-info">
        <table>
            <tr><td>Total Pinjaman (Marhun Bih)</td><td>: Rp {{ number_format($transaksi->total_pinjaman, 0, ',', '.') }}</td></tr>
            <tr><td>Biaya Administrasi</td><td>: Rp {{ number_format($transaksi->biaya_admin, 0, ',', '.') }}</td></tr>
            <tr><td>Ujrah per 30 Hari</td><td>: Rp {{ number_format($transaksi->ujrah_per_30hari, 0, ',', '.') }}</td></tr>
            <tr><td>Tenor</td><td>: {{ $transaksi->tenor_hari }} Hari</td></tr>
            <tr><td>Tanggal Jatuh Tempo</td><td>: {{ \Carbon\Carbon::parse($transaksi->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</td></tr>
            <tr><td>Batas Waktu Lelang</td><td>: {{ \Carbon\Carbon::parse($transaksi->tanggal_batas_lelang)->translatedFormat('d F Y') }}</td></tr>
        </table>
    </div>

    <!-- Syarat & Ketentuan -->
    <div class="section-title">SYARAT DAN KETENTUAN</div>
    <div class="terms">
        <ol>
            <li>PIHAK KEDUA menyerahkan barang jaminan (Marhun) kepada PIHAK PERTAMA sebagai jaminan atas pinjaman (Marhun Bih) yang diterima.</li>
            <li>PIHAK KEDUA wajib membayar biaya penitipan (Ujrah) sebagai imbalan atas jasa penyimpanan dan pemeliharaan barang jaminan sesuai prinsip syariah.</li>
            <li>PIHAK KEDUA wajib melunasi pinjaman sebelum atau pada tanggal jatuh tempo yang telah ditetapkan.</li>
            <li>Apabila PIHAK KEDUA tidak melunasi pinjaman hingga tanggal jatuh tempo, PIHAK KEDUA masih diberikan masa tenggang (grace period) selama 7 hari.</li>
            <li>Apabila setelah masa tenggang PIHAK KEDUA tetap tidak melunasi, maka PIHAK PERTAMA berhak melelang barang jaminan sesuai ketentuan yang berlaku.</li>
            <li>Hasil lelang setelah dikurangi biaya dan pinjaman pokok, sisanya akan dikembalikan kepada PIHAK KEDUA.</li>
            <li>Akad ini dilaksanakan sesuai dengan prinsip-prinsip syariah Islam.</li>
        </ol>
    </div>

    <!-- Tanda tangan -->
    <div class="signature-section">
        <table>
            <tr>
                <td>
                    <p>PIHAK PERTAMA</p>
                    <p style="font-size: 10px; color: #888;">(Penerima Gadai)</p>
                    <div class="sign-line"></div>
                    <p class="sign-name">{{ $transaksi->user->name }}</p>
                    <p style="font-size: 10px; color: #888;">Kasir MyPawnShop</p>
                </td>
                <td>
                    <p>PIHAK KEDUA</p>
                    <p style="font-size: 10px; color: #888;">(Pemberi Gadai / Nasabah)</p>
                    <div class="sign-line"></div>
                    <p class="sign-name">{{ $transaksi->nasabah->nama }}</p>
                    <p style="font-size: 10px; color: #888;">NIK: {{ $transaksi->nasabah->nik }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat secara elektronik oleh sistem MyPawnShop dan sah tanpa memerlukan stempel basah.</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>

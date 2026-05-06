<?php
$dirs = ['nasabah', 'barang', 'transaksi', 'lelang', 'cabang', 'pengaturan', 'laporan', 'kasir', '.'];
foreach ($dirs as $dir) {
    $path = __DIR__ . "/resources/views/" . ($dir === '.' ? '' : "$dir/");
    foreach (glob($path . "*.blade.php") as $f) {
        $c = file_get_contents($f);
        $c = str_replace('glass-card', 'bg-white rounded-xl shadow-sm border border-slate-200', $c);
        $c = str_replace('btn-gradient', 'bg-[#cf9e50] hover:bg-[#b48842] text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition-all', $c);
        $c = preg_replace('/\bglass\b/', 'bg-white border border-slate-200', $c);
        file_put_contents($f, $c);
    }
}
echo "Done replacing custom classes.\n";

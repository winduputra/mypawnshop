<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_rahn', function (Blueprint $table) {
            $table->decimal('biaya_penitipan', 15, 2)->default(0)->after('biaya_admin');
            $table->enum('metode_pembayaran', ['bayar_dimuka', 'potong_pinjaman'])->default('potong_pinjaman')->after('biaya_penitipan');
            $table->decimal('sisa_pinjaman', 15, 2)->default(0)->after('total_pinjaman');
        });

        // Set sisa_pinjaman = total_pinjaman for existing active transactions
        \DB::table('transaksi_rahn')
            ->whereIn('status', ['aktif', 'diperpanjang'])
            ->update(['sisa_pinjaman' => \DB::raw('total_pinjaman')]);
    }

    public function down(): void
    {
        Schema::table('transaksi_rahn', function (Blueprint $table) {
            $table->dropColumn(['biaya_penitipan', 'metode_pembayaran', 'sisa_pinjaman']);
        });
    }
};

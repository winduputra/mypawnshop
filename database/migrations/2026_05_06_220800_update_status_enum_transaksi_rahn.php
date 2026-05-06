<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN status ENUM('draft','aktif','diperpanjang','lunas','lelang','ditolak') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN status ENUM('aktif','diperpanjang','lunas','lelang') DEFAULT 'aktif'");
    }
};

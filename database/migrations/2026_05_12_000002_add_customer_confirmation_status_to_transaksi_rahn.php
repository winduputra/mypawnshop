<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN status_approval ENUM('draft','dikirim','pending','menunggu_persetujuan_nasabah','disetujui','ditolak') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN status_approval ENUM('draft','dikirim','pending','disetujui','ditolak') DEFAULT 'draft'");
    }
};

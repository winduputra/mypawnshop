<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN metode_pembayaran ENUM('bayar_dimuka','potong_pinjaman','bayar_pelunasan') DEFAULT 'potong_pinjaman'");

        Schema::create('transaksi_rahn_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_rahn_id')->constrained('transaksi_rahn')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('status_approval')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_rahn_histories');
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN metode_pembayaran ENUM('bayar_dimuka','potong_pinjaman') DEFAULT 'potong_pinjaman'");
    }
};

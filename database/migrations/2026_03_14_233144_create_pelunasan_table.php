<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelunasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_rahn_id')->constrained('transaksi_rahn')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->date('tanggal_pelunasan');
            $table->decimal('total_pinjaman', 15, 2);
            $table->decimal('total_ujrah', 15, 2);
            $table->decimal('total_bayar', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelunasan');
    }
};

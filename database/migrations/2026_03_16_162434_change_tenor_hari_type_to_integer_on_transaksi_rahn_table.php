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
        Schema::table('transaksi_rahn', function (Blueprint $table) {
            $table->integer('tenor_hari')->default(30)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_rahn', function (Blueprint $table) {
            $table->enum('tenor_hari', ['30', '60', '90'])->change();
        });
    }
};

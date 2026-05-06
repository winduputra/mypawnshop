<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('merk_type')->nullable()->after('kategori');
            $table->string('nomor_seri')->nullable()->after('merk_type');
            $table->text('spesifikasi')->nullable()->after('nomor_seri');
            $table->json('kelengkapan')->nullable()->after('spesifikasi');
            $table->text('kondisi_fisik')->nullable()->after('kelengkapan');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn(['merk_type', 'nomor_seri', 'spesifikasi', 'kelengkapan', 'kondisi_fisik']);
        });
    }
};

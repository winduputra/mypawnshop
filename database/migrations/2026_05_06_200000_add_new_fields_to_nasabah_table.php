<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            $table->text('alamat_domisili')->nullable()->after('alamat');
            $table->string('no_wa')->nullable()->after('telepon');
            $table->string('nama_pemilik_rekening')->nullable()->after('no_rekening');
            $table->string('nama_ibu_kandung')->nullable()->after('nama_pemilik_rekening');
            $table->string('pekerjaan')->nullable()->after('nama_ibu_kandung');
            $table->enum('status_pernikahan', ['Menikah', 'Belum Menikah', 'Duda/Janda'])->nullable()->after('pekerjaan');
        });
    }

    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_domisili',
                'no_wa',
                'nama_pemilik_rekening',
                'nama_ibu_kandung',
                'pekerjaan',
                'status_pernikahan',
            ]);
        });
    }
};

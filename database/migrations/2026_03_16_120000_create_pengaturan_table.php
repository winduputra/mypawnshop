<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('group')->default('umum');
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        DB::table('pengaturan')->insert([
            ['key' => 'biaya_admin', 'value' => '10000', 'label' => 'Biaya Administrasi (Rp)', 'group' => 'biaya', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'biaya_penitipan', 'value' => '5000', 'label' => 'Biaya Penitipan (Rp)', 'group' => 'biaya', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'persentase_emas', 'value' => '85', 'label' => 'Persentase Maks. Pinjaman Emas (%)', 'group' => 'persentase', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'persentase_elektronik', 'value' => '70', 'label' => 'Persentase Maks. Pinjaman Elektronik (%)', 'group' => 'persentase', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'persentase_kendaraan', 'value' => '75', 'label' => 'Persentase Maks. Pinjaman Kendaraan (%)', 'group' => 'persentase', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};

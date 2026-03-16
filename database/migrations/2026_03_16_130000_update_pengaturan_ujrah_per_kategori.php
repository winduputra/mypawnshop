<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Replace single biaya_penitipan with per-category ujrah (flat Rp per 30 days)
        DB::table('pengaturan')->where('key', 'biaya_penitipan')->delete();

        DB::table('pengaturan')->insert([
            ['key' => 'ujrah_emas', 'value' => '50000', 'label' => 'Biaya Penitipan Emas (Rp/30 hari)', 'group' => 'ujrah', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'ujrah_elektronik', 'value' => '75000', 'label' => 'Biaya Penitipan Elektronik (Rp/30 hari)', 'group' => 'ujrah', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'ujrah_kendaraan', 'value' => '100000', 'label' => 'Biaya Penitipan Kendaraan (Rp/30 hari)', 'group' => 'ujrah', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('pengaturan')->whereIn('key', ['ujrah_emas', 'ujrah_elektronik', 'ujrah_kendaraan'])->delete();

        $now = now();
        DB::table('pengaturan')->insert([
            ['key' => 'biaya_penitipan', 'value' => '5000', 'label' => 'Biaya Penitipan (Rp)', 'group' => 'biaya', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
};

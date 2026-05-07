<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        DB::table('pengaturan')->insert([
            ['key' => 'no_telepon_cs', 'value' => '6281234567890', 'label' => 'No. Telepon CS (format 62xxx)', 'group' => 'umum', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('pengaturan')->where('key', 'no_telepon_cs')->delete();
    }
};

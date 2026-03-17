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
        Schema::table('lelang', function (Blueprint $table) {
            $table->decimal('biaya_lelang', 15, 2)->default(0)->after('harga_lelang');
            $table->decimal('kerugian', 15, 2)->default(0)->after('sisa_untuk_nasabah');
            $table->decimal('sisa_pinjaman', 15, 2)->default(0)->after('kerugian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lelang', function (Blueprint $table) {
            $table->dropColumn(['biaya_lelang', 'kerugian', 'sisa_pinjaman']);
        });
    }
};

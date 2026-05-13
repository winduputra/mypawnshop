<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lelang', function (Blueprint $table) {
            $table->text('alamat_pembeli')->nullable()->after('pembeli');
        });
    }

    public function down(): void
    {
        Schema::table('lelang', function (Blueprint $table) {
            $table->dropColumn('alamat_pembeli');
        });
    }
};

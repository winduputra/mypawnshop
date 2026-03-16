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
        Schema::table('nasabah', function (Blueprint $table) {
            $table->string('nama_bank')->nullable()->after('email');
            $table->string('no_rekening')->nullable()->after('nama_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'no_rekening']);
        });
    }
};

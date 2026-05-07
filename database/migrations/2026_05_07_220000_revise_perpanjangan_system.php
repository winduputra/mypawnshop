<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change tambahan_tenor_hari from ENUM to INTEGER to support 50-day overdue extensions
        DB::statement("ALTER TABLE perpanjangan MODIFY COLUMN tambahan_tenor_hari INTEGER NOT NULL DEFAULT 30");

        Schema::table('perpanjangan', function (Blueprint $table) {
            $table->boolean('is_overdue_extension')->default(false)->after('ujrah_dibayar');
            $table->string('no_nota')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('perpanjangan', function (Blueprint $table) {
            $table->dropColumn(['is_overdue_extension', 'no_nota']);
        });

        DB::statement("ALTER TABLE perpanjangan MODIFY COLUMN tambahan_tenor_hari ENUM('30','60','90') NOT NULL DEFAULT '30'");
    }
};

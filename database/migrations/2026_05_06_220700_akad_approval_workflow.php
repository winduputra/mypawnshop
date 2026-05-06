<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'owner' role to users enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kasir','owner') DEFAULT 'kasir'");

        // 2. Add approval workflow columns to transaksi_rahn
        Schema::table('transaksi_rahn', function (Blueprint $table) {
            $table->string('no_register_akad')->nullable()->after('no_transaksi');
            $table->enum('status_approval', ['draft', 'dikirim', 'pending', 'disetujui', 'ditolak'])->default('draft')->after('status');
            $table->text('catatan_admin')->nullable()->after('status_approval');
            $table->decimal('taksiran_final', 15, 2)->nullable()->after('catatan_admin');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('taksiran_final');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        // 3. Update existing transaksi to disetujui (backward compat)
        DB::table('transaksi_rahn')->whereIn('status', ['aktif', 'diperpanjang', 'lunas', 'lelang'])
            ->update(['status_approval' => 'disetujui']);

        // 4. Add new pengaturan rows
        $now = now();
        // Remove old single biaya_admin
        DB::table('pengaturan')->where('key', 'biaya_admin')->delete();

        DB::table('pengaturan')->insert([
            ['key' => 'biaya_admin_elektronik', 'value' => '35000', 'label' => 'Biaya Admin Elektronik (Rp)', 'group' => 'biaya_admin', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'biaya_admin_emas', 'value' => '25000', 'label' => 'Biaya Admin Emas (Rp)', 'group' => 'biaya_admin', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'biaya_admin_kendaraan', 'value' => '50000', 'label' => 'Biaya Admin Kendaraan (Rp)', 'group' => 'biaya_admin', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'ijarah_persen', 'value' => '2', 'label' => 'Persentase Ijarah/Penitipan (% dari Taksiran per 30 hari)', 'group' => 'ijarah', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 5. Seed owner user
        DB::table('users')->insert([
            'name' => 'Owner MyPawnShop',
            'email' => 'owner@mypawnshop.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'owner@mypawnshop.com')->delete();
        DB::table('pengaturan')->whereIn('key', ['biaya_admin_elektronik', 'biaya_admin_emas', 'biaya_admin_kendaraan', 'ijarah_persen'])->delete();

        $now = now();
        DB::table('pengaturan')->insert([
            'key' => 'biaya_admin', 'value' => '10000', 'label' => 'Biaya Administrasi (Rp)', 'group' => 'biaya', 'created_at' => $now, 'updated_at' => $now,
        ]);

        Schema::table('transaksi_rahn', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['no_register_akad', 'status_approval', 'catatan_admin', 'taksiran_final', 'approved_by', 'approved_at']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kasir') DEFAULT 'kasir'");
    }
};

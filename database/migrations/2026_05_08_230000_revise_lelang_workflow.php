<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'superadmin' role to users enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kasir','owner','superadmin') DEFAULT 'kasir'");

        // 2. Seed superadmin user
        $now = now();
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@mypawnshop.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Add new columns to lelang table for workflow
        Schema::table('lelang', function (Blueprint $table) {
            $table->string('no_lelang')->nullable()->unique()->after('id');
            $table->enum('status_lelang', ['draft', 'pending', 'aktif', 'terjual', 'dibatalkan'])
                  ->default('draft')->after('tanggal_lelang');
            $table->decimal('ijarah', 15, 2)->default(0)->after('biaya_lelang');
            $table->date('tanggal_terjual')->nullable()->after('status_lelang');
            $table->decimal('sisa_dana_kembali', 15, 2)->default(0)->after('sisa_untuk_nasabah');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('catatan_owner')->nullable()->after('catatan');
        });

        // 4. Make pembeli nullable (filled only when sold)
        Schema::table('lelang', function (Blueprint $table) {
            $table->string('pembeli')->nullable()->change();
            $table->string('telepon_pembeli')->nullable()->change();
            $table->date('tanggal_lelang')->nullable()->change();
        });

        // 5. Update transaksi_rahn status enum to include new lelang states
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN status ENUM('draft','aktif','diperpanjang','lunas','lelang','lelang_pending','lelang_aktif','lelang_terjual','ditolak') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'superadmin@mypawnshop.com')->delete();

        Schema::table('lelang', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'no_lelang', 'status_lelang', 'ijarah', 'tanggal_terjual',
                'sisa_dana_kembali', 'approved_by', 'approved_at', 'catatan_owner',
            ]);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kasir','owner') DEFAULT 'kasir'");
        DB::statement("ALTER TABLE transaksi_rahn MODIFY COLUMN status ENUM('draft','aktif','diperpanjang','lunas','lelang','ditolak') DEFAULT 'draft'");
    }
};

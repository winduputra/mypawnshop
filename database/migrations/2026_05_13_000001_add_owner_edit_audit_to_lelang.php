<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lelang', function (Blueprint $table) {
            $table->foreignId('owner_edited_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('owner_edited_at')->nullable()->after('owner_edited_by');
            $table->unsignedInteger('owner_edit_count')->default(0)->after('owner_edited_at');
            $table->json('owner_edit_log')->nullable()->after('owner_edit_count');
        });
    }

    public function down(): void
    {
        Schema::table('lelang', function (Blueprint $table) {
            $table->dropForeign(['owner_edited_by']);
            $table->dropColumn(['owner_edited_by', 'owner_edited_at', 'owner_edit_count', 'owner_edit_log']);
        });
    }
};

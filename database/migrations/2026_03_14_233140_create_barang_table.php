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
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabah')->onDelete('cascade');
            $table->string('nama_barang');
            $table->enum('kategori', ['emas', 'elektronik', 'kendaraan']);
            $table->text('deskripsi')->nullable();
            $table->decimal('berat', 8, 2)->nullable(); // grams
            $table->decimal('taksiran', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};

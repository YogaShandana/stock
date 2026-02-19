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
        Schema::create('rekapans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->string('code');
            $table->integer('box_masuk')->default(0);
            $table->decimal('kgs_masuk', 10, 2)->default(0);
            $table->integer('box_keluar')->default(0);
            $table->decimal('kgs_keluar', 10, 2)->default(0);
            $table->decimal('stok_box', 10, 2)->storedAs('box_masuk - box_keluar');
            $table->decimal('stok_kgs', 10, 2)->storedAs('kgs_masuk - kgs_keluar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekapans');
    }
};

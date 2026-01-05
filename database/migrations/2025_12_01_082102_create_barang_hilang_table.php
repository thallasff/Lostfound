<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('barang_hilang', function (Blueprint $table) {
        $table->id('barang_id');
        $table->unsignedBigInteger('pelapor_id');
        $table->unsignedBigInteger('admin_id')->nullable();

        $table->string('nama_barang');
        $table->string('lokasi');
        $table->text('deskripsi')->nullable();
        $table->date('tanggal_hilang')->nullable();

        $table->decimal('longitude', 10, 7)->nullable();
        $table->decimal('latitude', 10, 7)->nullable();

        $table->string('status')->default('belum ditemukan');

        $table->timestamps();

        // foreign keys
        $table->foreign('pelapor_id')->references('pelapor_id')->on('pelapor')->onDelete('cascade');
        $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('set null');
    });
}

public function down()
{
    Schema::dropIfExists('barang_hilang');
}

};

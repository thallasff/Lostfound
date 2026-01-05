<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('laporan', function (Blueprint $table) {
        $table->id('laporan_id');

        $table->unsignedBigInteger('pelapor_id');
        $table->unsignedBigInteger('admin_id')->nullable();
        $table->unsignedBigInteger('barang_id');

        $table->dateTime('tanggal_laporan');
        $table->string('status_verifikasi')->default('pending');
        $table->text('deskripsi')->nullable();

        $table->timestamps();

        // FK
        $table->foreign('pelapor_id')->references('pelapor_id')->on('pelapor')->onDelete('cascade');
        $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('set null');
        $table->foreign('barang_id')->references('barang_id')->on('barang_hilang')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('laporan');
}

};

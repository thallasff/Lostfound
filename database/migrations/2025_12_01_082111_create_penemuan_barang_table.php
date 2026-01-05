<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('penemuan_barang', function (Blueprint $table) {
        $table->id('penemuan_id');

        $table->unsignedBigInteger('barang_id');
        $table->string('nama_penemu');
        $table->string('kondisi_barang')->nullable();
        $table->string('lokasi_penemuan');
        
        $table->decimal('longitude_penemuan', 10, 7)->nullable();
        $table->decimal('latitude_penemuan', 10, 7)->nullable();

        $table->dateTime('waktu_penemuan');
        $table->string('status_verifikasi')->default('pending');

        $table->timestamps();

        // FK
        $table->foreign('barang_id')->references('barang_id')->on('barang_hilang')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('penemuan_barang');
}

};

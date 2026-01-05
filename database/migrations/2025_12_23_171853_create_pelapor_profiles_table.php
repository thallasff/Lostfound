<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('pelapor_profiles', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('pelapor_id')->unique();

        $table->string('nama_lengkap')->nullable();
        $table->string('status')->nullable();   // Mahasiswa / Dosen / Staff (atau bebas dulu)
        $table->string('fakultas')->nullable();
        $table->string('jurusan')->nullable();
        $table->string('no_ponsel')->nullable();
        $table->string('foto_profil')->nullable(); // simpan path

        $table->timestamps();

        $table->foreign('pelapor_id')
            ->references('pelapor_id')
            ->on('pelapor')
            ->onDelete('cascade');
    });
}

};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('barang_hilang');

        Schema::create('barang_hilang', function (Blueprint $table) {
            $table->id('barang_id');

            $table->unsignedBigInteger('pelapor_id');
            $table->string('username_pelapor', 100);

            // 1) Informasi barang
            $table->string('nama_barang', 255);
            $table->string('kategori', 50);
            $table->text('deskripsi_singkat')->nullable();

            // foto barang opsional max 3
            $table->string('foto_barang_1')->nullable();
            $table->string('foto_barang_2')->nullable();
            $table->string('foto_barang_3')->nullable();

            // 2) Detail tambahan (opsional)
            $table->string('warna', 50)->nullable();
            $table->string('merek', 80)->nullable();
            $table->enum('kondisi_terakhir', ['baik', 'rusak'])->nullable();

            // 3) Lokasi terakhir terlihat (map)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // 4) Perkiraan waktu hilang
            $table->dateTime('terakhir_terlihat_at');       // wajib
            $table->dateTime('rentang_mulai')->nullable();  // opsional
            $table->dateTime('rentang_selesai')->nullable();// opsional

            // 5) Catatan tambahan (opsional)
            $table->text('catatan_tambahan')->nullable();

            // status default
            $table->enum('status', ['belum ditemukan', 'ditemukan'])->default('belum ditemukan');

            $table->timestamps();

            // FK (sesuaikan kalau nama tabel user kamu beda)
            $table->foreign('pelapor_id')->references('pelapor_id')->on('pelapor')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_hilang');
    }
};

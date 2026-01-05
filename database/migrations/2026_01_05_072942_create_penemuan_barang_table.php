<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penemuan_barang', function (Blueprint $table) {
            $table->bigIncrements('penemuan_id');

            // pelapor dari akun (auto)
            $table->unsignedBigInteger('pelapor_id'); // FK ke pelapor.id (atau pelapor_id, kamu sesuaikan)
            $table->string('username_penemu', 100);   // snapshot username saat submit

            // Informasi barang (1 section aja)
            $table->string('nama_barang', 255);
            $table->string('kategori', 50);
            $table->text('deskripsi_singkat')->nullable();

            // Foto barang (1 wajib, max 3)
            $table->string('foto_barang_1');
            $table->string('foto_barang_2')->nullable();
            $table->string('foto_barang_3')->nullable();

            // Detail tambahan (opsional)
            $table->string('warna', 50)->nullable();
            $table->string('merek', 80)->nullable();
            $table->enum('kondisi_barang', ['baik', 'rusak_ringan', 'rusak_berat'])->nullable();

            // Lokasi penemuan (tanpa field teks, klik map)
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Waktu ditemukan (default saat submit, boleh diedit)
            $table->dateTime('waktu_ditemukan')->useCurrent();

            // Status verifikasi
            $table->enum('status_verifikasi', ['belum diverifikasi', 'disetujui', 'ditolak'])
                  ->default('belum diverifikasi');

            $table->timestamps();

            $table->index('pelapor_id');
        });

        // FK (sesuaikan PK pelapor kamu!)
        Schema::table('penemuan_barang', function (Blueprint $table) {
            $table->foreign('pelapor_id')
                ->references('pelapor_id') // kalau PK pelapor kamu bukan 'id', ganti di sini
                ->on('pelapor')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penemuan_barang');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengambilan_barang', function (Blueprint $table) {
            $table->id();

            // Relasi ke chat thread (kita index dulu, FK nanti kalau nama tabel thread sudah fix)
            $table->unsignedBigInteger('thread_id')->index();

            // Item yang diklaim: bisa dari temuan atau hilang
            $table->enum('item_type', ['temuan', 'hilang'])->index();
            $table->unsignedBigInteger('item_id')->index();

            // Pihak terkait
            $table->unsignedBigInteger('pemilik_id')->index(); // user yang kehilangan
            $table->unsignedBigInteger('penemu_id')->index();  // user yang menemukan

            // Status flow
            $table->enum('status', [
                'menunggu_pemilik',
                'menunggu_konfirmasi_penemu',
                'menunggu_bukti_penyerahan',
                'ditolak',
                'selesai',
            ])->default('menunggu_pemilik')->index();

            // Isi form pemilik (ringkas + fleksibel)
            $table->text('jawaban_pemilik')->nullable();

            // Bukti dari pemilik (opsional)
            $table->string('bukti_pemilik_1')->nullable();
            $table->string('bukti_pemilik_2')->nullable();

            // Bukti penyerahan dari penemu (wajib saat selesai)
            $table->string('bukti_penyerahan_1')->nullable();
            $table->string('bukti_penyerahan_2')->nullable();
            $table->string('bukti_penyerahan_3')->nullable();

            $table->timestamps();

            // Biar satu item tidak dibikin klaim berkali-kali dalam 1 thread (opsional tapi bagus)
            $table->unique(['thread_id', 'item_type', 'item_id'], 'uniq_thread_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengambilan_barang');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penemuan_barang', function (Blueprint $table) {
            $table->string('foto_barang')->nullable()->after('lokasi_penemuan');
            $table->string('foto_tempat')->nullable()->after('foto_barang');
        });
    }

    public function down(): void
    {
        Schema::table('penemuan_barang', function (Blueprint $table) {
            $table->dropColumn(['foto_barang', 'foto_tempat']);
        });
    }
};

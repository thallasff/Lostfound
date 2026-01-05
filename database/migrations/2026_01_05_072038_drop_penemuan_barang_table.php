<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('penemuan_barang');
    }

    public function down(): void
    {
        // kosongin aja, nanti table dibuat di migration create berikutnya
    }
};

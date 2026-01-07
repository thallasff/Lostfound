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
    Schema::table('claims', function (Blueprint $table) {
        if (Schema::hasColumn('claims', 'claimant_proof_photo')) {
            $table->dropColumn('claimant_proof_photo');
        }
    });
}

public function down(): void
{
    Schema::table('claims', function (Blueprint $table) {
        $table->string('claimant_proof_photo')->nullable();
    });
}
};

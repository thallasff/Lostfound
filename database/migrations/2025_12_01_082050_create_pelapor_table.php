<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{
    Schema::create('pelapor', function (Blueprint $table) {
        $table->id('pelapor_id');
        $table->string('username')->unique();
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamp('email_verified_at')->nullable()->nullable();
    });
}

public function down()
{
    Schema::table('pelapor', function (Blueprint $table) {
        $table->dropColumn('email_verified_at');
    });
}
};

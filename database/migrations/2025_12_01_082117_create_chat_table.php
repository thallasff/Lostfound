    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
    public function up()
    {
        Schema::create('chat', function (Blueprint $table) {
            $table->id('chat_id');

            $table->unsignedBigInteger('pelapor_id');
            $table->unsignedBigInteger('admin_id');

            $table->text('pesan');
            $table->dateTime('waktu_kirim');

            $table->timestamps();

            // FK
            $table->foreign('pelapor_id')
                ->references('pelapor_id')->on('pelapor')
                ->onDelete('cascade');

            $table->foreign('admin_id')
                ->references('admin_id')->on('admin')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat');
    }

    };

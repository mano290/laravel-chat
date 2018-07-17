<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    use SoftDeletes;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');

            $table->string('uid', 20);
            $table->enum("type", [
                \App\Enum\RoomType::ROOM_CHAT,
                \App\Enum\RoomType::ROOM_GROUP,
            ])->default(\App\Enum\RoomType::ROOM_CHAT);

            $table->unsignedInteger("last_message_id")->nullable();

            $table->longText("data")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['id', 'type', "uid"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}

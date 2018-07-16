<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_messages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger("room_id");
            $table->foreign('room_id')->references('id')->on('rooms');

            $table->longText("data");
            $table->enum("type", [
                \App\Enum\MessagesType::FILE,
                \App\Enum\MessagesType::IMAGE,
                \App\Enum\MessagesType::INFO,
                \App\Enum\MessagesType::MESSAGE,
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_messages');
    }
}

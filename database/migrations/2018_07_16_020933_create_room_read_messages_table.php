<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomReadMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_read_messages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger("room_message_id");
            $table->foreign('room_message_id')->references('id')->on('room_messages');

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
        Schema::dropIfExists('room_read_messages');
    }
}

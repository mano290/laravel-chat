<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_users', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger("room_id");
            $table->foreign('room_id')->references('id')->on('rooms');

            $table->enum("is_admin", [
                \App\Enum\RoomType::USER_ADMIN,
                \App\Enum\RoomType::USER_NORMAL
            ])->default(\App\Enum\RoomType::USER_NORMAL);

            $table->softDeletes();
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
        Schema::dropIfExists('room_users');
    }
}

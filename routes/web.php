<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', 'ChatsController@index');

Route::get("/home", "HomeController@index");

Route::get('/messages', 'ChatsController@fetchMessages')->name("chat.get.fetch-messages");

Route::post('/messages', 'ChatsController@sendMessage')->name("chat.post.new-message");

Route::get('/create-chat', 'ChatsController@createNewChat')->name('chat.create-new-chat');

Route::group(["prefix" => "app/user", "namespace" => "Api"], function () {

    Route::get("/list-chats", "ChatController@listChats")->name("app.user.list-chat");

    Route::get("/messages-chat/{uid}", "ChatController@messagesChat")->name("app.user.messages-chat");

    Route::post("/new-message", "ChatController@newMessageUser")->name("app.user.new-message");

});
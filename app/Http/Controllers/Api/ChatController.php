<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

/**
 * Class ChatController
 * @package App\Http\Controllers\Api
 */
class ChatController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * ChatsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->userService = new UserService();
    }

    /**
     * List of chats the user
     * @return array
     */
    public function listChats()
    {
        return $this->userService->getChatsUser();
    }

    /**
     * @param $chat_uid
     * @return array
     */
    public function messagesChat($chat_uid)
    {
        return $this->userService->getMessagesFromChat($chat_uid);
    }
}

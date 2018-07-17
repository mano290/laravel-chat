<?php namespace App\Services;

use App\Enum\MessagesType;
use App\Enum\RoomType;
use App\Rooms;
use App\RoomUsers;
use App\User;
use Carbon\Carbon;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * @var array
     */
    public $return = [];

    /**
     * returns list chats user
     * @return array
     */
    public function getChatsUser()
    {
        $user = auth()->user();

        $chats = RoomUsers::with([
            "room.participantChat.user",
            "room.lastMessage"
        ])->where("user_id", $user->id)->get();

        foreach ($chats as $chat) {
            // Room chat
            $chat = $chat->room;
            // Last message of chat
            $last_message = $chat->lastMessage;
            // Type chat one to one
            if($chat->type == RoomType::ROOM_CHAT) {
                $this->return[] = [
                    "room_uid" => $chat->uid,
                    "room_title" => $chat->participantChat->user->name,
                    "room_image" => $chat->participantChat->user->avatar,
                    "room_last_message" => $last_message->data[$last_message->type],
                    "room_last_time" => $last_message->created_at->diffForHumans(),
                    "room_updated_at" => $last_message->created_at->timestamp,
                    "room_messages" => route("app.user.messages-chat", $chat->uid)
                ];
            }
        }

        // Sort by updated at
        usort($this->return, function ($a, $b) {
            return $a["room_updated_at"] < $b["room_updated_at"];
        });

        return $this->return;
    }

    /**
     * @param $to_user
     * @param $message
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createNewChatUser($to_user, $message)
    {
        $to_user = User::find($to_user);

        $new_room = Rooms::create();

        $new_room->participants()->save($to_user);

        $new_room->messages()->create([
            "user_id" => auth()->user()->id,
            "data" => [MessagesType::MESSAGE => $message],
            "type" => MessagesType::MESSAGE
        ]);

        return $new_room;
    }

    /**
     * @param $chat_uid
     * @return array
     */
    public function getMessagesFromChat($chat_uid)
    {
        $room = Rooms::with("messages.user")->where("uid", $chat_uid)->first();

        foreach ($room->messages as $message) {
            if($message->type == MessagesType::MESSAGE) {
                $this->return[] = [
                    "message_id" => $message->id,
                    "message_type" => MessagesType::MESSAGE,
                    "message_content" => $message->data[MessagesType::MESSAGE],
                    "message_user" => $message->user->name,
                    "message_user_avatar" => $message->user->avatar,
                    "message_date" => $message->created_at->format("d/m/Y H:i")
                ];
            }
        }

        return $this->return;
    }
}
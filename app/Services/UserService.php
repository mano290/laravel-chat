<?php namespace App\Services;

use App\Enum\RoomType;
use App\Rooms;
use App\User;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * returns list chats user
     * @return array
     */
    public function getChatsUser()
    {
        $return = [];
        $user = auth()->user();

        $rooms = Rooms::with(["users" => function($query) use ($user) {
            return $query->whereNotIn("{$query->getTable()}.id", [$user->id]);
        }])->where(auth());

        $chats = User::with([
            "rooms.users" => function($query) use ($user) {
                return $query->whereNotIn("{$query->getTable()}.id", [$user->id]);
            }
        ])->find(auth()->user()->id);

        foreach ($chats->rooms as $chat) {

            $last_message = $chat->lastMessage;

            if($chat->type == RoomType::ROOM_CHAT) {
                $return[] = [
                    "room_uid" => $chat->uid,
                    "room_title" => $chat->users->first()->name,
                    "room_image" => "https://image.flaticon.com/icons/svg/149/149071.svg",
                    "room_last_message" => $last_message->data[$last_message->type],
                    "room_last_time" => $last_message->created_at->diffForHumans()
                ];
            }
        }

        return $return;
    }
}
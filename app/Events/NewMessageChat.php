<?php namespace App\Events;

use App\Message;
use App\RoomMessage;
use App\Room;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class MessageSent
 * @package App\Events
 */
class NewMessageChat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Name event chat
     */
    const EVENT_CHAT = "roomChat.";

    /**
     * Data transport
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param RoomMessage $message
     * @param Room $room
     */
    public function __construct(User $user, RoomMessage $message, Room $room)
    {
        $this->data = [
            "message_user_avatar" => $user->avatar,
            "message_user" => $user->name,
            "message_date" => $message->created_at->format('d/m/Y H:i'),
            "message_content" => $message->data[$message->type],
            "message_room_uid" => $room->uid,
            "message_uid" => uniqid(),
            "user_uid" => $user->uid,
            "room_url" => url('/')
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(self::EVENT_CHAT . $this->data["message_room_uid"]);
    }
}

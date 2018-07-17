<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomReadMessages
 * @package App
 */
class RoomReadMessage extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "room_message_id"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function message()
    {
        return $this->belongsTo(RoomMessage::class);
    }
}

<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomMessages
 * @package App
 */
class RoomMessages extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        "user_id", "room_id", "data", "type"
    ];

    /**
     * @var array
     */
    protected $casts = [
        "data" => "array"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(Rooms::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Status read message
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusReadMessage()
    {
        return $this->hasMany(RoomReadMessages::class, "room_message_id");
    }
}

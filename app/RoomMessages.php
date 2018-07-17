<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RoomMessages
 * @package App
 */
class RoomMessages extends Model
{
    use SoftDeletes;

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
     * Save id the last message of chat
     * On rooms table
     */
    protected static function boot()
    {
        parent::boot();

        // Save id the last message
        static::created(function ($message) {
            $message->room()->update([
                "last_message_id" => $message->id
            ]);
        });
    }

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

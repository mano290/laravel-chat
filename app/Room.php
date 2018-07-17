<?php namespace App;

use App\Enum\RoomType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Rooms
 * @package App
 */
class Room extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        "uid", "type", "data",
        "last_message_id"
    ];

    /**
     * @var array 
     */
    protected $casts = [
        "data" => "array"
    ];

    /**
     * Generate UID on create
     * And add current user as admin
     */
    protected static function boot()
    {
        parent::boot();

        // Generate UID
        static::creating(function ($room) {
            $room->uid = uniqid("room_");
        });

        // Sync current user as room admin
        static::created(function ($room) {
            $room->participants()->save(auth()->user(), [
                'is_admin' => RoomType::USER_ADMIN
            ]);
        });
    }

    /**
     * Participants of chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, "room_users", "room_id")
            ->withPivot("is_admin")->withTimestamps();
    }

    /**
     * Participant of chat
     * One to One - Common chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function participantChat()
    {
        return $this->belongsTo(RoomUser::class, "id", "room_id")
            ->where("user_id", "!=", auth()->user()->id);
    }

    /**
     * Messages in chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(RoomMessage::class, "room_id")
            ->orderBy("created_at", "asc");
    }

    /**
     * Last message from chat
     *
     * @return mixed
     */
    public function lastMessage()
    {
        return $this->hasOne(RoomMessage::class, "id", "last_message_id")
            ->orderBy("created_at", "desc");
    }
}

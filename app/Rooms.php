<?php namespace App;

use App\Enum\RoomType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Rooms
 * @package App
 */
class Rooms extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        "uid",
        "type",
        "data"
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
            $room->users()->save(auth()->user(), [
                'is_admin' => RoomType::USER_ADMIN
            ]);
        });
    }

    /**
     * Users chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, "room_users", "room_id")
            ->withPivot("is_admin")->withTimestamps();
    }

    /**
     * Messages in chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(RoomMessages::class, "room_id")
            ->orderBy("created_at", "desc");
    }

    /**
     * Last message from chat
     *
     * @return mixed
     */
    public function getLastMessageAttribute()
    {
        $query = $this->hasOne(RoomMessages::class, "room_id")
            ->orderBy("created_at", "desc");

        return $query->limit(1)->first();
    }
}

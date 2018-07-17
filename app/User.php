<?php namespace App;

use App\Enum\UserAvatar;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $appends = [
        "uid"
    ];

    /**
     * A user can have many messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(RoomMessages::class);
    }

    /**
     * Chat rooms user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rooms()
    {
        return $this->belongsToMany(Rooms::class, "room_users", "user_id", "room_id")
            ->orderBy("created_at", "desc");
    }

    /**
     * Uid user
     * @return string
     */
    public function getUidAttribute()
    {
        return base64_encode($this->attributes["id"]);
    }

    /**
     * User avatar
     *
     * @return mixed
     */
    public function getAvatarAttribute()
    {
        return UserAvatar::USER_AVATAR[$this->attributes['id']];
    }
}

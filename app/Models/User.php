<?php

namespace App\Models;

use Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
	use HasRoles;
    use Notifiable {
		notify as protected laravelNotify;
	}
	use Traits\ActiveUserHelper;
	use Traits\LastActivedAtHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'name', 'phone', 'email', 'password', 'introduction', 'avatar',
		'weixin_openid', 'weixin_unionid','weixin_session_key', 'weapp_openid'
	];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
	];

	protected $dates = ['last_actived_at','created_at','updated_at'];
	
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
	
	public function topics()
	{
		return $this->hasMany(Topic::class);
	}

	public function replies()
	{
		return $this->hasMany(Reply::class);
	}

	public function notify($instance){
		if($this->id == Auth::id()){
			return;
		}
		$this->increment('notification_count');
		$this->laravelNotify($instance);
	}

	public function markAsRead()
	{
		$this->notification_count = 0;
		$this->save();
		$this->unreadNotifications->markAsRead();
	}

	public function setPasswordAttribute($value)
	{
		// 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }
		$this->attributes['password'] = $value;
	}

	public function setAvatarAttribute($value)
	{
		if(!starts_with($value,'http')){
			$value = url('/uploads/images/avatars/'.$value);
			// $value = config('app.url').'/uploads/images/avatars/'.$value;
		}
		$this->attributes['avatar'] = $value;
	}
}

<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\FriendableTempFix;

use Rennokki\Befriended\Traits\Follow;
use Rennokki\Befriended\Contracts\Following;

use Rennokki\Befriended\Traits\Block;
use Rennokki\Befriended\Contracts\Blocking;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject, Following, Blocking
{
    use HasApiTokens, Notifiable, HasRoleAndPermission, Follow, Block, FriendableTempFix;

    // use \Conner\Tagging\Taggable;
    //use HasRoles;
    //use Friendable;
    use Notifiable;

    // use SoftDeletes;
    //protected $table = 'users';
    public $jwt_token = '';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'username',
        'track_logins',
        'email',
        'password',
        'public',
        'bio',
        'avatar',
        'background'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*  public function sendEmailVerificationNotification()
    {
        $this->notify(new VerificateEmailNotification(''));
    } */

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function passwordSecurity()
    {
        $ps = $this->hasOne('App\TwoFactor');
        if (empty($ps)) {
            $ps = [];
        }
        return $ps;
    }

    public function email()
    {
        $email = '';
        if (!empty(\Auth::id())) {
            //  if(\Auth::user()->can('admin')){
            $email = $this->email;
            //  }
        }
        return $email;
    }

    public function avatar()
    {
        if (empty($this->avatar)) {
            return "img/404/avatar.png";
        }
        return $this->avatar;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function setJwtToken($jwt_token)
    {
        $this->jwt_token = $jwt_token;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function background()
    {
        if (empty($this->background)) {
            return "img/404/background.png";
        }
        return $this->background;
    }

    public function created_at_readable()
    {
        return $this->created_at->diffForHumans();
    }


}

<?php

namespace App;

//use App\Media;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Spatie\Permission\Traits\HasRoles;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\FriendableTempFix;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerificateEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Hootlex\Friendships\Traits\Friendable;
class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
  use HasApiTokens, Notifiable, HasRoleAndPermission,FriendableTempFix;
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
        'id','name','username','track_logins', 'email', 'password','public', 'bio', 'avatar', 'background'
    ];
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
  /*  public function sendEmailVerificationNotification()
  {
      $this->notify(new VerificateEmailNotification(''));
  } */ 
    public function sendPasswordResetNotification($token)
    {
      $this->notify(new ResetPasswordNotification($token));
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function passwordSecurity()
    {
      $ps = $this->hasOne('App\TwoFactor');
      if(empty($ps)){
        $ps = [];
      }
      return $ps;
    }
    public function email(){
      $email = '';
      if(!empty(\Auth::id())){
    //  if(\Auth::user()->can('admin')){
        $email = $this->email;
    //  }
      }
      return $email;
    }
    public function avatar(){
      if(empty($this->avatar)){
        return "img/404/avatar.png";
      }
      return $this->avatar;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function setJwtToken($jwt_token){
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
    public function background(){
      if(empty($this->background)){
        return "img/404/background.png";
      }
      return $this->background;
    }

    public function created_at_readable(){
      return $this->created_at->diffForHumans();
    }


}

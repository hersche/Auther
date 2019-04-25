<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class User extends JsonResource
{
    private function getUserUsernames($users,$rid=true){
      $ids = array();
      foreach($users as $u){
        if($u->recipient_id!=Auth::id()){
          array_push($ids,User::find($u->recipient_id)->username);
        } else {
          array_push($ids,User::find($u->sender_id)->username);
        }
      }
      return $ids;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $email = '';
      $google2fa_url = "";
      $admin = false;
      if(!empty(Auth::id())){
        if(Auth::user()->level()>(int)config('adminlevel')){
          $email = $this->email;
          $admin = true;
        }
        if($this->id==Auth::id()){
          $email = $this->email;
        }

  /*    if(Auth::user()->passwordSecurity()->exists()&&$this->id==Auth::id()){
          //$google2fa = app('pragmarx.google2fa');
          $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
          $google2fa_url = $google2fa->getQRCodeInline(
              '5Balloons 2A DEMO',
              Auth::user()->email,
              Auth::user()->passwordSecurity->secret
          );
      }*/
      
    }
    $avatar = $this->avatar();
    if((substr( $avatar, 0, 4 ) === "http")==false){
      $avatar = env('MIX_APP_URL', "")."/".$avatar;
    }
    $background = $this->background();
    if((substr( $background, 0, 4 ) === "http")==false){
      $background = env('MIX_APP_URL', "")."/".$background;
    }
    $bio = $this->bio;
    if(is_null($bio)){
      $bio="";
    }
    $simpleRoleArray = [];
    $i=0;
    foreach($this->roles as $role){
      $simpleRoleArray[$i] = $role->slug.":".$role->level;
      $i++;
    }
      return [
          'id' => $this->id,
          'name' => $this->name,
          'username' => $this->username,
          'avatar' => $avatar,
          'background' => $background,
          'bio' => $bio,
          'public' => $this->public,
          'roles' => $simpleRoleArray,
          'admin' => $admin,
          'allow_username_change' => $this->allow_username_change,
          'email' => $email,
          'friends' => [
            'pending' => $this->getUserUsernames($this->getPendingFriendships()),
            'accepted' => $this->getUserUsernames($this->getAcceptedFriendships()),
            'denied' => $this->getUserUsernames($this->getDeniedFriendships()),
            'blocked' => $this->getUserUsernames($this->getBlockedFriendships()),
            'pendingRequests' => $this->getUserUsernames($this->getFriendRequests(),false),
          ],
          'created_at' => $this->created_at,
          'updated_at' => $this->updated_at,
      ];
    }
}

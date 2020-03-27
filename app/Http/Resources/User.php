<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
      $theData = [
          'id' => $this->username,
          'name' => $this->name,
          'username' => $this->username,
          'public' => $this->public,
          'admin' => $admin,
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
      if(Auth::check()){
        if(Auth::user()->level()>(int)config('app.adminlevel')){
          $theData['email'] = $this->email;
          $admin = true;
        }
        if($this->id===Auth::id()){
          $theData['email'] = $this->email;
          $theData['you'] = true;
          $theData['track_logins'] = $this->track_logins;
          $theData['allow_username_change'] = $this->allow_username_change;
          if(Auth::guard('web')->check()){
              $theData['redirect'] = $request->session()->get('auth.redirectUrl');
          }
          if(!empty($this->jwt_token)){
            $theData['jwt_token'] = $this->jwt_token;
          }
        }
    }
    $avatar = $this->avatar();
    if((substr( $avatar, 0, 4 ) === "http")==false){
      $avatar = env('MIX_APP_URL', "")."/".$avatar;
    }
    $background = $this->background();
    if((substr( $background, 0, 4 ) === "http")==false){
      $background = env('MIX_APP_URL', "")."/".$background;
    }
    $theData['avatar'] = $avatar;
    $theData['background'] = $background;
    $bio = $this->bio;
    if(is_null($bio)){
      $bio="";
    }
    if(!$this->public){
      //$avatar="";
      //$background="";
      $bio="";
    }
    $theData['bio'] = $bio;
    $simpleRoleArray = [];
    $i=0;
    foreach($this->roles as $role){
      $simpleRoleArray[$i] = $role->slug.":".$role->level;
      $i++;
    }
    $theData['roles'] = $simpleRoleArray;
      return $theData;
    }
}

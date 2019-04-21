<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class Project extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $avatar = '';
      if(!empty($this->avatar)){
        $avatar = env('MIX_APP_URL', "")."/".$this->avatar;
      }
      $background = '';
      if(!empty($this->background)){
        $background = env('MIX_APP_URL', "")."/".$this->background;
      }
      return [
          'id' => $this->id,
          'title' => $this->title,
          'description' => $this->description,
          'avatar' => $avatar,
          'background' => $background,
          'status' => $this->status,
          'url' => $this->url,
          'client_id' => $this->client_id,
          'version' => $this->version,
          'direct_login_url' => $this->direct_login_url,
          'created_at' => $this->created_at,
          'updated_at' => $this->updated_at,
      ];
    }
}

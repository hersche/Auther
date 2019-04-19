<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    
    protected $fillable = [
        'title','description','version','url','direct_login_url','avatar','background'
    ];
    
    protected $hidden = [
        '_token',
    ];
    
    public function status()
    {
        return $this->hasOne('App\ProjectStatus')->latest();
    }
    
    public function statusHistory()
    {
        return $this->hasMany('App\ProjectStatus');
    }
}

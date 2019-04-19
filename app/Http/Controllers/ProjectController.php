<?php

namespace App\Http\Controllers;
use App\Project;
use Auth;
use App\Http\Resources\Project as ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    //
    
    function create(Request $request){
  //    echo "fooo";
    //  die();
      if(Auth::user()->level()>(int)config('adminlevel')){
        $project = Project::create($request->except(['_token','avatar','background']));
        $avatar = 'public/projects/avatars/'.$project->title.'.png';
        $data = $request->input('avatar');
        if(!empty($data)){
          //echo $data;
          list($type, $data) = explode(';', $data);
          list(, $data)      = explode(',', $data);
          $data = base64_decode($data);
          Storage::put('public/projects/avatars/'.$project->title.'.png', $data);
        } else {
          $avatar = '';
        }
        $background = 'public/projects/backgrounds/'.$project->title.'.png';
        $data = $request->input('background');
        if(!empty($data)){
          list($type, $data) = explode(';', $data);
          list(, $data)      = explode(',', $data);
          $data = base64_decode($data);
          Storage::put('public/projects/backgrounds/'.$project->title.'.png', $data);
        } else {
          $background = '';
        }
        $project->avatar = $avatar;
        $project->background = $background;
        $project->save();
        return $this->get($request);
      } else {
        echo "noadmin-fail";
        die();
      }
    }

    function get(Request $request){
      return ProjectResource::collection(Project::all());
    }
    
    function update(Request $request){
      if(Auth::user()->level()>(int)config('adminlevel')){
        $project = Project::find($request->input('pid'));
        $project->update($request->except(['_token','avatar','background','pid']));
        $avatar = 'public/projects/avatars/'.$project->title.'.png';
        $data = $request->input('avatar');
        if(!empty($data)){
          //echo $data;
          list($type, $data) = explode(';', $data);
          list(, $data)      = explode(',', $data);
          $data = base64_decode($data);
          Storage::put('public/projects/avatars/'.$project->title.'.png', $data);
        } else {
          $avatar = '';
        }
        $background = 'public/projects/backgrounds/'.$project->title.'.png';
        $data = $request->input('background');
        if(!empty($data)){
          list($type, $data) = explode(';', $data);
          list(, $data)      = explode(',', $data);
          $data = base64_decode($data);
          Storage::put('public/projects/backgrounds/'.$project->title.'.png', $data);
        } else {
          $background = '';
        }
        $project->avatar = $avatar;
        $project->background = $background;
        $project->save();
        return $this->get($request);
      }
    }
    
    function destroy(Request $request){
      if(Auth::user()->level()>(int)config('adminlevel')){
        Project::find($request->input("pid"))->delete();
        return $this->get($request);
      }
    }
}

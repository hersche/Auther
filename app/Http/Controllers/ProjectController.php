<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Project as ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{

    const AVATARSPATH = 'public/projects/avatars/';
    const BACKGROUNDSPATH = 'public/projects/backgrounds/';

    function create(Request $request)
    {
        if (Auth::user()->level() > (int)config('app.adminlevel')) {
            $project = Project::create($request->except(['_token', 'avatar', 'background']));
            $avatar = self::AVATARSPATH . $project->title . '.png';
            $data = $request->input('avatar');
            if (!empty($data)) {
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                Storage::put($avatar, $data);
            } else {
                $avatar = '';
            }
            $background = self::BACKGROUNDSPATH . $project->title . '.png';
            $data = $request->input('background');
            if (!empty($data)) {
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                Storage::put($background, $data);
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

    function get(Request $request)
    {
        return ProjectResource::collection(Project::all());
    }

    function update(Request $request)
    {
        if (Auth::user()->level() > (int)config('app.adminlevel')) {
            $project = Project::find($request->input('pid'));
            $project->update($request->except(['_token', 'avatar', 'background', 'pid']));
            $avatar = self::AVATARSPATH . $project->title . '.png';
            $data = $request->input('avatar');
            if (!empty($data)) {
                //echo $data;
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                Storage::put($avatar, $data);
            } else {
                $avatar = '';
            }
            $background = self::BACKGROUNDSPATH . $project->title . '.png';
            $data = $request->input('background');
            if (!empty($data)) {
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                Storage::put($background, $data);
            } else {
                $background = '';
            }
            $project->avatar = $avatar;
            $project->background = $background;
            $project->save();
            return $this->get($request);
        }
    }

    function destroy(Request $request)
    {
        if (Auth::user()->level() > (int)config('app.adminlevel')) {
            Project::find($request->input("pid"))->delete();
            return $this->get($request);
        }
    }
}

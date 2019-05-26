<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use Auth;

class FriendController extends Controller
{
    //
    public function get(Request $request){
      return UserResource::collection(User::all());
    }
    
    public function friendRequest(Request $request){
      if(Auth::check()){
      $u = User::find(Auth::id());
      if($u->level()>0){
        $fu = User::where("username","=",$request->input('fid'))->first();
        $u->befriend($fu);
        return UserResource::collection(User::all());
      }
    } else {
      return response()->json([],401);
    }
    }
    public function acceptRequest(Request $request){
    if(Auth::check()){
      $u = User::find(Auth::id());
      $fu = User::where("username","=",$request->input('fid'))->first();
      $u->acceptFriendRequest($fu);
      return UserResource::collection(User::all());
    } else {
      return response()->json([],401);
    }
    }
    public function denyRequest(Request $request){
      if(Auth::check()){
      $u = User::find(Auth::id());
      $fu = User::where("username","=",$request->input('fid'))->first();
      $u->denyFriendRequest($fu);
      return UserResource::collection(User::all());
    } else {
      return response()->json([],401);
    }
    }
    public function unfriend(Request $request){
      if(Auth::check()){
      $u = User::find(Auth::id());
      $fu = User::where("username","=",$request->input('fid'))->first();
      $u->unfriend($fu);
      return UserResource::collection(User::all());
    } else {
      return response()->json([],401);
    }
    }
    public function block(Request $request){
      if(Auth::check()){
      $u = User::find(Auth::id());
      $fu = User::where("username","=",$request->input('fid'))->first();
      $u->blockFriend($fu);
      return UserResource::collection(User::all());
    } else {
      return response()->json([],401);
    }
    }
    public function unblock(Request $request){
      if(Auth::check()){
      $u = User::find(Auth::id());
      $fu = User::where("username","=",$request->input('fid'))->first();
      $u->unblockFriend($fu);
      return UserResource::collection(User::all());
    } else {
      return response()->json([],401);
    }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    private User $user;
    private User $friend;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->friend = User::where('username', $request->input('fid'))->first();
            return $next($request);
        });
    }

    //
    public function get(Request $request)
    {
        return UserResource::collection(User::all());
    }

    public function friendRequest(Request $request)
    {
        $this->user->befriend($this->friend);
        return UserResource::collection(User::all());
    }

    public function follow(Request $request){
        $this->user->follow($this->friend);
        return UserResource::collection(User::all());
    }

    public function unfollow(Request $request){
        $this->user->unfollow($this->friend);
        return UserResource::collection(User::all());
    }

    public function acceptRequest(Request $request)
    {
        $this->user->acceptFriendRequest($this->friend);
        return UserResource::collection(User::all());
    }

    public function denyRequest(Request $request)
    {
        $this->user->denyFriendRequest($this->friend);
        return UserResource::collection(User::all());
    }

    public function unfriend(Request $request)
    {
        $this->user->unfriend($this->friend);
        return UserResource::collection(User::all());
    }

    public function block(Request $request)
    {
        $this->user->blockFriend($this->friend);
        return UserResource::collection(User::all());
    }

    public function unblock(Request $request)
    {
        $this->user->unblockFriend($this->friend);
        return UserResource::collection(User::all());
    }
}

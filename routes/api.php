<?php

use Illuminate\Http\Request;
use App\User;
use App\Notifications\GenericNotification;
use App\Http\Resources\User as UserResource;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return new UserResource($request->user());
})->middleware('scope:profile');

Route::middleware('auth:api')->post('/createNotification', function (Request $request) {
    $request->user()->notify(new GenericNotification($request->except("_token")));
    return $request->user()->notifications->toJson();
});


Route::middleware('auth:api')->post('/getNotifications', function (Request $request) {
  return $request->user()->notifications->toJson();
});

Route::middleware('auth:api')->post('/notificationsReaded', function (Request $request) {
  if(!empty(Auth::id())){
    Auth::user()->unreadNotifications->markAsRead();
    return Auth::user()->notifications->toJson();
  }
  return response()->json(["data"=>["msg"=>"No permission"]],200);
});

Route::middleware('auth:api')->post('/deleteNotifications', function (Request $request) {
  Auth::user()->notifications()->delete();
  return Auth::user()->notifications->toJson();
});

Route::middleware('auth:api')->post('/notificationReaded/{id}', function (Request $request) {
  if(!empty(Auth::id())){
    foreach (Auth::user()->unreadNotifications as $notification) {
      if($notification->id==$id){
        $notification->markAsRead();
      }
    }
    return Auth::user()->notifications->toJson();
  }
  return response()->json(["data"=>["msg"=>"No permission"]],200);
});



// custom API route
Route::middleware('auth:api')->get('/user/get', 'UserController@get');

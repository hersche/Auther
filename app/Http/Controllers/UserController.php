<?php
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use Auth;
use DB;
use Hash;
use Illuminate\Support\Facades\Storage;
 
class UserController extends Controller
{
  /*  public function get(Request $request)
    {
      $user_id = $request->get("uid", 0);
      $user = User::find(1);
      return $user;
    }*/
    public function changePassword(Request $request)
    {
      if(Auth::user()->level()>0){
        if(Hash::check($request->input("oldpass"), Auth::user()->password)){
          $user = User::find(Auth::id());
          if($request->input("newpass")==$request->input("newpass2")){
            $user->password = $request->input("newpass");
            $user->save();
            return $this->get($request);
          }
        }
      }
    }  
    
    public function checkUsernameExists(Request $request){
      if(!empty(User::where(['username' => $request->input('username')])->get())){
        return response()->json(["userexists"=>true],200);
      } else {
        return response()->json(["userexists"=>false],200);
      } 
    }
    
    public function setEmail(Request $request){
      if(Auth::check()){
        $user = User::find(Auth::id());
        $user->email = $request->input("email");
        $user->email_verified_at = null;
        $user->save();
        return response()->json(["email_set"=>true],200);
      }
      return response()->json(["email_set"=>false],401);
    }
    
    public function destroy(Request $request,$id){
      //echo "fooo";die();
      if(Auth::check()){
        if(Auth::id()===$id||Auth::user()->level()>(int)config('app.adminlevel')){
          $u = User::find($id);
          $u->notifications()->delete();
          $u->delete();
          return $this->get($request);
        }
      }
    }
    
    public function setLogin(Request $request){
      if(Auth::id()!=0){
        $u = User::find(Auth::id());
        if($u->allow_username_change==false){
          return response()->json(["msg"=>"Not allowed to set"],401);
        }
        if($request->input("password")==$request->input("confirm_password")){
          $u->allow_username_change=false;
          $u->username = $request->input("username");
          $u->password = Hash::make($request->input("password"));
          $u->save();
          return $this->get($request);
        } else {
          return response()->json(["msg"=>"Password missmatch"],401);
        }
        
      } else {
        return response()->json(["msg"=>"Not allowed to set (no login)"],401);
      }
    }    
    public function get(Request $request){
      return UserResource::collection(User::all());
    }
    public function changeRoles(Request $request){
      if(Auth::user()->level()>(int)config('app.adminlevel')){
        //$user = User::find($request->input("uid"));
        $slugArray = explode(",",$request->input("roles"));
        $i = 0;
        $tmpArr = array();
        foreach(config('roles.models.role')::all() as $r){
          foreach($slugArray as $s){
          if($s==$r->slug){
            array_push($tmpArr,$r->id);
          }
          }
          //$i++;
        }
        $user = config('roles.defaultUserModel')::find($request->input("uid"));
        
        $user->syncRoles($tmpArr);
        return $this->get($request);
      }
      //return response()->json(["data"=>["msg"=>"mkAdmin!"]],200);
    }
        /**
         * Update the specified resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, $id)
        {
            $this->validate($request, [
                'name' => 'required',
              //  'password' => 'same:confirm-password',
                //'roles' => 'required'
            ]);
            $input = $request->except(['avatar','background','_token']);
            if(!empty($input['password'])){
                $input['password'] = Hash::make($input['password']);
            }else{
                $input = array_except($input,array('password'));
            }
            $user = User::find($id);
            $user->update($input);
            $avatar = 'public/user/avatars/'.$user->username.'.png';
            $data = $request->input('avatar');
            if(!empty($data)){
              //echo $data;
              list($type, $data) = explode(';', $data);
              list(, $data)      = explode(',', $data);
              $data = base64_decode($data);
              Storage::put('public/user/avatars/'.$user->username.'.png', $data);
            } else {
              $avatar = '';
            }
            $background = 'public/user/backgrounds/'.$user->username.'.png';
            $data = $request->input('background');
            if(!empty($data)){
              list($type, $data) = explode(';', $data);
              list(, $data)      = explode(',', $data);
              $data = base64_decode($data);
              Storage::put('public/user/backgrounds/'.$user->username.'.png', $data);
            } else {
              $background = '';
            }
            
            $user->avatar = $avatar;
            $user->background = $background;

            $user->save();
            if(!empty($request->input('roles'))){
              DB::table('model_has_roles')->where('model_id',$id)->delete();
              $user->assignRole($request->input('roles'));
            }
            $tagArrayExtract = explode(' ', $request->input('tags'));
            $tagArray = array();
            foreach($tagArrayExtract as $tag){
              if(starts_with($tag, '#')){
                array_push($tagArray, substr($tag,1));
              } else {
                array_push($tagArray, $tag);
              }
            }
          //  $user->retag($tagArray);
            return $this->get($request);
          //  return response()->json(["data"=>["msg"=>"User updated"]],200);
          //  return redirect()->route('profile.view', $user->name)
            //                ->with('success','User updated successfully');
        }
}
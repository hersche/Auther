<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\User as UserRessource;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     * DEPRECATED!
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:'.config("app.minpasswordlength").'|confirmed',
        ]);
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    { 
      if((config('app.localauthenabled')==="1")&&(config('app.disableregister')==="0")){
        $this->validator($request->all())->validate();

        $user = User::create($request->except(['avatar','background','_token']));
        if(config("app.userneedverify")=="0"){
          $role = config('roles.models.role')::where('slug', '=', 'user')->first();
          $user->attachRole($role);
        } else {
          $role = config('roles.models.role')::where('slug', '=', 'unverified')->first();
          $user->attachRole($role);
        }
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
        $user->password = Hash::make($request->input('password'));
        $user->save();
        $this->guard()->login($user);
        if(config('app.needemailverify')=="0"||!empty($user->email_verified_at)){
          if($request->input('ajaxLogin')=="1"){
            return new UserRessource(Auth::user());
          } else {
            return $this->registered($request, $user)
                           ?: redirect($this->redirectPath());    
          }
        } else {
          if($request->ajax()){
            return response()->json(["data"=>["msg"=>"needemailverify"]],200);
          }
          return redirect("/email/resend");
        }
      }
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
  /*  protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }*/
}

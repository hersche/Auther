<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use Auth;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use App\SocialAccount;
use App\Http\Resources\User as UserRessource;
use App\Support\Google2FAAuthenticator;
use PragmaRX\Google2FALaravel\Support\Authenticator;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }
    
    
    
    protected function authenticated(Request $request, $user)
{
  if(config('app.localauthenabled')==="1"){
    if (is_null($user->passwordSecurity)||$user->passwordSecurity->enabled==false) {
      if(!empty($request->input("ajaxLogin"))){
        return new UserRessource(Auth::user());
      }
      return redirect()->intended($this->redirectTo);
    }
    Auth::logout();
    $request->session()->put('twofactor-user-id', $user->id);
    if(!empty($request->input("ajaxLogin"))){
      return response('{"twofactor":true}', 200);
    }
    $authenticator = app(Google2FAAuthenticator::class)->boot($request);
    return $authenticator->makeRequestOneTimePasswordResponse();
  } else {
    return response('{"login":"loginmethodnotenabled"}', 401);
  }
}
    /**
     * Custom logout function with no redirect if ajax.
     *
     * @return void
     */
    public function logout(Request $request) {
        if(Auth::user()->track_logins){
          Auth::user()->notify(new \App\Notifications\GenericNotification(["msg"=>"You logged out.", "appname"=>config("app.name"),"link"=>""]));
        }
        $this->guard()->logout();
        $request->session()->invalidate();
        (new Authenticator(request()))->logout();
        if($request->ajax()) {
            return Response::json(array(
                'success' => true,
                'data'   => 'Logout success'
            ));
        }
        else {
          return redirect(url("/"));
        }
    }
    public function login(Request $request)
    {
        if(config('app.localauthenabled')==="1"){
          $loginSuccess = false;
          if (Auth::attempt(['email' => $request->input("email"), 'password' => $request->input("password")])) {
            $loginSuccess = true;
          } else {
            if(Auth::attempt(['username' => $request->input("email"), 'password' => $request->input("password")])){
              $loginSuccess = true;
            }
          }
          if ($this->hasTooManyLoginAttempts($request)) {
              $this->fireLockoutEvent($request);
              return $this->sendLockoutResponse($request);
            }

            if ($loginSuccess) {
              //return response('{"success"}', 200);
              //  echo "aja$loginSuccessxLogin?";
              //  echo $request->input("ajaxLogin");
              if(!empty($request->input("ajaxLogin"))){
                $user = Auth::user();
                if (is_null($user->passwordSecurity)||$user->passwordSecurity->enabled==false) {      
                  // after we checked 2factor is disabled here
                  // we check if it's a recheck for socialite-logins on existing user.
                  if(!empty($request->session()->get('auth.social_provider'))){
                    //auth.social_local_user_id
                    $social = SocialAccount::firstOrNew([
                      'user_id' => $request->session()->get('auth.social_local_user_id'),
                      'provider' => $request->session()->get('auth.social_provider')
                    ]);
                    if($social->exists){
                      $social->verified=true;
                      $social->enabled=true;
                      $social->save();
                      $request->session()->put('auth.social_local_user_id',0);
                      $request->session()->put('auth.social_provider',0);
                    }
                  }
                  if(Auth::user()->track_logins){
                    Auth::user()->notify(new \App\Notifications\GenericNotification(["msg"=>"You logged in", "appname"=>config("app.name"),"link"=>""]));
                  //  $browser = get_browser(null, true);
                  //  Auth::user()->notify(new \App\Notifications\GenericNotification(["msg"=>"You logged in on ".$browser['browser']." ".$browser['version']." (@".$browser['platform'].")", "appname"=>config("app.name"),"link"=>""]));
                  }
                  return new UserRessource(Auth::user());
                } else {
                  Auth::logout();
                  $request->session()->put('twofactor-user-id', $user->id);
                  return response('{"twofactor":true}', 200);
                }
              } else {
                return $this->sendLoginResponse($request);
              }
            }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        if(!empty($request->input("ajaxLogin"))){
          return response('{"login":invalid}', 401);
        } else {
          return $this->sendFailedLoginResponse($request);
        }
        
    } else {
      return response('{"login":"loginmethodnotenabled"}', 401);
    }
  }
}

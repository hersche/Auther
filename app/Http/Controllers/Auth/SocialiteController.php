<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\SocialAccount;
use App\User;
use Illuminate\Auth\Events\Registered as RegisteredEvent;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
class SocialiteController extends Controller
{
    use RedirectsUsers;
    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider String
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    
    public function enable(Request $request,$provider){
        $sa = SocialAccount::firstOrNew(['user_id'=>Auth::id(),'provider'=>$provider]);
        $sa->enabled = (boolean)$request->input("enabled");
        $sa->save();
        return $this->get($request);
    }
    public function get(Request $request){
        return SocialAccount::where("user_id","=",Auth::id())->get()->toJson();
    }    
    
    /**
     * Obtain the user information from Provider.
     *
     * @param $provider string
     * @throws \Exception
     * @throws \Throwable 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
      if(config("app.".$provider."authenabled")=="0"){
        return redirect()->route('login')
            ->with('error', __('Provider '.$provider.' not enabled, ask your admin.'));
      }
        try {
            $providerUser = Socialite::driver($provider)->user();
        } catch (\Throwable | \Exception $e) {
            // Send actual error message in development
            if (config('app.debug')) {
                throw $e;
            }
            // Lets suppress this error
            return redirect()->route('login')
                ->with('error', __('Unable to authenticate. Please try again.'));
        }
        DB::beginTransaction();
        $userAndSocial = $this->findOrCreateUser($provider, $providerUser);
        $user = $userAndSocial[0];
        $social = $userAndSocial[1];
        if($social->enabled){
          if($social->verified){
            Auth::login($user, true);
            // This session variable can help to determine if user is logged-in via socialite
            session()->put([
              'auth.social_id' => $providerUser->getId()
            ]);
            DB::commit();
            if($user->allow_username_change){
              return redirect('/#/settings/editusername');
            } else {
              return redirect('/');
              // The below was failing and this->authenticated is empty?
              //return $this->authenticated($user)
              //  ?: redirect()->intended($this->redirectPath());
            }
          } else {
            // social is not verfied yet, maybe it's a email-based attack?
            // check by a login, to enable it!
            session()->put([
              'auth.social_provider' => $social->provider,
              'auth.social_local_user_id' => $user->id,
            ]);
            return redirect('/#/settings/checkLogin');
          }
        } else {
          // this login-method is disabled by the user.
          return redirect("/#/notallowedloginmethod/".$social->provider);
        }
    }
    
    /**
     * Create a user if does not exist
     *
     * @param $providerName string
     * @param $providerUser
     * @return mixed
     */
    protected function findOrCreateUser($providerName, $providerUser)
    {
        $social = SocialAccount::firstOrCreate([
            'provider_user_id' => $providerUser->getId(),
            'provider' => $providerName
        ]);
        $user = User::firstOrCreate([
            'email' => $providerUser->getEmail()
        ]);
        if ($social->exists&&$social->verified&&$social->enabled) {
            return [$user,$social];
        }
        if (!$user->exists) {
            $user->name = $providerUser->getName();
            $user->username = $providerUser->getNickname().str_random(10);
            $user->allow_username_change=true;
            $user->avatar = $providerUser->getAvatar();
            $user->password = bcrypt(str_random(30));
            $user->save();
            
            // if user doesn't exist yet, social need to be fine.
            // this protection is meaned for existing users
            $social->enabled=true;
            $social->verified=true;
	    //$social->save();
            if(config("app.userneedverify")=="0"){
              $role = config('roles.models.role')::where('slug', '=', 'user')->first();
              $user->attachRole($role);
            } else {
              $role = config('roles.models.role')::where('slug', '=', 'unverified')->first();
              $user->attachRole($role);
            }
            
//            event(new RegisteredEvent($user));
        }/* else {
          $tmpD = new DateTime("now");
          $tmpD->modify('-7 days');
          if($user->created_at<$tmpD){
            $user->allow_username_change=false;
            $user->save();
          }
        }*/
        $social->user()->associate($user);
        $social->save();
        return [$user,$social];
    }
    /**
     * The user has been authenticated.
     *
     * @param  User $user
     * @return mixed
     */
    protected function authenticated(User $user)
    {
    }
    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return redirect('/');
    }
}

<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use App\User;
use App\SocialAccount;

class CheckVerifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check()){
          $u = User::find(Auth::id());
          if(config("app.needemailverify")=="1"&&is_null($u->email_verified_at)&&url()->full()!=url("/email/verify")){
            return redirect(url("/email/verify"));
          }
          if(Auth::user()->allow_username_change){
            return redirect(url("/#/settings/editusername"));
          }
          if(config("app.googleauthenabled")=="1"||config("app.githubauthenabled")=="1"||config("app.gitlabauthenabled")=="1"||config("app.facebookauthenabled")=="1"||config("app.linkedinauthenabled")=="1"||config("app.twitterauthenabled")=="1"){
            $socialAccounts = SocialAccount::where(["user_id"=>Auth::id()])->get();
            foreach($socialAccounts as $ac){
              if(!$ac->verified){
                session()->put([
                  'auth.social_provider' => $ac->provider,
                  'auth.social_local_user_id' => Auth::id(),
                ]);
                return redirect('/#/settings/checkLogin');
              }
            }
          }
        }
        if(!empty($request->session()->get('twofactor-user-id'))){
          return redirect('/#/twofaLogin');
        }
        return $next($request);
    }
}

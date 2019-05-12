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
        if((url("/oauth/personal-access-tokens")===url()->full()||url("/oauth/clients")===url()->full())&&Auth::user()->level()<(int)config("app.adminlevel")){
          return response()->json(["PersonalAccessTokensDenied"=>false],401);
        }
        if(Auth::check()){
          $u = User::find(Auth::id());
          if(config("app.needemailverify")=="1"&&is_null($u->email_verified_at)&&(strpos(url()->full(), url("/email/verify")) === false)&&url()->full()!==url("/email/resend")&&url()->full()!==url("/logout")){
            return redirect(url("/email/verify"));
          }
/*          if(Auth::user()->allow_username_change){
            return redirect(url("/#/settings/editusername"));
          }*/
          if((url("/")!==url()->full())&&(config("app.googleauthenabled")==="1"||config("app.githubauthenabled")==="1"||config("app.gitlabauthenabled")==="1"||config("app.facebookauthenabled")==="1"||config("app.linkedinauthenabled")==="1"||config("app.twitterauthenabled")==="1")){
            $socialAccounts = SocialAccount::where(["user_id"=>Auth::id()])->get();
            foreach($socialAccounts as $ac){
              if(!$ac->verified&&$ac->enabled){
                session()->put([
                  'auth.social_provider' => $ac->provider,
                  'auth.social_local_user_id' => Auth::id(),
                ]);
                return redirect('/#/settings/checkLogin');
              }
            }
          }
        }
      //  echo url("/")." vs ".url()->full();
        //die();
        // should check for /#/twofaLogin, but ->full() does /
      /*  if((url("/twofactor/recovery")!==url()->full())&&(url("/")!==url()->full())&&!empty($request->session()->get('twofactor-user-id'))){
          return redirect('/#/twofaLogin');
        }*/
        return $next($request);
    }
}

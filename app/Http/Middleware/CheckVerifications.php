<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use App\User;
use App\SocialAccount;
use Illuminate\Support\Facades\Request;
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
        
      //  var_dump($request->ajax());
      //  die();
        $needAction = false;
        if(Auth::check()){
          $u = User::find(Auth::id());
          if(config("app.needemailverify")=="1"&&is_null($u->email_verified_at)) {
            $needAction = true;
            session()->put([
              'auth.redirectUrl' => url("/email/verify"),
            ]);
          }
          
          if(Auth::user()->allow_username_change){
            $needAction = true;
            session()->put([
              'auth.redirectUrl' => url("/#/settings/editusername"),
            ]);
          //  return redirect(url("/#/settings/editusername"));
          }
          if((config("app.googleauthenabled")==="1"||config("app.githubauthenabled")==="1"||config("app.gitlabauthenabled")==="1"||config("app.facebookauthenabled")==="1"||config("app.linkedinauthenabled")==="1"||config("app.twitterauthenabled")==="1")){
            $socialAccounts = SocialAccount::where(["user_id"=>Auth::id()])->get();
            foreach($socialAccounts as $ac){
              if(!$ac->verified&&$ac->enabled){
                $needAction = true;
                session()->put([
                  'auth.social_provider' => $ac->provider,
                  'auth.social_local_user_id' => Auth::id(),
                  'auth.redirectUrl' => url('/#/settings/checkLogin'),
                ]);
              }
            }
          }
        }
      //  echo url("/")." vs ".url()->full();
        //die();
        // should check for /#/twofaLogin, but ->full() does /
        if((url("/twofactor/recovery")!==url()->full())&&(url("/")!==url()->full())&&!empty($request->session()->get('twofactor-user-id'))){
          $needAction = true;
          session()->put([
            'auth.redirectUrl' => url('/#/twofaLogin'),
          ]);
        //  return redirect('/#/twofaLogin');
        }
        if($request->session()->get('auth.redirectUrl')!==""){
          if(!$needAction){
            session()->put([
              'auth.redirectUrl' => "",
            ]);
          } else {
            if(!$request->ajax()){
              if(url()->full()!=$request->session()->get('auth.redirectUrl')&&url()->full()!=url("/")&&url()->full()!=url("/email/resend")&&url()->full()!=url("/logout")&&strpos(url()->full(),"/email/verify")==false&&strpos(url()->full(),"/2faVerify")==false&&strpos(url()->full(),"/internal-api")==false) {
                // TODO catch all the exception-urls.. may using a method for this.
                // here, we simply redirect if we are not on the vue-view. if we are on vue, it's handled.
                //return redirect($request->session()->get('auth.redirectUrl'));
              }
            }
          }
        }
        return $next($request);
    }
}

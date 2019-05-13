<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;
use App\Http\Resources\User as UserResource;

//$google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

class TwoFactorController extends Controller
{
  
      use AuthenticatesUsers;
    //
    public function show2faForm(Request $request){
    $user = Auth::user();
    //$request->session()->get('user-id');
    //$user = \App\User::find($request->session()->get('user-id'));
    $google2fa_url = "";
    if($user->passwordSecurity()->exists()){
        //$google2fa = app('pragmarx.google2fa');
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
        $google2fa_url = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->passwordSecurity->secret
        );
    }
    $data = array(
        'user' => $user,
        'google2fa_url' => $google2fa_url
    );
    return view('auth.2fa')->with('data', $data);
}

public function generate2faSecret(Request $request){
    $user = Auth::user();
    // Initialise the 2FA class
    $google2fa = app('pragmarx.google2fa');

    // Add the secret key to the registration data
    \App\TwoFactor::create([
        'user_id' => $user->id,
        'enabled' => 0,
        'secret' => $google2fa->generateSecretKey(),
    ]);

    return redirect('/2fa')->with('success',"Secret Key is generated, Please verify Code to Enable 2FA");
}

public function enable2fa(Request $request){
    $user = Auth::user();
    $google2fa = app('pragmarx.google2fa');
    $secret = $request->input('verify-code');
    $valid = $google2fa->verifyKey($user->passwordSecurity->secret, $secret);
    if($valid){
        $user->passwordSecurity->enabled = 1;
        $user->passwordSecurity->save();
        return redirect('2fa')->with('success',"2FA is Enabled Successfully.");
    }else{
        return redirect('2fa')->with('error',"Invalid Verification Code, Please try again.");
    }
}

public function my2faForceDisable(Request $request){
  if(Auth::user()->level()>(int)config('app.adminlevel')){
    $u = User::find($request->input("uid"));
    $u->passwordSecurity->enabled = 0;
    $u->passwordSecurity->secret = "";
    $u->passwordSecurity->recovery_secret = "";
    $u->passwordSecurity->save();
    return response('{"data":{"enabled":"0","url":"","key":""}}', 200);  
  }
}

public function my2fadisable(Request $request){
  if(Hash::check($request->input("userpass"), Auth::user()->password)){
    Auth::user()->passwordSecurity->enabled = 0;
    Auth::user()->passwordSecurity->secret = "";
    Auth::user()->passwordSecurity->recovery_secret = "";
    Auth::user()->passwordSecurity->save();
    return response('{"data":{"enabled":"0","url":"","key":""}}', 200);
  }
}

public function my2faTest(Request $request){
  if(Hash::check($request->input("userpass"), Auth::user()->password)){
  // Login succeded as we pass the middleware
  $user = Auth::user();
  $google2fa = app('pragmarx.google2fa');
  $valid = $google2fa->verifyKey($user->passwordSecurity->secret, $request->input("one_time_test_password"));
  if ($valid) {
    Auth::user()->passwordSecurity->enabled = 1;
    Auth::user()->passwordSecurity->save();
    $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
    $google2fa_url = $google2fa->getQRCodeInline(
        config('app.name'),
        Auth::user()->email,
        Auth::user()->passwordSecurity->secret
    );

    return response('{"data":{"enabled":"'.Auth::user()->passwordSecurity->enabled.'","url":"'.$google2fa_url.'","key":"'.Auth::user()->passwordSecurity->secret.'","recovery_secret":"'.Auth::user()->passwordSecurity->recovery_secret.'"}}', 200);
  } else {
    return response('{"twofactor":testinvalid}', 401);
  }
}  else {
   return response('{"data":{"auth":"passwordinvalid"}}', 401);
 }
}

private function doRandStr($length){
  $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                   .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                   .'0123456789'); // and any other characters
  shuffle($seed); // probably optional since array_is randomized; this may be redundant
  $rand = '';
  foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];
  return $rand;
}

public function my2faRefresh(Request $request){
  if(Hash::check($request->input("userpass"), Auth::user()->password)){
    $google2fa_url="";
    $google2fa = app('pragmarx.google2fa');
    $user = User::find(Auth::id());

    if(Auth::user()->passwordSecurity()->exists()){
      $user->passwordSecurity->enabled = 0;
      $user->passwordSecurity->secret = $google2fa->generateSecretKey();
      $user->passwordSecurity->recovery_secret = $this->doRandStr(5)."-".$this->doRandStr(5)."-".$this->doRandStr(5)."-".$this->doRandStr(5);
      $user->passwordSecurity->save();
      $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
      $google2fa_url = $google2fa->getQRCodeInline(
        config('app.name'),
        Auth::user()->email,
        Auth::user()->passwordSecurity->secret
      );
      return response('{"data":{"enabled":"'.Auth::user()->passwordSecurity->enabled.'","url":"'.$google2fa_url.'","key":"'.Auth::user()->passwordSecurity->secret.'","recovery_secret":"'.Auth::user()->passwordSecurity->recovery_secret.'"}}', 200);
    } else {
      Auth::user()->passwordSecurity()->create(["enabled" => 0,"secret"=>$google2fa->generateSecretKey()]);
      return response('{"data":{"enabled":"'.Auth::user()->passwordSecurity->enabled.'","url":"'.$google2fa_url.'","key":"'.Auth::user()->passwordSecurity->secret.'","recovery_secret":"'.Auth::user()->passwordSecurity->recovery_secret.'"}}', 200);
    }
    return response('{"data":{"enabled":"0","url":"","key":"","recovery_secret":"","recovery_secret":"'.Auth::user()->passwordSecurity->recovery_secret.'"}}', 200);
  } else {
    return response('{"data":{"auth":"failed"}}', 200);
  }
}

public function my2faGet(Request $request){
  if(Hash::check($request->input("userpass"), Auth::user()->password)){
  $google2fa_url="";
  if(Auth::user()->passwordSecurity()->exists()){
    if(Auth::user()->passwordSecurity->secret != ""){
      //$google2fa = app('pragmarx.google2fa');
      $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
      $google2fa_url = $google2fa->getQRCodeInline(
          config('app.name'),
          Auth::user()->email,
          Auth::user()->passwordSecurity->secret
      );
    }
    return response('{"data":{"enabled":"'.Auth::user()->passwordSecurity->enabled.'","url":"'.$google2fa_url.'","key":"'.Auth::user()->passwordSecurity->secret.'","recovery_secret":"'.Auth::user()->passwordSecurity->recovery_secret.'"}}', 200);
  } else {
    Auth::user()->passwordSecurity()->create(["enabled" => 0,"secret"=>""]);
  }
  return response('{"data":{"enabled":"0","url":"","key":"","recovery_secret":""}}', 200);
} else {
  return response('{"data":{"auth":"failed"}}', 200);
}
}


public function my2faverifyrecovery(Request $request){
  // Login succeded as we pass the middleware
  $user = User::find($request->session()->get('twofactor-user-id'));
  $valid=false;
  if($user->passwordSecurity->recovery_secret===$request->input("recovery-secret")){
    $valid=true;
  }

  return $this->loginAction($request,$valid);
}

private function loginAction($request,$valid){
  if ($valid) {
    if(!empty($request->session()->get('auth.social_provider'))){
      //auth.social_local_user_id
      $social = SocialAccount::find([
        'user_id' => $request->session()->get('auth.social_local_user_id'),
        'provider' => $request->session()->get('auth.social_provider')
      ]);
      if(!empty($social)){
        $social->verified=true;
        $social->enabled=true;
        $social->save();
        $request->session()->put('auth.social_local_user_id',0);
        $request->session()->put('auth.social_provider',0);
      }
    }
    Auth::loginUsingId($request->session()->get('twofactor-user-id'));
    $request->session()->put('twofactor-user-id',0);
    if(!empty($request->input("ajaxLogin"))){
      if(Auth::user()->track_logins){
        Auth::user()->notify(new \App\Notifications\GenericNotification(["msg"=>"You logged in via 2factor-login.", "appname"=>config("app.name")." (This app)","link"=>""]));
        }
      return new UserResource(Auth::user());
    } else {
      return $this->sendLoginResponse($request);
    }
  } else {
    if(!empty($request->input("ajaxLogin"))){
      return response('{"twofactor":invalid}', 401);
    } else {
      return redirect("/login");
    }
  }
}

public function my2faverify(Request $request){
  // Login succeded as we pass the middleware
  $user = User::find($request->session()->get('twofactor-user-id'));
  $google2fa = app('pragmarx.google2fa');
  $valid = $google2fa->verifyKey($user->passwordSecurity->secret, $request->input("one_time_password"));
  return $this->loginAction($request,$valid);
}

public function cancelProcess(Request $request){
  $request->session()->put('twofactor-user-id',0);
}

public function disable2fa(Request $request){
    if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
        // The passwords matches
        return redirect()->back()->with("error","Your  password does not matches with your account password. Please try again.");
    }

    $validatedData = $request->validate([
        'current-password' => 'required',
    ]);
    $user = Auth::user();
    $user->passwordSecurity->enabled = 0;
    $user->passwordSecurity->save();
    return redirect('/2fa')->with('success',"2FA is now Disabled.");
}

}

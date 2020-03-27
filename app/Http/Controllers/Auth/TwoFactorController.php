<?php

namespace App\Http\Controllers\Auth;

use PragmaRX\Google2FAQRCode\Google2FA;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\TwoFactor;
use App\User;
use App\Http\Resources\User as UserResource;
use App\Notifications\GenericNotification;

class TwoFactorController extends Controller
{

    use AuthenticatesUsers;

    private $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = User::find(Auth::id());
            return $next($request);
        });
    }
    //
    public function show2faForm(Request $request)
    {
        $user = Auth::user();
        //$request->session()->get('user-id');
        //$user = \App\User::find($request->session()->get('user-id'));
        $google2fa_url = "";
        if ($this->user->passwordSecurity()->exists()) {
            //$google2fa = app('pragmarx.google2fa');
            $google2fa = new Google2FA();
            $google2fa_url = $google2fa->getQRCodeInline(
                config('app.name'),
                $this->user->email,
                $this->user->passwordSecurity->secret
            );
        }
        $data = array(
            'user' => $user,
            'google2fa_url' => $google2fa_url
        );
        return view('auth.2fa')->with('data', $data);
    }

    public function generate2faSecret(Request $request)
    {
        $user = Auth::user();
        // Initialise the 2FA class
        $google2fa = new Google2FA();

        // Add the secret key to the registration data
        TwoFactor::create(
            [
                'user_id' => $this->user->id,
                'enabled' => 0,
                'secret' => $google2fa->generateSecretKey(),
            ]
        );

        return redirect('/2fa')->with('success', "Secret Key is generated, Please verify Code to Enable 2FA");
    }

    public function enable2fa(Request $request)
    {
        $google2fa = new Google2FA();
        $secret = $request->input('verify-code');
        $passwordSecurity = $this->user->passwordSecurity;
        $valid = $google2fa->verifyKey($passwordSecurity->secret, $secret);
        if ($valid) {
            $passwordSecurity->enabled = 1;
            $passwordSecurity->save();
            return redirect('2fa')->with('success', "2FA is Enabled Successfully.");
        } else {
            return redirect('2fa')->with('error', "Invalid Verification Code, Please try again.");
        }
    }

    public function my2faForceDisable(Request $request)
    {
        if (Auth::user()->level() > (int)config('app.adminlevel')) {
            return $this->clean2FA();
        }
    }

    public function my2fadisable(Request $request)
    {
        if (Hash::check($request->input("userpass"), Auth::user()->password)) {
            return $this->clean2FA();
        }
    }

    private function clean2FA(){
        $passwordSecurity = $this->user->passwordSecurity;
        $passwordSecurity->enabled = 0;
        $passwordSecurity->secret = "";
        $passwordSecurity->recovery_secret = "";
        $passwordSecurity->save();
        return response('{"data":{"enabled":"0","url":"","key":""}}', 200);
    }

    public function my2faTest(Request $request)
    {
        if (Hash::check($request->input("userpass"), Auth::user()->password)) {
            // Login succeded as we pass the middleware
            $passwordSecurity = $this->user->passwordSecurity;
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($passwordSecurity->secret, $request->input("one_time_test_password"));
            if ($valid) {
                $passwordSecurity->enabled = 1;
                $passwordSecurity->save();
                $google2fa = new Google2FA();
                $google2fa_url = $google2fa->getQRCodeInline(
                    config('app.name'),
                    $this->user->email,
                    $passwordSecurity->secret
                );

                return response(
                    '{"data":{"enabled":"' . $passwordSecurity->enabled . '","url":"' . $google2fa_url . '","key":"' . $passwordSecurity->secret . '","recovery_secret":"' . $passwordSecurity->recovery_secret . '"}}',
                    200
                );
            } else {
                return response('{"twofactor":testinvalid}', 401);
            }
        } else {
            return response('{"data":{"auth":"passwordinvalid"}}', 401);
        }
    }

    public function my2faRefresh(Request $request)
    {
        if (Hash::check($request->input("userpass"), Auth::user()->password)) {
            $google2fa_url = "";
            $google2fa = new Google2FA();;
            $user = Auth::user();
            $passwordSecurity = $this->user->passwordSecurity;
            if ($this->user->passwordSecurity()->exists()) {
                $passwordSecurity->enabled = 0;
                $passwordSecurity->secret = $google2fa->generateSecretKey();
                $passwordSecurity->recovery_secret = $this->doRandStr(5) . "-" . $this->doRandStr(5) . "-" . $this->doRandStr(5) . "-" . $this->doRandStr(5);
                $passwordSecurity->save();
                $google2fa = (new Google2FA());
                $google2fa_url = $google2fa->getQRCodeInline(
                    config('app.name'),
                    $this->user->email,
                    $this->user->passwordSecurity->secret
                );
                return response(
                    '{"data":{"enabled":"' . $passwordSecurity->enabled . '","url":"' . $google2fa_url . '","key":"' . $passwordSecurity->secret . '","recovery_secret":"' . $passwordSecurity->recovery_secret . '"}}',
                    200
                );
            } else {
                $this->user->passwordSecurity()->create(["enabled" => 0, "secret" => $google2fa->generateSecretKey()]);
                return response(
                    '{"data":{"enabled":"' . $passwordSecurity->enabled . '","url":"' . $google2fa_url . '","key":"' . $passwordSecurity->secret . '","recovery_secret":"' . $passwordSecurity->recovery_secret . '"}}',
                    200
                );
            }
        } else {
            return response('{"data":{"auth":"failed"}}', 200);
        }
    }

    private function doRandStr($length)
    {
        $seed = str_split(
            'abcdefghijklmnopqrstuvwxyz'
            . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            . '0123456789'
        ); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $length) as $k) {
            $rand .= $seed[$k];
        }
        return $rand;
    }

    public function my2faGet(Request $request)
    {
        if (Hash::check($request->input("userpass"), Auth::user()->password)) {
            $google2fa_url = "";
            $passwordSecurity = $this->user->passwordSecurity;
            if ($this->user->passwordSecurity()->exists()) {
                if ($passwordSecurity->secret != "") {
                    //$google2fa = app('pragmarx.google2fa');
                    $google2fa = (new Google2FA());
                    $google2fa_url = $google2fa->getQRCodeInline(
                        config('app.name'),
                        $this->user->email,
                        $passwordSecurity->secret
                    );
                }
                return response(
                    '{"data":{"enabled":"' . $passwordSecurity->enabled . '","url":"' . $google2fa_url . '","key":"' . $passwordSecurity->secret . '","recovery_secret":"' . $passwordSecurity->recovery_secret . '"}}',
                    200
                );
            } else {
                $this->user->passwordSecurity()->create(["enabled" => 0, "secret" => ""]);
            }
            return response('{"data":{"enabled":"0","url":"","key":"","recovery_secret":""}}', 200);
        } else {
            return response('{"data":{"auth":"failed"}}', 200);
        }
    }


    public function my2faverifyrecovery(Request $request)
    {
        // Login succeded as we pass the middleware
        $user = User::find($request->session()->get('twofactor-user-id'));
        $valid = false;
        if ($user->passwordSecurity->recovery_secret === $request->input("recovery-secret")) {
            $valid = true;
        }

        return $this->loginAction($request, $valid);
    }

    private function loginAction($request, $valid)
    {
        if ($valid) {
            if (!empty($request->session()->get('auth.social_provider'))) {
                //auth.social_local_user_id
                $social = SocialAccount::find(
                    [
                        'user_id' => $request->session()->get('auth.social_local_user_id'),
                        'provider' => $request->session()->get('auth.social_provider')
                    ]
                );
                if (!empty($social)) {
                    $social->verified = true;
                    $social->enabled = true;
                    $social->save();
                    $request->session()->put('auth.social_local_user_id', 0);
                    $request->session()->put('auth.social_provider', 0);
                }
            }
            $jwt_token = Auth::loginUsingId($request->session()->get('twofactor-user-id'));
            Auth::user()->setJwtToken($jwt_token);
            $request->session()->put('twofactor-user-id', 0);
            if (!empty($request->input("ajaxLogin"))) {
                if ($this->user->track_logins) {
                    $this->user->notify(
                        new GenericNotification(
                            [
                                "msg" => "You logged in via 2factor-login.",
                                "appname" => config("app.name") . " (This app)",
                                "link" => ""
                            ]
                        )
                    );
                }
                return new UserResource(Auth::user());
            } else {
                return $this->sendLoginResponse($request);
            }
        } else {
            if (!empty($request->input("ajaxLogin"))) {
                return response('{"twofactor":invalid}', 401);
            } else {
                return redirect("/login");
            }
        }
    }

    public function my2faverify(Request $request)
    {
        // Login succeded as we pass the middleware
        $google2fa = new Google2FA();

        $jwt_token = Auth::loginUsingId($request->session()->get('twofactor-user-id'));
        $this->user = Auth::user();
        $valid = $google2fa->verifyKey($this->user->passwordSecurity->secret, $request->input("one_time_password"));
        return $this->loginAction($request, $valid);
    }

    public function cancelProcess(Request $request)
    {
        $request->session()->put('twofactor-user-id', 0);
    }

    public function disable2fa(Request $request)
    {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with(
                "error",
                "Your  password does not matches with your account password. Please try again."
            );
        }

        $validatedData = $request->validate(
            [
                'current-password' => 'required',
            ]
        );
        $this->user->passwordSecurity->enabled = 0;
        $this->user->passwordSecurity->save();
        return redirect('/2fa')->with('success', "2FA is now Disabled.");
    }

}

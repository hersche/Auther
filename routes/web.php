<?php
use App\SocialAccount;

# use App\Support\Google2FAAuthenticator;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('auth:web', 'throttle:60,1')->group(function () {

    Route::post('/internal-api/user/delete/{id}','UserController@destroy');
    Route::post('/internal-api/user/disable/{id}','UserController@disable');

    Route::post('/internal-api/profiles/edit/{id}','UserController@update')->name('useres.edit')->middleware(['auth', '2fa']);
    Route::post('/internal-api/register', 'Auth\RegisterController@register');
    Route::post('/internal-api/settings/password','UserController@changePassword');
    Route::post('/internal-api/settings/email','UserController@setEmail');

    Route::post('/internal-api/users/changeroles', 'UserController@changeRoles');

    Route::post('/internal-api/role', 'RoleController@update');
    Route::post('/internal-api/role/create', 'RoleController@create');
    Route::post('/internal-api/role/delete', 'RoleController@destroy');
    Route::get('/internal-api/role', 'RoleController@get');

    Route::post('/internal-api/project', 'ProjectController@update');
    Route::post('/internal-api/project/create', 'ProjectController@create');
    Route::post('/internal-api/project/delete', 'ProjectController@destroy');

    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('/internal-api/notifications', function (Request $request) {
            return Auth::user()->notifications->toJson();
    });

    Route::post('/internal-api/notifications/create', function (Request $request) {
        $user = Auth::user();
        $user->notify(new GenericNotification($request->except("_token")));
            return $user->notifications->toJson();
    });

    Route::get('/internal-api/socialAccounts', function (Request $request) {
            return SocialAccount::where("user_id","=",Auth::id())->get()->toJson();
    });
    Route::get('/internal-api/notifications/markasread', function (Request $request) {
        $user = Auth::user();
            $user->unreadNotifications->markAsRead();
            return $user->notifications->toJson();
    });


    Route::post('/internal-api/friends/block', 'FriendController@block');
    Route::post('/internal-api/friends/unblock', 'FriendController@unblock');
    Route::post('/internal-api/friends/friendRequest', 'FriendController@friendRequest');
    Route::post('/internal-api/friends/acceptRequest', 'FriendController@acceptRequest');
    Route::post('/internal-api/friends/denyRequest', 'FriendController@denyRequest');
    Route::post('/internal-api/friends/unfriend', 'FriendController@unfriend');

    Route::get('/internal-api/notifications/markasread/{id}', function (Request $request,$id) {
            foreach (Auth::user()->unreadNotifications as $notification) {
                if($notification->id==$id){
                    $notification->markAsRead();
                }
            }
            return Auth::user()->notifications->toJson();
    });
    Route::get('/internal-api/notifications/delete', function (Request $request) {
        Auth::user()->notifications()->delete();
        return Auth::user()->notifications->toJson();
    });

    Route::get('/2fa','Auth\TwoFactorController@show2faForm');
    Route::post('/generate2faSecret','Auth\TwoFactorController@generate2faSecret')->name('generate2faSecret');
    Route::post('/2fa','Auth\TwoFactorController@enable2fa')->name('enable2fa');
    Route::post('/disable2fa','Auth\TwoFactorController@disable2fa')->name('disable2fa');

    // Finish 2fa-login
    // Finaly enable 2fa with the test
    Route::post('/internal-api/settings/2faTest', 'Auth\TwoFactorController@my2faTest')->middleware('2fa');
    Route::post('/internal-api/settings/refresh/twofactor', 'Auth\TwoFactorController@my2faRefresh');
    Route::post('/internal-api/settings/get/twofactor', 'Auth\TwoFactorController@my2faGet');
    Route::post('/internal-api/settings/disable/twofactor', 'Auth\TwoFactorController@my2fadisable');
    Route::post('/internal-api/twofactor/cancel', 'Auth\TwoFactorController@cancelProcess');
    Route::post('/internal-api/twofactor/recovery', 'Auth\TwoFactorController@my2faverifyrecovery');
});

Route::middleware('guest', 'throttle:60,1')->group(function () {
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

    // Registration Routes...
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');


    Route::post('/2faVerify', 'Auth\TwoFactorController@my2faverify')->name('2faVerify')->middleware('2fa');
    // Password Reset Routes...
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name("password.reset");
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name("password.email");
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name("password.update");

    Route::post('/internal-api/login', 'Auth\LoginController@login');

});

Route::get('/', function () {
    return view('base');
});
Route::group(
    ['prefix' => 'oauth', 'as' => 'oauth.', 'middleware' => ['guest', 'throttle']], function () {
    Route::get('/{provider}', 'Auth\SocialiteController@redirectToProvider')->name('login')->where('provider', 'google|github|bitbucket|facebook|gitlab|twitter|linkedin');
    Route::get('/{provider}/callback', 'Auth\SocialiteController@handleProviderCallback')->where('provider', 'google|github|bitbucket|facebook|gitlab|twitter|linkedin');
});

Route::get('/internal-api/users', 'UserController@get');

Route::post('login', 'Auth\LoginController@login')->middleware('2fa');

Route::get('/internal-api/project', 'ProjectController@get');

Route::get('/home', 'HomeController@index')->name('home')->middleware(['auth', '2fa']);

Route::get('/internal-api/refresh-csrf', function(){
  return response()->json(["csrf"=>csrf_token()],200);
});

Route::post('/internal-api/setlogin','UserController@setLogin');
Route::post('/internal-api/checkusername','UserController@checkUsernameExists');

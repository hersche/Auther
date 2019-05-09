<?php
use App\User;
use App\Http\Resources\User as UserResource;

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

Route::get('/', function () {
    return view('base');
});
Route::group(
    ['prefix' => 'oauth', 'as' => 'oauth.', 'middleware' => ['guest', 'throttle']], function () {
    Route::get('/{provider}', 'Auth\SocialiteController@redirectToProvider')->name('login')->where('provider', 'google|github|bitbucket|facebook|gitlab|twitter|linkedin');
    Route::get('/{provider}/callback', 'Auth\SocialiteController@handleProviderCallback')->where('provider', 'google|github|bitbucket|facebook|gitlab|twitter|linkedin');
});
Route::get('/2fa','PasswordSecurityController@show2faForm');
Route::post('/generate2faSecret','PasswordSecurityController@generate2faSecret')->name('generate2faSecret');
Route::post('/2fa','PasswordSecurityController@enable2fa')->name('enable2fa');
Route::post('/disable2fa','PasswordSecurityController@disable2fa')->name('disable2fa');


Route::post('/verify2FA','HomeController@verify2FA');

// TODO move logic to controllers

// Finish 2fa-login
Route::post('/2faVerify', 'PasswordSecurityController@my2faverify')->name('2faVerify')->middleware('2fa');
// Finaly enable 2fa with the test
Route::post('/internal-api/settings/2faTest', 'PasswordSecurityController@my2faTest')->middleware('2fa');
Route::post('/internal-api/settings/refresh/twofactor', 'PasswordSecurityController@my2faRefresh');
Route::post('/internal-api/settings/get/twofactor', 'PasswordSecurityController@my2faGet');
Route::post('/internal-api/settings/disable/twofactor', 'PasswordSecurityController@my2fadisable');
Route::post('/internal-api/twofactor/cancel', 'PasswordSecurityController@cancelProcess');

Route::get('/internal-api/users', 'UserController@get');
/* auth routes start */

Auth::routes(['verify' => true]);

 // Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->middleware('2fa');
Route::post('/checkLogin', 'Auth\SocialiteController@login')->middleware('2fa');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name("password.reset");
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name("password.email");
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name("password.update");
/* auth routes end */


Route::post('/internal-api/project', 'ProjectController@update');
Route::post('/internal-api/project/create', 'ProjectController@create');
Route::post('/internal-api/project/delete', 'ProjectController@destroy');
Route::get('/internal-api/project', 'ProjectController@get');

Route::post('/internal-api/friends/block', 'FriendController@block');
Route::post('/internal-api/friends/unblock', 'FriendController@unblock');
Route::post('/internal-api/friends/friendRequest', 'FriendController@friendRequest');
Route::post('/internal-api/friends/acceptRequest', 'FriendController@acceptRequest');
Route::post('/internal-api/friends/denyRequest', 'FriendController@denyRequest');
Route::post('/internal-api/friends/unfriend', 'FriendController@unfriend');


Route::post('/internal-api/role', 'RoleController@update');
Route::post('/internal-api/role/create', 'RoleController@create');
Route::post('/internal-api/role/delete', 'RoleController@destroy');
Route::get('/internal-api/role', 'RoleController@get');

Route::post('/internal-api/users/changeroles', 'UserController@changeRoles');
Route::post('/internal-api/login', 'Auth\LoginController@login');
Route::get('/home', 'HomeController@index')->name('home')->middleware(['auth', '2fa']);
Route::post('/internal-api/profiles/edit/{id}','UserController@update')->name('useres.edit')->middleware(['auth', '2fa']);
Route::post('/internal-api/register', 'Auth\RegisterController@register');
Route::post('/internal-api/settings/password','UserController@changePassword');
Route::post('/internal-api/settings/email','UserController@setEmail');
Route::get('/internal-api/refresh-csrf', function(){
  return response()->json(["csrf"=>csrf_token()],200);
});

Route::post('/internal-api/setlogin','UserController@setLogin');   
Route::post('/internal-api/checkusername','UserController@checkUsernameExists');  

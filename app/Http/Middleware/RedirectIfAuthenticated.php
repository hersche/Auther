<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
          
          if(config("auth.guards.web.driver")==="jwt"){
            $jwt_token = Auth::tokenById(Auth::id());
            return redirect('/#/?token='.$jwt_token);
          }
            return redirect('/#/');
        }

        return $next($request);
    }
}

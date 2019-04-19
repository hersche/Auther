<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
    //  var_dump($request->all());
    //  die();
      if(!empty($request->input('redirect_uri'))&&!empty($request->input('response_type'))) {
      //  return redirect("/login?oauth=1");
        return route("login", ['oauth' => 1]);
      } else {
        if (! $request->expectsJson()) {
            return route('login');
        }
      } 
    }
}

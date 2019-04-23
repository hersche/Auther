@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <!-- <form method="POST" action="{{ route('login') }}"> -->
                    <form method="POST" action="/login">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                            <input id="oauth" type="hidden" name="oauth" value="{{ app('request')->input('oauth') }}" required>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row mb-0">
    <div class="col-md-8 offset-md-4">
      @if (config("app.googleauthenabled")=="1")
        <a href="{{route('oauth.login','google')}}" class="btn btn-outline-danger">
            Log in with Google
        </a>
      @endif
      @if (config("app.githubauthenabled")=="1")
        <a href="{{route('oauth.login','github')}}" class="btn btn-outline-secondary">
            Log in with Github
        </a>
      @endif
      @if (config("app.gitlabauthenabled")=="1")
        <a href="{{route('oauth.login','gitlab')}}" class="btn btn-outline-secondary">
            Log in with Gitlab
        </a>
      @endif
      @if (config("app.facebookauthenabled")=="1")
        <a href="{{route('oauth.login','facebook')}}" class="btn btn-outline-secondary">
            Log in with Facebook
        </a>
      @endif
      @if (config("app.bitbucketauthenabled")=="1")
        <a href="{{route('oauth.login','bitbucked')}}" class="btn btn-outline-secondary">
            Log in with Bitbucket
        </a>
      @endif
      
      @if (config("app.linkedinauthenabled")=="1")
        <a href="{{route('oauth.login','linkedin')}}" class="btn btn-outline-secondary">
            Log in with Linkedin
        </a>
      @endif
      @if (config("app.twitterauthenabled")=="1")
        <a href="{{route('oauth.login','twitter')}}" class="btn btn-outline-secondary">
            Log in with Twitter
        </a>
      @endif
    </div>
</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

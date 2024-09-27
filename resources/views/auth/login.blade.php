@extends('web.app')
@section('title')
    {{ __('messages.common.login') }}
@endsection
@section('content')
    <div class="container pt-5">
        <div class="row justify-content-center large-card shadow">
            <div class="col-md-6 pr-0 d-flex justify-content-center align-items-center">
                <img src="{{ asset('theme-assets/img/login.jpg') }}"
                     class="w-100" alt="">
            </div>
            <div class="col-md-6 pl-0">
                <h1 class="display-4 display-sm-2 mt-3  font-weight-bold text-center">{{ __('messages.common.login') }}</h1>
                <div class="d-flex justify-content-center align-items-center mt-md-5 mt-3">
                    <form method="POST" action="{{ route('login') }}" class="web-user-form">
                        @csrf
                        <div class="form-group">
                            <label class="ml-1" for="email">{{ __('messages.common.email') }}</label>
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                   placeholder="{{ __('messages.login.enter_email') }}" tabindex="1"
                                   autofocus
                                   value="{{ (Cookie::get('email') !== null) ? Cookie::get('email') : old('email') }}"
                                   required/>
                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="ml-1" for="password"
                                   class="control-label">{{ __('messages.common.password') }}</label>
                            <input id="password" type="password"
                                   value="{{ (Cookie::get('password') !== null) ? Cookie::get('password') : null }}"
                                   placeholder="{{ __('messages.login.enter_password') }}"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}"
                                   name="password" required tabindex="2"/>
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        </div>
                        <div class="form-group ml-1">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                                       id="remember"{{ (Cookie::get('remember') !== null) ? 'checked' : '' }}>
                                <label class="custom-control-label"
                                       for="remember">{{ __('messages.login.remember_me') }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block btn-submit" tabindex="4">
                                {{ __('messages.common.login') }}
                            </button>
                        </div>
                        <div class="form-group social-login-buttons row">
                            @if(config('services.google.client_id'))
                                <div class="col-6">
                                    <a class="btn btn-block btn-lg btn-outline-danger google-login"
                                       href="{{route('social.login','google')}}">
                                        <i class="fab fa-google"></i> {{ __('messages.login.login_via_google') }}
                                    </a>
                                </div>
                            @endif
                            @if(config('services.facebook.client_id'))
                                <div class="col-6">
                                    <a class="btn btn-block btn-lg btn-outline-info facebook-login"
                                       href="{{route('social.login','facebook')}}">
                                        <i class="fab fa-facebook"></i> {{ __('messages.login.login_via_facebook') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="form-group float-right">
                            <a href="{{ route('password.request') }}" class="text-small">
                                {{ __('messages.login.forgot_password') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

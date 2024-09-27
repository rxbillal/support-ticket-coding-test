@extends('web.app')
@section('title')
{{ __('messages.web.password.forgot_password') }}
@endsection
@section('content')
    <div class="card card-primary col-lg-6 offset-0 offset-lg-3 col-12 mt-5 web-user-form">
        <div class="card-header"><h4>{{ __('messages.web.password.reset_password') }}</h4></div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">{{ __('messages.common.email') }}</label>
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                           name="email" tabindex="1" value="{{ old('email') }}" autofocus required>
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        {{ __('messages.web.password.send_reset_link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="mt-5 text-muted text-center">
        {{ __('messages.web.password.recalled_your_login_info') }} <a href="{{ route('login') }}">{{ __('messages.web.password.sign_in') }}</a>
    </div>
@endsection

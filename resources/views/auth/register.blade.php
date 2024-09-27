@extends('web.app')
@section('title')
    {{ __('messages.login.register') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ mix('assets/css/phone-number-code.css') }}" type="text/css"/>
@endpush
@section('content')
    <div class="container pt-5">
        <div class="row justify-content-center shadow  large-card">
            <div class="col-md-6 pr-0 d-flex justify-content-center align-items-center">
                <img src="{{ asset('theme-assets/img/register.jpg') }}"
                     class="w-100" alt="">
            </div>
            <div class="col-md-6 web-user-form">
                <h1 class="text-center my-4">{{ __('messages.login.register') }}</h1>
                @include('flash::message')
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    <div class="form-group">
                        <label for="first_name">{{ __('messages.common.name').':' }}</label><span
                                class="text-danger">*</span>
                        <input aria-describedby="firstNameHelpBlock" id="firstName" type="text"
                               class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                               name="first_name"
                               placeholder="{{ __('messages.login.enter_name') }}" tabindex="1"
                               value="{{ old('first_name') }}"
                               autofocus
                               required>
                        <div class="invalid-feedback">
                            {{ $errors->first('first_name') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('messages.common.email').':' }}</label><span
                                class="text-danger">*</span>
                        <input aria-describedby="emailHelpBlock" id="email" type="email"
                               class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                               placeholder="{{ __('messages.login.enter_email') }}" tabindex="3"
                               value="{{ old('email') }}"
                               autofocus
                               required>
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('phone', __('messages.common.phone').':') }}<span
                                class="text-danger">*</span><br>
                        <div class="d-flex">
                            <div class="region-code">
                                <button type="button"
                                        class="btn btn-default mr-0 f16 dropdown-toggle region-code-button rounded-button mr-2"
                                        id="dropdownMenuButton" data-toggle="dropdown">
                <span class="flag {{ !empty(getSettingValue('default_country_code')) ? getSettingValue('default_country_code') : 'in' }}"
                      id="btnFlag"></span>
                                    <span class="btn-cc">&nbsp;&nbsp;{{ !empty(getSettingValue('default_region_code')) ? '+'.getSettingValue('default_region_code') : '+91' }}&nbsp;&nbsp;</span>
                                    <span class="caretButton"></span>
                                </button>
                                <div class="region-code-div" aria-labelledby="dropdownMenuButton">
                                    <ul class="f16 dropdown-menu region-code-ul">
                                        <div class="region-code-ul-input-div"><input type="text"
                                                                                     class="form-control search-country"/>
                                        </div>
                                        <div class="region-code-ul-div"></div>
                                    </ul>
                                </div>
                            </div>
                            <input type="tel" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                   name="phone" id="phoneNumber"
                                   placeholder="{{ __('messages.login.enter_phone') }}" tabindex="4"
                                   value="{{ old('phone') }}"
                                   onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                   required/>
                            <input type="hidden" name="region_code" id="regionCode" value="91"/>
                            <input type="hidden" name="region_code_flag" id="regionCodeFlag" value="in"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password"
                               class="control-label">{{ __('messages.common.password').':' }}</label><span
                                class="text-danger">*</span>
                        <div class="input-group">
                            <input aria-describedby="passwordHelpBlock" id="password" type="password"
                                   placeholder="{{ __('messages.login.enter_password') }}"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}"
                                   value="{{ old('password') }}"
                                   name="password"
                                   tabindex="5" required>
                            <div class="input-group-append">
                                <div class="input-group-text rounded-button ml-2">
                                    <a href="javascript:void(0)" class="" onclick="showPassword('password')">
                                        <i class="fa fa-eye-slash"></i></a>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">{{ __('messages.common.confirm_password').':' }}</label><span
                                class="text-danger">*</span>
                        <div class="input-group">
                            <input aria-describedby="confirmPasswordHelpBlock" id="passwordConfirmation" type="password"
                                   class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                   name="password_confirmation"
                                   placeholder="{{ __('messages.login.enter_confirm_password') }}" tabindex="6"
                                   autofocus
                                   value="{{ old('password_confirmation') }}"
                                   required>
                            <div class="input-group-append">
                                <div class="input-group-text rounded-button ml-2">
                                    <a href="javascript:void(0)" class=""
                                       onclick="showPassword('passwordConfirmation')">
                                        <i class="fa fa-eye-slash"></i></a>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block btn-submit">
                            {{ __('messages.common.register') }}
                        </button>
                    </div>
                    
                    <div class="or-social">
                        <span>Or</span>
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
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ mix('assets/js/custom/phone-number-code.js') }}"></script>
    <script>
        let isEdit = true;
        let phoneNo = "{{ old('region_code').old('phone') }}";
        let utilsScript = "{{asset('assets/js/inttel/js/utils.min.js')}}";
        $(document).on('submit', '#registerForm', function (event) {
            event.preventDefault();
            if ($('#error-msg').text() !== '') {
                $('#phoneNumber').focus();
                return false;
            }
            let phoneNumber = $('#phoneNumber').val();
            phoneNumber = phoneNumber.replace(/\s/g, '');
            $('#phoneNumber').val(phoneNumber);

            $('#registerForm')[0].submit();

            return true;
        });
    </script>
@endpush

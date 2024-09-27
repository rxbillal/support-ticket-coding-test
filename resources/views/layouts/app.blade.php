<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    @php
        if(!isset($settings)){
            $settings = \App\Models\Setting::whereIn('key',['application_name','favicon','logo'])->toBase()
            ->get()->pluck('value','key')->toArray();
        }
    @endphp
    <title>@yield('title') | {{ $settings['application_name'] }} </title>
    <link rel="shortcut icon" href="{{ asset($settings['favicon']) }}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>

    <!-- General CSS Files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>

    @stack('css')

<!-- Template CSS -->
    {{--    <link rel="stylesheet" href="{{ asset('theme-assets/css/style.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('theme-assets/css/components.css')}}">--}}

    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ mix('assets/css/phone-number-code.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin_theme.css') }}">
    @stack('page_css')
</head>
<body>
<div id="app" class="admin-app">
    <div class="infy-loader" id="overlay-screen-lock">
        @include('loader')
    </div>
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <nav class="navbar navbar-expand-lg main-navbar">
            @include('layouts.header')
        </nav>
        <div class="main-sidebar">
            @include('layouts.sidebar')
            @include('user_profile.edit_profile_modal')
            @include('user_profile.change_password_modal')
            @include('user_profile.email_setting_modal')
        </div>
        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
        @include('chat.templates.notification')
        @include('partials.file-upload')
        @include('partials.set_custom_status_modal')
        @include('user_profile.change_language_modal')
        <footer class="main-footer">
            @include('layouts.footer')
        </footer>
    </div>
</div>
@routes

<script src="{{ asset('messages.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('theme-assets/js/moment-timezone.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script src="{{ asset('theme-assets/js/stisla.js') }}"></script>
<script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ mix('assets/js/custom/phone-number-code.js') }}"></script>
<script src="{{ asset('assets/js/jsrender.js') }}"></script>
<script src="{{ asset('theme-assets/js/scripts.js') }}"></script>
<script src="{{ mix('assets/js/custom/custom.js') }}"></script>

<script>
    let currentLocale = "{{ Config::get('app.locale') }}";
    let loggedInUserId = '{{Auth::id()}}';
    let changeLanguageUrl = '{{ route('change.language') }}';
    let yesMessages = "{{ __('messages.common.yes') }}";
    let noMessages = "{{ __('messages.common.no') }}";
    let deleteHeading = "{{ __('messages.common.delete') }}";
    let deleteMessage = "{{ __('messages.common.are_you_sure_delete') }}";

    @role('Admin')
    let profileUrl = "{{ url('admin/profile') }}";
    let profileUpdateUrl = "{{ url('admin/profile-update') }}"
    let changePasswordUrl = "{{ url('admin/change-password') }}"
    @endrole
    @role('Agent')
    let profileUrl = "{{ url('agent/profile') }}"
    let profileUpdateUrl = "{{ url('agent/profile-update') }}"
    let changePasswordUrl = "{{ url('agent/change-password') }}"
    @endrole
    let currentUrlName = "{{ Request::url() }}"
    let baseUrl = "{{url('/')}}/"
    let defaultCountryCode = "{{ getSettingValue('default_country_code') }}";

    (function ($) {
        Lang.setLocale(currentLocale)
        $.fn.button = function (action) {
            if (action === 'loading' && this.data('loading-text')) {
                this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true)
            }
            if (action === 'reset' && this.data('original-text')) {
                this.html(this.data('original-text')).prop('disabled', false)
            }
        };
    }(jQuery));
    $(document).ready(function () {
        $('.alert').delay(5000).slideUp(300);
    });
</script>
<script src="{{ mix('assets/js/user_profile/user_profile.js') }}"></script>

@stack('scripts')
</body>
</html>

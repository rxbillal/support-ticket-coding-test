<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ isset($settings) ? asset($settings['favicon']) :  asset(getSettingValue('favicon')) }}"
          type="image/x-icon">

    <link
            href="//fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,400;1,500;1,600;1,700&family=Poppins:ital,wght@0,500;0,900;1,300;1,700&display=swap"
            rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- General CSS Files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
@stack('css')
<!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/web_chat.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/web_theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/web_theme_components.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ mix('assets/css/phone-number-code.css') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/emojionearea.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/web.css') }}">
</head>

<body>
<div id="app">
    <nav class="navbar navbar-expand-lg main-navbar">
        @include('web.header')
    </nav>
    <!-- Main Content -->
    <div class="mt-0">
        <div class="main-section-area">
            @yield('content')
        </div>
    </div>
    @include('web.chat')
    @include('web.templates.single_message')
    @include('web.footer')
</div>

@routes
<!-- General JS Scripts -->
<script src="{{ asset('messages.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('theme-assets/js/moment-timezone.min.js') }}"></script>
<script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('theme-assets/js/scripts.js') }}"></script>
<script src="{{ mix('assets/js/custom/phone-number-code.js') }}"></script>
<script src="{{ asset('assets/js/jsrender.js') }}"></script>
<script src="{{ mix('assets/js/custom/custom.js') }}"></script>
<script src="{{ asset('theme-assets/js/emojionearea.js') }}"></script>
<script src="{{ asset('assets/js/emojione.min.js') }}"></script>
<!-- JS Libraies -->

<!-- Template JS File -->
<script src="{{ asset('theme-assets/js/stisla.js') }}"></script>
<script src="{{ asset('theme-assets/js/scripts.js') }}"></script>
@stack('scripts')
<script>
    let defaultCountryCode = "{{ getSettingValue('default_country_code') }}"
    let chatUserStoreUrl = '{{ route('web.storeChatUser') }}'
    let conversationsStoreUrl = '{{ route('conversations.store') }}'
    let csrfToken = '{{csrf_token()}}'
    let readMessageURL = '{{url('web-read-message')}}'
    let isLogin = '{{ Auth::check() }}'

    let isUTCTimezone = '{{(config('app.timezone') == 'UTC') ? 1  :0 }}'
    let timeZone = '{{config('app.timezone')}}'
    let pusherKey = '{{ config('broadcasting.connections.pusher.key') }}'
    let pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') }}'

    let currentLocale = "{{ Config::get('app.locale') }}";
    
    /** Icons URL */
    let pdfURL = '{{ asset('assets/icons/pdf.png') }}';
    let xlsURL = '{{ asset('assets/icons/xls.png') }}';
    let textURL = '{{ asset('assets/icons/text.png') }}';
    let docsURL = '{{ asset('assets/icons/docs.png') }}';
    let videoURL = '{{ asset('assets/icons/video.png') }}';
    let youtubeURL = '{{ asset('assets/icons/youtube.png') }}';
    let audioURL = '{{ asset('assets/icons/audio.png') }}';
    let setLastSeenURL = '{{url('update-last-seen')}}';
    let baseUrl = "{{url('/')}}/";

    (function ($) {
        Lang.setLocale(currentLocale);
        $.fn.button = function (action) {
            if (action === 'loading' && this.data('loading-text')) {
                this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
            }
            if (action === 'reset' && this.data('original-text')) {
                this.html(this.data('original-text')).prop('disabled', false);
            }
        };
    }(jQuery));

    $(document).ready(function () {
        $('.alert').delay(5000).slideUp(300);

        if (!isLogin && localStorage.getItem('chat-visible') === '1') {
            if(document.location.pathname == '/'){
                $('#chatDivision').addClass('is-visible');
            }
        }
    });


</script>
<script src="{{ mix('assets/js/app.js') }}"></script>
<script src="{{ mix('assets/js/web/chat.js') }}"></script>
<script src="{{ mix('assets/js/web/set-user-on-off.js') }}"></script>
<!-- Page Specific JS File -->

</body>
</html>

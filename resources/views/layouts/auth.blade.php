<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title') | {{ config('app.name') }}</title>

    <!-- General CSS Files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
@stack('css')
<!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('theme-assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('theme-assets/css/components.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body>
<div id="app">
    <div id="app">
        <div class="navbar-bg"></div>
        <nav class="navbar navbar-expand-lg main-navbar">
            @include('web.header')
        </nav>
        <!-- Main Content -->
        <div class="main-content pl-5">
            <div class="container">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- General JS Scripts -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>

<!-- JS Libraies -->

<!-- Template JS File -->
<script src="{{ asset('theme-assets/js/stisla.js') }}"></script>
<script src="{{ asset('theme-assets/js/scripts.js') }}"></script>
@stack('scripts')
<!-- Page Specific JS File -->
</body>
</html>

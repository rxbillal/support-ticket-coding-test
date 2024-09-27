<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="//fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('assets/css/error-pages/minimal-error-page.css') }}" type="text/css"/>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="code">
        @yield('code')
    </div>

    <div class="message minimal-message">
        @yield('message')
    </div>
</div>
</body>
</html>

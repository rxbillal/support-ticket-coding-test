<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="title" content="{{ config('app.name') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>404 Not Found | {{ config('app.name') }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/404_error_page.css') }}" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="error_body">
    <div class="container error_container d-flex justify-content-center align-items-center flex-column w-100 h-100 p-5 text-center">
        <h1 class="error_heading text-center">
            404
        </h1>
        <h2 class="error_message text-center mb-3">Opps! Something's missing...</h2>
        <p class="error_paragraph text-center mb-5">
            The page you are looking for doesn't exists / isn't available / was loading
            incorrectly.
        </p>
        <a href="{{ route('web.home') }}" class="btn btn-primary error_btn">Back to Home Page</a>
    </div>
</div>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>
</html>

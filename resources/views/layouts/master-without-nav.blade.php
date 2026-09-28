<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="@yield('meta_description','AX Secondary CRM - Login')" name="description" />
    <meta content="Themesbrand" name="author" />
   
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/favicon-32x32.png') }}">
    @include('layouts.head-css')
    <style type="text/css">
        .auth-one-bg .bg-overlay {
            background: linear-gradient(90deg, #000000de, #92572261);
            opacity: .2;
        }

        .auth-one-bg {
            background-image: url({{ URL::asset('assets/images/bg_numbers.jpeg') }});
            background-position: top center;
            background-size: cover;
            background-size: cover;
        }

        .auth-one-bg-position {
            height: 480px;

        }

        .pt-8 {
            padding-top: 8rem !important;
        }

        .btn-success {
            background-color: #d3a188;
            border-color: #d3a188;
            color: #333;
        }

        .btn-success:hover {
            background-color: #333;
            border-color: #333;
            color: #d3a188;
        }
    </style>

    
</head>

@yield('body')

@yield('content')

@include('layouts.vendor-scripts')
</body>

</html>

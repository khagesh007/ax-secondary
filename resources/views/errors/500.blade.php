<!doctype html >
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>404 - Page not found! | AX Seconday CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/favicon-32x32.png') }}">

    <script src="{{ URL::asset('assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ URL::asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ URL::asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ URL::asset('assets/css/custom.min.css')}}" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper py-5 d-flex justify-content-center align-items-center min-vh-100">

        <div class="auth-page-content overflow-hidden p-0">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-4 text-center">
                        <div class="error-500 position-relative">
                            <img src="{{ URL::asset('assets/images/error500.png')}}" alt="" class="img-fluid error-500-img error-img">
                            <h1 class="title text-muted">500</h1>
                        </div>
                        <div>
                            <h4>Internal Server Error!</h4>
                            <p class="text-muted w-75 mx-auto">Server Error 500. We're not exactly sure what happened, but our servers say something is wrong.</p>
                            <a href="/" class="btn btn-success"><i class="mdi mdi-home me-1"></i>Back to home</a>
                        </div>
                    </div><!-- end col-->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>

    </div>
    @include('errors.partials._signature')
    <!-- end auth-page-wrapper -->

</body>

</html>

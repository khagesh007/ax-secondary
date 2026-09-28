@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.signin')
@endsection
@section('content')
    <div class="auth-page-wrapper pt-8">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="{{ asset('assets/images/logo/ax-short-logo.png') }}" alt="">
                        </div>

                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-6">
                        <div class="card mt-4">



                            <div class="p-2 mt-4">
                                <div class="col-md-12 mt-4">
                                    <div class="">
                                        <h4 class="card-heading text-center mt-4">Enable Google Two Factor Authentication
                                        </h4>
                                        @if ($errors->any())
                                            <div class="col-md-12">
                                                <div class="alert alert-danger">
                                                    <strong>{{ $errors->first() }}</strong>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="card-body" style="">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-check-circle text-success icon-dual-success icon-xs">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6>1. Download Google Authenticator <a
                                                            href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en&gl=US"
                                                            target="_blank">Andriod</a>
                                                        / <a href="https://apps.apple.com/us/app/google-authenticator/id388497605"
                                                            target="_blank">I0S</a>
                                                    </h6>
                                                    <p class="text-muted">Google Authenticator can be downloaded from the
                                                        App store or Google Play.
                                                        search "Google Authenticator' and proceed to download</p>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-check-circle text-success icon-dual-success icon-xs">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                    </svg>
                                                </div>

                                                <div class="flex-grow-2">

                                                    <h6>2. Add Authentication Token in Google 2FA and keep the key phrase
                                                    </h6>
                                                    <p class="text-muted">Open Google 2FA, scan below OR code or manually
                                                        enter the key phrase to add a
                                                        token.
                                                        Key Phrase is used to recover Google 2FA in the case of phone loss
                                                        of change. Please
                                                        make sure to keep the key phrase, in a safe location before ending.
                                                    </p>

                                                </div>
                                            </div>
                                            <div class="text-center">
                                                {!! $QR_Image !!}
                                            </div>

                                            <div class="text-center">
                                                <div>
                                                    <strong class="mb-3 bg-dark" style="">
                                                        <input type="text" class="form-control-lg"
                                                            value="{{ $secret }}" readonly>

                                                    </strong>
                                                </div>
                                            </div>

                                            <div class="d-flex mt-3">
                                                <div class="flex-shrink-0 me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-check-circle text-success icon-dual-success icon-xs">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                    </svg>
                                                </div>

                                                <div class="flex-grow-2">

                                                    <h6>3. Enable Google Two Factor Authentication
                                                    </h6>


                                                </div>
                                            </div>

                                            <div class="d-flex mt-3">
                                                <div class="flex-shrink-0 me-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-check-circle text-success icon-dual-success icon-xs">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                    </svg>
                                                </div>

                                                <div class="flex-grow-2">

                                                    <h6>4. After scanning and acquiring the 6-digit code, proceed to click
                                                        on "Proceed" below.
                                                    </h6>


                                                </div>
                                            </div>






                                            <div class="row text-center">
                                                <div class="col-12">
                                                    <div class="mt-1 mb-2">
                                                        <a href="{{ route('complete.registration') }}"
                                                            class="btn btn-primary">Proceed</a>
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="card-footer">
                                                <div class="d-flex">

                                                    {{-- <div class="flex-grow-1 text-start">
                                                        @can('Skip Google Authenticator')
                                                        <div class="">
                                                            <a href="{{ route('skip.registration') }}"
                                                                class="fw-semibold text-primary text-decoration-underline">Skip
                                                                two-factor authentication</a>
                                                        </div>
                                                        @endcan
                                                    </div> --}}


                                                    <div class="flex-grow-2">
                                                        <div class="fw-semibold text-primary text-decoration-underline">
                                                            <a href="{{ route('2fa.telegram') }}?auth_cancel=google" class="">
                                                                Login with Telegram
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>




                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- end card body -->
                            </div>
                            <!-- end card -->



                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </div>
            <!-- end auth page content -->

            <!-- footer -->
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">&copy;
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> {{ getApp('name') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('assets/libs/particles.js/particles.js.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>
        <script src="{{ URL::asset('assets/js/pages/password-addon.init.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#loginBtn').click(function() {
                    $(this).html('Please wait...');
                    $(this).addClass('d-none');
                    $('#postLoginBtn').removeClass('d-none');

                });
            });
        </script>
    @endsection

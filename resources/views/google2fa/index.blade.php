@extends('layouts.master-without-nav')
@section('title')
    Enter Google Authenticator Code
@endsection
@section('content')
    <style>
        .otp_input:focus {
            border-color: #d3a188;
            box-shadow: 0 0 5px #d3a188 inset;
        }

        .otp_input ::-moz-selection {
            background: transparent;
        }

        .otp_input ::selection {
            background: transparent;
        }

        @media only screen and (min-width: 100px) and (max-width: 768px) {
            .otp_input {
                margin: 0 5px;
                text-align: center;
                font-size: 12px;
                border: solid 1px #ccc;
                box-shadow: 0 0 5px #ccc inset;
                outline: none;
                transition: all 0.2s ease-in-out;
                border-radius: 3px;
                height: 25px;
                width: 25px;
            }
        }

        @media only screen and (min-width: 769px) {
            .otp_input {
                margin: 0 5px;
                text-align: center;
                font-size: 16px;
                border: solid 1px #ccc;
                box-shadow: 0 0 5px #ccc inset;
                outline: none;
                transition: all 0.2s ease-in-out;
                border-radius: 3px;
                height: 30px;
                width: 30px;
            }
        }
    </style>
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
                    <div class="col-md-8 col-lg-7 col-xl-7">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class=" mt-2">

                                    <h6>Please enter the <strong>OTP</strong> generated on your Google Authenticator App.
                                        Ensure you submit the current one because it refreshes every <b>30
                                            seconds</b>.
                                    </h6>
                                </div>
                                <div class="p-2 mt-4">
                                    <div class="form-group text-center">
                                        @if ($errors->any())
                                            <div class="col-md-12">
                                                <div class="alert alert-danger">
                                                    <strong>{{ $errors->first() }}</strong>
                                                </div>
                                            </div>
                                        @endif

                                        <label for="one_time_password" class="col-md-4 control-label">One Time
                                            Password</label>
                                        <div class="col-md-12 text-center">
                                            <input type="number" class="otp_input" id="input_1" maxLength="1"
                                                size="1" min="0" max="9" pattern="[0-9]{1}" />
                                            <input type="number" class="otp_input" id="input_2" maxLength="1"
                                                size="1" min="0" max="9" pattern="[0-9]{1}" />
                                            <input type="number" class="otp_input" id="input_3" maxLength="1"
                                                size="1" min="0" max="9" pattern="[0-9]{1}" />
                                            <input type="number" class="otp_input" id="input_4" maxLength="1"
                                                size="1" min="0" max="9" pattern="[0-9]{1}" />
                                            <input type="number" class="otp_input" id="input_5" maxLength="1"
                                                size="1" min="0" max="9" pattern="[0-9]{1}" />
                                            <input type="number" class="otp_input" id="input_6" maxLength="1"
                                                size="1" min="0" max="9" pattern="[0-9]{1}" />
                                        </div>
                                    </div>

                                    <form class="form-horizontal" id="otp_form" method="POST"
                                        action="{{ route('2fa') }}">
                                        {{ csrf_field() }}
                                        <div class="form-group">

                                            <div class="col-md-12">

                                                <input id="one_time_password" type="hidden" class="form-control"
                                                    name="one_time_password" required autofocus>
                                            </div>
                                        </div>

                                        <div class="form-group text-center">
                                            <div class="col-md-12 col-md-offset-4 mt-3">



                                                <button type="submit" class="btn btn-primary" id="loginBtn">
                                                    Submit
                                                </button>

                                                <button class="btn btn-primary d-none" id="postLoginBtn"
                                                    type="button">Please wait. <lord-icon
                                                        src="https://cdn.lordicon.com/xjovhxra.json" trigger="loop"
                                                        colors="primary:#121331,secondary:#08a88a"
                                                        style="width:20px;height:20px">
                                                    </lord-icon></button>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="fw-semibold text-primary text-decoration-underline text-center mt-5">


                                        <div class="signin-other-title">
                                            <h5 class="fs-13 mb-4 title">Get six digit code on Telegram.</h5>
                                        </div>
                                        <div>
                                            <a href="{{ route('2fa.telegram') }}?telegram_login=1"
                                                class="btn btn-outline-secondary custom-toggle telegram-btn"
                                                id="disable-btn">
                                                <span class="icon-on"><i
                                                        class="ri-telegram-fill fs-16 align-bottom me-1"></i> Get it
                                                    now</span>
                                            </a>
                                            {{-- <a href="{{ route('2fa.telegram') }}?telegram_login=1"
                                                class="btn btn-primary waves-effect waves-light"><i
                                                    class="ri-telegram-fill fs-16"></i> Get it now</a> --}}

                                        </div>

                                    </div>

                                    <div class="d-flex">

                                        {{-- <div class="flex-grow-1 text-start">
                                            @can('Skip Google Authenticator')
                                                <div class="text-center mt-5">
                                                    <a href="{{ route('skip.registration') }}"
                                                        class="fw-semibold text-primary text-decoration-underline">Skip
                                                        two-factor authentication</a>
                                                </div>
                                            @endcan
                                        </div> --}}

                                        <div class="flex-grow-2">
                                            @can('Regenerate QR Code')
                                                <div
                                                    class="fw-semibold text-primary text-decoration-underline text-center mt-5">
                                                    <a href="{{ route('regenerateQr') }}" class="">
                                                        Didn't Setup Google Authenticator? Setup Now.
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>


                                    <div class="d-flex mt-3 text-end text-danger">
                                        <div class="flex-grow-1">
                                            <a class="text-danger" href="javascript:void();"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                                    class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                                                <span key="t-logout">@lang('translation.logout')</span></a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>
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
        const otpInputs = document.querySelectorAll('.otp_input');

        $(document).ready(function() {
            $('#otp_form').submit(function() {
                $('#loginBtn').addClass('d-none');
                $('#postLoginBtn').removeClass('d-none');
            });





            $('.otp_input').keyup(function() {
                //
                var input_1 = $('#input_1').val();
                var input_2 = $('#input_2').val();
                var input_3 = $('#input_3').val();
                var input_4 = $('#input_4').val();
                var input_5 = $('#input_5').val();
                var input_6 = $('#input_6').val();
                var one_time_password = input_1 + input_2 + input_3 + input_4 + input_5 + input_6;
                $('#one_time_password').val(one_time_password);

            });

            // check if all inputs are filled submite the form auto submit
            $('.otp_input').keyup(function() {
                var filled = true;
                $('.otp_input').each(function() {
                    if ($(this).val() == '') {
                        filled = false;
                    }
                });
                if (filled) {
                    $('#otp_form').submit();
                }
            });


        });



        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.inputType === 'deleteContentBackward' && index > 0) {
                    otpInputs[index - 1].focus();
                } else if (index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && index === 0 && !input.value) {
                    // Move to the next input when pressing backspace on the first input
                    otpInputs[index + 1].focus();
                } else if (e.key === 'Backspace' && index > 0 && !input.value) {
                    otpInputs[index - 1].focus();
                }
            });
        });


        // handle code for if a copy 6 digit code handle that
    </script>

    <script>
        document.addEventListener('paste', function(event) {
            // Access the clipboard data
            var clipboardData = (event.clipboardData || window.clipboardData);

            // Get the pasted text
            var pastedText = clipboardData.getData('text');

            // if the pasted text is 6 digit code
            if (pastedText.length == 6) {
                // split the code into 6
                var splitText = pastedText.split("");
                // loop through the split text and paste it into the input field
                for (var i = 0; i < splitText.length; i++) {
                    $('#input_' + (i + 1)).val(splitText[i]);
                }

                var one_time_password = pastedText;
                $('#one_time_password').val(one_time_password);
                $('#loginBtn').click();
            }



        });


        $(document).on("click", "#disable-btn", function() {
            $('#disable-btn').addClass('disabled');
        });
    </script>
@endsection

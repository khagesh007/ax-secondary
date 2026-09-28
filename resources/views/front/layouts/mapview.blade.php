<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <meta name="format-detection" content="telephone=no">
 <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/favicon-32x32.png')}}?sas">

 @yield('meta_tags')

 <!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Include SimpleLightbox CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.1.0/simple-lightbox.min.css">

<!-- Include SimpleLightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.1.0/simple-lightbox.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />



<input type="hidden" value="{{url('/')}}" id="base_url" >

     @include("front.parts._head")
    </head>
    {{-- <body class="pxp-dark-mode"> --}}
    <body>
        <div class="pxp-header pxp-full fixed-top">
            <div class="container">
                <div class="row align-items-center text-center">
                    <div class="col-lg-12 col-md-12">
                        <a href="/front/auth/listing" class="pxp-logo text-decoration-none"><img class="AX-logo" src="{{ asset('assets/images/logo/short_logo.png') }}" width="140"></a>
                    </div>
                    
                  
                </div>
            </div>
        </div>

        <div class="pxp-content">

            @yield('content')

           
        </div>


      {{--   <div class="modal fade" id="pxp-signin-modal" tabindex="-1" role="dialog" aria-labelledby="pxpSigninModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5 class="modal-title" id="pxpSigninModal">Welcome back!</h5>
                        <form class="mt-4">
                            <div class="form-group">
                                <label for="pxp-signin-email">Email</label>
                                <input type="text" class="form-control" id="pxp-signin-email" placeholder="Enter your email address">
                            </div>
                            <div class="form-group">
                                <label for="pxp-signin-pass">Password</label>
                                <input type="password" class="form-control" id="pxp-signin-pass" placeholder="Enter your password">
                            </div>
                            <div class="form-group">
                                <a href="#" class="pxp-agent-contact-modal-btn">Sign In</a>
                            </div>
                            <div class="form-group mt-4 text-center pxp-modal-small">
                                <a href="#" class="pxp-modal-link">Forgot password</a>
                            </div>
                            <div class="text-center pxp-modal-small">
                                New to Resideo? <a href="javascript:void(0);" class="pxp-modal-link pxp-signup-trigger">Create an account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pxp-signup-modal" tabindex="-1" role="dialog" aria-labelledby="pxpSignupModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5 class="modal-title" id="pxpSignupModal">Create an account</h5>
                        <form class="mt-4">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="pxp-signup-firstname">First Name</label>
                                        <input type="text" class="form-control" id="pxp-signup-firstname" placeholder="Enter first name">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="pxp-signup-lastname">Last Name</label>
                                        <input type="text" class="form-control" id="pxp-signup-lastname" placeholder="Enter last name">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pxp-signup-email">Email</label>
                                <input type="text" class="form-control" id="pxp-signup-email" placeholder="Enter your email address">
                            </div>
                            <div class="form-group">
                                <label for="pxp-signup-pass">Password</label>
                                <input type="password" class="form-control" id="pxp-signup-pass" placeholder="Create a password">
                            </div>
                            <div class="form-group">
                                <a href="#" class="pxp-agent-contact-modal-btn">Sign Up</a>
                            </div>
                            <div class="text-center mt-4 pxp-modal-small">
                                Already have an account? <a href="javascript:void(0);" class="pxp-modal-link pxp-signin-trigger">Sign in</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}

        @include("front.parts._js")
       
    </body>
</html>
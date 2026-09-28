<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="html" data-layout="" data-topbar=""
    data-preloader="enable" data-sidebar="" data-sidebar-size="" data-sidebar-image="" data-layout-style = ""
    data-layout-mode = "" data-layout-width = "">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')| {{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/favicon-32x32.png') }}">
    @include('admin.layouts.head-css')
    <script>
        var dataLayout = localStorage.getItem('data-layout') || 'vertical';
        var dataTopbar = localStorage.getItem('data-topbar') || 'light';
        var dataSidebar = localStorage.getItem('data-sidebar') || 'dark';
        var dataSidebarSize = localStorage.getItem('data-sidebar-size') || 'lg';
        var dataSidebarImage = localStorage.getItem('data-sidebar-image') || 'none';
        var dataLayoutStyle = localStorage.getItem('data-layout-style') || 'default';
        var dataLayoutMode = localStorage.getItem('data-layout-mode') || 'light';
        var dataLayoutWidth = localStorage.getItem('data-layout-width') || 'fluid';
        var dataLayoutPosition = localStorage.getItem('data-layout-position') || 'fixed';
        document.getElementById('html').setAttribute('data-layout', dataLayout);
        document.getElementById('html').setAttribute('data-topbar', dataTopbar);
        document.getElementById('html').setAttribute('data-sidebar', dataSidebar);
        document.getElementById('html').setAttribute('data-sidebar-size', dataSidebarSize);
        document.getElementById('html').setAttribute('data-sidebar-image', dataSidebarImage);
        document.getElementById('html').setAttribute('data-layout-style', dataLayoutStyle);
        document.getElementById('html').setAttribute('data-layout-mode', dataLayoutMode);
        document.getElementById('html').setAttribute('data-layout-width', dataLayoutWidth);
        document.getElementById('html').setAttribute('data-layout-position', dataLayoutPosition);
    </script>

    <style>
        .localEnv {
            background: #424141 !important;
        }
    </style>
    @yield('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var userDetailsRoute = "{{ route('ajax.getUser') }}";
    </script>
</head>

{{--@if (!request()->is('/'))
    <script type="module" src="{{ asset('js/bootstrap.js') }}"></script>
@endif--}}

@section('body')

    @include('admin.layouts.body')
@show
<!-- Begin page -->


<div id="layout-wrapper">

    @include('admin.layouts.topbar')
    @include('admin.layouts.sidebar')
    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid " id="normal-account">
                @yield('content')



            </div>

            <div class="d-none container-fluid m-auto pt-5" id="banned-account">

                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="card">
                            <div class="card-body p-5 text-center" style="border: 2px solid #f06547">
                                <h5 class="">
                                    Please action your New leads in order to access the other modules.
                                    {{-- Contact your current leads to assist in restoring your account privileges;
                                    otherwise, your account
                                    will be suspended. --}}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        @include('admin.layouts.footer')

    </div>
    <!-- end main content-->
</div>
<!-- END layout-wrapper -->

{{-- // Global FILTER COMPONENT --}}

@include('admin.layouts.global-search')


@include('admin.layouts.customizer')

<!-- JAVASCRIPT -->
@include('admin.layouts.vendor-scripts')

@yield('global_scripts')
@stack('agent_filter')
{{-- @if (env('APP_ENV') == 'production')
<script>
    $(document).on("contextmenu",function(e){
    e.preventDefault();
});

</script>

@endif --}}
<script>
    $(document).on("change", "#dashboard-selector", function(e) {
        let url = $(this).find(':selected').data('url');
        if (url) {
            window.location.href = url;
        }

    })
</script>
<script>
    getLeadStatus();
    // setInterval(function() {
    //     getLeadStatus();
    // }, 60000);


    function showCountdown() {
        const now = new Date();
        const targetTime = new Date();
        targetTime.setHours(12, 30, 0, 0); // 9:30 PM
        const timeDiff = targetTime - now;
        if (timeDiff <= 0) {
            $('#liveTime').text('The time has passed.');
        } else {
            const hours = Math.floor(timeDiff / 3600000);
            const minutes = Math.floor((timeDiff % 3600000) / 60000);
            const seconds = Math.floor((timeDiff % 60000) / 1000);
            // make them 2 digit
            const hoursStr = hours.toString().padStart(2, '0');
            const minutesStr = minutes.toString().padStart(2, '0');
            const secondsStr = seconds.toString().padStart(2, '0');

            $('#liveTime').text(`${hoursStr}:${minutesStr}:${secondsStr}`);
        }
    }


    function runCountdown() {
        showCountdown();
        setInterval(showCountdown, 1000);
    }

    function getLeadStatus() {
        $.ajax({
            url: "{{ route('agentDailyReport') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                user_id: "{{ auth()->user()->id }}"
            },
            success: function(response) {
                if (response.status == 1) {
                    runCountdown();
                    $('#agentReportStatusBar').html(response.msg);
                    $('#agentReportStatus').removeClass('d-none');
                    timeBasedDisabler();
                }
                if (response.status == 0) {
                    $('#agentReportStatus').addClass('d-none');
                    timeBasedEnabler();
                }

            }
        });
    }


    function timeBasedDisabler() {
        const now = moment(); // Current date and time

        const targetTime = moment({
            year: now.year(),
            month: now.month(),
            date: now.date(),
            hour: 9,
            minute: 30,
            second: 0,
        });

        const timeDiff = targetTime - now;

        if (timeDiff <= 0) {
            $('#scrollbar .nav-link, .nav-item').addClass('disabled text-danger');
            $('.rm-disabled').removeClass('disabled text-danger');

            const currentURL = window.location.href;
            const baseURL = window.location.origin + '/';

            if (currentURL.includes("work/leads")) {} else if (currentURL == baseURL) {} else {
                $('#normal-account').addClass('d-none');
                $('#banned-account').removeClass('d-none');
            }

        }

    }

    function timeBasedEnabler() {
        const now = moment(); // Current date and time

        const targetTime = moment({
            year: now.year(),
            month: now.month(),
            date: now.date(),
            hour: 9,
            minute: 30,
            second: 0,
        });

        const timeDiff = targetTime - now;


        $('#scrollbar .nav-link, .nav-item').removeClass('disabled text-danger');
        $('.rm-disabled').removeClass('disabled text-danger');

        const currentURL = window.location.href;
        const baseURL = window.location.origin + '/';

        $('#normal-account').removeClass('d-none');
        $('#banned-account').addClass('d-none');



    }

    // $(document).ready(function() {
        var url = window.location.href;
        if (url.includes("portal")) {
            let portal = url.split('portal=')[1];
            if (portal == 'AX-CONTACTS-LOGIN') {
                $('#ax-contacts-login')[0].click();
                // $('#ax-contacts-login').trigger('click');
            } else if (portal == 'AX-CHATS-LOGIN') {
                $('#ax-chats-login')[0].click();

            } else if (portal == 'AX-TELELOGS-LOGIN') {
                $('#ax-telelogs-login')[0].click();

            }
        } else {

        }
    // });
</script>

</body>

</html>

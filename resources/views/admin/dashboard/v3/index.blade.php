@extends('admin.layouts.master')

@section('title')
    Dashboard
@endsection

@section('content')
    @include('admin.dashboard.partials._calender_notification')
    <div id="dashboardPage" class="v-application"></div>
    @include('admin.dashboard._annoucement_modals')
@endsection

@section('script')
    <!-- Load Vue App -->
    <script src="{{ asset('vue/js/dashboard.js') }}"></script>

    <script>
        $(document).ready(function() {
            // uncomment next line for testing. This will reset the acknowledgment status
            // localStorage.setItem('announcementApril01Acknowledged', 'false');
            // Get the current date
            const currentDate = new Date();
            const cutoffDate = new Date('2025-04-30'); // February 12, 2025

            // Check if the current date is before the cutoff date
            if (currentDate < cutoffDate) {
                // Check if the announcement has already been acknowledged
                if (localStorage.getItem('announcementApril01Acknowledged') === 'true') {
                    $('#annoucementModel').modal('hide');
                } else {
                    $('#annoucementModel').modal('show');
                }
            } else {
                // Do not show the modal if the date is February 3, 2025, or later
                $('#annoucementModel').modal('hide');
            }

            // Handle the acknowledgment button click
            $('#acknowledge-btn').on('click', function() {
                $('#annoucementModel').modal('hide');

                if ($('#acknowledged').is(':checked')) {
                    localStorage.setItem('announcementApril01Acknowledged', 'true');
                } else {
                    localStorage.setItem('announcementApril01Acknowledged', 'false');
                }
            });
        });
        $("#DashActive").addClass('active')
    </script>
@endsection

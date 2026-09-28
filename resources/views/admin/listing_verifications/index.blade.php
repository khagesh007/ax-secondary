@extends('admin.layouts.master')

@section('title')
    Listing Verifications
@endsection

@section('content')

    <div id="listing-verifications" class="v-application"></div>

@endsection

@section('script')
    <!-- Load Vue App -->
    <script>
        $("#adminDashboardSidebar").addClass('active')
    </script>
    <script src="{{ asset('vue/js/listing-verifications.js') }}"></script>
@endsection

@extends('admin.layouts.master')

@section('title')
    Admin Dashboard
@endsection

@section('content')

    <div id="admin-dashboard" class="v-application"></div>

@endsection

@section('script')
    <!-- Load Vue App -->
    <script>
        $("#adminDashboardSidebar").addClass('active')
    </script>
    <script src="{{ asset('vue/js/admin-dashboard.js') }}"></script>
@endsection

@extends('admin.layouts.master')

@section('title')
    Agent Dashboard
@endsection

@section('content')
    @include('admin.dashboard.partials._calender_notification')
    <div id="agentDashboardPage" class="v-application"></div>

@endsection

@section('script')
    <!-- Load Vue App -->
    <script src="{{ asset('vue/js/agentDashboard.js') }}"></script>
@endsection

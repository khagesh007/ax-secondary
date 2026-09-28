@extends('layouts.master-without-nav')

@section('title')
    Match Listing Form
@endsection

@section('content')

    <div id="match-listings" class="v-application"></div>

@endsection

@section('script')

    <!-- Telegram WebApp SDK -->
    {{-- <script src="https://telegram.org/js/telegram-web-app.js?57"></script> --}}
    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <!-- Load Vue App -->
    <script src="{{ asset('vue/js/match-listings.js') }}?v={{ time() }}"></script>
       
@endsection

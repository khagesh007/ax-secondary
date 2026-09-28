@extends('layouts.master-without-nav')

@section('title')
    Search Listing Form
@endsection

@section('content')

    <div id="search-listings" class="v-application"></div>

@endsection

@section('script')
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <!-- Load Vue App -->
    <script src="{{ asset('vue/js/search-listings.js') }}?v={{ time() }}"></script>
@endsection

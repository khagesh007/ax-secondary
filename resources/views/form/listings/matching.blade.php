@extends('layouts.master-without-nav')

@section('title')
  Matching
@endsection

@section('content')

<div id="matching" class="v-application"></div>
@endsection

@section('script')
    <!-- Telegram WebApp SDK -->
    {{-- <script src="https://telegram.org/js/telegram-web-app.js?57"></script> --}}
    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <!-- Load Vue App -->
    <script src="{{ asset('vue/js/matching.js') }}?v={{ time() }}"></script>
@endsection

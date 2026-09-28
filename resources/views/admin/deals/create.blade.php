@extends('admin.layouts.master')
@section('title')
    Deals
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--datatable responsive css-->
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/aos/aos.min.css')}}"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div id="dealsApp">
        <deals :deals="{{$deals}}"></deals>
    </div>
@endsection
@section('script')

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{asset('vue/js/vue.js')}}"></script>
    <script>
        // $("#sidebarWork").addClass('collapse show');
        $("#sidebarDeals").addClass('active');
    </script>

@endsection

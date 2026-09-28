@extends('admin.layouts.master')

@section('title')
    Consolidated Reports
@endsection

@section('content')

    <div id="reportPage" ></div>

@endsection

@section('script')
    <script src="{{ asset('vue/js/consolidatedReport.js') }}"></script>

    <script>
        $(document).ready(function() {

        });
        $("#reportsActive").addClass('active')
    </script>
@endsection

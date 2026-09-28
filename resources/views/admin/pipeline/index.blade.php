@extends('admin.layouts.master')

@section('title')
    Pipeline
@endsection

@section('content')

    <div id="pipelinePage" ></div>

@endsection

@section('script')
    <!-- Load Vue App -->
    <script src="{{ asset('vue/js/pipeline.js') }}"></script>

    <script>
        $(document).ready(function() {

        });
        $("#sidebarPipelines").addClass('active')
    </script>
@endsection

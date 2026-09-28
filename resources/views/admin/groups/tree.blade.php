@extends('admin.layouts.master')
@section('title')
    General Settings
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">General Settings</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                            <li class="breadcrumb-item active">General Settings</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#myModal">Add
                            new</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="tree">
                        @foreach ($users as $user)
                            <ul>
                                <li>{{ $user->name }}</li>
                                @if ($user->children->isNotEmpty())
                                    @include('admin.groups.partials.child', ['users' => $user->children])
                                @endif
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
@endsection
@section('script')
@endsection

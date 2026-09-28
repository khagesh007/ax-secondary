@extends('admin.layouts.master')
@section('title')
    Developers MetaData
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }

        h2 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h5 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        table tr td {
            padding: 0px !important
        }
    </style>
@endsection
@section('content')
    @if (session()->has('message'))
        <div class="alert alert-success" id="alertMessage">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">Developers </h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Developers</a></li>
                            <li class="breadcrumb-item active">Meta</li>
                        </ol>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class=" text-center">
                        <h3 class="">
                            Execute Jobs
                        </h3>
                        <hr>
                    </div>
                    <div class="row">

                        <div class="col-md-3">
                            <a href="{{ route('jobs.public.emaar') }}" class="btn btn-primary btn-block">Fetch Emaar
                                Properties</a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('jobs.public.createOffPlanListing') }}"
                                class="btn btn-primary btn-block">Publish Properties</a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('jobs.public.azizi') }}" class="btn btn-primary btn-block">Fetch Azizi
                                Properties</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row d-none">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">
                    <div>
                        <table id="developersTable" class="table align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Job</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jobs as $job)
                                    <tr>
                                        <th>{{ $job->id }}</th>
                                        @foreach (json_decode($job->payload) as $key => $value)
                                            @if ($key == 'displayName')
                                                <td><span class="mx-2"> {{ $value }} </span></td>
                                            @endif
                                        @endforeach


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">
                    <div>
                        <table id="developersTable" class="table align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Unique ID</th>
                                    <th>Developer</th>
                                    <th>Unit No</th>
                                    <th>Title</th>
                                    <th>Project Name</th>
                                    <th>Location</th>
                                    <th>Bed</th>
                                    <th>Plot Size</th>
                                    <th>Price</th>
                                    <th>Type</th>
                                    <th>Created at</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dumps as $dump)
                                    <tr>

                                        <td><span class="mx-2">{{ $dump->id }}</span></td>
                                        <td><span class="mx-2">{{ $dump->unique_id }}</span></td>
                                        <td><span class="mx-2">{{ $dump->developer_id?  $dump?->developer?->full_name:'-' }}</span>

                                        </td>
                                        <td><span class="mx-2">{{ $dump->unit_no }}</span></td>
                                        <td><span class="mx-2">{{ $dump->title }}</span></td>
                                        <td><span class="mx-2">{{ $dump->project_name }}</span></td>
                                        <td><span class="mx-2">{{ $dump->building_location }},  {{ $dump->community_name }}, {{ $dump->community_name }} </span></td>
                                        <td><span class="mx-2">{{ $dump->bed_id }}</span></td>
                                        <td><span class="mx-2">{{ $dump->plot_size }}</span></td>
                                        <td><span class="mx-2">AED {{ $dump->price }}</span></td>
                                        <td><span class="mx-2">{{ $dump->property_type }}</span></td>
                                        <td><span class="mx-2">{{ $dump->created_at->diffForHumans() }}</span></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('#alertMessage').fadeOut('fast');
            }, 5000);
        });
    </script>
@endsection

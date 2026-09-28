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
                            Developers Meta Data
                        </h3>
                        <hr>
                    </div>



                    <form action="{{ route('admin.developer.meta.store') }}" id="mainForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <select name="developer_id" id="developer_id" class="form-control">
                                            <option value="">Select Developer</option>
                                            @foreach ($developers as $developer)
                                                <option value="{{ $developer->id }}">{{ $developer->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Project Name"
                                            name="project_name">
                                    </div>

                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-10" id="appendHere">
                                        <input type="text" class="form-control mt-2" placeholder="Meta Title"
                                            name="titles[]">
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary" id="appendTitle" type="button">Add</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-5">
                                        <input type="file" class="form-control" placeholder="Meta Title" name="images[]"
                                            multiple>
                                    </div>
                                </div>
                            </div> --}}

                        </div>
                        <div class="col-md-12 text-end mt-2">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div id="cloneInput" class="d-none">
        <input type="text" class="form-control mt-2" placeholder="Meta Title" name="titles[]">
    </div>
@endsection
@section('script')
    @include('admin.partials.datatable')

    <script>
        $(document).ready(function() {
            $('#developersTable').DataTable({
                responsive: true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],


            });

            $("#appendTitle").click(function() {
                $("#appendHere").append($("#cloneInput").html());
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#developer_id').select2();
            // Get references to the developer and project dropdowns
            var developerDropdown = $("#developer_id");
            var projectDropdown = $("#project_id");

            // Define the projects for each developer
            var projects = {
                'Emaar': @json(config('developers.projects.Emaar')), // Replace with your actual config key
                'Azizi': @json(config('developers.projects.Azizi')), // Replace with your actual config key
            };

            // Function to populate the project dropdown based on the selected developer
            function populateProjects(developerId) {
                projectDropdown.empty();
                var developerProjects = projects[developerId];
                if (developerProjects) {
                    $.each(developerProjects, function(key, value) {
                        projectDropdown.append($("<option></option>")
                            .attr("value", value)
                            .text(value));
                    });
                }
                $('#project_id').select2();


            }

            // Initialize the project dropdown based on the default selected developer
            populateProjects(developerDropdown.val());

            // Event listener to update the project dropdown when the developer selection changes
            developerDropdown.on("change", function() {
                populateProjects($(this).val());
            });
        });
    </script>
@endsection

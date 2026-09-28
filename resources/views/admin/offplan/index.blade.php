@extends('admin.layouts.master')
@section('title')
    Projects MetaData
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

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

        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }

        .scrollable-notes .card-animate:hover {
            box-shadow: 0px 0px 0px 0px rgba(30, 32, 37, .12) !important;
        }

        .non-clickable {
            pointer-events: none;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">Developers</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Projects</a></li>
                            <li class="breadcrumb-item active">Meta</li>
                        </ol>
                    </div>

                    <div class="flex-grow-2">
                        <a href="{{ route('admin.developer.meta.create') }}" class="btn btn-primary">Add Project</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row d-none">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class=" text-center">
                        <h3 class="">
                            Developers Meta Data
                        </h3>
                        <hr>
                    </div>



                    <form action="" id="mainForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <select name="developer" id="developer_id" class="form-control">
                                            <option value="">Select Developer</option>
                                            @foreach (config('developers.developers') as $key => $developer)
                                                <option value="{{ $developer }}">{{ $developer }}</option>
                                            @endforeach



                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="project_name" id="project_id" class="form-control">
                                            <option value="">Select Project</option>
                                            @foreach (config('developers.projects.Emaar') as $developer)
                                                <option value="{{ $developer }}">{{ $developer }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6">
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
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
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
                                    <th>Thumbnail</th>
                                    <th>Project</th>
                                    <th>Developer</th>
                                    {{-- <th>Location</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($metas as $meta)
                                    <tr>

                                        <td><span class="mx-2">{{ $meta->id }}</span></td>
                                        @if ($meta->images->count() > 0)
                                            <td><img src="{{ getSingleImage($meta?->images?->first()?->value) ?? 'https://dry-hire.com/media/platzhalter.jpg' }}"
                                                    alt="" class="img-fluid img-thumbnail my-2"
                                                    style="max-width: 40px"></td>
                                        @else
                                            <td><img src="https://dry-hire.com/media/platzhalter.jpg" alt=""
                                                    class="img-fluid img-thumbnail my-2" style="max-width: 40px"></td>
                                        @endif
                                        {{-- <td><img src="{{ getSingleImage($meta?->images?->first()?->value) ?? 'https://dry-hire.com/media/platzhalter.jpg' }}" alt="" class="img-fluid img-thumbnail my-2" style="max-width: 40px"></td> --}}
                                        <td><a
                                                href="{{ route('admin.developer.meta.edit', $meta->id) }}">{{ $meta->project_name }}</a>
                                        </td>
                                        <td>{{ $meta->developer->full_name }}</td>
                                        {{-- <td>{{ $meta->developer->full_name }}</td> --}}

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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


        // submit form #mainform
        $("#mainForm").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.developer.meta.store') }}",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.status == "1") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });
                    }
                },
                error: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
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

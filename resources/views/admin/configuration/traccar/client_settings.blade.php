@extends('admin.layouts.master')
@section('title')
    Traccar | Client Settings
@endsection
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Configuration</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Traccar</a></li>
                            <li class="breadcrumb-item active">Client Settings v<span id="versionValue">{{$record['version']}}</span></li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        @can('Traccar Client Settings Store')
                        <div class="form-check form-switch form-switch-right form-switch-md">

                        <button type="submit" class="btn btn-success action-save" id="saveBtn">Save</button>
                        @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="form-section">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form id="mainForm" class="needs-validation" novalidate>
                        <input type="hidden" id="id" name="id" value="{{$record['id']}}">
                        <div class="row">
                            <div class="col-6">
                                <div class="row mb-3">


                                    <div class="col-lg-4 mt-2">
                                        <label for="name" class="form-label">Server URL</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="server_url" name="server_url"
                                               placeholder="Enter server URL" value="{{$record['server_url']}}" required>
                                    </div>


                                    <div class="col-lg-4 mt-2">
                                        <label for="name" class="form-label">Server Login</label>
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <input type="text" class="form-control inputs" id="username" name="username"
                                               placeholder="Enter server Username" value="{{$record['username']}}" required>
                                    </div>


                                    <div class="col-lg-4 mt-2">
                                        <input type="text" class="form-control inputs" id="password" name="password"
                                               placeholder="Enter server Password" value="{{$record['password']}}" required>
                                    </div>


                                    <div class="col-lg-4 mt-2">
                                        <label for="name" class="form-label">Port</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="port" name="port"
                                               placeholder="Enter Port" value="{{$record['port']}}" required>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="region_id" class="form-label">Location Accuracy</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <select class="form-control select2 inputs" id="location_accuracy"
                                                name="location_accuracy" required>
                                                <option value="high">High</option>
                                                <option value="medium">Medium</option>
                                                <option value="low">Low</option>
                                        </select>
                                    </div>


                                    <div class="col-lg-4 mt-2">
                                        <label for="path_name" class="form-label">Frequency (seconds)</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="number" class="form-control inputs" id="frequency" name="frequency" value="{{$record['frequency']}}"
                                               placeholder="Enter frequency (seconds)" required min="180">
                                              <span> <small class="mt-2">Please enter value Greater than 180</small> </span>
                                    </div>



                                </div>
                            </div>

                            <div class="col-6">
                                <div class="row mb-3">

                                    <div class="col-lg-4 mt-2">
                                        <label for="coordinates_accuracy" class="form-label">Coordinates Accuracy</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="coordinates_accuracy" name="coordinates_accuracy"
                                               placeholder="Coordinares Accuracy" required value="{{$record['coordinates_accuracy']}}">
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="distance" class="form-label">Distance (meters)</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="distance" name="distance"
                                               placeholder="Enter distance (meters)" required value="{{$record['distance']}}">
                                    </div>


                                    <div class="col-lg-4 mt-2">
                                        <label for="alternate_path_name" class="form-label">Angle (degree)</label>
                                    </div>
                                    <div class="col-lg-8 mt-2">
                                        <input type="text" class="form-control inputs" id="angle" name="angle"
                                               placeholder="Enter Angle (degree)" value="{{$record['angle']}}" required>
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <label for="track_from" class="form-label">Track from</label>
                                    </div>
                                    <div class="col-lg-3 mt-2">
                                        <input type="time" class="form-control inputs" id="track_from" name="track_from"
                                               placeholder="Track from" value="{{$record['track_from']}}" required>
                                    </div>
                                    <div class="col-lg-2 mt-2">
                                        <label for="track_to" class="form-label">Track To</label>
                                    </div>
                                    <div class="col-lg-3 mt-2">
                                        <input type="time" class="form-control inputs" id="track_to" name="track_to"
                                               placeholder="Track to" value="{{$record['track_to']}}" required>
                                    </div>

                                    <div class="col-lg-4 mt-2">
                                        <label for="statusCheckbox" class="form-label">Offline Buffering</label>
                                    </div>
                                    <div class="col-lg-3 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success"
                                             dir="ltr">
                                            <input type="checkbox" name="offline_buffering" class="form-check-input" id="offline_buffering" @if($record['offline_buffering']==1) checked @endif>
                                            <label class="form-check-label" for="offline_buffering"></label>
                                        </div>
                                    </div>


                                    <div class="col-lg-3 mt-2">
                                        <label for="Checkstatus" class="form-label mt-1">Status</label>
                                    </div>
                                    <div class="col-lg-2 mt-2">
                                        <div class="form-check form-switch form-switch-lg form-switch-success"
                                             dir="ltr">
                                            <input type="checkbox" name="status" class="form-check-input"  @if($record['status']==1) checked @endif>
                                            <label class="form-check-label" for="Checkstatus"></label>
                                        </div>
                                    </div>




                                </div>
                            </div>

                        </div>

                        <div class="text-end">
                            <button type="submit" id="submitBtn" class="btn btn-primary d-none">Save</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')

    <script>
        let isUpdate = true;





        $("#location_accuracy").select2({
            placeholder: 'Select',
            allowClear: true
        })

        $("#newBtn").click(function () {
            @can("Communities Create")
                isUpdate = false;
                initMap(25.2048, 55.2708, "Dubai")
                $('#mainForm').trigger('reset');
                $('#mainForm').removeClass('was-validated');
                $(".inputs").prop("disabled", false)
                $(".inputs").val("").trigger("change")
                $(".inputs").removeClass("is-invalid")
                $("#id").val("")
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn").addClass("d-none");
                toggleForm(true)
            @endcan
        });



        $(document).on("click", "#saveBtn", function (){
            $("#submitBtn").trigger("click")
        })






        $(document).on('submit', '#mainForm', function (event) {
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('admin.configuration.traccar.update_client_settings')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#submitBtn").prop('disabled', false); //enable submit button
                        let version = $("#versionValue").text();
                        version = parseFloat(version) + 0.1;
                        $("#versionValue").text(version.toFixed(1));
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $("#submitBtn").prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });




        $(document).on("select2:open", () => {
            document.querySelector(".select2-container--open .select2-search__field").focus()
        })


        $("#sidebarConfiguration").addClass('collapse show');
        $("#sidebarConfigTraccarClient").addClass('active');
        $("#configActive").addClass('active');


    </script>

@endsection

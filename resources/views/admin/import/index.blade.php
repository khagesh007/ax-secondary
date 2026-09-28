@extends('admin.layouts.master')
@section('title')
    Property Deals Status
@endsection
@section('style')
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Bulk Import Data</h4>
                       
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                       
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3>Landlord import</h3>
                <form id="landlordForm" enctype="multipart/form-data">
                    <div class="row">
                    <div class="col"><input type="file" class="form-control" name="file"></div>
                    <div class="col"><input type="submit" class="btn btn-primary" value="Import"></div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3>Leads import</h3>
                <form id="leadsForm" enctype="multipart/form-data">
                    <div class="row">
                    <div class="col"><input type="file" class="form-control" name="file"></div>
                    <div class="col"><input type="submit" class="btn btn-primary" value="Import"></div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h3>Property import</h3>
                <form id="propertyForm" enctype="multipart/form-data">
                    <div class="row">
                    <div class="col"><input type="file" class="form-control" name="file"></div>
                    <div class="col"><input type="submit" class="btn btn-primary" value="Import"></div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')

        {{-- <script src=" "></script> --}}


    <script>
        let isUpdate = false;

    


    
   






  



        $(document).on('submit', '#landlordForm', function (event) {
        
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url:  "{{route('admin.import.landlord')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.message, "success")

                      
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                    } else {
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.message, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
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
        $(document).on('submit', '#leadsForm', function (event) {
        
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url:  "{{route('admin.import.leads')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.message, "success")

                      
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                    } else {
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.message, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
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
        $(document).on('submit', '#propertyForm', function (event) {
        
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url:  "{{route('admin.import.property')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.message, "success")

                      
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                    } else {
                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.message, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
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


 

      
    </script>

@endsection

@extends('admin.layouts.master')
@section('title')
    Listing Image
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
       <style>
        .relative svg{
            width: 50px !important;
        }
    </style>
@endsection
@section('content')


    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead>
                                    <th>#</th>
                                    <th>Ref No</th>
                                    <th>Unit Number</th>
                                    <th>Address</th>
                                    <th>Agent</th>
                                </thead>

                            <tbody>
                        @foreach ($listings as $listing)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$listing->ref_no}}</td>
                            <td>{{$listing->unit_no}}</td>
                            <td>{{$listing->location}}</td>
                            <td>{{$listing->agent->full_name}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                   </table>
                    </div>

                </div>
            </div>
        </div>
    </div>


    </div>
@endsection
@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>

</script>

@endsection

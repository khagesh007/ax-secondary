@extends('admin.layouts.master')
@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
    {{--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="{{asset('assets/deal-wizard/assets/css/bd-wizard.css')}}">
    <style>
        main{
            font-family: 'Karla', sans-serif;
        }
        .content{
            font-family: 'Karla', sans-serif !important;
        }
        .page-content {
            padding: calc(21px + 1.5rem) .75rem 60px !important;
        }
        .modal-backdrop.show:nth-of-type(even) {
            z-index: 1049 !important;
        }
        .media{
            display: flex;
        }
        body{
            font-size: 1.0rem;
        }

    </style>
    <title>Leads</title>
@endsection
@section('content')
    <!-- Default Modals -->
    <div class="mt-5"></div>
    <button type="button" class="btn btn-primary " id="listingDealCreateModal">Standard Modal</button>
    <input type="text" id="id" value="12009">
    <input type="text" id="owner_id" value="13783">
{{--    <div id="vueWidgetApp">--}}
{{--       <listing-widget-index></listing-widget-index>--}}
{{--    </div>--}}

    <table class="table">
        <tbody>
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        @foreach($users as $user)
            <tr>
                <td>{{
    $loop->iteration }}</td>
             
                <td>{{$user->id}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection
@section('global_scripts')
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="{{asset('assets/deal-wizard/assets/js/jquery.steps.min.js')}}"></script>
    <script src="{{asset('vue/js/vueWidgets.js')}}"></script>



@endsection


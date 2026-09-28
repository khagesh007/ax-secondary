@extends('layouts.master')
@section('title') @lang('translation.starter') @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Pages @endslot
@slot('title') Starter @endslot
@endcomponent
                
<div class="row">
    <div class="col-xl-12">
        <div class="card">
                <!-- Light Alert -->
                @if(session()->has('message'))
                        <!-- Success Alert -->
                <div class="alert alert-success alert-dismissible shadow fade show" role="alert">
                    <strong><center> {{ session('message')}}</center> </strong> 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                @endif
                <!-- Warning Alert -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible shadow fade show" role="alert">
                        @foreach ($errors->all() as $error)
                            <center><b>{{$error}} </b></center>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Permission Show</h4>

                <div class="flex-shrink-0">
                    <div class="form-check form-switch form-switch-right form-switch-md">
                        <button type="button" class="btn btn-secondary waves-effect waves-light action-add" data-bs-toggle="modal"
                            data-bs-target="#exampleModalgrid">New Permission</button>
                        <!-- <input class="form-check-input code-switcher" type="checkbox" id="card-tables-showcode"> -->
                    </div>

                </div>
            </div><!-- end card header -->

            <div class="card-body">
                <!-- <p class="text-muted mb-4">Use <code>table-card</code> class to show card-based table within a &lt;tbody&gt;.</p> -->

                <div class="live-preview">
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 46px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="cardtableCheck">
                                            <label class="form-check-label" for="cardtableCheck"></label>
                                        </div>
                                    </th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Permission Name</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($permissions as $key=>$permission)
                            
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="cardtableCheck01">
                                            <label class="form-check-label" for="cardtableCheck01"></label>
                                        </div>
                                    </td>
                                    <td><a href="#" class="fw-medium">{{$key+1}}</a></td>
                                    <td>{{ $permission->name }}</td>
                                    <td><span class="badge bg-success">Block</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="edit">
                                                <button type="button" class="btn btn-sm btn-success edit-item-btn" data-bs-toggle="modal" data-bs-target="#exampleModalgrid">
                                                    Edit
                                                 </button>
                                            </div>
                                            <div class="remove">
                                            <form id="sa-params" action="{{route('permission.destroy', $permission->id)}}"
                                                method="POST">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-sm btn-danger remove-item-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteRecordModal">Delete</button>
                                            </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>
<!-- Grids in modals -->

<div class="modal fade" id="exampleModalgrid" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">Permission </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('permissions.store')}}" method="POST">
                @csrf
                 @method('post')
                    <div class="row g-3">
                        <div class="col-xxl-6">
                            <div>
                                <label for="firstName" class="form-label">Permission Name</label>
                                <input type="text" class="form-control" id="role_name" name="name" placeholder="Enter Permission" value="{{ old('name') }}">
                                    @error('name')
                                    <div class="alert alert-danger col-sm-12">
                                        <h6>
                                            <center>{{$message}}</center>
                                        </h6>
                                    </div>
                                    @enderror
                            </div>
                        </div>
                        <!--end col-->
                       
                        <!--end col-->
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div>
    </div>
</div>
 
@endsection
@section('script')
    {{-- <script src=" "></script> --}}

@endsection
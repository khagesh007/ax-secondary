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

        <div class="col-lg-12">
            <div class="card">
                <form method="GET">
                    @csrf
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Listings</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Listings</a></li>
                            <li class="breadcrumb-item active">Listings Images</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                       Publish status :  {{request()->query('publish_status')}}
                    </div>
                    <div class="flex-shrink-0">
                       <input type="text" class="form-control" name="keyword" id="search" placeholder="Search">
                    </div>
                    <div class="flex-shrink-0">
                      <select class="form-control" name="length">
                        <option value="12">12</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>

                     </div>
                     <div class="flex-shrink-0">

                        <select class="form-control select2" name="publish_status">
                            <option value="PUBLISHED">Published</option>
                          <option value="PENDING">Pending</option>
                          <option value="REFUSED">Refused</option>
                          <option value="RENTED">Rented</option>
                          <option value="SOLD">Sold</option>
                          <option value="DRAFT">Draft</option>
                          <option value="POCKET">Pocket</option>
                          <option value="UNPUBLISHED">Un-Published</option>
                          <option value="ARCHIVED">Archive</option>
                        </select>

                       </div>
                    <div class="flex-shrink-0">
                        <button type="submit" class="btn btn-danger add-btn action-add">
                            <i class=" ri-filter-line align-bottom me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">


                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($listings as $listing)
        @if($listing?->first_image?->original != null || $listing?->first_image?->original != '')
        <div  class="col-3">

            <div class="card">
                <img class="card-img-top img-fluid" src="{{$listing?->first_image?->original}}" alt="Card image cap">
                <div class="card-body">
                    <h4 class="card-title mb-2"> {{$listing->ref_no}}

                        <span class="fs-12 float-end">Status : {{$listing->publish_status}}</span>
                    </h4>
                    <p class="card-text">{{$listing->title}}



                    </p>
                    <div class="text-end">
                        <a href="javascript:void(0);" onclick="copyText('{{$listing->ref_no}}')" class="btn btn-primary">Copy Ref</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach

        <div class="col-12 mt-3 text-center">
            {{$listings->withQueryString()->links()}}
        </div>
    </div>
@endsection
@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    function copyText(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).then(function() {
      console.log('Text copied to clipboard successfully!');
    }, function(err) {
      console.error('Could not copy text: ', err);
    });
  } else {
    console.error('Clipboard API not available or not supported in this browser.');
  }
}
</script>

@endsection

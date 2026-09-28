@extends('admin.layouts.master')
@section('title')
    Listing for {{ $property->for == "RENT" ? "Rent" : "Sale" }} - {{ $property->ref_no ?? "" }}
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/swiper/swiper.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="{{ URL::asset('assets/libs/lightbox2/css/lightbox.min.css') }}"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card ribbon-box border ribbon-fill">
                <div class="card-body">

                    <div
                        class="ribbon {{ $property->for == "RENT" ? "ribbon-primary" : "ribbon-danger" }}">{{ $property->for == "RENT" ? "Rent" : "Sale" }}</div>

                    <div class="row gx-lg-5">
                        <div class="col-xl-4 col-md-8 mx-auto">
                            <div class="product-img-slider sticky-side-div">
                                <div class="swiper product-thumbnail-slider p-2 rounded bg-light">
                                    <div class="swiper-wrapper">
                                        @forelse ($property->photos as $photo)
                                            <div class="swiper-slide" data-lightbox="listing-gallery">
                                                <a data-lightbox="listing-gallery" href="{{ $photo->path ?? "" }}">
                                                <img src="{{ $photo->path ?? "" }}" alt=""
                                                     class="img-fluid d-block"/>
                                                </a>
                                            </div>
                                        @empty
                                            <div class="swiper-slide">
                                                <img src="{{ URL::asset('assets/images/image_placeholder2.png') }}"
                                                     alt=""
                                                     class="img-fluid d-block"/>
                                            </div>
                                        @endforelse
                                    </div>
                                    <div class="swiper-button-next bg-white shadow"></div>
                                    <div class="swiper-button-prev bg-white shadow"></div>
                                </div>
                                <!-- end swiper thumbnail slide -->
                                <div class="swiper product-nav-slider mt-2">
                                    <div class="swiper-wrapper">
                                        @forelse ($property->photos as $photo)
                                            <div class="swiper-slide">
                                                <div class="nav-slide-item">
                                                    <img src="{{ $photo->thumbnail ?? "" }}"
                                                         alt=""
                                                         class="img-fluid d-block"/>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="swiper-slide">
                                                <div class="nav-slide-item">
                                                    <img src="{{ URL::asset('assets/images/image_placeholder2.png') }}"
                                                         alt=""
                                                         class="img-fluid d-block"/>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <!-- end swiper nav slide -->
                            </div>
                        </div>
                        <!-- end col -->

                        <div class="col-xl-8">
                            <div class="mt-xl-0 mt-5">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h4>
                                            #{{ $property->ref_no ?? "" }} {{ $property->bed->name ?? "" }} {{ $property->bath->name ?? "" }} {{ $property->propertyCategory->title ?? "" }}</h4>
                                        <div class="hstack gap-3  flex-wrap">
                                            <div>Agent: <a href="#"
                                                           class="text-primary d-block">{{ $property->agent->name ?? "" }}</a>
                                            </div>
                                            <div class="vr"></div>
                                            <div class="text-muted">Owner : <span
                                                    class="text-body fw-medium">{{ $property->owner->first_name ?? "" }} {{ $property->owner->last_name ?? "" }}</span>
                                            </div>
                                            <div class="vr"></div>
                                            <div class="text-muted">Date : <span
                                                    class="text-body fw-medium">{{ $property?->created_at->format("d M, Y") }}</span>
                                            </div>
                                            <div class="vr"></div>
                                            <div class="text-muted">Publish Status : <span
                                                    class="text-body fw-medium">{{ \App\Models\Property::PUBLISH_STATUS_SELECT[$property->publish_status] ?? "" }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div>
                                            <a href="javascript:void(0)"
                                               class="btn btn-light"
                                               data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i
                                                    class="ri-pencil-fill align-bottom"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-bill-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">Price :</p>
                                                    <h5 class="mb-0" style="white-space: nowrap;">AED {{ $property->price->price ?? 0 }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-hotel-bed-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">Beds</p>
                                                    <h5 class="mb-0">{{ $property->bed->name ?? "" }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-stack-fill ri-bed"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">Baths</p>
                                                    <h5 class="mb-0">{{ $property->bath->name ?? "" }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="p-2 border border-dashed rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-title rounded bg-transparent text-success fs-24">
                                                        <i class="ri-fire-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted mb-1">Exclusive</p>
                                                    <h5 class="mb-0">{{ $property->exclusive ? "Yes" : "No" }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class=" mt-4">
                                            <h5 class="fs-14">Agent :</h5>
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 me-3">
                                                    <div>
                                                        <img class="image avatar-xs rounded-circle shadow" alt=""
                                                             src="{{$property->agent->thumbnail ? $property->agent->thumbnail : URL::asset('assets/images/users/avatar-8.jpg')}}">
                                                    </div>
                                                </div>

                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="contact-name fs-13 mb-1"><a href="#"
                                                                                           class="link text-dark">{{ $property->agent->name ?? "" }}</a>
                                                    </h5>
                                                    <p class="contact-born text-muted mb-0">{{ $property->agent->email ?? "" }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class=" mt-4">
                                            <h5 class="fs-14">Portals :</h5>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($property->portals ?? [] as $portal)
                                                    <div data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                         data-bs-placement="top"
                                                         title="{{ $portal->portal->name ?? "" }}">
                                                        <button type="button"
                                                                class="btn avatar-xs p-0 d-flex align-items-center justify-content-center border rounded-circle fs-20 text-primary"
                                                                disabled>
                                                            <img src="{{ $portal->portal->thumbnail ?? "" }}" alt=""
                                                                 height="20px" width="20px">
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-muted">
                                    <h5 class="fs-14">Description :</h5>
                                    <div>{!! $property->description ?? "" !!}</div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mt-3">
                                            <h5 class="fs-14">Features :</h5>
                                            <ul class="list-unstyled">
                                                @foreach($property->amenities ?? [] as $amenity)
                                                    <li class="py-1">
                                                        <img src="{{ $amenity->amenity->thumbnail ?? "" }}" alt=""
                                                             height="20px">
                                                        <span
                                                            class="badge bg-soft-secondary text-secondary align-middle">{{ $amenity->amenity->title ?? "" }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>


                                <div class="product-content mt-5">
                                    <h5 class="fs-14 mb-3">Property Description :</h5>
                                    <nav>
                                        <ul class="nav nav-tabs nav-tabs-custom nav-success" id="nav-tab"
                                            role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="nav-speci-tab" data-bs-toggle="tab"
                                                   href="#nav-speci" role="tab" aria-controls="nav-speci"
                                                   aria-selected="true">Specification</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="nav-detail-tab" data-bs-toggle="tab"
                                                   href="#nav-detail" role="tab" aria-controls="nav-detail"
                                                   aria-selected="false">Details</a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <div class="tab-content border border-top-0 p-4" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="nav-speci" role="tabpanel"
                                             aria-labelledby="nav-speci-tab">
                                            <div class="table-responsive">
                                                <table class="table mb-0">
                                                    <tbody>
                                                    <tr>
                                                        <th scope="row" style="width: 200px;">
                                                            Category
                                                        </th>
                                                        <td>{{ $property->propertyCategory->title ?? "" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Beds</th>
                                                        <td>{{ $property->bed->name ?? "" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Baths</th>
                                                        <td>{{ $property->bath->name ?? "" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Sub Community</th>
                                                        <td>{{ $property->subCommunity->name ?? "" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Property</th>
                                                        <td>{{ $property->communityProperty->name ?? "" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Unit #</th>
                                                        <td>{{ $property->unit_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Built-up Area</th>
                                                        <td>{{ $property->built_up_area }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Furnished</th>
                                                        <td>{{ $property->furnished }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">RERA Permit #</th>
                                                        <td>{{ $property->rera_permit_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Parkings</th>
                                                        <td>{{ $property->parking }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Status</th>
                                                        <td>{{ $property->propertyStatus->title ?? "" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Exclusive</th>
                                                        <td>{{ $property->exclusive ? "Yes" : "No" }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-detail" role="tabpanel"
                                             aria-labelledby="nav-detail-tab">
                                            <div>
                                                {!! $property->description ?? "" !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>

@endsection
@section('script')

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ URL::asset('/assets/libs/swiper/swiper.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/js/pages/ecommerce-product-details.init.js')}}"></script>
        {{-- <script src=" "></script> --}}

    <script src="{{ URL::asset('assets/libs/lightbox2/js/lightbox.min.js') }}"></script>
@endsection

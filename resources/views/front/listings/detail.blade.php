
@extends('front.layouts.mapview')

@section('meta_tags')
@if(Request::route()->getName() =='single_by_ref')
<meta name="robots" content="noindex">
@endif
@if($data['property'])
<title>@if($data['property']['meta_title']){{$data['property']['meta_title']}} @else {{$data['property']['title']}} @endif</title>
@if($data['property']['meta_description'])
<meta name='description' itemprop='description' content='{{$data['property']['meta_description']}} - PK Holidays' />
@else
<meta name='description' itemprop='description' content='{{$data["property"]["title"]}}, {{ $data["property"]["communityProperty"]["full_path_name"] ?? $data["property"]["subCommunity"]["full_path_name"] }}'>
@endif
<meta name='keywords' content=''>
<meta property='article:published_time' content='{{$data["property"]["created_at"]}}'>
<meta property='article:section' content='event' />
<meta property="og:description" content="{{$data['property']['title']}}">
<meta property="og:title" content="{{$data['property']['title']}}">
<meta property="og:url" content="{{url()->current()}}">
<meta property="og:locale" content="en-us" />
<meta property="og:locale:alternate" content="en-us">
<meta property="og:site_name" content="AX CAPITAL">

@if($data['property']->photos->first())
<meta property="og:image:url" itemprop="image primaryImageOfPage"   content="{{env('PPK_CRM_BASE_URL')}}{{ ('/images/listings/full/'.$data['property']->photos->first()->name)}}">
@endif

@foreach($data['property']['photos'] as $key=> $gallery_photo)
<meta property="og:image" content="{{env('PPK_CRM_BASE_URL')}}{{ ('/images/listings/full/'.$gallery_photo['name'])}}">
@endforeach

<meta property="og:image:size" content="300">
<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{{$data['property']['title']}}">
<meta name="twitter:site" content="@PKHolidaysDxb" />
<meta property="og:type" content="website">
<meta property="place:location:latitude" content="25.0954116">
<meta property="place:location:longitude" content="55.1057481">
<meta property="business:contact_data:street_address" content="Suite 304, Cayan Business Centre, Tecom 3">
<meta property="business:contact_data:locality" content="Dubai">
<meta property="business:contact_data:country_name" content="United Arab Emirates">
<meta property="business:contact_data:postal_code" content="212693">
<meta property="business:contact_data:website" content="https://pkholidays.ae">
<meta property="business:contact_data:region" content="NA">
<meta property="business:contact_data:email" content="info@pkproperties.ae">
<meta property="business:contact_data:phone_number" content="04 4541140">
<meta property="business:contact_data:fax_number" content="04 4541141">
<meta property="business:hours:day" content="sunday">
<meta property="business:hours:start" content="08:30">
<meta property="business:hours:end" content="17:30">
<meta property="business:hours:day" content="monday">
<meta property="business:hours:start" content="08:30">
<meta property="business:hours:end" content="17:30">
<meta property="business:hours:day" content="tuesday">
<meta property="business:hours:start" content="08:30">
<meta property="business:hours:end" content="17:30">
<meta property="business:hours:day" content="wednesday">
<meta property="business:hours:start" content="08:30">
<meta property="business:hours:end" content="17:30">
<meta property="business:hours:day" content="thursday">
<meta property="business:hours:start" content="08:30">
<meta property="business:hours:end" content="17:30">
<meta property="business:hours:day" content="saturday">
<meta property="business:hours:start" content="09:00">
<meta property="business:hours:end" content="16:00">

<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="{{ ('/images/assets/ms-icon-144x144.png') }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.1.0/simple-lightbox.min.css">

<meta name="theme-color" content="#ffffff">
<style>

.t-check-in .t-date-info-title, .t-check-out .t-date-info-title {
    left: 32px !important;
}

.sl-overlay{
    background-color: black !important;
    opacity: 0.99 !important;
}
.sl-close{
    color: #fff !important;
}

.sl-next{
    color: #fff !important;
}

.sl-prev{
    color: #fff !important;
}

.sl-counter{
    color: #fff !important;
}
</style>
@endif
@endsection
@section('content')


<div class="pxp-content">

    <div class="pxp-single-property-gallery-container pxp-single-property-top">
        <div class="pxp-single-property-gallery gallery"  itemscope itemtype="http://schema.org/ImageGallery">
           @if($data['property']['photos'] ?? [])
            @isset($data['property']['photos'][0])
            <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" class="pxp-sp-gallery-main-img" data-pswp-uid="1">
                <a href="{{$data['property']['photos'][0]['path']}}" data-lightbox="roadtrip"   data-title="Image Title" data-alt="Image Alt Text">
                    <img src="{{$data['property']['photos'][0]['path']}}" alt="Thumbnail Image" style="width: 100%; height: 100%" >
                </a>
                <figcaption itemprop="caption description">{{$data['property']['title']}}</figcaption>
            </figure>
           @endif
           @isset($data['property']['photos'][1])
           <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" >
              <a href="{{$data['property']['photos'][1]['path']}}" data-lightbox="roadtrip"  data-title="Image Title" data-alt="Image Alt Text">
                <img src="{{$data['property']['photos'][1]['path']}}" alt="Thumbnail Image" style="width: 100%; height: 100%" >
            </a>
              <figcaption itemprop="caption description">{{$data['property']['title']}}</figcaption>
           </figure>
           @endif
           @isset($data['property']['photos'][2])
           <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
            <a href="{{$data['property']['photos'][2]['path']}}" data-lightbox="roadtrip" data-title="Image Title" data-alt="Image Alt Text">
                <img src="{{$data['property']['photos'][2]['path']}}" alt="Thumbnail Image" style="width: 100%; height: 100%">
            </a>
              <figcaption itemprop="caption description">{{$data['property']['title']}}</figcaption>
           </figure>
           @endif
           @isset($data['property']['photos'][3])
           <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
            <a href="{{$data['property']['photos'][3]['path']}}" data-lightbox="roadtrip" data-title="Image Title" data-alt="Image Alt Text">
                <img src="{{$data['property']['photos'][3]['path']}}" alt="Thumbnail Image" style="width: 100%; height: 100%">
            </a>
              <figcaption itemprop="caption description">{{$data['property']['title']}}</figcaption>
           </figure>
           @endif
           @isset($data['property']['photos'][4])
           <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
            <a href="{{$data['property']['photos'][4]['path']}}" data-lightbox="roadtrip" data-title="Image Title" data-alt="Image Alt Text">
                <img src="{{$data['property']['photos'][4]['path']}}" alt="Thumbnail Image" style="width: 100%; height: 100%">
            </a>
              <figcaption itemprop="caption description">{{$data['property']['title']}}</figcaption>
           </figure>
           @endif
           @endif

           @foreach($data['property']['photos'] ?? [] as $photo)

              @if($loop->index >=5)

              <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" style="display:none;">
                <a href="{{$photo['path']}}" data-lightbox="roadtrip" data-title="Image Title" data-alt="Image Alt Text">
                    <img src="{{$photo['path']}}" alt="Thumbnail Image" style="width: 100%; height: 100%">
                </a>
              <figcaption itemprop="caption description">{{$data['property']['title']}}</figcaption>

              @endif
           </figure>


           @endforeach
        </div>

        {{-- <a href="#" class="pxp-sp-gallery-btn">View Photos</a> --}}
        <div class="clearfix"></div>
    </div>


   {{-- <div class="clearfix"></div>
   <div class="show-more-images d-none">

    <div class="row mt-3">

        @foreach ($data['property']['photos'] as $photo )
        @isset($photo)
        <figure class="col-sm-3" itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
           <a  itemprop="contentUrl"  >
               <img src="{{$photo['path']}}" alt="" style="width: 100%"  srcset="">
           </a>
        </figure>
        @endisset
        @endforeach
    </div>
  </div>
  <div class="d-flex justify-content-center mt-2">
      <span class="button-show-more-images pxp-view-more-photos-and-close-button">View Photos</span>
      <span class="button-close-more-images pxp-view-more-photos-and-close-button d-none">Hide Photos</span>
  </div> --}}



  <div class="mt-100">
      <div class="container">
          <div class="row">

            <div class="col-sm-12 ">
               <div class="pxp-blog-post-category"> @php ($slug1='furnished-'.$data['property']['propertyCategory']['slug'].'-for-rent')
                  <span><a style="color:goldenrod" href="/front/auth/listing?search_location_id={{$data['property']['community']['id']}}&search_location_type=community">{{$data['property']['community']['name']}}</a> <span class="mx-1">|</span>  </span>
                  <span><a style="color:goldenrod" href="/front/auth/listing?search_location_id={{$data['property']['subCommunity']['id']}}&search_location_type=sub_community">{{$data['property']['subCommunity']['name']}}</a> <span class="mx-1">|</span>  </span>
                  @if($data['property']['communityProperty'] != null)
                    <span><a style="color:goldenrod" href="/front/auth/listing?search_location_id={{$data['property']['communityProperty']['id']}}&search_location_type=community_property">{{$data['property']['communityProperty']['name']}}</a>  </span>
                  @endif

                  <span class="pxp-publish-status-badge">
                    <span class="pxp-sale-or-rent-badge-text">
                    {{$data['property']['publish_status']}}
                    </span>
                </span>
               </div>
               <div class="d-flex w-100 justify-content-between">
                   <h2 class="pxp-sp-top-title">{{$data['property']['title']}}</h2>

                   <div class="d-flex">
                    <div class="pxp-sp-top-price mt-3  mt-md-0" style="font-size:32px;">
                        AED {{number_format($data['property']['price']['price'])}}@if($data['property']['price_week'])
                        {{-- <p class="list-inline-item" style="font-size:11px; margin:0px; display:inherit;line-height: 11px;"> <small class="sml">AED</small> {{number_format($data['property']['price_week'])}} / week</p> --}}
                        @endif
                     </div>
                     <br>


                   </div>

               </div>
               <p class="pxp-sp-top-address pxp-text-light"><i class="fa fa-map-marker"></i> {{ $data['property']['communityProperty']['path_name'] ?? $data['property']['subCommunity']['path_name']  }}</p>

            </div>

         </div>
      </div>
   </div>
   <div class="container mt-100 pb-100">
      <div class="row">
         <div class="col-lg-8">
            <div class="pxp-single-property-section">
               <h3>Key Details</h3>
               <div class="row mt-3 mt-md-4">
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Property Type</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['propertyCategory']['title']}}</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Beds</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['bed']['name']}}</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Baths</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['bath']['name']}}</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Size</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['built_up_area']}} SF</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Parking Spaces</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['parking']}}</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Reference #</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['ref_no']}}</div>
                     </div>
                  </div>




               </div>
            </div>





            <div class="pxp-single-property-section mt-4 mt-md-5">
                <h3>Overview</h3>
                <div class="mt-3 mt-md-4">
                    <p>{!! nl2br($data['property']['description']) !!}</p>

                </div>
            </div>
            @if($data['property']->photos->first() && $data['property']['link_youtube'])
            <div class="pxp-blog-post-block pxp-full mt-4 mt-md-5">

               <a href="javascript:void(0);" class="pxp-blog-post-video pxp-cover" style="background-image: url({{env('PPK_CRM_BASE_URL')}}{{ ('images/listings/full/'.$data['property']->photos->first()->name)}}); background-position: 50% 50%;"></a>
               <iframe width="889" height="500" src="https://www.youtube.com/embed/{{$data['link_youtube']}}" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width: 100%; border: 0 none;"></iframe>
            </div>
            @endif

            @if($data['property']->photos->first() && $data['property']['link_360'])
            <div class="pxp-blog-post-block pxp-full mt-4 mt-md-5">
                <h3>360 View</h3>
               <iframe width="889" height="500" src="{{$data['property']['link_360']}}" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width: 100%; border: 0 none;"></iframe>
            </div>
            @endif


            <div class="pxp-single-property-section mt-4 mt-md-5">
               <h3>Amenities</h3>
               <div class="row mt-3 mt-md-4">
                  @foreach($data['property']['amenities'] ?? [] as $amenity)
                  @if(!isset($amenity['amenity']))
                    @continue
                  @endif
                  <div class="col-sm-6 col-lg-4">
                     <div class="pxp-sp-amenities-item"><span><img src="{{$amenity['amenity']['thumbnail'] ?? 'https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/amenities/GtJfiq1VfjgWWj3RB79oYgPY2WFJ42YRWsgpL8uI.svg'}}" width="20"> &nbsp; &nbsp;{{$amenity['amenity']['title']}}</span>
                     </div>
                  </div>
                  @endforeach
               </div>
            </div>
            <div class="pxp-single-property-section mt-4 mt-md-5">
               <h3>Location</h3>
               <div class="pxp-sp-pois-nav mt-3 mt-md-4">
                  {{-- <div class="pxp-sp-pois-nav-transportation text-uppercase">Transportation</div>
                  <div class="pxp-sp-pois-nav-restaurants text-uppercase">Restaurants</div>
                  <div class="pxp-sp-pois-nav-shopping text-uppercase">Shopping</div>
                  <div class="pxp-sp-pois-nav-cafes text-uppercase">Cafes & Bars</div>
                  <div class="pxp-sp-pois-nav-arts text-uppercase">Arts & Entertainment</div>
                  <div class="pxp-sp-pois-nav-fitness text-uppercase">Fitness</div> --}}
               </div>
               <div id="pxp-sp-map" class="mt-3" style="width: 100%; height: 400px;"></div>
            </div>

            <div class="pxp-single-property-section mt-4 mt-md-5">
               <h3>Location Details</h3>
               <div class="row mt-3 mt-md-4">
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Tower/Property</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['communityProperty']['name'] ?? ""}} &nbsp;</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Sub community</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['subCommunity']['name'] ?? ""}}</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">Community</div>
                        <div class="pxp-sp-kd-item-value">{{$data['property']['community']['name'] ?? ""}}</div>
                     </div>
                  </div>
                  <div class="col-sm-6">
                     <div class="pxp-sp-key-details-item">
                        <div class="pxp-sp-kd-item-label text-uppercase">State/City</div>
                        <div class="pxp-sp-kd-item-value">Dubai</div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-lg-4">
            <div class="pxp-single-property-section pxp-sp-agent-section mt-4 mt-md-5 mt-lg-0">
               <h3>{{$data['property']['agent']['position']}}</h3>
               <div class="pxp-sp-agent mt-3 mt-md-4">
                  <a href="javascript:;" class="pxp-sp-agent-fig pxp-cover rounded-lg" style="background-image: url({{$data['property']['agent']['image']}}); background-position: top center">

                </a>
                  <div class="pxp-sp-agent-info ">
                     <div class="pxp-sp-agent-info-name mb-4"><a href="javascript:;">{{$data['property']['agent']['first_name'] ?? ""}} {{$data['property']['agent']['last_name'] ?? ""}}</a></div>
                     {{-- <div class="pxp-sp-agent-info-rating"><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></div> --}}
                     <div class="pxp-sp-agent-info-email "><span class="fa fa-envelope mr-1 "></span><a href="mailto:{{$data['property']['agent']['email'] ?? ''}}">{{$data['property']['agent']['email'] ?? ""}}</a></div>
                     <div class="pxp-sp-agent-info-phone"><span class="fa fa-phone mr-1"></span> {{$data['property']['agent']['mobile'] ?? ""}}</div>
                  </div>
                  <div class="clearfix"></div>

               </div>
            </div>
         </div>
      </div>
   </div>




</div>
@endsection
@section('page_js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&amp;libraries=geometry&amp;libraries=places"></script>
<script src="{{asset('/assets/front/js/markerclusterer.js?lasjkj')}}"></script>
<script src="{{asset('/assets/front/js/photoswipe.min.js')}}"></script>
<script src="{{asset('/assets/front/js/photoswipe-ui-default.min.js')}}"></script>
<script src="{{asset('/assets/front/js/jquery.sticky.js')}}"></script>
<script src="{{asset('/assets/front/js/gallery.js')}}"></script>
<script src="{{asset('/assets/front/js/infobox.js')}}"></script>

<!-- <script src="{{asset('/front/js/single-map.js')}}"></script> -->
<!-- <script src="{{asset('/front/js/map.js?lasjkj')}}"></script> -->
@endsection
@section('inline_js')
<script>

    $(document).ready(function() {
        // Initialize SimpleLightbox
        const gallery = new SimpleLightbox('.gallery a', {
            captions: true, // Enable captions
            close: true,    // Enable close button
            nav: true,       // Enable navigation arrows
            animationSpeed: 250,
        });

        // Add event listener to open lightbox when button is clicked
        $('.pxp-sp-gallery-btn').click(function() {
            console.log(gallery);
            gallery.open();
        });


    });





   $(window).scroll(function (event) {


   if($(window).scrollTop() <= 0) {
      $(".pkh-logo").attr("src", $("#base_url").val()+"/pkh/images/logo-dark.png");
   } else if($(window).scrollTop() > 75) {
      $(".pkh-logo").attr("src", $("#base_url").val()+"/pkh/images/logo-dark.png");
   }
   });




   (function($) {
      "use strict";

    $('.button-show-more-images').click(function(event) {
        $('.show-more-images').removeClass('d-none');
        $('.button-show-more-images').addClass('d-none')
        $('.button-close-more-images').removeClass('d-none')

    });
    $('.button-close-more-images').click(function(event) {
        $('.show-more-images').addClass('d-none');
        $('.button-close-more-images').addClass('d-none')
        $('.button-show-more-images').removeClass('d-none')
    });




      var map;
      var styles;
      var marker                = [];
      var placesIDs             = [];
      var transportationMarkers = [];
      var restaurantsMarkers    = [];
      var shoppingMarkers       = [];
      var cafesMarkers          = [];
      var artsMarkers           = [];
      var fitnessMarkers        = [];

      var propertyData = @json($data['property']);

      var propLat = "";
      var propLng = "";

        if(propertyData['community_property']){

            propLat = propertyData['community_property']['latitude'];
            propLng = propertyData['community_property']['longitude'];

        }else{
            propLat =  propertyData['sub_community']['latitude'];
            propLng = propertyData['sub_community']['longitude'];


        }


    var customStyles =     [
  {
    "featureType": "landscape.man_made",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f7f1df"
      }
    ]
  },
  {
    "featureType": "landscape.natural",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#d0e3b4"
      }
    ]
  },
  {
    "featureType": "landscape.natural.terrain",
    "elementType": "geometry",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "labels",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "poi.business",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f5f1e6"
      }
    ]
  },
  {
    "featureType": "road.arterial",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "road.arterial",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#fdfcf8"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f8c967"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#e9bc62"
      }
    ]
  },
  {
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#e98d58"
      }
    ]
  },
  {
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#db8555"
      }
    ]
  },
  {
    "featureType": "road.local",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "road.local",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#806b63"
      }
    ]
  },
  {
    "featureType": "transit",
    "stylers": [
      {
        "visibility": "off"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#b9d3c2"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#92998d"
      }
    ]
  },
  {
    "featureType": "building",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#ffffff"
      }
    ]
  },
  {
    "featureType": "building",
    "elementType": "geometry.fill",
    "stylers": [
      {
        "color": "#ffffff"
      }
    ]
  },
  {
    "featureType": "building",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#dadada"
      }
    ]
  },
  {
    "featureType": "building",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#616161"
      }
    ]
  },
  {
    "featureType": "building",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#ffffff"
      }
    ]
  },
  {
    "featureType": "3d",
    "elementType": "geometry",
    "stylers": [
      {
        "visibility": "on"
      }
    ]
  }
];




        var options = {
            zoom: 14,
            panControl: false,
            zoomControl: true,
            mapTypeControl: true,
            scaleControl: false,
            streetViewControl: true,
            overviewMapControl: false,
            scrollwheel: true,
            styles: customStyles,
            mapTypeId: 'roadmap', // Set the map type to satellite
            mapId: '7562e9a5aecc3114',
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM,
            },
            fullscreenControl: true,
            tilt: 45, // Set tilt to enable 3D view
        };





      styles = [{"featureType": "water","elementType": "geometry","stylers": [{"color": "#e9e9e9"},{"lightness": 17}]},{"featureType": "landscape","elementType": "geometry","stylers": [{"color": "#f5f5f5"},{"lightness": 20}]},{"featureType": "road.highway","elementType": "geometry.fill","stylers": [{"color": "#ffffff"},{"lightness": 17}]},{"featureType": "road.highway","elementType": "geometry.stroke","stylers": [{"color": "#ffffff"},{"lightness": 29},{"weight": 0.2}]},{"featureType": "road.arterial","elementType": "geometry","stylers": [{"color": "#ffffff"},{"lightness": 18}]},{"featureType": "road.local","elementType": "geometry","stylers": [{"color": "#ffffff"},{"lightness": 16}]},{"featureType": "poi","elementType": "geometry","stylers": [{"color": "#f5f5f5"},{"lightness": 21}]},{"featureType": "poi.park","elementType": "geometry","stylers": [{"color": "#dedede"},{"lightness": 21}]},{"elementType": "labels.text.stroke","stylers": [{"visibility": "on"},{"color": "#ffffff"},{"lightness": 16}]},{"elementType": "labels.text.fill","stylers": [{"saturation": 36},{"color": "#333333"},{"lightness": 40}]},{"elementType": "labels.icon","stylers": [{"visibility": "off"}]},{"featureType": "transit","elementType": "geometry","stylers": [{"color": "#f2f2f2"},{"lightness": 19}]},{"featureType": "administrative","elementType": "geometry.fill","stylers": [{"color": "#fefefe"},{"lightness": 20}]},{"featureType": "administrative","elementType": "geometry.stroke","stylers": [{"color": "#fefefe"},{"lightness": 17},{"weight": 1.2}]}];

      var transportationMarkerImage = {
          url: '{{asset("pkh/images/transportation-marker.png")}}',
          size: new google.maps.Size(47, 47),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(24, 21),
          scaledSize: { width: 47, height: 47 }
      };
      var restaurantsMarkerImage = {
          url: '{{asset("pkh/images/restaurants-marker.png")}}',
          size: new google.maps.Size(47, 47),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(24, 21),
          scaledSize: { width: 47, height: 47 }
      };
      var shoppingMarkerImage = {
          url: '{{asset("pkh/images/shopping-marker.png")}}',
          size: new google.maps.Size(47, 47),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(24, 21),
          scaledSize: { width: 47, height: 47 }
      };
      var cafesMarkerImage = {
          url: '{{asset("pkh/images/cafes-marker.png")}}',
          size: new google.maps.Size(47, 47),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(24, 21),
          scaledSize: { width: 47, height: 47 }
      };
      var artsMarkerImage = {
          url: '{{asset("pkh/images/arts-marker.png")}}',
          size: new google.maps.Size(47, 47),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(24, 21),
          scaledSize: { width: 47, height: 47 }
      };
      var fitnessMarkerImage = {
          url: '{{asset("pkh/images/fitness-marker.png")}}',
          size: new google.maps.Size(47, 47),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(24, 21),
          scaledSize: { width: 47, height: 47 }
      };

      var info = new InfoBox({
          disableAutoPan: false,
          maxWidth: 200,
          pixelOffset: new google.maps.Size(-70, -44),
          zIndex: null,
          boxClass: 'poi-box',
          boxStyle: {
              'background' : '#fff',
              'opacity'    : 1,
              'padding'    : '5px',
              'box-shadow' : '0 1px 2px 0 rgba(0, 0, 0, 0.13)',
              'width'      : '140px',
              'text-align' : 'center',
              'border-radius' : '3px'
          },
          closeBoxMargin: "28px 26px 0px 0px",
          closeBoxURL: "",
          infoBoxClearance: new google.maps.Size(1, 1),
          pane: "floatPane",
          enableEventPropagation: false
      });

      function getPOIs(pos, map, type) {
          var service   = new google.maps.places.PlacesService(map);
          var bounds    = map.getBounds();
          var types     = new Array();

          switch(type) {
              case 'transportation':
                  types = ['bus_station', 'subway_station', 'train_station', 'transit_station', 'airport'];
                  break;
              case 'restaurants':
                  types = ['restaurant'];
                  break;
              case 'shopping':
                  types = ['bicycle_store', 'book_store', 'clothing_store', 'convenience_store', 'department_store', 'electronics_store', 'florist', 'furniture_store', 'hardware_store', 'home_goods_store', 'jewelry_store', 'liquor_store', 'shoe_store', 'shopping_mall', 'store', 'supermarket'];
                  break;
              case 'cafes':
                  types = ['bar', 'cafe'];
                  break;
              case 'arts':
                  types = ['amusement_park', 'aquarium', 'art_gallery', 'bowling_alley', 'casino', 'movie_rental', 'movie_theater', 'museum', 'stadium', 'zoo'];
                  break;
              case 'fitness':
                  types = ['gym'];
                  break;
          }

          $.each(types, function(i, t) {
              service.nearbySearch({
                  location: pos,
                  bounds: bounds,
                  radius: 2000,
                  types: [t]
              }, function poiCallback(results, status) {
                  if (status === google.maps.places.PlacesServiceStatus.OK) {
                      for (var i = 0; i < results.length; i++) {
                          if (jQuery.inArray(results[i].place_id, placesIDs) == -1) {
                              createPOI(results[i], map, type);
                              placesIDs.push(results[i].place_id);
                          }
                      }
                  }
              });
          });
      }

      function createPOI(place, map, type) {
          var placeLoc = place.geometry.location;
          var poiMarker;

          switch (type) {
              case 'transportation':
                  poiMarker = new google.maps.Marker({
                      map: map,
                      position: placeLoc,
                      icon: transportationMarkerImage,
                  });
                  transportationMarkers.push(poiMarker);
                  break;
              case 'restaurants':
                  poiMarker = new google.maps.Marker({
                      map: map,
                      position: placeLoc,
                      icon: restaurantsMarkerImage,
                  });
                  restaurantsMarkers.push(poiMarker);
                  break;
              case 'shopping':
                  poiMarker = new google.maps.Marker({
                      map: map,
                      position: placeLoc,
                      icon: shoppingMarkerImage,
                  });
                  shoppingMarkers.push(poiMarker);
                  break;
              case 'cafes':
                  poiMarker = new google.maps.Marker({
                      map: map,
                      position: placeLoc,
                      icon: cafesMarkerImage,
                  });
                  cafesMarkers.push(poiMarker);
                  break;
              case 'arts':
                  poiMarker = new google.maps.Marker({
                      map: map,
                      position: placeLoc,
                      icon: artsMarkerImage,
                  });
                  artsMarkers.push(poiMarker);
                  break;
              case 'fitness':
                  poiMarker = new google.maps.Marker({
                      map: map,
                      position: placeLoc,
                      icon: fitnessMarkerImage,
                  });
                  fitnessMarkers.push(poiMarker);
                  break;
          }

          google.maps.event.addListener(poiMarker, 'mouseover', function() {
              info.setContent(place.name);
              info.open(map, this);
          });
          google.maps.event.addListener(poiMarker, 'mouseout', function() {
              info.open(null,null);
          });
      }

      function tooglePOIs(map, type) {
          for (var i = 0; i < type.length; i++) {
              if (type[i].getMap() != null) {
                  type[i].setMap(null);
              } else {
                  type[i].setMap(map);
              }
          }
      }

      function CustomMarker(latlng, map, classname) {
          this.latlng_   = latlng;
          this.classname = classname;

          this.setMap(map);
      }

      CustomMarker.prototype = new google.maps.OverlayView();

      CustomMarker.prototype.draw = function() {
          var me = this;
          var div = this.div_;

          if (!div) {
              div = this.div_ = document.createElement('div');
              div.classList.add(this.classname);

              var panes = this.getPanes();
              panes.overlayImage.appendChild(div);
          }

          var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);

          if (point) {
              div.style.left = point.x + 'px';
              div.style.top = point.y + 'px';
          }
      };

      function addPropMarker(propLat, propLng, map) {
          var latlng = new google.maps.LatLng(propLat, propLng);
          marker = new CustomMarker(latlng, map, 'pxp-single-marker');
      }

      setTimeout(function() {
          if($('#pxp-sp-map').length > 0) {
              map = new google.maps.Map(document.getElementById('pxp-sp-map'), options);

              var styledMapType = new google.maps.StyledMapType(styles, {
                  name : 'Styled',
              });
              var center = new google.maps.LatLng(propLat, propLng);

              map.mapTypes.set('Styled', styledMapType);
              map.setCenter(center);
              map.setZoom(15);

              addPropMarker(propLat, propLng, map);

              $('.pxp-sp-pois-nav-transportation').click(function() {

                  var this_ = $(this);
                  if($(this).hasClass('pxp-active')) {
                      $(this).removeClass('pxp-active');

                      tooglePOIs(map, transportationMarkers);
                  } else {
                      $(this).addClass('pxp-active');

                      getPOIs(center, map, 'transportation');
                      tooglePOIs(map, transportationMarkers);
                  }
                  google.maps.event.addListener(map, 'bounds_changed', function() {

                      if(this_.hasClass('pxp-active')) {
                          getPOIs(map.getCenter(), map, 'transportation');
                      }
                  });
              });

              $('.pxp-sp-pois-nav-restaurants').click(function() {
                  var this_ = $(this);
                  if($(this).hasClass('pxp-active')) {
                      $(this).removeClass('pxp-active');

                      tooglePOIs(map, restaurantsMarkers);
                  } else {
                      $(this).addClass('pxp-active');

                      getPOIs(center, map, 'restaurants');
                      tooglePOIs(map, restaurantsMarkers);
                  }
                  google.maps.event.addListener(map, 'bounds_changed', function() {
                      if(this_.hasClass('pxp-active')) {
                          getPOIs(map.getCenter(), map, 'restaurants');
                      }
                  });
              });

              $('.pxp-sp-pois-nav-shopping').click(function() {
                  var this_ = $(this);
                  if($(this).hasClass('pxp-active')) {
                      $(this).removeClass('pxp-active');

                      tooglePOIs(map, shoppingMarkers);
                  } else {
                      $(this).addClass('pxp-active');

                      getPOIs(center, map, 'shopping');
                      tooglePOIs(map, shoppingMarkers);
                  }
                  google.maps.event.addListener(map, 'bounds_changed', function() {
                      if(this_.hasClass('pxp-active')) {
                          getPOIs(map.getCenter(), map, 'shopping');
                      }
                  });
              });

              $('.pxp-sp-pois-nav-cafes').click(function() {
                  var this_ = $(this);
                  if($(this).hasClass('pxp-active')) {
                      $(this).removeClass('pxp-active');

                      tooglePOIs(map, cafesMarkers);
                  } else {
                      $(this).addClass('pxp-active');

                      getPOIs(center, map, 'cafes');
                      tooglePOIs(map, cafesMarkers);
                  }
                  google.maps.event.addListener(map, 'bounds_changed', function() {
                      if(this_.hasClass('pxp-active')) {
                          getPOIs(map.getCenter(), map, 'cafes');
                      }
                  });
              });

              $('.pxp-sp-pois-nav-arts').click(function() {
                  var this_ = $(this);
                  if($(this).hasClass('pxp-active')) {
                      $(this).removeClass('pxp-active');

                      tooglePOIs(map, artsMarkers);
                  } else {
                      $(this).addClass('pxp-active');

                      getPOIs(center, map, 'arts');
                      tooglePOIs(map, artsMarkers);
                  }
                  google.maps.event.addListener(map, 'bounds_changed', function() {
                      if(this_.hasClass('pxp-active')) {
                          getPOIs(map.getCenter(), map, 'arts');
                      }
                  });
              });

              $('.pxp-sp-pois-nav-fitness').click(function() {
                  var this_ = $(this);
                  if($(this).hasClass('pxp-active')) {
                      $(this).removeClass('pxp-active');

                      tooglePOIs(map, fitnessMarkers);
                  } else {
                      $(this).addClass('pxp-active');

                      getPOIs(center, map, 'fitness');
                      tooglePOIs(map, fitnessMarkers);
                  }
                  google.maps.event.addListener(map, 'bounds_changed', function() {
                      if(this_.hasClass('pxp-active')) {
                          getPOIs(map.getCenter(), map, 'fitness');
                      }
                  });
              });


          }
      }, 300);
   })(jQuery);



</script>
@endsection

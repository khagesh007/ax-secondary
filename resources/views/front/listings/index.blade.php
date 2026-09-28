    @extends('front.layouts.mapview')
@section('meta_tags')
    <title> AX Secondary Listings</title>
@endsection
@section('content')
    <div class="pxp-content pxp-full-height">
        <div class="pxp-map-side pxp-map-right pxp-half">
            <div id="results-map"></div>
            <a href="javascript:void(0);" class="pxp-list-toggle"><span class="fa fa-list"></span></a>
        </div>
        <div class="pxp-content-side pxp-content-left pxp-half">
            <div class="pxp-content-side-wrapper">
                <form class="hero__form" id="search_prop_form" method="GET" autocomplete="off"
                    action="{{ route('front.auth.listing') }}">
                    <div class="d-flex">
                        <div class="pxp-content-side-search-form">
                            <input type="hidden" name="search_location_id" id="search_location_id" value="">
                            <input type="hidden" name="search_location_type" id="search_location_type" value="">
                            <input type="hidden" name="search_sort_by" id="search_sort_by" value="new">
                            <div class="row pxp-content-side-search-form-row">

                                <div class="col-sm-5 col-md-5 col-lg-5 pxp-content-side-search-form-col">

                                    <select name="search_type[]" multiple="multiple" class="w-100 " style="border: 1px solid black"    id="pxp-p-filter-types-pocket">

                                        @foreach ([['value' => 'sale', 'name' => 'Buy'], ['value' => 'rent', 'name' => 'Rent'] , ['value' => 'pocket', 'name' => 'Pocket Listing']] as $type)
                                            <option value="{{ $type['value'] }}" {{ in_array($type['value'], request()->input('search_type', [])) ? 'selected' : '' }}>
                                                {{ $type['name'] }} </option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6 pxp-content-side-search-form-col">

                                    <input type="text" class="form-control pxp-is-address" name="search_location"
                                        id="gsearchsimple" placeholder="Enter Property, Location, Landmark ...">
                                    <ul class="list-group list-hm" style="display: none;"></ul>
                                    <span id="searchclear" class="fa fa-times mr-3 my-auto"
                                        style="display: none; height: auto"></span>
                                </div>

                            </div>
                        </div>
                        <div class="d-flex ">
                            <button class="pxp-hero-search-btn custom_ic_btn"><span class="fa fa-search"></span></button>
                            <a role="button" class="pxp-adv-toggle mr-2"><span class="fa fa-sliders"></span></a>

                        </div>
                    </div>
                    <div class="pxp-content-side-search-form-adv mb-3">
                        <div class="row pxp-content-side-search-form-row">
                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-price-min">Price</label>
                                    <input name="min_price" type="text" class="form-control" placeholder="Min"
                                        id="pxp-p-filter-price-min" value="{{ Request::input('min_price') }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-price-max" class="d-none d-sm-inline-block">&nbsp;</label>
                                    <input name="max_price" type="text" class="form-control" placeholder="Max"
                                        id="pxp-p-filter-price-max" value="{{ Request::input('max_price') }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-price-min">Size (sq ft)</label>
                                    <input name="min_size" type="text" class="form-control" placeholder="Min"
                                        id="pxp-p-filter-price-min" value="{{ Request::input('min_size') }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-price-max" class="d-none d-sm-inline-block">&nbsp;</label>
                                    <input name="max_size" type="text" class="form-control" placeholder="Max"
                                        id="pxp-p-filter-price-max" value="{{ Request::input('max_size') }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group ">
                                    <label for="pxp-p-filter-beds">Property Type</label>
                                        <select name="property_type" class="w-100 custom-select" id="pxp-p-filter-property_type">
                                            <option value="" selected="selected">Any</option>
                                            <option value="rent" {{ Request::input('property_type')=='rent'?'selected':'' }}>Rent</option>
                                            <option value="sale"  {{ Request::input('property_type')=='sale'?'selected':'' }}>Sale</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col" id="price-per-sqft-div">
                                <div class="form-group">
                                    <label for="price_per_sqft">Price per sqft (sq ft)</label>
                                    <input name="price_per_sqft" type="text" class="form-control" placeholder="Min"
                                        id="pxp-p-filter-price-per-sqft" value="{{ Request::input('price_per_sqft') }}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group ">
                                    <label for="pxp-p-filter-beds">Beds</label>
                                    {{-- <select name="search_bed" class="custom-select" id="pxp-p-filter-beds"> --}}
                                        <select name="search_bed"  class="w-100 custom-select"    id="pxp-p-filter-beds">
                                        <option value="" selected="selected">Any</option>
                                        @foreach ($data['beds'] as $bed)
                                            <option value="{{ $bed['id'] }}"
                                                {{ Request::input('search_bed') == $bed['id'] ? 'selected' : '' }}>
                                                {{ $bed['name'] }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-baths">Baths</label>
                                    {{-- <select name="search_bath" class="custom-select" id="pxp-p-filter-baths"> --}}
                                        <select name="search_bath"  class="w-100"    id="pxp-p-filter-baths">

                                        <option value="" selected="selected">Any</option>
                                        @foreach ($data['baths'] as $bath)
                                            <option value="{{ $bath['id'] }}"
                                                {{ Request::input('search_bath') == $bath['id'] ? 'selected' : '' }}>
                                                {{ $bath['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-baths">Types</label>
                                    {{-- <select name="search_category" class="custom-select" id="pxp-p-filter-baths"> --}}
                                        <select name="search_category"  class="w-100"    id="pxp-p-filter-category">

                                        <option value="" selected="selected">Any</option>
                                        @foreach ($data['categories'] as $category)
                                            <option value="{{ $category['id'] }}"
                                                {{ Request::input('search_category') == $category['id'] ? 'selected' : '' }}>
                                                {{ $category['title'] }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-baths">Zone</label>
                                        <select name="search_zone"  class="w-100" id="pxp-p-filter-zone">

                                        <option value="" selected="selected">Any</option>
                                        @foreach ($data['groups'] as $group)
                                            <option value="{{ $group['id'] }}"
                                                {{ Request::input('search_zone') == $group['id'] ? 'selected' : '' }}>
                                                {{ $group['name'] }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-baths">Agent</label>
                                        <select name="search_agent"  class="w-100" id="pxp-p-filter-agent">

                                        <option value="" selected="selected">Any</option>
                                        @foreach ($data['agents'] as $agent)
                                            <option value="{{ $agent['id'] }}"
                                                {{ Request::input('search_agent') == $agent['id'] ? 'selected' : '' }}>
                                                {{ $agent['full_name'] }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6 col-md-6 pxp-content-side-search-form-col">
                                <div class="form-group">
                                    <label for="pxp-p-filter-baths">Keywords</label>
                                    <input name="search_keywords" type="text" class="form-control"
                                        value="{{ Request::input('search_keywords') }}" placeholder="Search by keywords in description/titles/Amenities">
                                </div>
                            </div>


                            {{-- <div class=" pxp-content-side-search-form-col">
                                <div class="form-group d-flex">
                                    <input name="is_pocket" type="checkbox"
                                        {{ Request::input('is_pocket') ? 'checked' : '' }} style="font-size: 18px"
                                        class="form-control form-control-checkbox mr-2" id="pxp-p-filter-price-max"
                                        value="1">
                                    <label for="pxp-p-filter-price-max" class="d-none d-sm-inline-block">Pocket Listing
                                        ({{ $data['pocket_count'] }})</label>

                                </div>
                            </div> --}}
                        </div>
                        <button class="custom_ic_btn2" type="submit">Apply Filters</button>

                        <a href="/front/auth/listing" class=" custom_ic_btn4">Reset</a>
                    </div>
                </form>
                <div class="row pb-4">
                    <div class="col-sm-6">
                        <h3 class="" style="text-transform: uppercase">{{ $data['properties']->total() }} Results
                        </h3>
                    </div>
                    <div class="col-sm-6">
                        <div class="pxp-sort-form form-inline float-right">
                            <div class="form-group">
                                <select class="custom-select" id="sort_by">
                                    <option value="" selected="selected">Default Sort</option>
                                    <option value="new" @if (\Request::input('sort_by') == 'new') selected="" @endif>Sort by
                                        Newest</option>
                                    <option value="old" @if (\Request::input('sort_by') == 'old') selected="" @endif>Sort by
                                        Oldest</option>
                                    <option value="price_asc" @if (\Request::input('sort_by') == 'price_asc') selected="" @endif>Sort by
                                        Price(Low to High)</option>
                                    <option value="price_desc" @if (\Request::input('sort_by') == 'price_desc') selected="" @endif>Sort by
                                        Price(High to Low)</option>
                                </select>
                            </div>
                            <div class="form-group d-flex">
                                <a role="button" class="pxp-map-toggle"><span class="fa fa-map-o"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if (count($data['properties']) < 1)
                        <div class="col-md-12">
                            <div class="align-items-center justify-content-center">
                                <h4 class="text-center light-gray">
                                    {{-- <img src="{{ asset('pkh/images/oops.jpg') }}" />  --}}
                                    <br>
                                    No Listing to Display
                                </h4>
                                <p class="text-center light-gray">Try applying another filter</p>
                                {{-- <p  class="text-center light-gray mr-2"> <a href="{{ route('contact') }}" class="text-center justify-content-center"> <span class="icon-long-arrow-right mr-2"></span>Contact Us!</a></p> --}}
                            </div>
                        </div>
                    @else
                        @foreach ($data['properties'] as $property)
                            @if ($property['communityProperty'])
                                @php('-' . ($p_community_slug = $property['communityProperty']['slug']))
                            @else
                                @php($p_community_slug = '')
                            @endif
                            <div class="col-sm-12 col-md-6 col-xxxl-4">

                                <a href="/front/auth/listing-details/{{ $property['id'] }}"
                                    class="pxp-results-card-1 rounded-lg" data-prop="{{ $property['id'] }}">
                                    <div id="card-carousel-{{ $property['id'] }}" class="carousel slide"
                                        data-ride="carousel" data-interval="false">

                                        <div class="carousel-inner">
                                            @if ($property->photos->first())
                                                <div class="carousel-item active ">
                                                    <img src="{{ $property->photos->first()->path ?? 'https://picsum.photos/200/300' }}"
                                                        class="d-block w-100 h-100" alt="First slide">
                                                </div>
                                                @foreach ($property->photos as $photo)
                                                    <div class="carousel-item">
                                                        <img src="{{ $photo->path }}" class="d-block w-100 h-100 "
                                                            alt="Slide">
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <span class="carousel-control-prev"
                                            data-href="#card-carousel-{{ $property['id'] }}" data-slide="prev">
                                            <span class="fa fa-angle-left" aria-hidden="true"></span>
                                        </span>
                                        <span class="carousel-control-next"
                                            data-href="#card-carousel-{{ $property['id'] }}" data-slide="next">
                                            <span class="fa fa-angle-right" aria-hidden="true"></span>
                                        </span>
                                    </div>
                                    <div class="pxp-results-card-1-gradient"></div>
                                    <div class="pxp-results-card-1-details">

                                        <div
                                            class="pxp-results-card-1-details-price pxp-results-card-1-details-price-extended">
                                            <?php
                                            $for = explode(' ', $property['for']);
                                            $for = $for[count($for) - 1];
                                            $for = strtoupper($for);
                                            ?>
                                            @if ($property['publish_status'] == 'POCKET')
                                                <span class="pxp-sale-or-rent-badge-bg-sale">
                                                    <span class="pxp-sale-or-rent-badge-text">
                                                        Pocket
                                                    </span>
                                                </span>
                                            @endif
                                            @if ($for == 'SALE')
                                                <span class="pxp-sale-or-rent-badge-bg-sale">
                                                    <span class="pxp-sale-or-rent-badge-text">
                                                        {{ $for }}
                                                    </span>
                                                </span>
                                            @endif
                                            @if ($for == 'RENT')
                                                <span class="pxp-sale-or-rent-badge-bg-rent">
                                                    <span class="pxp-sale-or-rent-badge-text">
                                                        {{ $for }}
                                                    </span>
                                                </span>
                                            @endif

                                            AED {{ number_format($property['price']['price']) }}

                                        </div>
                                        <div class="pxp-results-card-1-details-title">{{ $property['location'] }}</div>
                                    </div>
                                    <div class="pxp-results-card-1-features">
                                        <span>{{ $property['bed']['name'] }} <span>|</span>
                                            {{ $property['bath']['name'] }}<span>|</span> {{ $property['built_up_area'] }}
                                            SF</span>
                                    </div>
                                    <div class="pxp-results-card-1-save"><span class="fa fa-star-o"></span></div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                @if (method_exists($data['properties'], 'links'))
                    @if ($data['properties']->lastPage() > 1)
                        <ul class="pagination pxp-paginantion mt-2 mt-md-4">
                            {{-- Previous Page Link --}}
                            @if ($data['properties']->currentPage() != 1)
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ Request::getRequestUri() }}{{ strpos(Request::getRequestUri(), '?') !== false ? '&page=' . ($data['properties']->currentPage() - 1) : '?page=' . ($data['properties']->currentPage() - 1) }}"
                                        title="Previous">
                                        Previous <span class="fa fa-angle-left"></span>
                                    </a>
                                </li>
                            @endif
                            {{-- Pagination Links --}}
                            @for ($i = max($data['properties']->currentPage() - 2, 1); $i <= min(max($data['properties']->currentPage() - 2, 1) + 4, $data['properties']->lastPage()); $i++)
                                <li class="page-item {{ $data['properties']->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link"
                                        href="{{ Request::getRequestUri() }}{{ strpos(Request::getRequestUri(), '?') !== false ? '&page=' . $i : '?page=' . $i }}">{{ $i }}</a>
                                </li>
                            @endfor
                            {{-- Next Page Link --}}
                            @if ($data['properties']->currentPage() != $data['properties']->lastPage())
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ Request::getRequestUri() }}{{ strpos(Request::getRequestUri(), '?') !== false ? '&page=' . ($data['properties']->currentPage() + 1) : '?page=' . ($data['properties']->currentPage() + 1) }}"
                                        title="Next">
                                        Next <span class="fa fa-angle-right"></span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                @endif
                <!--
                    <ul class="pagination pxp-paginantion mt-2 mt-md-4">
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next <span class="fa fa-angle-right"></span></a></li>
                    </ul> -->
            </div>
        </div>
    </div>
@endsection
@section('page_js')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&amp;libraries=geometry&amp;libraries=places">
    </script>
    <script src="{{ asset('/assets/front/js/markerclusterer.js?lasjkj') }}"></script>
    <!-- <script src="{{ asset('/assets/front/js/map.js?lasjkj') }}"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

@endsection
@section('inline_js')
    <script>
        (function($) {
            "use strict";
            var jsonObj = [];
            $('#pxp-p-filter-types-pocket').select2();
            $('#pxp-p-filter-beds').select2();
            $('#pxp-p-filter-baths').select2();
            $('#pxp-p-filter-category').select2();
            $('#pxp-p-filter-zone').select2();
            $('#pxp-p-filter-agent').select2();
            $('#pxp-p-filter-property_type').select2();






            var data = [];
            data = <?php echo json_encode($data['properties']); ?>;
            // console.log(data['data']);
            $.each(data['data'], function(key, value) {

                let route = '';

                var item = [];
                var features = [];
                var price = [];
                var position = [];

                item['id'] = (value['id']);
                item['title'] = (value['title'] === '' ? '' : value['title']);
                // item['location_longitude'] = (value['longitude'] === '' ? '55.26677426615' : value['longitude']);
                // item['bed_num'] = value['beds']['name'];
                // item['bath_num'] = value['baths']['name'];
                // item['garage_num'] = value['parking'];
                // features.push({'beds':1,'baths':2,'size': '290 SF'});
                //price.push({'long':'$890,000','short':'$890,000',});
                // position.push({ lat: '37.779023',lng: '-122.48862200000002'});

                item['features'] = {
                    'bed': value['bed']['name'],
                    'bath': value['bath']['name'],
                    'size': value['built_up_area'] + ' SF'
                };
                //   console.log(value);
                item['price'] = {
                    'long': value['price']['price'],
                    'short': value['price']['price'],
                };
                item['position'] = {
                    lat: (
                        value['community_property'] && value['community_property']['latitude'] !== '' ?
                        value['community_property']['latitude'] :
                        (
                            value['sub_community'] && value['sub_community']['latitude'] !== '' ?
                            value['sub_community']['latitude'] :
                            '55.26677426615'
                        )
                    ),
                    lng: (
                        value['community_property'] && value['community_property']['longitude'] !== '' ?
                        value['community_property']['longitude'] :
                        (
                            value['sub_community'] && value['sub_community']['longitude'] !== '' ?
                            value['sub_community']['longitude'] :
                            '55.26677426615'
                        )
                    )
                };



                if (value['photos'][0]) {
                    item['photo'] = value['photos'][0]['path'];
                } else {
                    item['photo'] = "{{ asset('images/remax-default-listing.jpg') }}";
                }
                // item['total_area'] = value['built_up_area'];
                //item['price'] = value['price'];
                //item['frequency'] = value['frequency']? value['frequency']['name'] : 'from';
                //item['name_point'] = value['communityProperty'] ? value['communityProperty']['full_path_name'] : value['sub_community']['full_path_name'];
                var view_url = '';




                var view_url = "#";
                //   console.log(view_url);

                item['link'] = view_url;

                jsonObj.push(item);

            });




            var map;
            var markers = [];
            var markerCluster;
            var styles;
            var propertiesList;
            var options = {
                zoom: 14,
                mapTypeId: 'roadmap',
                panControl: true,
                zoomControl: true,
                mapTypeControl: true,
                scaleControl: false,
                streetViewControl: false,
                overviewMapControl: false,
                scrollwheel: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM,
                },
                fullscreenControl: true,
                tilt: 45,
                mapId: "7562e9a5aecc3114"
            };


            // const options = {
            //     "tilt": 60,
            //     "heading": 0,
            //     "zoom": 14,
            //     "center": {
            //         lat: 35.6594945,
            //         lng: 139.6999859
            //     },
            // };



            propertiesList = jsonObj
            //[

            // {
            //     id: 1,
            //     title: 'Chic Apartment in Downtown',
            //     photo: 'images/properties/prop-1-1-thmb.jpg',
            //     position: {
            //         lat: '37.779023',
            //         lng: '-122.48862200000002'
            //     },
            //     price: {
            //         long: '$890,000',
            //         short: '$890k'
            //     },
            //     link: 'single-property.html',
            //     features: {
            //         beds: 2,
            //         baths: 2,
            //         size: '920 SF'
            //     }
            // },



            // ];


            // console.log('-----------------');
            // console.log(propertiesList);
            // console.log('=======================');

            function CustomMarker(id, latlng, map, classname, html) {
                this.id = id;
                this.latlng_ = latlng;
                this.classname = classname;
                this.html = html;

                this.setMap(map);
            }

            CustomMarker.prototype = new google.maps.OverlayView();

            CustomMarker.prototype.draw = function() {
                var me = this;
                var div = this.div_;

                if (!div) {
                    div = this.div_ = document.createElement('div');
                    div.classList.add(this.classname);
                    div.innerHTML = this.html;

                    google.maps.event.addDomListener(div, 'click', function(event) {
                        google.maps.event.trigger(me, 'click');
                    });

                    var panes = this.getPanes();
                    panes.overlayImage.appendChild(div);
                }

                var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);

                if (point) {
                    div.style.left = point.x + 'px';
                    div.style.top = point.y + 'px';
                }
            };

            CustomMarker.prototype.remove = function() {
                if (this.div_) {
                    this.div_.parentNode.removeChild(this.div_);
                    this.div_ = null;
                }
            };

            CustomMarker.prototype.getPosition = function() {
                return this.latlng_;
            };

            CustomMarker.prototype.addActive = function() {
                if (this.div_) {
                    $('.pxp-price-marker').removeClass('active');
                    this.div_.classList.add('active');
                }
            };

            CustomMarker.prototype.removeActive = function() {
                if (this.div_) {
                    this.div_.classList.remove('active');
                }
            };

            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }



            function addMarkers(props, map) {
                $.each(props, function(i, prop) {

                    var latlng = new google.maps.LatLng(prop.position.lat, prop.position.lng);
                    var formattedPrice = formatNumber(prop.price.short);

                    var link = "/front/auth/listing-details/" + prop.id
                    var html = '<div class="pxp-marker-short-price">AED ' + formattedPrice + '</div>' +
                        '<a href="' + link + '" class="pxp-marker-details">' +
                        '<div class="pxp-marker-details-fig pxp-cover" style="background-image: url(' + prop
                        .photo + ');"></div>' +
                        '<div class="pxp-marker-details-info">' +
                        '<div class="pxp-marker-details-info-title">' + prop.title + '</div>' +
                        '<div class="pxp-marker-details-info-price">AED ' + formattedPrice + '</div>' +
                        '<div class="pxp-marker-details-info-feat">' + prop.features.bed + ' <span>|</span>' +
                        prop.features.bath + ' <span>|</span>' + prop.features.size + '</div>' +
                        '</div>' +
                        '</a>';

                    var marker = new CustomMarker(prop.id, latlng, map, 'pxp-price-marker', html);

                    marker.id = prop.id;
                    markers.push(marker);
                });
            }

            setTimeout(function() {
                if ($('#results-map').length > 0) {
                    map = new google.maps.Map(document.getElementById('results-map'), options);
                    var styledMapType = new google.maps.StyledMapType(styles, {
                        name: 'Styled',
                    });

                    map.mapTypes.set('Styled', styledMapType);
                    map.setCenter(new google.maps.LatLng(37.7577627, -122.4726194));
                    map.setZoom(15);

                    addMarkers(propertiesList, map);

                    map.fitBounds(markers.reduce(function(bounds, marker) {
                        return bounds.extend(marker.getPosition());
                    }, new google.maps.LatLngBounds()));

                    markerCluster = new MarkerClusterer(map, markers, {
                        maxZoom: 18,
                        gridSize: 60,
                        styles: [{
                                width: 40,
                                height: 40,
                            },
                            {
                                width: 60,
                                height: 60,
                            },
                            {
                                width: 80,
                                height: 80,
                            },
                        ]
                    });

                    google.maps.event.trigger(map, 'resize');

                    $('.pxp-results-card-1').each(function(i) {
                        var propID = $(this).attr('data-prop');

                        $(this).on('mouseenter', function() {
                            if (map) {
                                var targetMarker = $.grep(markers, function(e) {
                                    return e.id == propID;
                                });

                                if (targetMarker.length > 0) {
                                    targetMarker[0].addActive();
                                    map.setCenter(targetMarker[0].latlng_);
                                }
                            }
                        });
                        $(this).on('mouseleave', function() {
                            var targetMarker = $.grep(markers, function(e) {
                                return e.id == propID;
                            });

                            if (targetMarker.length > 0) {
                                targetMarker[0].removeActive();
                            }
                        });
                    });
                }
            }, 300);
        })(jQuery);




        $(document).ready(function() {
            $('#price-per-sqft-div').hide();
            $('#pxp-p-filter-price-per-sqft').val('')
            // Call global the function
            $('.t-datepicker').tDatePicker({
                // options here
                limitNextMonth: 2,
                dateCheckIn: "{{ Request::input('t-start') }}",
                dateCheckOut: "{{ Request::input('t-end') }}",


            });




            // console.log('Ready!');


            let search_location = "{{ Request::input('search_location') }}";


            let sort_by = "{{ Request::input('sort_by') }}";
            if (search_location) {
                $('#searchclear').show();
                $('#gsearchsimple').val(search_location);
            }

            $('#search_location_id').val("{{ Request::input('search_location_id') }}");
            $('#search_location_type').val("{{ Request::input('search_location_type') }}");

            $('#pxp-p-filter-beds').val({{ Request::input('search_bed') }});
            $('#pxp-p-filter-baths').val({{ Request::input('search_bath') }});




            $('#gsearchsimple').keyup(function() {
                var query = $('#gsearchsimple').val();
                $('#detail').html('');

                if (query.length >= 3) {
                    $('.list-group').slideDown();
                    $('#searchclear').show();
                    $.ajax({
                        url: "{{ route('front.auth.search_location') }}",
                        method: "GET",
                        data: 'keyword=' + $(this).val(),
                        success: function(data) {
                            let output = '';

                            $.each(data, function(key, value) {
                                //   console.log(query);
                                let txt = String(value.name).replace(new RegExp(query,
                                    "gi"), "<span class='marktext' >$&</span>");
                                output +=
                                    '<li class="list-group-item list-hm-item contsearch"><a p_txt="' +
                                    value.name + '" p_type="' + value.type +
                                    '"p_value="' + value.id +
                                    '" href="javascript:void(0)" class="gsearch" style="color:#000;text-decoration:none;"> ' +
                                    txt + '<small> ' + value.path_name +
                                    '</small></a></li>';

                            });

                            $('.list-group').html(output);

                        }


                    })
                }
                if (query.length == 0) {


                    $('.list-group').slideUp();
                    $('.list-group').html('');
                    $('#searchclear').hide();
                    $('#search_location_id').val('');
                    $('#search_location_type').val('');
                    $("#gsearchsimple").val('');

                    $('#search_prop_form').submit();

                }
            });

            $("#searchclear").click(function() {

                $('.list-group').slideUp();
                $('.list-group').html('');
                $('#searchclear').hide();
                $('#search_location_id').val('');
                $('#search_location_type').val('');
                $("#gsearchsimple").val('');
                $('#search_prop_form').submit();

            });



            $(document).on('click', '.gsearch', function() {
                var location_text = $(this).attr("p_txt");
                var location_value = $(this).attr("p_value");
                var location_type = $(this).attr("p_type");

                //   console.log(location_text);
                //   console.log(location_value);

                $('#search_location_id').val(location_value);
                $('#search_location_type').val(location_type);


                $('#gsearchsimple').val(location_text);
                $('.list-group').slideUp();
                //$('#search_btn_hm').trigger('click');

                $('#search_prop_form').submit();

            });



        }); // end document ready



        $(document).on('change', '#pxp-p-filter-property_type', function() {
            $('#price-per-sqft-div').hide();
            $('#pxp-p-filter-price-per-sqft').val('')
            if(this.value == 'rent'){
             $('#price-per-sqft-div').hide();
            }
            else if(this.value == 'sale'){
                $('#price-per-sqft-div').show();
            }
        });

        $(document).on('change', '#sort_by', function() {
            //  console.log($(this).val());
            $('#search_sort_by').val($(this).val());


            window.location.replace("{{ route('front.auth.listing') }}/?sort_by=" + $(this).val());


        });
    </script>
@endsection

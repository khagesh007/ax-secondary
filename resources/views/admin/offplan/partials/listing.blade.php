<div class="row ">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class=" text-center">
                    <h3 class="">
                        Import Properties
                    </h3>
                    <hr>
                </div>
                <div class="row mt-2" id="">
                    <div class="col-lg-12">

                        <div class="table-responsive  mb-4" style="overflow: scroll;">
                            <table id="developersTable" class="table align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>
                                            {{-- <span><input type="checkbox" id="check-all-input" value="1" name="check-all-input"></span> --}}
                                        </th>
                                        <th>Property ID</th>
                                        <th>ID</th>
                                        <th>Unique ID</th>
                                        <th>Developer</th>
                                        <th>Unit No</th>
                                        <th>Title</th>
                                        <th>Project Name</th>
                                        <th>Location</th>
                                        <th>Bed</th>
                                        <th>Plot Size</th>
                                        <th>Price</th>
                                        <th>Type</th>
                                        <th>Created at</th>
                                        <th>Publish Status</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dumps as $dump)
                                        <tr>
                                            <td><input type="checkbox" class="check-all" name="property_id[]" id="property_id" value="{{ $dump->id }}"></td>
                                            <td><span class="mx-2">{{ $dump->property_id }}</span></td>
                                            <td><span class="mx-2">{{ $dump->id }}</span></td>
                                            <td><span class="mx-2">{{ $dump->unique_id }}</span></td>
                                            <td><span
                                                    class="mx-2">{{ $dump->developer_id?$dump?->developer?->full_name:'-' }}</span>
                                            </td>
                                            <td><span class="mx-2">{{ $dump->unit_no }}</span></td>
                                            <td><span class="mx-2">{{ $dump->title }}</span></td>
                                            <td><span class="mx-2">{{ $dump->project_name }}</span></td>
                                            @php
                                                $location = json_decode($dump->location_data);
                                                $building_name = $location->building;
                                                $location_name = $location->location;
                                                $property_name = $location->property;

                                            @endphp


                                            <td><span class="mx-2">{{ $location_name }}, {{ $building_name }}, {{ $property_name }} </span></td>
                                            <td><span class="mx-2">{{ $dump->bed_id }}</span></td>
                                            <td><span class="mx-2">{{ $dump->plot_size }}</span></td>
                                            <td><span class="mx-2">AED {{ $dump->price }}</span></td>
                                            <td><span class="mx-2">{{ $dump->property_type }}</span></td>
                                            <td><span class="mx-2">{{ $dump->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td><span class="mx-2">{{$dump->property_id ? $dump->property?->publish_status:'-'}}</td>
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
</div>
<div class="row d-" id="LocationMapping">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="">
                    <h3 class="">
                        Location Mapping
                    </h3>
                    <hr>
                </div>
                <div class="row mt-2" id="">
                    <div class="col-md-6">

                        <select class="form-control select22 inputs" name="" id="maplocation">
                          {{-- <option value="null">Null</option> --}}
                            @foreach ($locations ?? [] as $loc)
                                @php
                                    $location = json_decode($loc->location_data);
                                    $building_name = $location->building;
                                    $location_name = $location->location;
                                    $property_name = $location->property;

                                @endphp
                                <option value="{{ $location_name }},{{ $building_name }},{{ $property_name }}">
                                    {{ $location_name }}, {{ $building_name }}, {{ $property_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <input type="hidden" class="search-inputs sub_communities" name="sub_communities[]"
                            id="search_sub_communities">
                        <input type="hidden" class="search-inputs search_properties" name="search_properties[]"
                            id="search_properties">
                        <select name='search_locations[]' id="" class="search_locations"
                            class="form-control select2 search-inputs searchInput">
                            <option value="">Select Option</option>
                        </select>
                    </div>

                </div>
                <div class="row">
                    <div class="col-12">
                        <div id="appendLocation">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="">
                    <h3 class="">
                        Bed Mapping
                    </h3>
                    <hr>
                </div>
                <div class="row mt-2" id="">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control select22 inputs" name="" id="mapBed">
                                    @foreach ($beds ?? [] as $bed)
                                        <option value="{{ $bed->bed_id }}"> {{ $bed->bed_id }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <select class="form-control select22 inputs" name="" id="saveBed">
                                    <option value="">Select Option</option>
                                    @foreach ($all_beds ?? [] as $bed)
                                        <option value="{{ $bed->id }}"> {{ $bed->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>



                </div>
                <div class="row">
                    <div class="col-12">
                        <div id="appendBed">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="">
                    <h3 class="">
                        Property Type Mapping
                    </h3>
                    <hr>
                </div>
                <div class="row mt-2" id="">

                    <div class="col-md-6">
                        <select class="form-control select22 inputs bedMapping" name="" id="mapType">
                            <option value="">Select Option</option>
                            @foreach ($propertytypes ?? [] as $ptype)
                                <option value="{{ $ptype->property_type }}" data-name="{{ $ptype->property_type }}">
                                    {{ $ptype->property_type }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-6">
                        <select class="form-control select22 inputs " name="" id="saveType">
                            <option value="">Select Option</option>
                            @foreach ($types ?? [] as $type)
                                <option value="{{ $type->id }}" data-name="{{ $type->title }}">
                                    {{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div id="appendType">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="">
                    <h3 class="">
                        Unit No Formula
                    </h3>
                    <hr>
                </div>
                <div class="row mt-2" id="">

                    <div class="col-md-12">
                        <select class="form-control select22 inputs unitno" name="unit_no_mapping" id="unitNo">


                            <option value="case1">Last 3 Seperator - </option>
                            <option value="case2">Last 2 Seperator - </option>
                            <option value="case3">Last 1 Seperator - </option>

                            <option value="case4">Last 3 Seperator / </option>
                            <option value="case5">Last 2 Seperator / </option>
                            <option value="case6">Last 1 Seperator / </option>

                        </select>
                    </div>


                </div>

            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12 text-end">
        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                <button class="btn btn-primary d-none" id="submitBtnPlaceholder" disabled>Submit</button>
            </div>
        </div>
    </div>
</div>

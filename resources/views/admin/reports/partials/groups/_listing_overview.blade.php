<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Properties Split</h4>
            </div>
            <div class="card-body">
                <div id="propertiesSplit"
                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                    class="apex-charts propertiesSplit" dir="ltr"
                    data-secondary_properties="{{ $secondary_properties }}"
                    data-primary_properties="{{ $primary_properties }}"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Listing Insights</h4>
            </div>
            <div class="card-body">
                <div id="propertiesFor"
                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                    class="apex-charts propertiesFor" dir="ltr"
                    data-commercial_for_rent="{{ $commercial_for_rent }}"
                    data-commercial_for_sale="{{ $commercial_for_sale }}"
                    data-residential_for_sale="{{ $residential_for_sale }}"
                    data-residential_for_rent="{{ $residential_for_rent }}"
                    ></div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Statistics</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card mb-3">
                    <table class="table align-middle table-nowrap" id="customerTable">
                        <thead class="table-light">
                            <tr>
                                {{-- <th scope="col" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                    </div>
                                </th> --}}
                                <th class="sort" data-sort="customer_name">Attribute</th>
                                <th class="sort" data-sort="email">Value</th>

                            </tr>
                        </thead>
                        <tbody class="list form-check-all">
                            <tr>
                                {{-- <th scope="row">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="chk_child"
                                            value="option1">
                                    </div>
                                </th> --}}

                                <td class="status"><span class="badge bg-success-subtle text-success text-uppercase">Published on Portals (Bayut & Property Finder)</span></td>

                                <td class="id"><b>{{$truePublishions}}</b></td>


                            </tr>

                            <tr>
                                <td class="status"><span class="badge bg-success-subtle text-danger text-uppercase" id="primary_properties" data-primary_properties="{{$primary_properties}}">Primary Properties</span></td>
                                <td class="id"><b>{{$primary_properties}}</b></td>
                            </tr>

                            <tr>
                                <td class="status"><span class="badge bg-success-subtle text-info text-uppercase" id="secondary_properties" data-secondary_properties="{{$secondary_properties}}">Secondary Properties</span></td>
                                <td class="id"><b>{{$secondary_properties}}</b></td>
                            </tr>

                            <tr>
                                <td class="status"><span class="badge bg-success-subtle text-primary text-uppercase" id="commercial_for_rent" data-commercial_for_rent="{{$commercial_for_rent}}">Commercial For Rent</span></td>
                                <td class="id"><b>{{$commercial_for_rent}}</b></td>
                            </tr>
                            <tr>
                                <td class="status"><span class="badge bg-success-subtle text-warning text-uppercase" id="commercial_for_sale" data-commercial_for_sale="{{$commercial_for_sale}}">Commercial For Sale</span></td>
                                <td class="id"><b>{{$commercial_for_sale}}</b></td>
                            </tr>
                            <tr>
                                <td class="status"><span class="badge bg-success-subtle text-info text-uppercase" id="residential_for_rent" data-residential_for_rent="{{$residential_for_rent}}">Residential For Rent</span></td>
                                <td class="id"><b>{{$residential_for_rent}}</b></td>
                            </tr>
                            <tr>
                                <td class="status"><span class="badge bg-success-subtle text-danger text-uppercase" id="residential_for_sale" data-residential_for_sale="{{$residential_for_sale}}">Residential For Sale</span></td>
                                <td class="id"><b>{{$residential_for_sale}}</b></td>
                            </tr>

                        </tbody>
                    </table>

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

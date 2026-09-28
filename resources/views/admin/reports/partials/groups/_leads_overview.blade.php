<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Properties Split</h4>
            </div>
            <div class="card-body">
                <div id="leadStatusChart"
                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                    class="apex-charts leadStatusChart" dir="ltr" data-names="" data-counts=""></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Properties Split</h4>
            </div>
            <div class="card-body">
                <div id="leadTypeChart"
                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                    class="apex-charts leadTypeChart" dir="ltr" data-names="" data-counts=""></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Properties Split</h4>
            </div>
            <div class="card-body">
                <div id="leadSMediumsChart"
                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                    class="apex-charts leadSMediumsChart" dir="ltr" data-names="" data-counts=""></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Properties Split</h4>
            </div>
            <div class="card-body">
                <div id="leadSourceChart"
                    data-colors='["--vz-success","--vz-primary", "--vz-secondary",  "--vz-info", "--vz-warning", "--vz-danger", "--vz-dark", "--vz-primary", "--vz-success", "--vz-secondary"]'
                    class="apex-charts leadSourceChart" dir="ltr" data-names="" data-counts=""></div>
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

                                <td class="status"><span
                                        class="badge bg-success-subtle text-success text-uppercase">True
                                        Publications</span></td>

                                <td class="id"><b>dd</b></td>


                            </tr>



                        </tbody>
                    </table>

                    </table>

                </div>
            </div>
        </div>
    </div>

</div>

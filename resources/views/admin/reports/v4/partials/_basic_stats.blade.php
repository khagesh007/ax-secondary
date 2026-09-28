@php
    $counter = 1;
@endphp
@foreach ($groupdata as $dat)
    <div class="row {{ $counter == 1 ? '' : 'd-none' }} group_{{ $dat['group_id'] }}_div hide-all">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0">
                        <div class="col">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Properties <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-space-ship-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $dat['property'] }}">{{ formatPriceToInt($dat['property']) }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Leads <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $dat['leads'] }}">{{ formatPriceToInt($dat['leads']) }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Listing Leads <i
                                        class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-pulse-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $dat['listingleads'] }}">{{ formatPriceToInt($dat['listingleads']) }}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Contacts <i
                                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-trophy-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value"
                                                data-target="{{ $dat['contacts'] }}">{{ formatPriceToInt($dat['contacts']) }}</span></h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                        <div class="col">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">Termination Rate <i
                                        class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-service-line display-6 text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h2 class="mb-0"><span class="counter-value" data-target="{{ $dat['suspensions_rate'] }}">{{ $dat['suspensions_rate'] }}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->


        <div class="col-md-12 text-center">
            <div class="card mt-2 mb-2 sticky-top-with-margin-bottom position-sticky">
                <div class="card-body">
                    <b>Overall Performance</b>
                </div>
            </div>
            <div class="row row-cols-md-3 row-cols-1">
                <div class="col col-lg border-end p-2">
                    <div class="card"
                        style="border:2px {{ request()->child ? 'dotted' : 'solid' }} {{ $dat['group_color'] }}">
                        <div class="card-body">
                            <div class="accordion accordion-border-box" id="privacy-accordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="privacy-headingOne">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#privacy-collapseOne-{{ $dat['property'] }}"
                                            aria-expanded="false"
                                            aria-controls="privacy-collapseOne-{{ $dat['property'] }}">
                                            Properties <span
                                                class="badge bg-warning ms-2">{{ formatPriceToInt($dat['property']) }}</span>
                                        </button>
                                    </h2>
                                    <div id="privacy-collapseOne-{{ $dat['property'] }}"
                                        class="accordion-collapse collapse" aria-labelledby="privacy-headingOne"
                                        data-bs-parent="#privacy-accordion" style="">
                                        <div class="accordion-body">
                                            <div class="table-responsive table-card mb-3">
                                                <table class="table align-middle table-nowrap mb-0" id="customerTable">


                                                    <tbody class="list form-check-all">
                                                        @foreach ($dat['property_status'] as $key => $value)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex text-start">
                                                                        <div class="flex-grow-1 ms-2 name">
                                                                            {{ $key }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="owner">{{ formatPriceToInt($value) }}</td>
                                                            </tr>
                                                        @endforeach



                                                    </tbody>

                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="privacy-headingTwo">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#privacy-collapseTwo-{{ $dat['leads'] }}"
                                            aria-expanded="false"
                                            aria-controls="privacy-collapseTwo-{{ $dat['leads'] }}">
                                            Leads <span
                                                class="badge bg-primary ms-2">{{ formatPriceToInt($dat['leads']) }}</span>
                                        </button>
                                    </h2>
                                    <div id="privacy-collapseTwo-{{ $dat['leads'] }}"
                                        class="accordion-collapse collapse" aria-labelledby="privacy-headingTwo"
                                        data-bs-parent="#privacy-accordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive table-card mb-3">
                                                <table class="table align-middle table-nowrap mb-0"
                                                    id="customerTable">


                                                    <tbody class="list form-check-all">

                                                        @foreach ($dat['leads_status'] as $key => $value)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex text-start">

                                                                        <div class="flex-grow-1 ms-2 name">
                                                                            {{ getLeadStatusName($key) ?? 'N/A' }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="owner">{{ formatPriceToInt($value) }}</td>
                                                            </tr>
                                                        @endforeach


                                                    </tbody>

                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="privacy-headingTwo">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#privacy-collapseListingLeads-{{ $dat['listingleads'] }}"
                                            aria-expanded="false"
                                            aria-controls="privacy-collapseListingLeads-{{ $dat['listingleads'] }}">
                                            Listing leads <span
                                                class="badge bg-info ms-2">{{ formatPriceToInt($dat['listingleads']) }}</span>
                                        </button>
                                    </h2>
                                    <div id="privacy-collapseListingLeads-{{ $dat['listingleads'] }}"
                                        class="accordion-collapse collapse" aria-labelledby="privacy-headingTwo"
                                        data-bs-parent="#privacy-accordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive table-card mb-3">
                                                <table class="table align-middle table-nowrap mb-0"
                                                    id="customerTable">


                                                    <tbody class="list form-check-all">

                                                        @foreach ($dat['listingleads_status'] as $key => $value)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex text-start">

                                                                        <div class="flex-grow-1 ms-2 name">
                                                                            {{ getLeadStatusName($key) ?? 'N/A' }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="owner">{{ formatPriceToInt($value) }}</td>
                                                            </tr>
                                                        @endforeach


                                                    </tbody>

                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="privacy-headingThree">
                                        <button class="accordion-button collapsed" disabled type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#privacy-collapseThree-{{ $dat['contacts'] }}"
                                            aria-expanded="false"
                                            aria-controls="privacy-collapseThree-{{ $dat['contacts'] }}">
                                            Contacts <span
                                                class="badge bg-success ms-2">{{ formatPriceToInt($dat['contacts']) }}</span>
                                        </button>
                                    </h2>
                                    <div id="privacy-collapseThree-{{ $dat['contacts'] }}"
                                        class="accordion-collapse collapse" aria-labelledby="privacy-headingThree"
                                        data-bs-parent="#privacy-accordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive table-card mb-3">
                                                <table class="table align-middle table-nowrap mb-0"
                                                    id="customerTable">


                                                    <tbody class="list form-check-all">
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="mdi mdi-home"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-2 name">Published
                                                                        Listings
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="owner">122</td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="mdi mdi-contacts"></i>

                                                                    </div>
                                                                    <div class="flex-grow-1 ms-2 name">Contacts</div>
                                                                </div>
                                                            </td>
                                                            <td class="owner">1342</td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="mdi mdi-account-cash"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-2 name">Leads</div>
                                                                </div>
                                                            </td>
                                                            <td class="owner">2122</td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="mdi mdi-cash-check"></i>

                                                                    </div>
                                                                    <div class="flex-grow-1 ms-2 name">Deals</div>
                                                                </div>
                                                            </td>
                                                            <td class="owner">0</td>
                                                        </tr>
                                                    </tbody>

                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="privacy-headingFour">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#privacy-collapseFour"
                                            aria-expanded="false" aria-controls="privacy-collapseFour" disabled>
                                            Deals <span class="badge bg-danger ms-2">Coming Soon</span>
                                        </button>
                                    </h2>
                                    <div id="privacy-collapseFour" class="accordion-collapse collapse"
                                        aria-labelledby="privacy-headingFour" data-bs-parent="#privacy-accordion">
                                    </div>
                                </div>





                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="privacy-warning">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#privacy-warningsuspensions-{{ $dat['property'] }}"
                                            aria-expanded="false"
                                            aria-controls="privacy-warningsuspensions-{{ $dat['property'] }}">
                                            Terminations <span
                                                class="badge bg-warning ms-2">{{ $dat['suspensions_rate'] }}</span>
                                        </button>
                                    </h2>
                                    <div id="privacy-warningsuspensions-{{ $dat['property'] }}"
                                        class="accordion-collapse collapse" aria-labelledby="privacy-warning"
                                        data-bs-parent="#privacy-accordion" style="">
                                        <div class="accordion-body">
                                            <div class="table-responsive table-card mb-3">
                                                <table class="table align-middle table-nowrap mb-0"
                                                    id="customerTable">


                                                    <tbody class="list form-check-all">
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex text-start">
                                                                    <div class="flex-grow-1 ms-2 name">
                                                                        Terminations
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="owner">{{ $dat['suspensions'] }}
                                                                ({{ $dat['suspensions_rate'] }})</td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <div class="d-flex text-start">
                                                                    <div class="flex-grow-1 ms-2 name">
                                                                        Warnings
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="owner">{{ $dat['warnings'] }}
                                                                ({{ $dat['warnings_rate'] }})</td>
                                                        </tr>




                                                    </tbody>

                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>


                </div>
            </div>
        </div>

    </div>
    @php
        $counter++;
    @endphp
@endforeach

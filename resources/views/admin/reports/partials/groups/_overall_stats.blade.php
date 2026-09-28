<div class="row">

    <div class="col-md-12 text-center">
        <div class="card mt-2 mb-2 sticky-top-with-margin-bottom position-sticky">
            <div class="card-body">
                <b>Overall Performance</b>
            </div>
        </div>
        <div class="row row-cols-md-3 row-cols-1">
            @foreach ($groupdata as $dat)
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
                                                <table class="table align-middle table-nowrap mb-0" id="customerTable">


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
                                                <table class="table align-middle table-nowrap mb-0" id="customerTable">


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
                                                <table class="table align-middle table-nowrap mb-0" id="customerTable">


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

                                {{-- <div class="accordion-item">
                                    <h2 class="accordion-header" id="warnings-{{ $dat['warnings'] }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#warnings-{{ $dat['warnings'] }}" aria-expanded="false"
                                            aria-controls="warnings-{{ $dat['warnings'] }}" disabled>
                                            Warnings Rate <span
                                                class="badge bg-warning ms-2">{{ $dat['warnings'] }}</span>
                                        </button>
                                    </h2>
                                    <div id="warnings-{{ $dat['warnings'] }}" class="accordion-collapse collapse"
                                        aria-labelledby="privacy-{{ $dat['warnings'] }}"
                                        data-bs-parent="#privacy-{{ $dat['warnings'] }}">
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="suspensions-{{ $dat['suspensions'] }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#suspensions-{{ $dat['suspensions'] }}"
                                            aria-expanded="false"
                                            aria-controls="suspensions-{{ $dat['suspensions'] }}" disabled>
                                            Suspensions Rate <span
                                                class="badge bg-danger ms-2">{{ $dat['suspensions'] }}</span>
                                        </button>
                                    </h2>
                                    <div id="suspensions-{{ $dat['suspensions'] }}"
                                        class="accordion-collapse collapse"
                                        aria-labelledby="suspensions-{{ $dat['suspensions'] }}"
                                        data-bs-parent="#suspensions-{{ $dat['suspensions'] }}">
                                    </div>
                                </div> --}}



                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="privacy-warning">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#privacy-warningsuspensions-{{ $dat['property'] }}"
                                            aria-expanded="false"
                                            aria-controls="privacy-warningsuspensions-{{ $dat['property'] }}">
                                            Terminations <span class="badge bg-warning ms-2">{{ $dat['suspensions_rate'] }}</span>
                                        </button>
                                    </h2>
                                    <div id="privacy-warningsuspensions-{{ $dat['property'] }}"
                                        class="accordion-collapse collapse" aria-labelledby="privacy-warning"
                                        data-bs-parent="#privacy-accordion" style="">
                                        <div class="accordion-body">
                                            <div class="table-responsive table-card mb-3">
                                                <table class="table align-middle table-nowrap mb-0" id="customerTable">


                                                    <tbody class="list form-check-all">
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex text-start">
                                                                    <div class="flex-grow-1 ms-2 name">
                                                                       Terminations
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="owner">{{ $dat['suspensions'] }} ({{ $dat['suspensions_rate'] }})</td>
                                                        </tr>

                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex text-start">
                                                                        <div class="flex-grow-1 ms-2 name">
                                                                           Warnings
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="owner">{{ $dat['warnings'] }} ({{ $dat['warnings_rate'] }})</td>
                                                            </tr>




                                                    </tbody>

                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                        {{-- <div class="card-body">
                        <div>
                            <div class="table-responsive table-card mb-3 d-none">
                                <table class="table align-middle table-nowrap mb-0" id="customerTable">

                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="name" scope="col">Type</th>
                                            <th class="sort" data-sort="owner" scope="col">Total No.</th>

                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="mdi mdi-home"></i>

                                                    </div>
                                                    <div class="flex-grow-1 ms-2 name">Published Listings</div>
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
                    </div> --}}
                    </div>


                </div>
            @endforeach
        </div>
    </div>
</div>

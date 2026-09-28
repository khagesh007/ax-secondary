


<div class="row mt-3">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-secure-payment-line align-bottom me-1 text-muted"></i>
                    Lead Criteria</h5>

            </div>
            <div class="card-body">


                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Property Category:</p>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <h6 class="mb-0"> {{ $listing?->propertyCategory?->title }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Beds:</p>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <h6 class="mb-0">{{ $listing?->bed?->name ?? '' }}</h6>
                    </div>
                </div>
                @php
                    $listingNoteLines = explode("\n", $listing->note);

                    $sourceDetails = '';
                    $sourceName = '';
                    $locationName = '';

                    foreach ($listingNoteLines as $line) {
                        if (strpos($line, 'Source Details:') !== false) {
                            $sourceDetails = trim(str_replace('Source Details:', '', $line));
                        } elseif (strpos($line, 'Source Name:') !== false) {
                            $sourceName = trim(str_replace('Source Name:', '', $line));
                        } elseif (strpos($line, 'Location:') !== false) {
                            $locationName = trim(str_replace('Location:', '', $line));
                        }
                    }
                @endphp

                @if (!empty($sourceDetails))
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Source Details:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $sourceDetails }}</h6>
                        </div>
                    </div>
                @endif

                @if (!empty($sourceName))
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Source:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $sourceName }}</h6>
                        </div>
                    </div>
                @endif
                @if (!empty($locationName))
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Location Message:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $locationName }}</h6>
                        </div>
                    </div>
                @endif
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0">
                        <p class="text-muted mb-0">Locations:</p>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <h6 class="mb-0">{{ $listing->location }}
                        </h6>
                    </div>
                </div>


            </div>
        </div>

    </div>

</div>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('brochure/css/style.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.1/html2pdf.bundle.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.0/html2pdf.bundle.min.js"></script> --}}


    <meta name="ref_no" content="{{ $property->ref_no }}">
</head>

<body>
    @php
        $agentImage = $property->agent?->pp_enabled == 1 ? $property->agent?->pp_photo : $property->agent?->image_url;
        $agentImage = $agentImage != null ? $agentImage : $property->agent?->image_url;
    @endphp
    <main>
        <div class="container-a4-size">
            <div class="container-a4">
                <div class="container">
                    @if (isset($images))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex img-container">
                                    {{-- <div class="main-img"
                                        style='background-image: url("{{ $images[0]?->path }}"); background-size: cover;'>
                                        <img src="{{ $property?->qr_link }}" class="qr-code" alt="Marina">
                                    </div> --}}
                                    <div class="main-img"
                                        style='background-image: url("{{ imageToBase64($images[0]?->path) }}"); background-size: cover; background-position: center; background-repeat: no-repeat;'>
                                        <img src="{{ imageToBase64($property?->qr_link) }}" class="qr-code"
                                            alt="Marina">
                                    </div>
                                    <div class="three-img">
                                        <img src="{{ imageToBase64($images[1]?->path) }}">
                                        <img src="{{ imageToBase64($images[2]?->path) }}">
                                        <img src="{{ imageToBase64($images[3]?->path) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex img-container">
                                    <div class="main-img"
                                        style='background-image: url("{{ imageToBase64($image->original ?? $property?->photos[0]->original) }}"); background-size: cover;'>
                                        <img src="{{ imageToBase64($property?->qr_link) }}" class="qr-code"
                                            alt="Marina">
                                    </div>
                                    <div class="three-img">
                                        <img src="{{ imageToBase64($property?->photos[1]->path) }}">
                                        <img src="{{ imageToBase64($property?->photos[2]->path) }}">
                                        <img src="{{ imageToBase64($property?->photos[3]->path) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-block-header-top d-flex justify-content-between">
                                <div class="left">
                                    <h1>{{ $property->propertyCategory->title }}</h1>
                                    <p> {{ $property->title }}</p>
                                </div>
                                <div class="right">
                                    @if (in_array($property->for, ['Residential for rent', 'Commercial for rent']))
                                        @if ($show_price == true)
                                            <h2> AED {{ number_format($property->price?->price) }} <br>/year</h2>
                                        @endif
                                    @else
                                        @if ($show_price == true)
                                            <h2> AED {{ number_format($property->price?->price) }}</h2>
                                        @endif
                                    @endif

                                </div>
                            </div>
                            <div class="info-block-header-bottom  d-flex justify-content-between">
                                <div class="left">
                                    <p>Address:
                                        {{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}
                                    </p>
                                </div>
                                <div class="right">
                                    <p>{{ $property->for }}<br>{{ $property->ref_no }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="hr-line">
                    <div class="row">
                        <div class="col-md-12" style="min-height: 212px;max-height: 213px">
                            <div class="details d-flex justify-content-between">
                                <div class="left">
                                    <ul>
                                        {{-- @foreach ($property?->amenities->take(14) as $amenity)
                                            @if ($amenity?->amenity != null)
                                                <li><img src="images/check-mark.png">{{ $amenity?->amenity?->title }}
                                                </li>
                                            @endif
                                        @endforeach --}}

                                        @if ($amenities)

                                            @php
                                                $amenities = explode(',', $amenities);
                                            @endphp
                                            @foreach ($amenities as $amenity)
                                                <li class=""><img
                                                        src="{{ asset('brochure/images/check-mark.png') }}">{{ $amenity }}
                                                </li>
                                            @endforeach
                                        @else
                                            @foreach ($property?->amenities as $amenities)
                                                @if ($amenities?->amenity != null)
                                                    <li><img
                                                            src="{{ asset('brochure/images/check-mark.png') }}">{{ $amenities?->amenity?->title }}
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif
                                        {{-- <li><img src="images/check-mark.png">Covered Parking</li>
										<li><img src="images/check-mark.png">Community View</li>
										<li><img src="images/check-mark.png">Fully Fitted Kitchen</li>
										<li><img src="images/check-mark.png">Covered Parking</li>
										<li><img src="images/check-mark.png">Covered Parking</li>
										<li><img src="images/check-mark.png">Fully Fitted Kitchen</li>
										<li><img src="images/check-mark.png">Covered Parking</li>
										<li><img src="images/check-mark.png">Community View</li>
										<li><img src="images/check-mark.png">Fully Fitted Kitchen</li>
										<li><img src="images/check-mark.png">Covered Parking</li>
										<li><img src="images/check-mark.png">Covered Parking</li> --}}
                                    </ul>
                                </div>
                                <div class="right">
                                    <div class="item">
                                        <img src="{{ asset('brochure/images/icon1.png') }}">
                                        @if ($property?->bed?->name == 'Studio')
                                            <span>Studio</span>
                                        @else
                                            <span>{{ $property?->bed?->name }}</span>
                                        @endif
                                    </div>
                                    <div class="item">
                                        <img src="{{ asset('brochure/images/icon2.png') }}">
                                        <span>{{ $property?->bath?->name }}</span>
                                    </div>
                                    <div class="item">
                                        <img src="{{ asset('brochure/images/icon3.png') }}">
                                        <span>{{ $property->built_up_area }}</span>
                                    </div>
                                    <div class="item">
                                        <img src="{{ asset('brochure/images/icon4.png') }}">
                                        <span>{{ ucfirst($property->furnished) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                <div class="container-a4-footer p-2">
                    @php
                        if ($profile_user == 'agent') {
                            $agentImage =
                                $property->agent?->pp_enabled == 1
                                    ? $property->agent?->pp_photo
                                    : $property->agent?->image_url;
                            $agentImage = $agentImage != null ? $agentImage : $property->agent?->image_url;
                        } else {
                            $agentImage = auth()->user()?->image_url;
                            $agentImage = $agentImage != null ? $agentImage : auth()->user()?->image_url;
                        }
                    @endphp

                    <div class="container mt-2"
                        @if ($show_profile) @else style="visibility: hidden" @endif>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="agent-details d-flex justify-content-between align-items-center">
                                    <div class="agent d-flex">
                                        <div class="img">
                                            <img src="{{ imageToBase64($agentImage) }}">
                                        </div>
                                        <div class="info">
                                            <h4>
                                                @if ($profile_user == 'agent')
                                                    {{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}
                                                @else
                                                    {{ auth()->user()?->name }}
                                                @endif
                                            </h4>
                                            <div class="contact-details d-flex">
                                                <div class="left">
                                                    <p>
                                                        @if ($profile_user == 'agent')
                                                            {{ $property->agent?->position ?? '' }}
                                                        @else
                                                            {{ auth()->user()?->position ?? '' }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="right">
                                                    <p>
                                                        <span>
                                                            @if ($profile_user == 'agent')
                                                                +{{ numbers_only($property->agent?->mobile) ?? '' }}
                                                            @else
                                                                +{{ numbers_only(auth()->user()?->mobile) ?? '' }}
                                                            @endif
                                                        </span>
                                                        <span>
                                                            <img src="{{ asset('brochure/images/tg.png') }}">
                                                            <img src="{{ asset('brochure/images/wa.png') }}">
                                                        </span>
                                                    </p>
                                                    <p>
                                                        @if ($profile_user == 'agent')
                                                            {{ $property->agent?->email ?? '' }}
                                                        @else
                                                            {{ auth()->user()?->email ?? '' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="company-logo">
                                        <img src="{{ asset('brochure/images/logo.png') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <button id="downloadPDF" class="btn btn-primary mt-3">If it hasn't started automatically, click here to download
            the PDF.</button>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        function download() {
            var element = document.querySelector('.container-a4-size');
            console.log('Element to convert:', element);

            var ref_no = document.querySelector('meta[name="ref_no"]').getAttribute('content');

            var opt = {

                filename: 'preview-' + ref_no + '.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                console.log('PDF generated successfully.');
            }).catch((error) => {
                console.error('Error generating PDF:', error);
            });
        }




        setTimeout(() => {
            download();
        }, 4000);

        document.getElementById('downloadPDF').addEventListener('click', function() {
            download();
        });
    </script>
</body>

</html>

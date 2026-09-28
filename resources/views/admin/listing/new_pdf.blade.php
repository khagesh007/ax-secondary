<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Infant:wght@500&display=swap" rel="stylesheet" />
    <link href="{{ asset('new_preview/vendors/swiper/swiper.min.css') }}" rel="stylesheet" />
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.css" rel="stylesheet" />
    <link href="{{ asset('new_preview/styles/output.css') }}" rel="stylesheet" />
</head>

<body>
    <div class="absolute right-5 top-5">
        <button class="generate-pdf bg-white38 px-7 py-3 uppercase">
            generate pdf
        </button>
        {{-- <button class="generate-inst-images bg-white38 px-7 py-3 uppercase">
            generate instagram posts
        </button> --}}

        <button class=" bg-white38 px-7 py-3 uppercase gen" id="gen">
            generate instagram posts
        </button>
        <div>

    {{-- <div id="myModal" class="odal-dialog.dynamic-post-dialog">
        <div class="modal-content">

            <label for="">Show Price</label>
            <select name="" id="price">
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label for="">Show Profile</label>
            <select name="" id="profile">
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <input type="text" name="title_first" id="title_first">
            <input type="text" name="title_last" id="title_last">

            <button type="button" id="gen" style="background: red">Generate</button>

        </div>
    </div> --}}
        </div>
    </div>
    <div
        class="loading fixed top-0 left-0 z-[50] hidden h-full w-full items-center justify-center bg-white60 text-4xl font-semibold uppercase text-black">
        generate...

    </div>


    <header class="border-[1px] border-b-[#595858]">
        <div class="container" style="max-width: 1412px">
            <div class="flex items-center justify-center py-8 tablet:py-12">
                <a href="/">
                    <img src="{{ asset('preview/images/logo.svg') }}" alt="logo"
                        class="h-[37px] w-[137px] tablet:h-[47px] tablet:w-[174px]" />
                </a>
            </div>
        </div>
    </header>
    <div class="pt-24">
        <div class="container" style="max-width: 1412px">
            <div class="page-wrapper">
                <main>
                    @if (isset($property->photos) && count($property->photos) > 0)
                        <div class="gallery-wrapper">
                            <div class="gallery">
                                <div class="image">
                                    <picture class="relative w-full cursor-pointer">
                                        <source srcset="{{ $property?->photos[0]->path }}" type="image/jpeg" />
                                        <img src="{{ $property?->photos[0]->path }}" alt="slider-image"
                                            loading="lazy" />
                                    </picture>
                                </div>
                                <div class="image">
                                    <picture class="relative w-full cursor-pointer">
                                        <source srcset="{{ $property?->photos[1]->path }}" type="image/jpeg" />
                                        <img src="{{ $property?->photos[1]->path }}" alt="slider-image"
                                            loading="lazy" />
                                    </picture>
                                </div>
                                <div class="image">
                                    <picture class="relative w-full cursor-pointer">
                                        <source srcset="{{ $property?->photos[2]->path }}" type="image/jpeg" />
                                        <img src="{{ $property?->photos[2]->path }}" alt="slider-image"
                                            loading="lazy" />
                                    </picture>

                                    <button class="show-all absolute hidden xlTablet:flex">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20"
                                            viewBox="0 0 21 20" fill="none">
                                            <path
                                                d="M16.3333 4.16667V15.8333H4.66667V4.16667H16.3333ZM16.3333 2.5H4.66667C3.75 2.5 3 3.25 3 4.16667V15.8333C3 16.75 3.75 17.5 4.66667 17.5H16.3333C17.25 17.5 18 16.75 18 15.8333V4.16667C18 3.25 17.25 2.5 16.3333 2.5ZM12.2833 9.88333L9.78333 13.1083L8 10.95L5.5 14.1667H15.5L12.2833 9.88333Z"
                                                fill="white" />
                                        </svg>
                                        <span> show all </span>
                                    </button>
                                </div>
                            </div>
                            <button class="show-all mt-6 flex w-full xlTablet:hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20"
                                    viewBox="0 0 21 20" fill="none">
                                    <path
                                        d="M16.3333 4.16667V15.8333H4.66667V4.16667H16.3333ZM16.3333 2.5H4.66667C3.75 2.5 3 3.25 3 4.16667V15.8333C3 16.75 3.75 17.5 4.66667 17.5H16.3333C17.25 17.5 18 16.75 18 15.8333V4.16667C18 3.25 17.25 2.5 16.3333 2.5ZM12.2833 9.88333L9.78333 13.1083L8 10.95L5.5 14.1667H15.5L12.2833 9.88333Z"
                                        fill="white" />
                                </svg>
                                <span> show all </span>
                            </button>
                            <div class="modal-dialog gallery-dialog items-center justify-center">
                                <div>
                                    <div class="swiper-gallery">
                                        <div
                                            class="burger open absolute top-5 right-5 z-[2] h-8 w-8 cursor-pointer bg-black80">
                                            <span></span><span></span><span></span>
                                        </div>
                                        <div class="swiper swiper-main">
                                            <div class="swiper-wrapper">
                                                @foreach ($property?->photos as $photo)
                                                    <div class="swiper-slide">

                                                        <picture class="relative w-full">
                                                            <source srcset="{{ $photo->path }}" type="image/jpeg" />
                                                            <img src="{{ $photo->path }}" loading="lazy" />
                                                        </picture>
                                                    </div>
                                                @endforeach

                                            </div>
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
                                        </div>
                                        <div class="thumb">
                                            <div class="swiper-wrapper">
                                                @foreach ($property?->photos as $photo)
                                                    <div class="swiper-slide">
                                                        <picture>
                                                            <source srcset="{{ $photo->thumbnail }}"
                                                                type="image/jpeg" />
                                                            <img src="{{ $photo->thumbnail }}" loading="lazy" />
                                                        </picture>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <h1 class="mt-16 mb-8 text-start text-[32px] normal-case leading-[1.3]">
                        {{ $property->title }},
                        ({{ $property->ref_no }})
                    </h1>
                    <div class="mb-16 flex flex-col items-center justify-between text-white xlTablet:flex-row">
                        <div class="flex items-center">
                            <strong class="text-[28px] font-light tablet:text-5xl">AED
                                {{ number_format($property->price?->price) }}</strong>
                        </div>
                        <div>
                            <span class="text-white60">{{ strtoupper($property->for) }}</span>
                        </div>
                    </div>
                    <div
                        class="mb-16 flex flex-wrap justify-between gap-x-9 gap-y-6 border-y-[1px] border-borderMain py-8 uppercase text-white60 tablet:gap-y-4">
                        <div class="flex flex-wrap items-center tablet:flex-nowrap tablet:space-x-9">
                            <div class="flex w-full items-center space-x-9 tablet:w-auto">
                                <strong class="flex items-center space-x-3">
                                    <svg width="15" height="18" viewBox="0 0 15 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" class="opacity-60">
                                        <path
                                            d="M7.40007 0.5C3.87127 0.5 1.00007 3.3712 1.00007 6.896C0.976865 12.052 7.15687 16.3272 7.40007 16.5C7.40007 16.5 13.8233 12.052 13.8001 6.9C13.8001 3.3712 10.9289 0.5 7.40007 0.5ZM7.40007 10.1C5.63207 10.1 4.20007 8.668 4.20007 6.9C4.20007 5.132 5.63207 3.7 7.40007 3.7C9.16807 3.7 10.6001 5.132 10.6001 6.9C10.6001 8.668 9.16807 10.1 7.40007 10.1Z"
                                            stroke="white"></path>
                                    </svg>
                                    <span
                                        class="capitalize">{{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}</span>
                                </strong>
                                <strong class="flex items-center space-x-3 capitalize">
                                    <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_21_1771)">
                                            <path
                                                d="M14.8334 12.5002H16.5V14.1668H14.8334V12.5002ZM14.8334 9.16683H16.5V10.8335H14.8334V9.16683ZM14.8334 5.8335H16.5V7.50016H14.8334V5.8335ZM12.1167 5.8335L13.1667 6.5335V5.8335H12.1167Z"
                                                fill="white" fill-opacity="0.6" />
                                            <path
                                                d="M9 2.5L9 3.09167L10.6667 4.2V3.5H18.8667V9.66667V16.5H14.8333V17.5H19.8333V2.5H9Z"
                                                fill="white" fill-opacity="0.6" />
                                            <path
                                                d="M7.6083 4.75L13.3 8.54167V17.5H1.6333V8.73333L7.6083 4.75ZM8 16.5H12V9L7.6083 6L2.70918 9.3V16.5H6.5V13H8V16.5Z"
                                                fill="white" fill-opacity="0.6" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_21_1771">
                                                <rect width="20" height="20" fill="white"
                                                    transform="translate(0.800049)" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <span>{{ $property?->propertyCategory?->title }}</span>
                                </strong>
                            </div>
                            <div class="mt-2 flex items-center space-x-9 tablet:mt-0">
                                <strong class="flex items-center space-x-3">
                                    <svg width="21" height="17" viewBox="0 0 21 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M4.0498 0.125C3.22138 0.125 2.5498 0.796571 2.5498 1.625V6.125C1.72138 6.125 1.0498 6.79657 1.0498 7.625L1.0498 8.125C0.49752 8.125 0.0498047 8.57271 0.0498047 9.125V14.625C0.0498047 15.1773 0.49752 15.625 1.0498 15.625H1.2998V16.875H2.2998V15.625H17.7498V16.875H18.7498V15.625H19.0498C19.6021 15.625 20.0498 15.1773 20.0498 14.625V9.125C20.0498 8.57271 19.6021 8.125 19.0498 8.125V7.625C19.0498 6.79657 18.3782 6.125 17.5498 6.125V1.625C17.5498 0.796573 16.8782 0.125 16.0498 0.125H4.0498ZM16.5498 6.125V1.625C16.5498 1.34886 16.3259 1.125 16.0498 1.125H4.0498C3.77366 1.125 3.5498 1.34886 3.5498 1.625V6.125H4.0498V5.125C4.0498 4.29657 4.72138 3.625 5.5498 3.625H8.5498C9.37823 3.625 10.0498 4.29657 10.0498 5.125C10.0498 4.29657 10.7214 3.625 11.5498 3.625H14.5498C15.3782 3.625 16.0498 4.29657 16.0498 5.125V6.125H16.5498ZM15.0498 6.125V5.125C15.0498 4.84886 14.8259 4.625 14.5498 4.625H11.5498C11.2737 4.625 11.0498 4.84886 11.0498 5.125V6.125H15.0498ZM9.0498 6.125H5.0498V5.125C5.0498 4.84886 5.27366 4.625 5.5498 4.625H8.5498C8.82595 4.625 9.0498 4.84886 9.0498 5.125V6.125ZM18.0498 7.625V8.125H2.0498V7.625C2.0498 7.34886 2.27366 7.125 2.5498 7.125H17.5498C17.8259 7.125 18.0498 7.34886 18.0498 7.625ZM1.0498 9.125V14.625H19.0498V9.125H1.0498Z"
                                            fill="white" fill-opacity="0.6"></path>
                                    </svg>
                                    <span>
                                        @if ($property?->bed?->name == 'Studio')
                                            <span>Studio</span>
                                        @else
                                            <span>{{ numbers_only($property?->bed?->name) }}</span>
                                        @endif
                                    </span>
                                </strong>
                                <strong class="flex items-center space-x-3"><svg width="18" height="15"
                                        viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M3.8269 1.98438C3.8269 1.70823 4.05076 1.48438 4.3269 1.48438H4.55418C4.83032 1.48438 5.05418 1.70823 5.05418 1.98438V2.09801H6.05418V1.98438C6.05418 1.15595 5.3826 0.484375 4.55418 0.484375H4.3269C3.49848 0.484375 2.8269 1.15595 2.8269 1.98438V3.91895H1.59961C0.771182 3.91895 0.0996094 4.59052 0.0996094 5.41895V6.60636V7.41895V7.51545C0.0996094 11.3814 3.23362 14.5154 7.09961 14.5154H10.9178C14.7838 14.5154 17.9178 11.3814 17.9178 7.51545V7.41895H17.9496V5.41895C17.9496 4.59052 17.278 3.91895 16.4496 3.91895H3.8269V1.98438ZM16.9496 5.41895V5.60685C16.939 5.60652 16.9284 5.60636 16.9178 5.60636H1.09961V5.41895C1.09961 5.1428 1.32347 4.91895 1.59961 4.91895H16.4496C16.7258 4.91895 16.9496 5.1428 16.9496 5.41895ZM1.09961 7.41895V6.60636H16.9178V7.51545C16.9178 10.8292 14.2315 13.5154 10.9178 13.5154H7.09961C3.7859 13.5154 1.09961 10.8292 1.09961 7.51545V7.41895Z"
                                            fill="white" fill-opacity="0.6"></path>
                                    </svg>
                                    <span>{{ numbers_only($property?->bath?->name) }}</span>
                                </strong>
                                <strong class="flex items-center space-x-3"><svg width="16" height="17"
                                        viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.5 16V1.70711L14.7929 16H0.5Z" stroke="white"
                                            stroke-opacity="0.6"></path>
                                        <path d="M4 12.5V10.2071L6.29289 12.5H4Z" stroke="white"
                                            stroke-opacity="0.6"></path>
                                    </svg>
                                    <span>{{ $property->built_up_area }} SQ.FT</span>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <h2 class="mb-10 font-mont text-3xl font-extralight uppercase text-white">
                        features &amp; amenities
                    </h2>
                    <div class="mb-16 grid border-t-[1px] border-l-[1px] border-borderMain laptop:grid-cols-3">
                        @foreach ($property?->amenities as $amenities)
                            <div
                                class="flex min-h-[59px] items-center space-x-2 whitespace-pre-wrap border-b-[1px] border-r-[1px] border-borderMain px-8 text-xl font-light text-white">
                                <img src="{{ asset('new_preview/images/done.svg') }}"
                                    alt="{{ $amenities?->amenity?->title }}" class="-mt-1 w-6" />
                                <span>{{ $amenities?->amenity?->title }}</span>
                            </div>
                        @endforeach


                    </div>
                    <div class="mb-14 border-y-[1px] border-borderMain py-16">
                        <h2 class="mb-8 font-mont text-3xl font-extralight uppercase text-white">
                            description
                        </h2>
                        <p class="mb-16 font-extralight text-white opacity-70">
                            {!! nl2br(e(strip_tags($property?->description))) !!}
                        </p>
                        <h4 class="font-mont text-3xl font-extralight">
                            Listing Details
                        </h4>
                        <div class="table">
                            <ul>
                                <li>
                                    <span>Category:</span>
                                    <span>{{ $property?->propertyCategory?->title }}</span>
                                </li>
                                <li>
                                    <span>Beds:</span>
                                    <span>{{ ucwords(strtolower($property?->bed?->name)) }}</span>
                                </li>
                                <li>
                                    <span>Baths:</span>
                                    <span>{{ ucwords(strtolower($property?->bath?->name)) }}</span>
                                </li>

                                <li>
                                    <span>Parking:</span>
                                    <span>{{ $property->parking }}</span>
                                </li>
                                <li>
                                    <span>RERA Permit #:</span>
                                    <span>{{ $property->rera_permit_number }}</span>
                                </li>
                            </ul>
                            <ul>
                                @if (!empty($property?->communityProperty))
                                    <li>
                                        <span>Tower:</span>
                                        <span> {{ $property?->communityProperty?->name }}</span>
                                    </li>
                                @endif
                                @if (!empty($property?->subCommunity))
                                    <li>
                                        <span>Sub Community:</span>
                                        <span> {{ $property?->subCommunity?->name }}</span>
                                    </li>
                                @endif
                                @if (!empty($property?->community))
                                    <li>
                                        <span>Community:</span>
                                        <span> {{ $property?->community?->name }}</span>
                                    </li>
                                @endif
                                <li>
                                    <span>Property:</span>
                                    <span> {{ $property?->ref_no }}</span>
                                </li>
                                <li>
                                    <span>Built-up Area:</span>
                                    <span>{{ $property->built_up_area }} SQ.FT</span>
                                </li>
                                <li>
                                    <span>Furnished:</span>
                                    <span>{{ strtolower($property->furnished) }}</span>
                                </li>

                                <li>
                                    <span>Status:</span>
                                    <span>{{ $property?->propertyStatus?->title }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-6 font-mont text-3xl font-extralight uppercase text-white">
                            Location
                        </h2>
                        <div id="map" style="width: '100%'; height: 300px"></div>
                    </div>
                </main>
                <aside class="relative">
                    <div class="laptop:h-full">
                        <div class="sticky top-0 z-10 transition-all duration-500">
                            <div class="border-[1px] border-borderMain bg-black p-8">
                                <div class="flex flex-col items-center text-white">
                                    <img src="{{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_photo : $property->agent?->image_url }}"
                                        alt="{{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}"
                                        class="mb-[8px] h-[95px] w-[95px] object-cover object-top" />
                                    <span
                                        class="text-sm uppercase text-white opacity-60">{{ $property->agent?->position ?? '' }}</span>
                                    <span
                                        class="mt-6 font-ap text-3xl">{{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}</span>
                                    <a href="mailto:{{ $property->agent?->email }}"
                                        class="mt-[4px] text-sm opacity-60">{{ $property->agent?->email }}</a>
                                </div>
                                <div class="mb-4 mt-8 grid grid-cols-2 gap-2">
                                    <a href="tel:{{ $property->agent?->mobile }}"
                                        class="primary-btn text-center uppercase">Call us</a>
                                    <button class="primary-btn inquiry-btn-aside uppercase">
                                        Inquiry
                                    </button>
                                </div>
                                <p class="flex cursor-pointer items-center justify-center text-base text-white60">
                                    or contact via
                                    <a class="text_primary-gradients flex items-center text-primary"
                                        href="https://api.whatsapp.com/send?phone={{ $property->agent?->mobile }}&text=Hi I am interested in property {{ $property->ref_no }}">
                                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" class="ml-3 mr-1">
                                            <g clip-path="url(#clip0_45_5852)">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M16.1202 14.8495C15.8831 14.731 14.721 14.1598 14.5046 14.0803C14.2882 14.0015 14.1307 13.9625 13.9724 14.1996C13.8149 14.435 13.3623 14.968 13.2247 15.1255C13.0863 15.2838 12.9486 15.3029 12.7124 15.1852C12.4761 15.0659 11.7141 14.8169 10.8112 14.0119C10.1088 13.385 9.63388 12.611 9.49627 12.374C9.35865 12.1377 9.48115 12.0096 9.59968 11.8919C9.70627 11.7861 9.83593 11.6159 9.95446 11.4783C10.073 11.3399 10.112 11.2412 10.1907 11.0829C10.2703 10.9254 10.2305 10.7878 10.1708 10.6693C10.112 10.5507 9.63945 9.38696 9.44217 8.91365C9.25046 8.45307 9.05557 8.51591 8.9108 8.50795C8.77238 8.50159 8.61488 8.5 8.45737 8.5C8.29987 8.5 8.04373 8.55887 7.82736 8.79592C7.61019 9.03217 7.00006 9.60412 7.00006 10.7679C7.00006 11.9309 7.84645 13.0549 7.96497 13.2132C8.0835 13.3707 9.6315 15.7587 12.0028 16.7825C12.5676 17.0259 13.0075 17.1715 13.3503 17.2797C13.9167 17.4602 14.4322 17.4348 14.8395 17.3735C15.2929 17.3059 16.2379 16.8016 16.4352 16.2495C16.6317 15.6975 16.6317 15.2242 16.5728 15.1255C16.514 15.0269 16.3573 14.968 16.1202 14.8495Z"
                                                    fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M16.1202 14.8495C15.8831 14.731 14.721 14.1598 14.5046 14.0803C14.2882 14.0015 14.1307 13.9625 13.9724 14.1996C13.8149 14.435 13.3623 14.968 13.2247 15.1255C13.0863 15.2838 12.9486 15.3029 12.7124 15.1852C12.4761 15.0659 11.7141 14.8169 10.8112 14.0119C10.1088 13.385 9.63388 12.611 9.49627 12.374C9.35865 12.1377 9.48115 12.0096 9.59968 11.8919C9.70627 11.7861 9.83593 11.6159 9.95446 11.4783C10.073 11.3399 10.112 11.2412 10.1907 11.0829C10.2703 10.9254 10.2305 10.7878 10.1708 10.6693C10.112 10.5507 9.63945 9.38696 9.44217 8.91365C9.25046 8.45307 9.05557 8.51591 8.9108 8.50795C8.77238 8.50159 8.61488 8.5 8.45737 8.5C8.29987 8.5 8.04373 8.55887 7.82736 8.79592C7.61019 9.03217 7.00006 9.60412 7.00006 10.7679C7.00006 11.9309 7.84645 13.0549 7.96497 13.2132C8.0835 13.3707 9.6315 15.7587 12.0028 16.7825C12.5676 17.0259 13.0075 17.1715 13.3503 17.2797C13.9167 17.4602 14.4322 17.4348 14.8395 17.3735C15.2929 17.3059 16.2379 16.8016 16.4352 16.2495C16.6317 15.6975 16.6317 15.2242 16.5728 15.1255C16.514 15.0269 16.3573 14.968 16.1202 14.8495Z"
                                                    fill="url(#paint0_linear_45_5852)"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M16.1202 14.8495C15.8831 14.731 14.721 14.1598 14.5046 14.0803C14.2882 14.0015 14.1307 13.9625 13.9724 14.1996C13.8149 14.435 13.3623 14.968 13.2247 15.1255C13.0863 15.2838 12.9486 15.3029 12.7124 15.1852C12.4761 15.0659 11.7141 14.8169 10.8112 14.0119C10.1088 13.385 9.63388 12.611 9.49627 12.374C9.35865 12.1377 9.48115 12.0096 9.59968 11.8919C9.70627 11.7861 9.83593 11.6159 9.95446 11.4783C10.073 11.3399 10.112 11.2412 10.1907 11.0829C10.2703 10.9254 10.2305 10.7878 10.1708 10.6693C10.112 10.5507 9.63945 9.38696 9.44217 8.91365C9.25046 8.45307 9.05557 8.51591 8.9108 8.50795C8.77238 8.50159 8.61488 8.5 8.45737 8.5C8.29987 8.5 8.04373 8.55887 7.82736 8.79592C7.61019 9.03217 7.00006 9.60412 7.00006 10.7679C7.00006 11.9309 7.84645 13.0549 7.96497 13.2132C8.0835 13.3707 9.6315 15.7587 12.0028 16.7825C12.5676 17.0259 13.0075 17.1715 13.3503 17.2797C13.9167 17.4602 14.4322 17.4348 14.8395 17.3735C15.2929 17.3059 16.2379 16.8016 16.4352 16.2495C16.6317 15.6975 16.6317 15.2242 16.5728 15.1255C16.514 15.0269 16.3573 14.968 16.1202 14.8495Z"
                                                    fill="url(#paint1_linear_45_5852)"></path>
                                                <path
                                                    d="M20.5043 7.35613C17.5126 2.49984 11.4044 1.05608 6.66747 4.07485C2.05518 7.09362 0.559298 13.6562 3.55105 18.5125L3.80037 18.9062L2.80311 22.8437L6.54281 21.7937L6.91678 22.0562C8.53731 22.975 10.2825 23.5 12.0277 23.5C13.8975 23.5 15.7674 22.975 17.3879 21.925C22.0002 18.775 23.3714 12.3437 20.5043 7.35613Z"
                                                    stroke="url(#paint2_linear_45_5852)" stroke-width="1.2"></path>
                                            </g>
                                            <defs>
                                                <linearGradient id="paint0_linear_45_5852" x1="2.86042"
                                                    y1="3.40892" x2="24.087" y2="17.0673"
                                                    gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#A15C40"></stop>
                                                    <stop offset="0.488334" stop-color="#E28F68"></stop>
                                                    <stop offset="0.872347" stop-color="#A15C40"></stop>
                                                </linearGradient>
                                                <linearGradient id="paint1_linear_45_5852" x1="1.53534"
                                                    y1="3.82302" x2="25.0803" y2="19.1816"
                                                    gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#CC9177"></stop>
                                                    <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                                    <stop offset="0.515487" stop-color="#C18369"></stop>
                                                    <stop offset="0.729115" stop-color="#EAB498"></stop>
                                                    <stop offset="1" stop-color="#E6A483"></stop>
                                                </linearGradient>
                                                <linearGradient id="paint2_linear_45_5852" x1="1.23028"
                                                    y1="2.95543" x2="26.6535" y2="18.8256"
                                                    gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#CC9177"></stop>
                                                    <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                                    <stop offset="0.515487" stop-color="#C18369"></stop>
                                                    <stop offset="0.729115" stop-color="#EAB498"></stop>
                                                    <stop offset="1" stop-color="#E6A483"></stop>
                                                </linearGradient>
                                                <clipPath id="clip0_45_5852">
                                                    <rect width="24" height="24" fill="white"
                                                        transform="translate(6.10352e-05 0.5)"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        WHATSAPP
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
    <footer class="pb-24 pt-32">
        <div class="container" style="max-width: 1412px">
            <span class="text-white opacity-60">AX CAPITAL © 2022 All Rights Reserved</span>
        </div>
    </footer>

    <div class="modal-dialog inquiry-dialog items-center justify-center">
        <div>
            <div class="relative bg-darkBlack px-28 py-11">
                <div class="burger open absolute top-8 right-8 cursor-pointer">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <form class="inquiry-form border-none after:hidden">
                    <h4>Get professional agent consultation</h4>
                    <p>Fill form below and our agent will contact you shortly</p>
                    <fieldset>
                        <label><input required type="text" placeholder="your name" /></label><label><input required
                                type="tel" placeholder="your phone" /></label><label><input required
                                type="email" placeholder="your e-mail" /></label><button
                            class="primary-btn">send</button>
                    </fieldset>
                    <strong>Or contact us right now via
                        <a class="text_primary-gradients ml-5 flex items-center" href="#">
                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="mr-1">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.5202 14.3495C16.2831 14.231 15.1209 13.6598 14.9045 13.5803C14.6882 13.5015 14.5307 13.4625 14.3724 13.6996C14.2149 13.935 13.7622 14.468 13.6246 14.6255C13.4862 14.7838 13.3486 14.8029 13.1123 14.6852C12.8761 14.5659 12.114 14.3169 11.2112 13.5119C10.5087 12.885 10.0338 12.111 9.89623 11.874C9.75861 11.6377 9.88112 11.5096 9.99964 11.3919C10.1062 11.2861 10.2359 11.1159 10.3544 10.9783C10.4729 10.8399 10.5119 10.7412 10.5907 10.5829C10.6702 10.4254 10.6305 10.2878 10.5708 10.1693C10.5119 10.0507 10.0394 8.88696 9.84214 8.41365C9.65043 7.95307 9.45554 8.01591 9.31076 8.00795C9.17235 8.00159 9.01484 8 8.85734 8C8.69983 8 8.44369 8.05887 8.22732 8.29592C8.01015 8.53217 7.40002 9.10412 7.40002 10.2679C7.40002 11.4309 8.24641 12.5549 8.36494 12.7132C8.48346 12.8707 10.0315 15.2587 12.4028 16.2825C12.9676 16.5259 13.4075 16.6715 13.7503 16.7797C14.3167 16.9602 14.8322 16.9348 15.2394 16.8735C15.6929 16.8059 16.6379 16.3016 16.8352 15.7495C17.0317 15.1975 17.0317 14.7242 16.9728 14.6255C16.9139 14.5269 16.7572 14.468 16.5202 14.3495Z"
                                    fill="#D3A188"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M16.5202 14.3495C16.2831 14.231 15.1209 13.6598 14.9045 13.5803C14.6882 13.5015 14.5307 13.4625 14.3724 13.6996C14.2149 13.935 13.7622 14.468 13.6246 14.6255C13.4862 14.7838 13.3486 14.8029 13.1123 14.6852C12.8761 14.5659 12.114 14.3169 11.2112 13.5119C10.5087 12.885 10.0338 12.111 9.89623 11.874C9.75861 11.6377 9.88112 11.5096 9.99964 11.3919C10.1062 11.2861 10.2359 11.1159 10.3544 10.9783C10.4729 10.8399 10.5119 10.7412 10.5907 10.5829C10.6702 10.4254 10.6305 10.2878 10.5708 10.1693C10.5119 10.0507 10.0394 8.88696 9.84214 8.41365C9.65043 7.95307 9.45554 8.01591 9.31076 8.00795C9.17235 8.00159 9.01484 8 8.85734 8C8.69983 8 8.44369 8.05887 8.22732 8.29592C8.01015 8.53217 7.40002 9.10412 7.40002 10.2679C7.40002 11.4309 8.24641 12.5549 8.36494 12.7132C8.48346 12.8707 10.0315 15.2587 12.4028 16.2825C12.9676 16.5259 13.4075 16.6715 13.7503 16.7797C14.3167 16.9602 14.8322 16.9348 15.2394 16.8735C15.6929 16.8059 16.6379 16.3016 16.8352 15.7495C17.0317 15.1975 17.0317 14.7242 16.9728 14.6255C16.9139 14.5269 16.7572 14.468 16.5202 14.3495Z"
                                    fill="url(#paint0_linear_1_13)"></path>
                                <path
                                    d="M20.9043 6.85613C17.9125 1.99984 11.8044 0.556079 7.06743 3.57485C2.45514 6.59362 0.959261 13.1562 3.95102 18.0125L4.20033 18.4062L3.20308 22.3437L6.94277 21.2937L7.31674 21.5562C8.93728 22.475 10.6825 23 12.4277 23C14.2975 23 16.1674 22.475 17.7879 21.425C22.4002 18.275 23.7714 11.8437 20.9043 6.85613Z"
                                    stroke="url(#paint1_linear_1_13)" stroke-width="1.2"></path>
                                <defs>
                                    <linearGradient id="paint0_linear_1_13" x1="7.42778" y1="6.91513"
                                        x2="16.9846" y2="6.71908" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#CC9177"></stop>
                                        <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                        <stop offset="0.515487" stop-color="#C18369"></stop>
                                        <stop offset="0.729115" stop-color="#EAB498"></stop>
                                        <stop offset="1" stop-color="#E6A483"></stop>
                                    </linearGradient>
                                    <linearGradient id="paint1_linear_1_13" x1="1.63025" y1="2.45543"
                                        x2="27.0535" y2="18.3256" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#CC9177"></stop>
                                        <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                        <stop offset="0.515487" stop-color="#C18369"></stop>
                                        <stop offset="0.729115" stop-color="#EAB498"></stop>
                                        <stop offset="1" stop-color="#E6A483"></stop>
                                    </linearGradient>
                                </defs>
                            </svg>
                            WHATSAPP
                        </a></strong>
                </form>
            </div>
        </div>
    </div>


    {{-- <div class="modal-dialog post-dialog newModalBlog items-center justify-center">
        <div>
            <div class="relative bg-darkBlack px-28 py-11">
                <div class="burger open absolute top-8 right-8 cursor-pointer">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <form class="inquiry-form border-none after:hidden">
                    <h4>Generate Instagram Post</h4>
                    <p>Complete the post specification form by providing the required information.</p>
                    <fieldset>
                        <label><input required type="text" placeholder="First Text"id="title_first" /></label>
                        <label><input required type="text" id="title_last"  placeholder="Second Text" /></label>

                            <select required id="price"><option value="" disabled selected>Show Price</option><option value="yes">Yes</option><option value="no">No</option></select>
                            <select  data-te-select-init required id="profile" placeholder="Show Profile"><option value="" disabled selected>Show Profile</option><option value="yes">Yes</option><option value="no">No</option></select>

                            <button class="primary-btn" id="gen">Generate</button>
                    </fieldset>

                </form>
            </div>
        </div>
    </div> --}}

    <div class="print-page container bg-black laptop:px-8 hidden" style="max-width: 595px">
        <div
            class="relative -mx-[200px] after:absolute after:bottom-0 after:h-[1px] after:w-full after:bg-selectGradient">
            <div class="flex items-center justify-center py-8">
                <a href="/">
                    <img src="{{asset('new_preview/images/logo.svg')}}" alt="logo" class="h-[24px] w-[88.85px]" />
                </a>
            </div>
        </div>

        @if (isset($property->photos) && count($property->photos) > 0)
            <div class="gallery-wrapper pt-24">
                <div class="gallery">
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ imageToBase64($property?->photos[0]->original) }}" type="image/jpeg" />
                            <img src="{{ $property?->photos[0]->thumbnail }}" alt="slider-image" loading="lazy" />
                        </picture>
                    </div>
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ imageToBase64($property?->photos[1]->original) }}" type="image/jpeg" />
                            <img src="{{ $property?->photos[1]->path }}" alt="slider-image" loading="lazy" />
                        </picture>
                    </div>
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ imageToBase64($property?->photos[2]->original) }}" type="image/jpeg" />
                            <img src="{{ $property?->photos[2]->path }}" alt="slider-image" loading="lazy" />
                        </picture>
                    </div>
                </div>
            </div>
        @endif
        <h1 class="my-6 text-start font-mont text-base font-medium normal-case leading-[1.3]">
            {{ $property->title }},
            ({{ $property->ref_no }})
        </h1>
        <div class="mb-6 flex flex-col items-center justify-between text-white xlTablet:flex-row">
            <div class="flex items-center">
                <strong class="text-[28px] font-light tablet:text-5xl">AED
                    {{ number_format($property->price?->price) }}</strong>
            </div>
            <div>
                <span class="text-white60">{{ strtoupper($property->for) }}</span>
            </div>
        </div>
        <div
            class="flex flex-wrap justify-between gap-x-9 gap-y-6 border-y-[1px] border-borderMain py-6 uppercase text-white60 tablet:gap-y-4">
            <div
                class="flex w-full flex-wrap items-center justify-between text-[10px] tablet:flex-nowrap tablet:space-x-9">
                <div class="flex w-full items-center space-x-9 tablet:w-auto">
                    <strong class="data-item flex items-center space-x-2">
                        <svg width="9.6" height="12" viewBox="0 0 15 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="opacity-60">
                            <path
                                d="M7.40007 0.5C3.87127 0.5 1.00007 3.3712 1.00007 6.896C0.976865 12.052 7.15687 16.3272 7.40007 16.5C7.40007 16.5 13.8233 12.052 13.8001 6.9C13.8001 3.3712 10.9289 0.5 7.40007 0.5ZM7.40007 10.1C5.63207 10.1 4.20007 8.668 4.20007 6.9C4.20007 5.132 5.63207 3.7 7.40007 3.7C9.16807 3.7 10.6001 5.132 10.6001 6.9C10.6001 8.668 9.16807 10.1 7.40007 10.1Z"
                                stroke="white"></path>
                        </svg>
                        <span
                            class="capitalize">{{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}</span>
                    </strong>
                    <strong class="data-item flex items-center space-x-2 capitalize">
                        <svg width="13" height="12" viewBox="0 0 21 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_21_1771)">
                                <path
                                    d="M14.8334 12.5002H16.5V14.1668H14.8334V12.5002ZM14.8334 9.16683H16.5V10.8335H14.8334V9.16683ZM14.8334 5.8335H16.5V7.50016H14.8334V5.8335ZM12.1167 5.8335L13.1667 6.5335V5.8335H12.1167Z"
                                    fill="white" fill-opacity="0.6" />
                                <path
                                    d="M9 2.5L9 3.09167L10.6667 4.2V3.5H18.8667V9.66667V16.5H14.8333V17.5H19.8333V2.5H9Z"
                                    fill="white" fill-opacity="0.6" />
                                <path
                                    d="M7.6083 4.75L13.3 8.54167V17.5H1.6333V8.73333L7.6083 4.75ZM8 16.5H12V9L7.6083 6L2.70918 9.3V16.5H6.5V13H8V16.5Z"
                                    fill="white" fill-opacity="0.6" />
                            </g>
                            <defs>
                                <clipPath id="clip0_21_1771">
                                    <rect width="20" height="20" fill="white"
                                        transform="translate(0.800049)" />
                                </clipPath>
                            </defs>
                        </svg>
                        <span>{{ strtoupper($property?->propertyCategory?->title) }}</span>
                    </strong>
                </div>
                <div class="mt-2 flex items-center space-x-9 tablet:mt-0">
                    <strong class="data-item flex items-center space-x-2">
                        <svg width="16" height="12" viewBox="0 0 21 17" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M4.0498 0.125C3.22138 0.125 2.5498 0.796571 2.5498 1.625V6.125C1.72138 6.125 1.0498 6.79657 1.0498 7.625L1.0498 8.125C0.49752 8.125 0.0498047 8.57271 0.0498047 9.125V14.625C0.0498047 15.1773 0.49752 15.625 1.0498 15.625H1.2998V16.875H2.2998V15.625H17.7498V16.875H18.7498V15.625H19.0498C19.6021 15.625 20.0498 15.1773 20.0498 14.625V9.125C20.0498 8.57271 19.6021 8.125 19.0498 8.125V7.625C19.0498 6.79657 18.3782 6.125 17.5498 6.125V1.625C17.5498 0.796573 16.8782 0.125 16.0498 0.125H4.0498ZM16.5498 6.125V1.625C16.5498 1.34886 16.3259 1.125 16.0498 1.125H4.0498C3.77366 1.125 3.5498 1.34886 3.5498 1.625V6.125H4.0498V5.125C4.0498 4.29657 4.72138 3.625 5.5498 3.625H8.5498C9.37823 3.625 10.0498 4.29657 10.0498 5.125C10.0498 4.29657 10.7214 3.625 11.5498 3.625H14.5498C15.3782 3.625 16.0498 4.29657 16.0498 5.125V6.125H16.5498ZM15.0498 6.125V5.125C15.0498 4.84886 14.8259 4.625 14.5498 4.625H11.5498C11.2737 4.625 11.0498 4.84886 11.0498 5.125V6.125H15.0498ZM9.0498 6.125H5.0498V5.125C5.0498 4.84886 5.27366 4.625 5.5498 4.625H8.5498C8.82595 4.625 9.0498 4.84886 9.0498 5.125V6.125ZM18.0498 7.625V8.125H2.0498V7.625C2.0498 7.34886 2.27366 7.125 2.5498 7.125H17.5498C17.8259 7.125 18.0498 7.34886 18.0498 7.625ZM1.0498 9.125V14.625H19.0498V9.125H1.0498Z"
                                fill="white" fill-opacity="0.6"></path>
                        </svg>

                        @if ($property?->bed?->name == 'Studio')
                            <span>Studio</span>
                        @else
                            <span>{{ numbers_only($property?->bed?->name) }}</span>
                        @endif
                    </strong>
                    <strong class="data-item flex items-center space-x-2"><svg width="15" height="12"
                            viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M3.8269 1.98438C3.8269 1.70823 4.05076 1.48438 4.3269 1.48438H4.55418C4.83032 1.48438 5.05418 1.70823 5.05418 1.98438V2.09801H6.05418V1.98438C6.05418 1.15595 5.3826 0.484375 4.55418 0.484375H4.3269C3.49848 0.484375 2.8269 1.15595 2.8269 1.98438V3.91895H1.59961C0.771182 3.91895 0.0996094 4.59052 0.0996094 5.41895V6.60636V7.41895V7.51545C0.0996094 11.3814 3.23362 14.5154 7.09961 14.5154H10.9178C14.7838 14.5154 17.9178 11.3814 17.9178 7.51545V7.41895H17.9496V5.41895C17.9496 4.59052 17.278 3.91895 16.4496 3.91895H3.8269V1.98438ZM16.9496 5.41895V5.60685C16.939 5.60652 16.9284 5.60636 16.9178 5.60636H1.09961V5.41895C1.09961 5.1428 1.32347 4.91895 1.59961 4.91895H16.4496C16.7258 4.91895 16.9496 5.1428 16.9496 5.41895ZM1.09961 7.41895V6.60636H16.9178V7.51545C16.9178 10.8292 14.2315 13.5154 10.9178 13.5154H7.09961C3.7859 13.5154 1.09961 10.8292 1.09961 7.51545V7.41895Z"
                                fill="white" fill-opacity="0.6"></path>
                        </svg>
                        <span>{{ numbers_only($property?->bath?->name) }}</span>
                    </strong>
                    <strong class="data-item flex items-center space-x-2"><svg width="11" height="12"
                            viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.5 16V1.70711L14.7929 16H0.5Z" stroke="white" stroke-opacity="0.6"></path>
                            <path d="M4 12.5V10.2071L6.29289 12.5H4Z" stroke="white" stroke-opacity="0.6"></path>
                        </svg>
                        <span>{{ $property->built_up_area }} SQ.FT</span>
                    </strong>
                </div>
            </div>
        </div>
        <div class="mb-3 py-6">
            <h2 class="mb-6 font-mont text-base font-extralight uppercase text-white">
                description
            </h2>
            <p class="text-[10px] font-extralight text-white opacity-70">
                {{-- {!! nl2br(e(substr(strip_tags($property?->description), 0, 1000))) !!} {{strlen($property?->description) > 1000?'...':''}} --}}
                {!! e(mb_substr(strip_tags(str_replace(["\r\n", "\r", "\n"], ' ', $property?->description)), 0, 1000)) !!}{{ strlen($property?->description) > 1000 ? '...' : '' }}

            </p>
        </div>
        <div>
            <h2 class="mb-6 font-mont text-base font-extralight uppercase text-white">
                features &amp; amenities
            </h2>
            <div class="mb-12 grid border-t-[1px] border-l-[1px] border-borderMain laptop:grid-cols-3">
                @foreach ($property?->amenities->slice(0,6) as $amenities)
                    @if ($amenities?->amenity != null)
                        <div
                            class="features-item flex min-h-[59px] items-center space-x-2 whitespace-pre-wrap border-b-[1px] border-r-[1px] border-borderMain px-8 text-xl font-light text-white">
                            <img src="{{ asset('preview/images/done.svg') }}"
                                alt="{{ $amenities?->amenity?->title }}" class="-mt-1 w-6" />
                            <span>{{ $amenities?->amenity?->title }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        @php
            $agentImage = $property->agent?->pp_enabled == 1 ? $property->agent?->pp_photo : $property->agent?->image_url;
            $agentImage = $agentImage != null ? $agentImage : $property->agent?->image_url;
        @endphp

        <div class="relative pb-[41.71px]">
            <div class="relative z-[1] flex items-center">
                <div class="h-[60px] w-[60px]">
                    <div class="print-agent-image h-full w-full bg-cover bg-top bg-no-repeat"
                        style="background-image: url('{{ imageToBase64($agentImage) }}')"></div>
                </div>
                <div class="pl-4 -mt-3">
                    <div class="uppercase text-white">
                        <h2 class="font-mont text-[12px]">
                            {{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}
                        </h2>
                        <p class="mt-[4px] text-[8px] font-extralight">
                            {{ $property->agent?->position ?? '' }}
                        </p>
                    </div>
                    <div class="contact-block mt-2 flex text-white">
                        <div class="relative flex">
                            <div class="contact-item flex items-center">
                                <svg width="10" height="8" viewBox="0 0 22 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M19.9864 13.8141C19.4929 13.5749 17.0734 12.4222 16.623 12.2617C16.1725 12.1027 15.8446 12.0241 15.5151 12.5025C15.1872 12.9777 14.2449 14.0533 13.9584 14.3711C13.6703 14.6906 13.3838 14.7291 12.8919 14.4915C12.4001 14.2507 10.8136 13.7483 8.93403 12.1236C7.47176 10.8586 6.48311 9.29656 6.19662 8.81816C5.91012 8.34136 6.16515 8.0829 6.4119 7.8453C6.63381 7.63179 6.90374 7.28824 7.15049 7.01051C7.39723 6.73117 7.47838 6.53211 7.64233 6.21264C7.80793 5.89478 7.72513 5.61705 7.60093 5.37785C7.47838 5.13865 6.4947 2.78999 6.08401 1.83479C5.6849 0.905283 5.27918 1.03211 4.97778 1.01605C4.68963 1.00321 4.36174 1 4.03384 1C3.70595 1 3.17271 1.1188 2.72227 1.5972C2.27017 2.07399 1 3.22825 1 5.57691C1 7.92396 2.76201 10.1924 3.00876 10.5118C3.25551 10.8297 6.47814 15.649 11.4148 17.7151C12.5905 18.2064 13.5063 18.5001 14.2201 18.7185C15.3992 19.0829 16.4723 19.0315 17.3202 18.9079C18.2641 18.7715 20.2315 17.7536 20.6421 16.6395C21.0512 15.5254 21.0512 14.5702 20.9286 14.3711C20.8061 14.1721 20.4799 14.0533 19.9864 13.8141Z"
                                        stroke="#D3A188"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M19.9864 13.8141C19.4929 13.5749 17.0734 12.4222 16.623 12.2617C16.1725 12.1027 15.8446 12.0241 15.5151 12.5025C15.1872 12.9777 14.2449 14.0533 13.9584 14.3711C13.6703 14.6906 13.3838 14.7291 12.8919 14.4915C12.4001 14.2507 10.8136 13.7483 8.93403 12.1236C7.47176 10.8586 6.48311 9.29656 6.19662 8.81816C5.91012 8.34136 6.16515 8.0829 6.4119 7.8453C6.63381 7.63179 6.90374 7.28824 7.15049 7.01051C7.39723 6.73117 7.47838 6.53211 7.64233 6.21264C7.80793 5.89478 7.72513 5.61705 7.60093 5.37785C7.47838 5.13865 6.4947 2.78999 6.08401 1.83479C5.6849 0.905283 5.27918 1.03211 4.97778 1.01605C4.68963 1.00321 4.36174 1 4.03384 1C3.70595 1 3.17271 1.1188 2.72227 1.5972C2.27017 2.07399 1 3.22825 1 5.57691C1 7.92396 2.76201 10.1924 3.00876 10.5118C3.25551 10.8297 6.47814 15.649 11.4148 17.7151C12.5905 18.2064 13.5063 18.5001 14.2201 18.7185C15.3992 19.0829 16.4723 19.0315 17.3202 18.9079C18.2641 18.7715 20.2315 17.7536 20.6421 16.6395C21.0512 15.5254 21.0512 14.5702 20.9286 14.3711C20.8061 14.1721 20.4799 14.0533 19.9864 13.8141Z"
                                        stroke="url(#paint0_linear_2087_11745)"></path>
                                    <defs>
                                        <linearGradient id="paint0_linear_2087_11745" x1="1.05777" y1="-1.1894"
                                            x2="20.9526" y2="-1.6104" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span
                                    class="ml-[4px] text-[8px] font-light leading-[1]">+{{ $property->agent?->mobile }}</span>
                            </div>
                            <div class="contact-item ml-6 flex items-center">
                                <svg width="8" height="8" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 8.66667V20H23V8.66667M1 3V5.83333L12 13L23 5.83333V3H1Z"
                                        stroke="url(#paint0_linear_1_5)"></path>
                                    <defs>
                                        <linearGradient id="paint0_linear_1_5" x1="0.153244" y1="3.36868"
                                            x2="22.7546" y2="22.5399" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span
                                    class="ml-[4px] text-[8px] font-light leading-[1]">{{ $property->agent?->email }}</span>
                            </div>
                            <div class="contact-item-wp ml-6 flex items-center">
                                <svg width="8" height="9" viewBox="0 0 24 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_45_5852)">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.1202 14.8495C15.8831 14.731 14.721 14.1598 14.5046 14.0803C14.2882 14.0015 14.1307 13.9625 13.9724 14.1996C13.8149 14.435 13.3623 14.968 13.2247 15.1255C13.0863 15.2838 12.9486 15.3029 12.7124 15.1852C12.4761 15.0659 11.7141 14.8169 10.8112 14.0119C10.1088 13.385 9.63388 12.611 9.49627 12.374C9.35865 12.1377 9.48115 12.0096 9.59968 11.8919C9.70627 11.7861 9.83593 11.6159 9.95446 11.4783C10.073 11.3399 10.112 11.2412 10.1907 11.0829C10.2703 10.9254 10.2305 10.7878 10.1708 10.6693C10.112 10.5507 9.63945 9.38696 9.44217 8.91365C9.25046 8.45307 9.05557 8.51591 8.9108 8.50795C8.77238 8.50159 8.61488 8.5 8.45737 8.5C8.29987 8.5 8.04373 8.55887 7.82736 8.79592C7.61019 9.03217 7.00006 9.60412 7.00006 10.7679C7.00006 11.9309 7.84645 13.0549 7.96497 13.2132C8.0835 13.3707 9.6315 15.7587 12.0028 16.7825C12.5676 17.0259 13.0075 17.1715 13.3503 17.2797C13.9167 17.4602 14.4322 17.4348 14.8395 17.3735C15.2929 17.3059 16.2379 16.8016 16.4352 16.2495C16.6317 15.6975 16.6317 15.2242 16.5728 15.1255C16.514 15.0269 16.3573 14.968 16.1202 14.8495Z"
                                            fill="black"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.1202 14.8495C15.8831 14.731 14.721 14.1598 14.5046 14.0803C14.2882 14.0015 14.1307 13.9625 13.9724 14.1996C13.8149 14.435 13.3623 14.968 13.2247 15.1255C13.0863 15.2838 12.9486 15.3029 12.7124 15.1852C12.4761 15.0659 11.7141 14.8169 10.8112 14.0119C10.1088 13.385 9.63388 12.611 9.49627 12.374C9.35865 12.1377 9.48115 12.0096 9.59968 11.8919C9.70627 11.7861 9.83593 11.6159 9.95446 11.4783C10.073 11.3399 10.112 11.2412 10.1907 11.0829C10.2703 10.9254 10.2305 10.7878 10.1708 10.6693C10.112 10.5507 9.63945 9.38696 9.44217 8.91365C9.25046 8.45307 9.05557 8.51591 8.9108 8.50795C8.77238 8.50159 8.61488 8.5 8.45737 8.5C8.29987 8.5 8.04373 8.55887 7.82736 8.79592C7.61019 9.03217 7.00006 9.60412 7.00006 10.7679C7.00006 11.9309 7.84645 13.0549 7.96497 13.2132C8.0835 13.3707 9.6315 15.7587 12.0028 16.7825C12.5676 17.0259 13.0075 17.1715 13.3503 17.2797C13.9167 17.4602 14.4322 17.4348 14.8395 17.3735C15.2929 17.3059 16.2379 16.8016 16.4352 16.2495C16.6317 15.6975 16.6317 15.2242 16.5728 15.1255C16.514 15.0269 16.3573 14.968 16.1202 14.8495Z"
                                            fill="url(#paint0_linear_45_5852)"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M16.1202 14.8495C15.8831 14.731 14.721 14.1598 14.5046 14.0803C14.2882 14.0015 14.1307 13.9625 13.9724 14.1996C13.8149 14.435 13.3623 14.968 13.2247 15.1255C13.0863 15.2838 12.9486 15.3029 12.7124 15.1852C12.4761 15.0659 11.7141 14.8169 10.8112 14.0119C10.1088 13.385 9.63388 12.611 9.49627 12.374C9.35865 12.1377 9.48115 12.0096 9.59968 11.8919C9.70627 11.7861 9.83593 11.6159 9.95446 11.4783C10.073 11.3399 10.112 11.2412 10.1907 11.0829C10.2703 10.9254 10.2305 10.7878 10.1708 10.6693C10.112 10.5507 9.63945 9.38696 9.44217 8.91365C9.25046 8.45307 9.05557 8.51591 8.9108 8.50795C8.77238 8.50159 8.61488 8.5 8.45737 8.5C8.29987 8.5 8.04373 8.55887 7.82736 8.79592C7.61019 9.03217 7.00006 9.60412 7.00006 10.7679C7.00006 11.9309 7.84645 13.0549 7.96497 13.2132C8.0835 13.3707 9.6315 15.7587 12.0028 16.7825C12.5676 17.0259 13.0075 17.1715 13.3503 17.2797C13.9167 17.4602 14.4322 17.4348 14.8395 17.3735C15.2929 17.3059 16.2379 16.8016 16.4352 16.2495C16.6317 15.6975 16.6317 15.2242 16.5728 15.1255C16.514 15.0269 16.3573 14.968 16.1202 14.8495Z"
                                            fill="url(#paint1_linear_45_5852)"></path>
                                        <path
                                            d="M20.5043 7.35613C17.5126 2.49984 11.4044 1.05608 6.66747 4.07485C2.05518 7.09362 0.559298 13.6562 3.55105 18.5125L3.80037 18.9062L2.80311 22.8437L6.54281 21.7937L6.91678 22.0562C8.53731 22.975 10.2825 23.5 12.0277 23.5C13.8975 23.5 15.7674 22.975 17.3879 21.925C22.0002 18.775 23.3714 12.3437 20.5043 7.35613Z"
                                            stroke="url(#paint2_linear_45_5852)" stroke-width="1.2"></path>
                                    </g>
                                    <defs>
                                        <linearGradient id="paint0_linear_45_5852" x1="2.86042" y1="3.40892"
                                            x2="24.087" y2="17.0673" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#A15C40"></stop>
                                            <stop offset="0.488334" stop-color="#E28F68"></stop>
                                            <stop offset="0.872347" stop-color="#A15C40"></stop>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_45_5852" x1="1.53534" y1="3.82302"
                                            x2="25.0803" y2="19.1816" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                        <linearGradient id="paint2_linear_45_5852" x1="1.23028" y1="2.95543"
                                            x2="26.6535" y2="18.8256" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                        <clipPath id="clip0_45_5852">
                                            <rect width="24" height="24" fill="white"
                                                transform="translate(6.10352e-05 0.5)"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <span
                                    class="ml-[4px] text-[8px] font-light leading-[1]">+{{ numbers_only($property->agent?->mobile) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="instagram-post hidden">
        {{-- <div class="instagram-post__item sold">
            <div class="h-[1080px] w-[1080px] bg-cover bg-center bg-no-repeat p-[60px]"
                style="
            background-image: url('{{ imageToBase64($property?->photos[0]->original) }}');">
                <div class="relative h-full w-full">
                    <div
                        class="pointer-events-none absolute right-0 top-[-24px] h-[calc(100%+24px)] w-full overflow-hidden before:absolute before:left-[-181px] before:top-[22px] before:block before:h-[3px] before:w-full before:bg-instaGradient after:absolute after:right-0 after:top-[107px] after:block after:h-full after:w-[3px] after:bg-instaGradient">
                        <img src="{{ asset('new_preview/images/inst_logo.svg') }}" alt="logo"
                            class="ml-auto h-[77px] w-[151px]" />
                    </div>
                    <div
                        class="absolute left-0 top-0 ml-[-30px] flex h-[calc(100%+40px)] w-[calc(100%+30px)] overflow-hidden font-cont">
                        <div class="flex w-full">
                            <div
                                class="flex w-full flex-col before:ml-[30px] before:inline-block before:h-full before:w-[3px] before:bg-instaGradientReverse">
                                <div class="inst-post-text">
                                    <p class="text-[160px] font-bold leading-[148px]">
                                        <span class="block"> JUST<br />SOLD </span>
                                    </p>
                                    <div
                                        class="flex after:mb-[40px] after:mt-auto after:inline-block after:h-[3px] after:w-full after:bg-instaGradient">
                                        <p class="whitespace-nowrap pr-[30px] text-[64px] font-light">
                                            <span class="block">
                                                {{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="instagram-post__item sale">
            <div class="h-[1080px] w-[1080px] bg-cover bg-center bg-no-repeat p-[60px]"
                style="background-image: url('{{ imageToBase64($property?->photos[0]->original) }}')">
                <div class="relative h-full w-full">
                    <div
                        class="pointer-events-none absolute right-0 top-[-24px] h-[calc(100%+24px)] w-full overflow-hidden before:absolute before:left-[-181px] before:top-[22px] before:block before:h-[3px] before:w-full before:bg-instaGradient after:absolute after:right-0 after:top-[107px] after:block after:h-full after:w-[3px] after:bg-instaGradient">
                        <img src="{{ asset('new_preview/images/inst_logo.svg') }}" alt="logo"
                            class="ml-auto h-[77px] w-[151px]" />
                    </div>
                    <div
                        class="absolute left-0 top-0 ml-[-30px] flex h-[calc(100%+40px)] w-[calc(100%+30px)] overflow-hidden font-cont">
                        <div class="flex w-full">
                            <div
                                class="flex w-full flex-col before:ml-[30px] before:inline-block before:h-full before:w-[3px] before:bg-instaGradientReverse">
                                <div class="inst-post-text">
                                    <p class="text-[160px] font-bold leading-[148px]">
                                        <span class="block"> <span class="text_first">FOR</span> <br /> <span class="text_last">SALE</span> </span>
                                    </p>
                                    <div
                                        class="flex after:mb-[40px] after:mt-auto after:inline-block after:h-[3px] after:w-full after:bg-instaGradient">
                                        <p class="whitespace-nowrap pr-[30px] text-[64px] font-light">
                                            <span class="block">
                                                {{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex h-full w-full items-end justify-end">
                        <div class="p-16">
                            <div class="flex items-center border-[1px] border-white bg-black50 p-6 agentSection">
                                <div class="print-agent-image h-[72px] w-[72px] bg-cover bg-top bg-no-repeat"
                                    style="background-image: url('{{ imageToBase64($agentImage) }}')"></div>
                                <div class="inst-post-agent ml-6 text-white">
                                    <p class="font-cont text-3xl font-bold">
                                        {{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}
                                    </p>
                                    <p class="font-cormorantInfant text-3xl font-medium">
                                        +{{ numbers_only($property->agent?->mobile) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-6 flex h-[106px] items-center border-[1px] border-white bg-black50 p-6 priceSection">
                                <p class="inst-post-price font-cormorant text-6xl font-bold">
                                    AED {{ number_format($property->price?->price) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="instagram-post__item rented">
            <div class="h-[1080px] w-[1080px] bg-cover bg-center bg-no-repeat p-[60px]"
                style="background-image: url('{{ imageToBase64($property?->photos[0]->original) }}')">
                <div class="relative h-full w-full">
                    <div
                        class="pointer-events-none absolute right-0 top-[-24px] h-[calc(100%+24px)] w-full overflow-hidden before:absolute before:left-[-181px] before:top-[22px] before:block before:h-[3px] before:w-full before:bg-instaGradient after:absolute after:right-0 after:top-[107px] after:block after:h-full after:w-[3px] after:bg-instaGradient">
                        <img src="{{ asset('new_preview/images/inst_logo.svg') }}" alt="logo"
                            class="ml-auto h-[77px] w-[151px]" />
                    </div>
                    <div
                        class="absolute left-0 top-0 ml-[-30px] flex h-[calc(100%+40px)] w-[calc(100%+30px)] overflow-hidden font-cont">
                        <div class="flex w-full">
                            <div
                                class="flex w-full flex-col before:ml-[30px] before:inline-block before:h-full before:w-[3px] before:bg-instaGradientReverse">
                                <div class="inst-post-text">
                                    <p class="text-[160px] font-bold leading-[148px]">
                                        <span class="block"> JUST<br />RENTED </span>
                                    </p>
                                    <div
                                        class="flex after:mb-[40px] after:mt-auto after:inline-block after:h-[3px] after:w-full after:bg-instaGradient">
                                        <p class="whitespace-nowrap pr-[30px] text-[64px] font-light">
                                            <span class="block">
                                                {{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="instagram-post__item rent">
            <div class="h-[1080px] w-[1080px] bg-cover bg-center bg-no-repeat p-[60px]"
                style="background-image: url('{{ imageToBase64($property?->photos[0]->original) }}')">
                <div class="relative h-full w-full">
                    <div
                        class="pointer-events-none absolute right-0 top-[-24px] h-[calc(100%+24px)] w-full overflow-hidden before:absolute before:left-[-181px] before:top-[22px] before:block before:h-[3px] before:w-full before:bg-instaGradient after:absolute after:right-0 after:top-[107px] after:block after:h-full after:w-[3px] after:bg-instaGradient">
                        <img src="{{ asset('new_preview/images/inst_logo.svg') }}" alt="logo"
                            class="ml-auto h-[77px] w-[151px]" />
                    </div>
                    <div
                        class="absolute left-0 top-0 ml-[-30px] flex h-[calc(100%+40px)] w-[calc(100%+30px)] overflow-hidden font-cont">
                        <div class="flex w-full">
                            <div
                                class="flex w-full flex-col before:ml-[30px] before:inline-block before:h-full before:w-[3px] before:bg-instaGradientReverse">
                                <div class="inst-post-text">
                                    <p class="text-[160px] font-bold leading-[148px]">
                                        <span class="block"> FOR<br />RENT </span>
                                    </p>
                                    <div
                                        class="flex after:mb-[40px] after:mt-auto after:inline-block after:h-[3px] after:w-full after:bg-instaGradient">
                                        <p class="whitespace-nowrap pr-[30px] text-[64px] font-light">
                                            <span class="block">
                                                {{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex h-full w-full items-end justify-end">
                        <div class="p-16">
                            <div class="flex items-center border-[1px] border-white bg-black50 p-6">
                                <div class="print-agent-image h-[72px] w-[72px] bg-cover bg-top bg-no-repeat"
                                    style="background-image: url('{{ imageToBase64($agentImage) }}')"></div>
                                <div class="inst-post-agent ml-6 text-white">
                                    <p class="font-cont text-3xl font-bold">
                                        {{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}
                                    </p>
                                    <p class="font-cormorantInfant text-3xl font-medium">
                                        +{{ numbers_only($property->agent?->mobile) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-6 flex h-[106px] items-center border-[1px] border-white bg-black50 p-6">
                                <p class="inst-post-price font-cormorant text-6xl font-bold">
                                    AED {{ number_format($property->price?->price) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="pb-24 pt-32">
            <span class="text-white opacity-60">AX CAPITAL ©
                <script>
                    document.write(new Date().getFullYear())
                </script> All Rights Reserved
            </span>
        </div>
    </div>

    {{-- @include('admin.listing.pdf_component.modal') --}}

    <script>
        let property_code = "{{ $property?->ref_no }}";
        let longitude = "{{ $property?->communityProperty?->longitude ?? $property?->subCommunity?->longitude }}";
        let latitude = "{{ $property?->communityProperty?->latitude ?? $property?->subCommunity?->latitude }}";
        let mapLicense =
            "pk.eyJ1Ijoic2FpbWF4Y2FwaXRhbCIsImEiOiJjbGZ0ZXM3a3gwMDhrM2VyM3FvZmdxdmx3In0.nFc4SGgYByc3beoXFp9a7A";
    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="{{ asset('new_preview/vendors/swiper/swiper.min.js') }}"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.js"></script>
    <script src="{{ asset('new_preview/vendors/jspdf/jspdf.min.js') }}"></script>

    <script src="{{ asset('new_preview/vendors/html2canvas/html2canvas.min.js') }}"></script>
    <script src="{{ asset('new_preview/scripts/main.js') }}"></script>

    <script>
        $(document).ready(function() {
            const urlParamsCheck = new URLSearchParams(window.location.search);
            console.log(urlParamsCheck);
            // check if urlParamsCheck have show_price and show_profile first_text and last text if yes show alert
            if (urlParamsCheck.has('show_price') && urlParamsCheck.has('show_profile') && urlParamsCheck.has('first_text') && urlParamsCheck.has('second_text')) {
                generateInstagramPostsDynamic();
            }


            $('#gen').click(function() {
                generateInstagramPostsDynamic();
            })
        });


        const generateSinglePostDynamic = (el, title) => {
            html2canvas(document.querySelector(el), {
                allowTaint: false,
                useCORS: true,
            }).then(function(canvas) {
                canvas.getContext("2d");

                const image = canvas.toDataURL("image/png", 1.0);
                image.crossOrigin = "anonymous";

                const link = document.createElement("a");
                link.setAttribute("id", "instagram-post-link");
                link.href = canvas.toDataURL();
                link.download = title;
                link.click();
                document.getElementById("instagram-post-link") &&
                    document.getElementById("instagram-post-link").remove();

                // document.body.appendChild(canvas);
            });
        };


        const generateInstagramPostsDynamic = () => {
                const loading_element = document.querySelector(".loading");
                loading_element.style.display = "flex";
                document.body.style.overflow = "hidden";

                const urlParams = new URLSearchParams(window.location.search);
                const showPriceParam = urlParams.get('show_price');
                const showProfileParam = urlParams.get('show_profile');
                const first_text = urlParams.get('first_text');
                const second_text = urlParams.get('second_text');


                const element = document.querySelector(".instagram-post");
                element.style.display = "block";

                // if #price select has yes selected then this
                if (showPriceParam === 'yes') {
                    document.querySelectorAll(".inst-post-price").forEach((el) => {
                        el.style.marginTop = "-50px";
                    });
                }else{
                    document.querySelectorAll(".priceSection").forEach((el) => {
                        el.style.display = "none";
                    });
                }

                if (showProfileParam === 'yes') {
                    document.querySelectorAll(".inst-post-agent").forEach((el) => {
                        el.style.marginTop = "-24px";
                    });
                }else{
                    document.querySelectorAll(".agentSection").forEach((el) => {
                        el.style.display = "none";
                    });
                }

                // set .text_first and .text_last as #title_first and #title_last values

                document.querySelectorAll(".text_first").forEach((el) => {
                    // el.innerHTML = document.getElementById('title_first').value;
                    el.innerHTML = first_text;
                });

                document.querySelectorAll(".text_last").forEach((el) => {

                    el.innerHTML = second_text;
                    // el.innerHTML = document.getElementById('title_last').value;
                });



                    document.querySelectorAll(".inst-post-text").forEach((el) => {
                        el.classList.add("position");
                    });
                    setTimeout(() => {
                        generateSinglePostDynamic(".instagram-post__item.sale > div", "for_sale.png");
                        element.removeAttribute("style");
                        loading_element.removeAttribute("style");
                        document.body.removeAttribute("style");
                    }, 400);
                };
    </script>

</body>

</html>

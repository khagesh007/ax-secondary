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
    <!-- <link href="./vendors/swiper/swiper.min.css" rel="stylesheet" /> -->
    <link href="{{ asset('new_preview2/vendors/swiper/swiper.min.css') }}" rel="stylesheet" />
    <!-- <link href="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.css" rel="stylesheet" /> -->
    <link href="{{ asset('new_preview2/styles/output.css') }}?v=213" rel="stylesheet" />
</head>

<body>

    <div class="print-page container bg-black laptop:px-8" style="max-width: 595px">
        <div
            class="relative -mx-[200px] after:absolute after:bottom-0 after:h-[1px] after:w-full after:bg-selectGradient">
            <div class="flex items-center justify-center py-8">
                <a href="/">
                    <img src="{{ asset('new_preview2/images/logo.svg') }}" alt="logo"
                        class="h-[24px] w-[88.85px]" />
                </a>
            </div>
        </div>

        <div class="gallery-wrapper pt-8">
            <div class="gallery pdf">

                @if (isset($images))
                    <div class="image">
                        <picture
                            class="relative  cursor-pointer >
                            <source srcset="{{ $images[0]?->original }}"
                            type="image/jpeg" />
                        <img src="{{ $images[0]?->thumbnail }}" alt="slider-image" loading="lazy" />
                        </picture>
                    </div>

                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ $images[1]?->original ?? $images[0]?->original }}" type="image/jpeg" />
                            <img src="{{ $images[2]?->thumbnail ?? $images[0]?->thumbnail }}" alt="slider-image"
                                loading="lazy" />
                        </picture>
                    </div>
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ $images[2]?->original ?? $images[0]?->original }}" type="image/jpeg" />
                            <img src="{{ $images[2]?->thumbnail ?? $images[0]?->thumbnail }}" alt="slider-image"
                                loading="lazy" />
                        </picture>
                    </div>
                @else
                    {{-- <div class="grid-cols-8"> --}}


                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ $property?->photos[0]?->original }}" type="image/jpeg" />
                            <img src="{{ $property?->photos[0]?->thumbnail ?? $property?->photos[0]?->thumbnail }}"
                                alt="slider-image" loading="lazy" />
                        </picture>
                    </div>
                    {{-- </div> --}}
                    {{-- <div class="grid-cols-4"> --}}
                    {{-- <div class="grid-cols-12"> --}}
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ $property?->photos[1]?->original ?? $property?->photos[0]?->original }}"
                                type="image/jpeg" />
                            <img src="{{ $property?->photos[1]?->path ?? $property?->photos[0]?->thumbnail }}"
                                alt="slider-image" loading="lazy" />
                        </picture>
                    </div>
                    {{-- </div> --}}
                    {{-- <div class="grid-cols-12"> --}}
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source srcset="{{ $property?->photos[2]?->original ?? $property?->photos[0]?->original }}"
                                type="image/jpeg" />
                            <img src="{{ $property?->photos[2]?->path ?? $property?->photos[0]?->thumbnail }}"
                                alt="slider-image" loading="lazy" />
                        </picture>
                    </div>
                    {{-- </div> --}}
                    {{-- </div> --}}
                @endif
            </div>
        </div>
        <h1 class="my-6 text-start font-mont text-base font-medium normal-case leading-[1.3]">
            {{ $property->title }},
            ({{ $property->ref_no }})
        </h1>
        <div class="mb-6 flex flex-col items-center justify-between text-white xlTablet:flex-row">
            {{-- @if ($show_price == true) --}}
            <div class="flex items-center" @if ($show_price == true) @else style="visibility: hidden" @endif>
                <strong class="font-mont text-2xl font-medium">AED
                    {{ number_format($property->price?->price) }}</strong>
            </div>
            {{-- @endif --}}

            <div>
                <span class="font-mont text-[10px] text-white60">{{ strtoupper($property->for) }}</span>
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
                        <span>{{ $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}</span></strong>
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
                {!! nl2br(e(strip_tags($property?->description))) !!}

                {{-- {!! e(mb_substr(strip_tags(str_replace(["\r\n", "\r", "\n"], ' ', $property?->description)), 0, 1000)) !!}{{ strlen($property?->description) > 1000 ? '...' : '' }} --}}
            </p>
        </div>
        <div>
            <h2 class="mb-6 font-mont text-base font-extralight uppercase text-white">
                features &amp; amenities
            </h2>

            <div class="mb-12 grid border-t-[1px] border-l-[1px] border-borderMain laptop:grid-cols-3">

                @if ($amenities)
                    @php
                        $amenities = explode(',', $amenities);
                    @endphp
                    @foreach ($amenities as $amenity)
                        <div
                            class="features-item flex min-h-[31px] items-center space-x-2 whitespace-pre-wrap border-b-[1px] border-r-[1px] border-borderMain px-2 text-[10px] font-light text-white">
                            <img src="{{ asset('new_preview2/images/done.svg') }}" alt="{{ $amenity }}"
                                class="w-3" />
                            <span>{{ $amenity }}</span>
                        </div>
                    @endforeach
                @else
                    @foreach ($property?->amenities as $amenities)
                        @if ($amenities?->amenity != null)
                            <div
                                class="features-item flex min-h-[31px] items-center space-x-2 whitespace-pre-wrap border-b-[1px] border-r-[1px] border-borderMain px-2 text-[10px] font-light text-white">
                                <img src="{{ asset('new_preview2/images/done.svg') }}"
                                    alt="{{ $amenities?->amenity?->title }}" class="w-3" />
                                <span>{{ $amenities?->amenity?->title }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif

            </div>
        </div>

        @php
            if ($profile_user == 'agent') {
                $agentImage = $property->agent?->pp_enabled == 1 ? $property->agent?->pp_photo : $property->agent?->image_url;
                $agentImage = $agentImage != null ? $agentImage : $property->agent?->image_url;
            } else {
                $agentImage = auth()->user()?->image_url;
                $agentImage = $agentImage != null ? $agentImage : auth()->user()?->image_url;
            }
        @endphp

        <div class="relative pb-[41.71px] " @if ($show_profile) @else style="visibility: hidden" @endif>
            <div class="relative z-[1] flex items-center">
                <div class="h-[60px] w-[60px]">
                    <div class="print-agent-image h-full w-full bg-cover bg-top bg-no-repeat"
                        style="background-image: url('{{ $agentImage }}')"></div>
                </div>
                <div class="pl-4 -mt-3">
                    <div class="uppercase text-white">
                        <h2 class="font-mont text-[12px]">
                            @if ($profile_user == 'agent')
                                {{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}
                            @else
                                {{ auth()->user()?->name }}
                            @endif
                        </h2>
                        <p class="mt-[4px] text-[8px] font-extralight">
                            @if ($profile_user == 'agent')
                                {{ $property->agent?->position ?? '' }}
                            @else
                                {{ auth()->user()?->position ?? '' }}
                            @endif

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
                                <span class="ml-[4px] text-[8px] font-light leading-[1]">
                                    @if ($profile_user == 'agent')
                                        +{{ numbers_only($property->agent?->mobile) ?? '' }}
                                    @else
                                        +{{ numbers_only(auth()->user()?->mobile) ?? '' }}
                                    @endif
                                </span>
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
                                <span class="ml-[4px] text-[8px] font-light leading-[1]">

                                    @if ($profile_user == 'agent')
                                        {{ $property->agent?->email ?? '' }}
                                    @else
                                        {{ auth()->user()?->email ?? '' }}
                                    @endif
                                </span>
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
                                <span class="ml-[4px] text-[8px] font-light leading-[1]">
                                    @if ($profile_user == 'agent')
                                        +{{ numbers_only($property->agent?->mobile) }}
                                    @else
                                        +{{ numbers_only(auth()->user()?->mobile) }}
                                    @endif
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
                @if ($property?->qr_link)
                <div class="ml-auto">
                    <div class="h-[60px] w-[60px]">
                        <div class="print-agent-image h-full w-full bg-cover bg-top bg-no-repeat"
                            style="background-image: url('{{ $property->qr_link }}')"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>



    <script src="{{ asset('new_preview2/vendors/swiper/swiper.min.js') }}"></script>
    <!-- <script src="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.js"></script> -->
    <script src="{{ asset('new_preview2/vendors/jspdf/jspdf.min.js') }}"></script>
    <script src="{{ asset('new_preview2/vendors/html2canvas/html2canvas.min.js') }}"></script>
    <script src="{{ asset('new_preview2/scripts/main_without_map.js') }}"></script>
</body>

</html>

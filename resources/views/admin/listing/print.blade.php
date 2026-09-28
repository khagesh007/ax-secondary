<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{$property->title}}</title>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    />
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    />
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    />
    <link
        href="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.css"
        rel="stylesheet"
    />
    <link href="{{asset('preview/styles/output.css')}}" rel="stylesheet"/>
</head>
<body>
<div>
    {{-- <button
        class="generate-pdf absolute right-5 top-5 bg-white38 px-7 py-3 uppercase"
    >
        generate pdf
    </button> --}}
    <div
        class="loading fixed top-0 left-0 z-[50] hidden h-full w-full items-center justify-center bg-white60 text-4xl font-semibold uppercase text-black"
    >
        generate pdf...
    </div>
    <div
        class="print-page container bg-black laptop:px-40"
        style="max-width: 1278px"
    >
        <div class="-mx-[2000px] border-[1px] border-b-[#595858]">
            <div class="flex items-center justify-center py-8 tablet:py-12">
                <a href="/">
                    <img
                        src="{{asset('preview/images/logo.svg')}}"
                        alt="logo"
                        class="h-[37px] w-[137px] tablet:h-[47px] tablet:w-[174px]"
                    />
                </a>
            </div>
        </div>
        @if(isset($property->photos) &&  count($property->photos)>0)
            <div class="gallery-wrapper pt-24">
                <div class="gallery">
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source
                                srcset="{{$property?->photos[0]->path}}"
                                type="image/jpeg"
                            />
                            <img
                                src="{{$property?->photos[0]->thumbnail}}"
                                alt="slider-image"
                                loading="lazy"
                            />
                        </picture>
                    </div>
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source
                                srcset="{{$property?->photos[1]->path}}"
                                type="image/jpeg"
                            />
                            <img
                                src="{{$property?->photos[1]->thumbnail}}"
                                alt="slider-image"
                                loading="lazy"
                            />
                        </picture>
                    </div>
                    <div class="image">
                        <picture class="relative w-full cursor-pointer">
                            <source
                                srcset="{{$property?->photos[2]->path}}"
                                type="image/jpeg"
                            />
                            <img
                                src="{{$property?->photos[2]->thumbnail}}"
                                alt="slider-image"
                                loading="lazy"
                            />
                        </picture>
                    </div>
                </div>
            </div>
        @endif
        <h1 class="mt-16 mb-8 text-start text-[32px] normal-case leading-[1.3]">
            {{$property->title}}, {{$property->region}}, UAE
            ({{$property->ref_no}})
        </h1>
        <div
            class="mb-16 flex flex-col items-center justify-between text-white xlTablet:flex-row"
        >
            <div class="flex items-center">
                <strong class="text-[28px] font-light tablet:text-5xl"
                >AED {{$property->price?->price}}</strong
                >
            </div>
            <div>
                <span class="text-white60">{{$property->for}}</span>
            </div>
        </div>
        <div
            class="mb-16 flex flex-wrap justify-between gap-x-9 gap-y-6 border-y-[1px] border-borderMain py-8 uppercase text-white60 tablet:gap-y-4"
        >
            <div
                class="flex flex-wrap items-center tablet:flex-nowrap tablet:space-x-9"
            >
                <div class="flex w-full items-center space-x-9 tablet:w-auto">
                    <strong class="flex items-center space-x-3">
                        <svg
                            width="15"
                            height="18"
                            viewBox="0 0 15 18"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="opacity-60"
                        >
                            <path
                                d="M7.40007 0.5C3.87127 0.5 1.00007 3.3712 1.00007 6.896C0.976865 12.052 7.15687 16.3272 7.40007 16.5C7.40007 16.5 13.8233 12.052 13.8001 6.9C13.8001 3.3712 10.9289 0.5 7.40007 0.5ZM7.40007 10.1C5.63207 10.1 4.20007 8.668 4.20007 6.9C4.20007 5.132 5.63207 3.7 7.40007 3.7C9.16807 3.7 10.6001 5.132 10.6001 6.9C10.6001 8.668 9.16807 10.1 7.40007 10.1Z"
                                stroke="white"
                            ></path>
                        </svg>
                        <span
                            class="capitalize">{{$property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name}}</span></strong
                    >
                    <strong class="flex items-center space-x-3 capitalize">
                        <svg
                            width="21"
                            height="20"
                            viewBox="0 0 21 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <g clip-path="url(#clip0_21_1771)">
                                <path
                                    d="M14.8334 12.5002H16.5V14.1668H14.8334V12.5002ZM14.8334 9.16683H16.5V10.8335H14.8334V9.16683ZM14.8334 5.8335H16.5V7.50016H14.8334V5.8335ZM12.1167 5.8335L13.1667 6.5335V5.8335H12.1167Z"
                                    fill="white"
                                    fill-opacity="0.6"
                                />
                                <path
                                    d="M9 2.5L9 3.09167L10.6667 4.2V3.5H18.8667V9.66667V16.5H14.8333V17.5H19.8333V2.5H9Z"
                                    fill="white"
                                    fill-opacity="0.6"
                                />
                                <path
                                    d="M7.6083 4.75L13.3 8.54167V17.5H1.6333V8.73333L7.6083 4.75ZM8 16.5H12V9L7.6083 6L2.70918 9.3V16.5H6.5V13H8V16.5Z"
                                    fill="white"
                                    fill-opacity="0.6"
                                />
                            </g>
                            <defs>
                                <clipPath id="clip0_21_1771">
                                    <rect
                                        width="20"
                                        height="20"
                                        fill="white"
                                        transform="translate(0.800049)"
                                    />
                                </clipPath>
                            </defs>
                        </svg>
                        <span>{{$property?->propertyCategory?->title}}</span>
                    </strong>
                </div>
                <div class="mt-2 flex items-center space-x-9 tablet:mt-0">
                    <strong class="flex items-center space-x-3">
                        <svg
                            width="21"
                            height="17"
                            viewBox="0 0 21 17"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M4.0498 0.125C3.22138 0.125 2.5498 0.796571 2.5498 1.625V6.125C1.72138 6.125 1.0498 6.79657 1.0498 7.625L1.0498 8.125C0.49752 8.125 0.0498047 8.57271 0.0498047 9.125V14.625C0.0498047 15.1773 0.49752 15.625 1.0498 15.625H1.2998V16.875H2.2998V15.625H17.7498V16.875H18.7498V15.625H19.0498C19.6021 15.625 20.0498 15.1773 20.0498 14.625V9.125C20.0498 8.57271 19.6021 8.125 19.0498 8.125V7.625C19.0498 6.79657 18.3782 6.125 17.5498 6.125V1.625C17.5498 0.796573 16.8782 0.125 16.0498 0.125H4.0498ZM16.5498 6.125V1.625C16.5498 1.34886 16.3259 1.125 16.0498 1.125H4.0498C3.77366 1.125 3.5498 1.34886 3.5498 1.625V6.125H4.0498V5.125C4.0498 4.29657 4.72138 3.625 5.5498 3.625H8.5498C9.37823 3.625 10.0498 4.29657 10.0498 5.125C10.0498 4.29657 10.7214 3.625 11.5498 3.625H14.5498C15.3782 3.625 16.0498 4.29657 16.0498 5.125V6.125H16.5498ZM15.0498 6.125V5.125C15.0498 4.84886 14.8259 4.625 14.5498 4.625H11.5498C11.2737 4.625 11.0498 4.84886 11.0498 5.125V6.125H15.0498ZM9.0498 6.125H5.0498V5.125C5.0498 4.84886 5.27366 4.625 5.5498 4.625H8.5498C8.82595 4.625 9.0498 4.84886 9.0498 5.125V6.125ZM18.0498 7.625V8.125H2.0498V7.625C2.0498 7.34886 2.27366 7.125 2.5498 7.125H17.5498C17.8259 7.125 18.0498 7.34886 18.0498 7.625ZM1.0498 9.125V14.625H19.0498V9.125H1.0498Z"
                                fill="white"
                                fill-opacity="0.6"
                            ></path>
                        </svg>
                        <span>{{ucwords($property?->bed?->name)}}</span>
                    </strong>
                    <strong class="flex items-center space-x-3"
                    >
                        <svg
                            width="18"
                            height="15"
                            viewBox="0 0 18 15"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M3.8269 1.98438C3.8269 1.70823 4.05076 1.48438 4.3269 1.48438H4.55418C4.83032 1.48438 5.05418 1.70823 5.05418 1.98438V2.09801H6.05418V1.98438C6.05418 1.15595 5.3826 0.484375 4.55418 0.484375H4.3269C3.49848 0.484375 2.8269 1.15595 2.8269 1.98438V3.91895H1.59961C0.771182 3.91895 0.0996094 4.59052 0.0996094 5.41895V6.60636V7.41895V7.51545C0.0996094 11.3814 3.23362 14.5154 7.09961 14.5154H10.9178C14.7838 14.5154 17.9178 11.3814 17.9178 7.51545V7.41895H17.9496V5.41895C17.9496 4.59052 17.278 3.91895 16.4496 3.91895H3.8269V1.98438ZM16.9496 5.41895V5.60685C16.939 5.60652 16.9284 5.60636 16.9178 5.60636H1.09961V5.41895C1.09961 5.1428 1.32347 4.91895 1.59961 4.91895H16.4496C16.7258 4.91895 16.9496 5.1428 16.9496 5.41895ZM1.09961 7.41895V6.60636H16.9178V7.51545C16.9178 10.8292 14.2315 13.5154 10.9178 13.5154H7.09961C3.7859 13.5154 1.09961 10.8292 1.09961 7.51545V7.41895Z"
                                fill="white"
                                fill-opacity="0.6"
                            ></path>
                        </svg>
                        <span>{{ucwords($property?->bath?->name)}}</span>
                    </strong>
                    <strong class="flex items-center space-x-3"
                    >
                        <svg
                            width="16"
                            height="17"
                            viewBox="0 0 16 17"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M0.5 16V1.70711L14.7929 16H0.5Z"
                                stroke="white"
                                stroke-opacity="0.6"
                            ></path>
                            <path
                                d="M4 12.5V10.2071L6.29289 12.5H4Z"
                                stroke="white"
                                stroke-opacity="0.6"
                            ></path>
                        </svg>
                        <span>{{ucwords($property->plot_size)}} SQ.FT</span>
                    </strong>
                </div>
            </div>
        </div>
        <h2
            class="mb-10 font-mont text-3xl font-extralight uppercase text-white"
        >
            features &amp; amenities
        </h2>
        <div
            class="mb-16 grid border-t-[1px] border-l-[1px] border-borderMain laptop:grid-cols-3"
        >
            @foreach($property?->amenities as $amenities)
                <div
                    class="flex min-h-[59px] items-center space-x-2 whitespace-pre-wrap border-b-[1px] border-r-[1px] border-borderMain px-8 text-xl font-light text-white"
                >
                    <img src="{{asset('preview/images/done.svg')}}" alt="BALCONY" class="-mt-1 w-6"/>
                    <span>{{$amenities?->amenity?->title}}</span>
                </div>
            @endforeach


        </div>
        <div class="mb-14 border-y-[1px] border-borderMain py-16">
            <h2
                class="mb-8 font-mont text-3xl font-extralight uppercase text-white"
            >
                description
            </h2>
            <p class="mb-16 font-extralight text-white opacity-70">
                {{$property?->description}}
            </p>
            <h4 class="font-mont text-3xl font-extralight">Listing Details</h4>
            <div class="table">
                <ul>
                    <li>
                        <span>Category:</span>
                        <span>{{$property?->propertyCategory?->title}}</span>
                    </li>
                    <li>
                        <span>Beds:</span>
                        <span>{{ucwords($property?->bed?->name)}}</span>
                    </li>
                    <li>
                        <span>Baths:</span>
                        <span>{{ucwords($property?->bath?->name)}}</span>
                    </li>
                    <li>
                        <span>Sub Community:</span>
                        <span>{{ucwords($property?->subCommunity?->name)}}</span>
                    </li>
                    <li>
                        <span>Parking:</span>
                        <span>{{$property->parking}}</span>
                    </li>
                    <li>
                        <span>Exclusive:</span>
                        <span>{{$property->exclusive == 1 ? 'Yes' : 'No'}}</span>
                    </li>
                </ul>
                <ul>
                    <li>
                        <span>Property:</span>
                        <span>{{ucwords($property?->communityProperty?->name)}}</span>
                    </li>
                    <li>
                        <span>Built-up Area:</span>
                        <span>{{$property->built_up_area}}</span>
                    </li>
                    <li>
                        <span>Furnished:</span>
                        <span>{{ucwords($property?->furnished)}}</span>
                    </li>
                    <li>
                        <span>RERA Permit #:</span>
                        <span>{{$property->rera_permit_number}}</span>
                    </li>
                    <li>
                        <span>Status:</span>
                        <span>{{$property?->propertyStatus?->title}}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div>
            <h2
                class="mb-6 font-mont text-3xl font-extralight uppercase text-white"
            >
                Location
            </h2>
            <div id="map" style="width: '100%'; height: 300px"></div>
        </div>

        @if(isset($property->photos) &&  count($property->photos)>0)
        <div
            class="relative mb-8 mt-24 pt-24 before:absolute before:top-0 before:h-[1px] before:w-full before:bg-selectGradient"
        >
            <h2 class="font-mont text-3xl font-extralight uppercase text-white">
                GALLERY
            </h2>
        </div>


        @php $counter = 1; @endphp
            @foreach($property?->photos as $photo)
      @if($counter == '1')
                    <div class="gallery-page mt-[6px] flex flex-wrap gap-[6px]">

                <div class="flex-1">
                    <img
                        src="{{$photo->path}}"
                        alt="gallery-image"
                        class="h-[338px] w-full object-cover"
                    />
                </div>

            @elseif($counter == '2')
            <div class="flex-1">
                <img
                    src="{{$photo->path}}"
                    alt="gallery-image"
                    class="h-[338px] w-full object-cover"
                />
            </div>

                    @elseif($counter == '3')
            <div>
                <img
                    src="{{$photo->path}}"
                    alt="gallery-image"
                    class="h-[570px] w-full object-cover"
                />
            </div>

                @endif
                        @if($counter == 3)
                        </div>
                        @php $counter  = 0; @endphp
                        @else
                        @php $counter++; @endphp
                         @endif


            @endforeach

        <div class="mt-[6px] flex flex-wrap gap-[6px]">
            <div class="flex-1">
                <img
                    src="./images/gallery01.jpg"
                    alt="gallery-image"
                    class="h-[338px] w-full object-cover"
                />
            </div>
            <div class="flex-1">
                <img
                    src="./images/gallery02.jpg"
                    alt="gallery-image"
                    class="h-[338px] w-full object-cover"
                />
            </div>
            <div>
                <img
                    src="./images/gallery03.jpg"
                    alt="gallery-image"
                    class="h-[570px] w-full object-cover"
                />
            </div>
        </div>
    </div>
        @endif

        <div
            class="relative mt-24 pt-24 before:absolute before:top-0 before:h-[1px] before:w-full before:bg-selectGradient"
        >
            <div class="flex border-[1px] border-[#595858] py-8 pl-[18px] pr-8">
                <div class="mr-16 h-[556px] w-[457px]">
                    <img
                        src="{{$property->agent?->image}}"
                        alt="{{$property->agent?->name}}"
                        class="h-full w-full object-cover"
                    />
                </div>
                <div class="flex-grow text-white">
                    <h2 class="font-mont text-[40px]">{{$property->agent?->name}}</h2>
                    <p class="mt-[8px] text-3xl font-extralight uppercase">
                        {{$property->agent?->position ?? ''}}
                    </p>
                    <div
                        class="mt-8 hidden h-[1px] w-full bg-gradient-to-r from-[#D3A188] to-[rgba(211,161,136,0.24)] tablet:block"
                    ></div>
                    <?php /*
                    <div class="mt-8">
                        <div class="text-center tablet:text-left">
                  <span class="mr-[18px] text-xl font-light capitalize text-primary"
                  >experience:</span>
                            <span class="text-xl font-light text-white">18 years</span>
                        </div>
                        <div class="mt-2 text-center tablet:mt-6 tablet:text-left">
                  <span
                      class="mr-[18px] text-xl font-light capitalize text-primary"
                  >Specialization:</span
                  ><span class="text-xl font-light text-white"
                            >Head of Off-plan</span
                            >
                        </div>
                        <div class="mt-2 text-center tablet:mt-6 tablet:text-left">
                  <span
                      class="mr-[18px] text-xl font-light capitalize text-primary"
                  >Language:</span
                  ><span class="text-xl font-light capitalize text-white">en, ar</span>
                        </div>
                    </div>
 */ ?>

                    <div class="mt-16">
                        <p class="text-xl font-light uppercase">CONTACT</p>
                        <div>
                            <div class="mt-8 flex items-center">
                                <svg
                                    width="20"
                                    height="18"
                                    viewBox="0 0 22 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M19.9864 13.8141C19.4929 13.5749 17.0734 12.4222 16.623 12.2617C16.1725 12.1027 15.8446 12.0241 15.5151 12.5025C15.1872 12.9777 14.2449 14.0533 13.9584 14.3711C13.6703 14.6906 13.3838 14.7291 12.8919 14.4915C12.4001 14.2507 10.8136 13.7483 8.93403 12.1236C7.47176 10.8586 6.48311 9.29656 6.19662 8.81816C5.91012 8.34136 6.16515 8.0829 6.4119 7.8453C6.63381 7.63179 6.90374 7.28824 7.15049 7.01051C7.39723 6.73117 7.47838 6.53211 7.64233 6.21264C7.80793 5.89478 7.72513 5.61705 7.60093 5.37785C7.47838 5.13865 6.4947 2.78999 6.08401 1.83479C5.6849 0.905283 5.27918 1.03211 4.97778 1.01605C4.68963 1.00321 4.36174 1 4.03384 1C3.70595 1 3.17271 1.1188 2.72227 1.5972C2.27017 2.07399 1 3.22825 1 5.57691C1 7.92396 2.76201 10.1924 3.00876 10.5118C3.25551 10.8297 6.47814 15.649 11.4148 17.7151C12.5905 18.2064 13.5063 18.5001 14.2201 18.7185C15.3992 19.0829 16.4723 19.0315 17.3202 18.9079C18.2641 18.7715 20.2315 17.7536 20.6421 16.6395C21.0512 15.5254 21.0512 14.5702 20.9286 14.3711C20.8061 14.1721 20.4799 14.0533 19.9864 13.8141Z"
                                        stroke="#D3A188"
                                    ></path>
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M19.9864 13.8141C19.4929 13.5749 17.0734 12.4222 16.623 12.2617C16.1725 12.1027 15.8446 12.0241 15.5151 12.5025C15.1872 12.9777 14.2449 14.0533 13.9584 14.3711C13.6703 14.6906 13.3838 14.7291 12.8919 14.4915C12.4001 14.2507 10.8136 13.7483 8.93403 12.1236C7.47176 10.8586 6.48311 9.29656 6.19662 8.81816C5.91012 8.34136 6.16515 8.0829 6.4119 7.8453C6.63381 7.63179 6.90374 7.28824 7.15049 7.01051C7.39723 6.73117 7.47838 6.53211 7.64233 6.21264C7.80793 5.89478 7.72513 5.61705 7.60093 5.37785C7.47838 5.13865 6.4947 2.78999 6.08401 1.83479C5.6849 0.905283 5.27918 1.03211 4.97778 1.01605C4.68963 1.00321 4.36174 1 4.03384 1C3.70595 1 3.17271 1.1188 2.72227 1.5972C2.27017 2.07399 1 3.22825 1 5.57691C1 7.92396 2.76201 10.1924 3.00876 10.5118C3.25551 10.8297 6.47814 15.649 11.4148 17.7151C12.5905 18.2064 13.5063 18.5001 14.2201 18.7185C15.3992 19.0829 16.4723 19.0315 17.3202 18.9079C18.2641 18.7715 20.2315 17.7536 20.6421 16.6395C21.0512 15.5254 21.0512 14.5702 20.9286 14.3711C20.8061 14.1721 20.4799 14.0533 19.9864 13.8141Z"
                                        stroke="url(#paint0_linear_2087_11745)"
                                    ></path>
                                    <defs>
                                        <linearGradient
                                            id="paint0_linear_2087_11745"
                                            x1="1.05777"
                                            y1="-1.1894"
                                            x2="20.9526"
                                            y2="-1.6104"
                                            gradientUnits="userSpaceOnUse"
                                        >
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span class="ml-[12px] text-lg font-light leading-[1]"
                                >+{{$property->agent?->mobile}}</span
                                >
                            </div>
                            <div class="mt-8 flex items-center">
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M1 8.66667V20H23V8.66667M1 3V5.83333L12 13L23 5.83333V3H1Z"
                                        stroke="url(#paint0_linear_1_5)"
                                    ></path>
                                    <defs>
                                        <linearGradient
                                            id="paint0_linear_1_5"
                                            x1="0.153244"
                                            y1="3.36868"
                                            x2="22.7546"
                                            y2="22.5399"
                                            gradientUnits="userSpaceOnUse"
                                        >
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span class="ml-[12px] text-lg font-light leading-[1]"
                                >{{$property->agent?->email}}</span
                                >
                            </div>
                            <div class="mt-8 flex items-center">
                                <svg
                                    width="24"
                                    height="24"
                                    viewBox="0 0 25 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M16.5202 14.3495C16.2831 14.231 15.1209 13.6598 14.9045 13.5803C14.6882 13.5015 14.5307 13.4625 14.3724 13.6996C14.2149 13.935 13.7622 14.468 13.6246 14.6255C13.4862 14.7838 13.3486 14.8029 13.1123 14.6852C12.8761 14.5659 12.114 14.3169 11.2112 13.5119C10.5087 12.885 10.0338 12.111 9.89623 11.874C9.75861 11.6377 9.88112 11.5096 9.99964 11.3919C10.1062 11.2861 10.2359 11.1159 10.3544 10.9783C10.4729 10.8399 10.5119 10.7412 10.5907 10.5829C10.6702 10.4254 10.6305 10.2878 10.5708 10.1693C10.5119 10.0507 10.0394 8.88696 9.84214 8.41365C9.65043 7.95307 9.45554 8.01591 9.31076 8.00795C9.17235 8.00159 9.01484 8 8.85734 8C8.69983 8 8.44369 8.05887 8.22732 8.29592C8.01015 8.53217 7.40002 9.10412 7.40002 10.2679C7.40002 11.4309 8.24641 12.5549 8.36494 12.7132C8.48346 12.8707 10.0315 15.2587 12.4028 16.2825C12.9676 16.5259 13.4075 16.6715 13.7503 16.7797C14.3167 16.9602 14.8322 16.9348 15.2394 16.8735C15.6929 16.8059 16.6379 16.3016 16.8352 15.7495C17.0317 15.1975 17.0317 14.7242 16.9728 14.6255C16.9139 14.5269 16.7572 14.468 16.5202 14.3495Z"
                                        fill="#D3A188"
                                    ></path>
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M16.5202 14.3495C16.2831 14.231 15.1209 13.6598 14.9045 13.5803C14.6882 13.5015 14.5307 13.4625 14.3724 13.6996C14.2149 13.935 13.7622 14.468 13.6246 14.6255C13.4862 14.7838 13.3486 14.8029 13.1123 14.6852C12.8761 14.5659 12.114 14.3169 11.2112 13.5119C10.5087 12.885 10.0338 12.111 9.89623 11.874C9.75861 11.6377 9.88112 11.5096 9.99964 11.3919C10.1062 11.2861 10.2359 11.1159 10.3544 10.9783C10.4729 10.8399 10.5119 10.7412 10.5907 10.5829C10.6702 10.4254 10.6305 10.2878 10.5708 10.1693C10.5119 10.0507 10.0394 8.88696 9.84214 8.41365C9.65043 7.95307 9.45554 8.01591 9.31076 8.00795C9.17235 8.00159 9.01484 8 8.85734 8C8.69983 8 8.44369 8.05887 8.22732 8.29592C8.01015 8.53217 7.40002 9.10412 7.40002 10.2679C7.40002 11.4309 8.24641 12.5549 8.36494 12.7132C8.48346 12.8707 10.0315 15.2587 12.4028 16.2825C12.9676 16.5259 13.4075 16.6715 13.7503 16.7797C14.3167 16.9602 14.8322 16.9348 15.2394 16.8735C15.6929 16.8059 16.6379 16.3016 16.8352 15.7495C17.0317 15.1975 17.0317 14.7242 16.9728 14.6255C16.9139 14.5269 16.7572 14.468 16.5202 14.3495Z"
                                        fill="url(#paint0_linear_1_13)"
                                    ></path>
                                    <path
                                        d="M20.9043 6.85613C17.9125 1.99984 11.8044 0.556079 7.06743 3.57485C2.45514 6.59362 0.959261 13.1562 3.95102 18.0125L4.20033 18.4062L3.20308 22.3437L6.94277 21.2937L7.31674 21.5562C8.93728 22.475 10.6825 23 12.4277 23C14.2975 23 16.1674 22.475 17.7879 21.425C22.4002 18.275 23.7714 11.8437 20.9043 6.85613Z"
                                        stroke="url(#paint1_linear_1_13)"
                                        stroke-width="1.2"
                                    ></path>
                                    <defs>
                                        <linearGradient
                                            id="paint0_linear_1_13"
                                            x1="7.42778"
                                            y1="6.91513"
                                            x2="16.9846"
                                            y2="6.71908"
                                            gradientUnits="userSpaceOnUse"
                                        >
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                        <linearGradient
                                            id="paint1_linear_1_13"
                                            x1="1.63025"
                                            y1="2.45543"
                                            x2="27.0535"
                                            y2="18.3256"
                                            gradientUnits="userSpaceOnUse"
                                        >
                                            <stop stop-color="#CC9177"></stop>
                                            <stop offset="0.242874" stop-color="#E5B59E"></stop>
                                            <stop offset="0.515487" stop-color="#C18369"></stop>
                                            <stop offset="0.729115" stop-color="#EAB498"></stop>
                                            <stop offset="1" stop-color="#E6A483"></stop>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span
                                    class="ml-[12px] mt-[2.5px] text-lg font-light leading-[1]"
                                >+{{$property->agent?->mobile}}</span
                                >
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="pb-24 pt-32">
          <span class="text-white opacity-60">AX CAPITAL ©  <script>
            document.write(new Date().getFullYear())
        </script> All Rights Reserved</span
          >
        </div>
    </div>
</div>
<script src="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.js"></script>
<script src="{{asset('preview/vendors/jspdf/jspdf.min.js')}}"></script>
<script src="{{asset('preview/vendors/html2canvas/html2canvas.min.js')}}"></script>
<script>
    let longitude = "{{$property?->communityProperty?->longitude ?? $property?->subCommunity?->longitude}}";
    let latitude = "{{$property?->communityProperty?->latitude ?? $property?->subCommunity?->latitude}}";
    let mapLicense = "pk.eyJ1Ijoic2FpbWF4Y2FwaXRhbCIsImEiOiJjbGZ0ZXM3a3gwMDhrM2VyM3FvZmdxdmx3In0.nFc4SGgYByc3beoXFp9a7A";
</script>
<script src="{{asset('preview/scripts/main.js')}}"></script>
</body>
</html>

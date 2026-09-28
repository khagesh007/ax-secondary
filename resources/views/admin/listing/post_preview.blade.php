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
    <link href="{{ asset('new_preview2/styles/output.css') }}" rel="stylesheet" />
</head>

<body>
    <div class="absolute right-5 top-5 hidden">
        <button class="generate-pdf bg-white38 px-7 py-3 uppercase" id="genPDF">
            generate pdf
        </button>
        <button class="bg-white38 px-7 py-3 uppercase gen" id="gen">
            generate instagram posts
        </button>
    </div>
    <div
        class="loading fixed top-0 left-0 z-[50] hidden h-full w-full items-center justify-center bg-white60 text-4xl font-semibold uppercase text-black">
        Generating
    </div>

    @php
    $agentImage = $property->agent?->pp_enabled == 1 ? $property->agent?->pp_photo : $property->agent?->image_url;
    $agentImage = $agentImage != null ? $agentImage : $property->agent?->image_url;
@endphp



    <div class="instagram-post">

        <div class="instagram-post__item sale">
            <div class="bg-cover bg-center bg-no-repeat p-[60px]"
                style="background-image: url('{{ $image->original ??$property?->photos[0]->original }}')">
                <div class="relative h-full w-full">
                    <div
                        class="pointer-events-none absolute right-0 top-[-24px] h-[calc(100%+24px)] w-full overflow-hidden before:absolute before:left-[-181px] before:top-[22px] before:block before:h-[3px] before:w-full before:bg-instaGradient after:absolute after:right-0 after:top-[107px] after:block after:h-full after:w-[3px] after:bg-instaGradient">
                        <img src="{{asset('new_preview2/images/inst_logo.svg')}}" alt="logo" class="ml-auto h-[77px] w-[151px]" />
                    </div>
                    <div
                        class="absolute left-0 top-0 ml-[-30px] flex h-[calc(100%+40px)] w-[calc(100%+30px)] overflow-hidden font-cont">
                        <div class="flex w-full">
                            <div
                                class="flex w-full flex-col before:ml-[30px] before:inline-block before:h-full before:w-[3px] before:bg-instaGradientReverse">
                                <div class="inst-post-text">
                                    <p class="text-[160px] font-bold leading-[148px]">
                                        <span class="block"> <span class="text_first">{{request()->pretext??'For'}}</span> <br /> <span
                                                class="text_last">{{request()->posttext??'Sale'}}</span> </span>
                                    </p>
                                    <div
                                        class="flex after:mb-[40px] after:mt-auto after:inline-block after:h-[3px] after:w-full after:bg-instaGradient">
                                        <p class="whitespace-nowrap pr-[30px] text-[64px] font-light">
                                            <span class="block">
                                                {{request()->location ?? $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex h-full w-full items-end justify-end">
                        <div class="p-16">
                            @if ($show_profile == true)
                            <div class="flex items-center border-[1px] border-white bg-black50 p-6">
                                <div class="print-agent-image h-[72px] w-[72px] bg-cover bg-top bg-no-repeat"
                                    style="background-image: url('{{ ($agentImage) }}')"></div>
                                <div class="inst-post-agent ml-6 text-white">
                                    <p class="font-cont text-3xl font-bold">
                                        {{ $property->agent?->pp_enabled == 1 ? $property->agent?->pp_name : $property->agent?->name }}
                                    </p>
                                    <p class="font-cormorantInfant text-3xl font-medium">
                                        +{{ numbers_only($property->agent?->mobile) }}
                                    </p>
                                </div>
                            </div>
                            @endif
                            @if ($show_price == true)
                            <div class="mt-6 flex h-[106px] items-center border-[1px] border-white bg-black50 p-6">
                                <p class="inst-post-price font-cormorant text-6xl font-bold">
                                    AED {{ number_format($property->price?->price) }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="{{ asset('new_preview2/vendors/swiper/swiper.min.js') }}"></script>
   <!-- <script src="https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.js"></script> -->
    <script src="{{ asset('new_preview2/vendors/jspdf/jspdf.min.js') }}"></script>
    <script src="{{ asset('new_preview2/vendors/html2canvas/html2canvas.min.js') }}"></script>
    <script src="{{ asset('new_preview2/scripts/main_without_map.js') }}"></script>


    <script>
        $(document).ready(function() {
            const urlParamsCheck = new URLSearchParams(window.location.search);
            console.log(urlParamsCheck);
            if (urlParamsCheck.has('show_price') && urlParamsCheck.has('show_profile') && urlParamsCheck.has(
                    'first_text') && urlParamsCheck.has('second_text')) {
                generateInstagramPostsDynamic();
            }
            if (urlParamsCheck.has('type') && urlParamsCheck.has('features') && urlParamsCheck.has('description')) {
                // $('#genPDF').click(function() {
                generatePDF();
                // })
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
            } else {
                document.querySelectorAll(".priceSection").forEach((el) => {
                    el.style.display = "none";
                });
            }

            if (showProfileParam === 'yes') {
                document.querySelectorAll(".inst-post-agent").forEach((el) => {
                    el.style.marginTop = "-24px";
                });
            } else {
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

    <script></script>
</body>

</html>

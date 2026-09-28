@extends('admin.layouts.master')
@section('title')
    KPI - {{ $agent->name ?? "" }}
@endsection
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
          integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>


    <style>
        .progress-ring-container {
            display: inline-block;
            position: relative;
        }

        .progress-ring {
            transform: rotate(-90deg);
            /* Rotate to make the progress start from the top */
        }

        .progress-ring circle {
            fill: none;
            stroke-width: 4;
            /* Adjust stroke width to suit the smaller size */
            stroke-linecap: round;
        }

        .progress-ring .background {
            stroke: #ddd;
        }

        .progress-ring .progress-success {
            stroke: #45cb85;
            transition: stroke-dasharray 1s ease;
        }

        .progress-ring .progress-warning {
            stroke: #ffbe0b;
            transition: stroke-dasharray 1s ease;
        }

        .progress-ring .progress-danger {
            stroke: #f06548;
            transition: stroke-dasharray 1s ease;
        }

        .percentage-text {
            font-size: 8px;
            /* Smaller text size */
            font-weight: bold;
            text-anchor: middle;
            dominant-baseline: middle;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* Ensures text is centered */
        }

        .bg-dark-subtle {
            background-color: rgb(247, 247, 247);
        }

        .quality-listers {
            border: 1px solid #64b7e3;
            border-radius: 5px;
            padding: 3px;
            background: #d9ebf4;
        }

        .responsive-broker {
            background-color: rgb(244, 233, 245);
            border: 1px solid rgb(200, 148, 204);
            border-radius: 3px;
            padding: 2px;
        }

        .super-agent-badge {
            background: #3a307f;
            font-weight: 400;
            color: white !important;
            font-size: 11px !important;
            border-radius: 0.4rem !important;
            padding: .16em .45em !important;
        }

        .table {
            margin-bottom: 0px !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Key Performance Indicator</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Kpi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('reports.kpi-v3.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">View</li>
                            <li class="breadcrumb-item active">{{ $agent->full_name }}</li>
                        </ol>
                    </div>
                    <div class="flex-grow-2">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="d-flex">
                                @php
                                    $month = request('month') ? date('F Y', strtotime(request('month'))) : date('F Y');
                                @endphp
                                <input type="text" class="form-control" name="month"
                                       value="{{ $month }}"
                                       id="month" placeholder="Select Month">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="card bg-transparent h-100 p-0" style="box-shadow: none">
                <div class="row">
                    <div class="col-12">
                        @isset($agent->zone)
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-start align-items-center gap-4">
                                        <div class="avatar-sm flex-shrink-0 rounded-circle"
                                             style="width: 3rem; height: 3rem; border-width: 2px; border-style: solid; border-color: {{ $agent->parent->group ?? '' }};">
                                    <span class="avatar-title bg-light rounded-circle fs-3 ax-text ">
                                        {{ $agent->parent->group->name ?? '' }}
                                    </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $agent->parent->name ?? '' }}</h6>
                                            <p class="text-muted mb-0">{{ $agent->parent->position ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endisset
                    </div>
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center">
                                    <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                        <img src="{{ $agent->thumb_url }}"
                                             class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                             alt="user-profile-image">
                                    </div>
                                    <h5 class="fs-16 mb-1">{{ $agent->full_name }}</h5>
                                    <p class="text-muted mb-1"><b>{{ $agent->position }}</b></p>
                                    <p class="text-muted mb-0">Joining
                                        Date: {{ $agent->joining_date ? date('d M, Y', strtotime($agent->joining_date)) : '' }}
                                    </p>
                                    <div class="mt-1 d-flex justify-content-center gap-2">
                                        @if (($bayutUsefulData['trubroker'] ?? '') == 'Active')
                                            <svg width="84" height="22" viewBox="0 0 84 22" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg" format="plural">
                                                <rect width="84" height="22" rx="5"
                                                      fill="url(#paint0_linear_666_62002)"></rect>
                                                <path
                                                    d="M11.066 16V8.374H8.12V7.36H15.08V8.374H12.134V16H11.066ZM15.4377 16V9.52H16.3917V11.086L16.2357 10.882C16.3117 10.682 16.4097 10.498 16.5297 10.33C16.6537 10.162 16.7917 10.024 16.9437 9.916C17.1117 9.78 17.3017 9.676 17.5137 9.604C17.7257 9.532 17.9417 9.49 18.1617 9.478C18.3817 9.462 18.5877 9.476 18.7797 9.52V10.522C18.5557 10.462 18.3097 10.446 18.0417 10.474C17.7737 10.502 17.5257 10.6 17.2977 10.768C17.0897 10.916 16.9277 11.096 16.8117 11.308C16.6997 11.52 16.6217 11.75 16.5777 11.998C16.5337 12.242 16.5117 12.492 16.5117 12.748V16H15.4377ZM22.3742 16.168C21.9582 16.168 21.6002 16.102 21.3002 15.97C21.0042 15.838 20.7582 15.662 20.5622 15.442C20.3662 15.222 20.2122 14.978 20.1002 14.71C19.9882 14.442 19.9082 14.168 19.8602 13.888C19.8162 13.608 19.7942 13.346 19.7942 13.102V9.52H20.8742V12.766C20.8742 13.07 20.9022 13.364 20.9582 13.648C21.0142 13.932 21.1082 14.188 21.2402 14.416C21.3762 14.644 21.5562 14.824 21.7802 14.956C22.0082 15.088 22.2922 15.154 22.6322 15.154C22.9002 15.154 23.1442 15.108 23.3642 15.016C23.5842 14.924 23.7722 14.788 23.9282 14.608C24.0842 14.424 24.2042 14.192 24.2882 13.912C24.3722 13.632 24.4142 13.306 24.4142 12.934L25.1162 13.15C25.1162 13.774 25.0022 14.312 24.7742 14.764C24.5502 15.216 24.2322 15.564 23.8202 15.808C23.4122 16.048 22.9302 16.168 22.3742 16.168ZM24.5342 16V14.266H24.4142V9.52H25.4882V16H24.5342ZM27.1681 16V7.36H30.5941C31.1781 7.36 31.6561 7.478 32.0281 7.714C32.4041 7.946 32.6821 8.244 32.8621 8.608C33.0461 8.972 33.1381 9.348 33.1381 9.736C33.1381 10.212 33.0241 10.616 32.7961 10.948C32.5721 11.28 32.2661 11.504 31.8781 11.62V11.32C32.4221 11.444 32.8341 11.702 33.1141 12.094C33.3981 12.486 33.5401 12.932 33.5401 13.432C33.5401 13.944 33.4421 14.392 33.2461 14.776C33.0501 15.16 32.7581 15.46 32.3701 15.676C31.9861 15.892 31.5101 16 30.9421 16H27.1681ZM28.6321 14.638H30.7621C31.0141 14.638 31.2401 14.588 31.4401 14.488C31.6401 14.384 31.7961 14.24 31.9081 14.056C32.0241 13.868 32.0821 13.646 32.0821 13.39C32.0821 13.158 32.0321 12.95 31.9321 12.766C31.8321 12.582 31.6861 12.436 31.4941 12.328C31.3021 12.216 31.0721 12.16 30.8041 12.16H28.6321V14.638ZM28.6321 10.81H30.5761C30.7841 10.81 30.9701 10.77 31.1341 10.69C31.2981 10.61 31.4281 10.492 31.5241 10.336C31.6201 10.18 31.6681 9.988 31.6681 9.76C31.6681 9.46 31.5721 9.21 31.3801 9.01C31.1881 8.81 30.9201 8.71 30.5761 8.71H28.6321V10.81ZM34.7384 16V9.52H36.0164V11.098L35.8604 10.894C35.9404 10.678 36.0464 10.482 36.1784 10.306C36.3144 10.126 36.4764 9.978 36.6644 9.862C36.8244 9.754 37.0004 9.67 37.1924 9.61C37.3884 9.546 37.5884 9.508 37.7924 9.496C37.9964 9.48 38.1944 9.488 38.3864 9.52V10.87C38.1944 10.814 37.9724 10.796 37.7204 10.816C37.4724 10.836 37.2484 10.906 37.0484 11.026C36.8484 11.134 36.6844 11.272 36.5564 11.44C36.4324 11.608 36.3404 11.8 36.2804 12.016C36.2204 12.228 36.1904 12.458 36.1904 12.706V16H34.7384ZM42.2762 16.18C41.6282 16.18 41.0622 16.034 40.5782 15.742C40.0942 15.45 39.7182 15.048 39.4502 14.536C39.1862 14.02 39.0542 13.428 39.0542 12.76C39.0542 12.08 39.1902 11.484 39.4622 10.972C39.7342 10.46 40.1122 10.06 40.5962 9.772C41.0802 9.484 41.6402 9.34 42.2762 9.34C42.9282 9.34 43.4962 9.486 43.9802 9.778C44.4642 10.07 44.8402 10.474 45.1082 10.99C45.3762 11.502 45.5102 12.092 45.5102 12.76C45.5102 13.432 45.3742 14.026 45.1022 14.542C44.8342 15.054 44.4582 15.456 43.9742 15.748C43.4902 16.036 42.9242 16.18 42.2762 16.18ZM42.2762 14.824C42.8522 14.824 43.2802 14.632 43.5602 14.248C43.8402 13.864 43.9802 13.368 43.9802 12.76C43.9802 12.132 43.8382 11.632 43.5542 11.26C43.2702 10.884 42.8442 10.696 42.2762 10.696C41.8882 10.696 41.5682 10.784 41.3162 10.96C41.0682 11.132 40.8842 11.374 40.7642 11.686C40.6442 11.994 40.5842 12.352 40.5842 12.76C40.5842 13.388 40.7262 13.89 41.0102 14.266C41.2982 14.638 41.7202 14.824 42.2762 14.824ZM46.8202 16L46.8322 7.36H48.2962V12.64L50.6662 9.52H52.4722L49.9582 12.76L52.6882 16H50.7742L48.2962 12.88V16H46.8202ZM56.118 16.18C55.462 16.18 54.886 16.038 54.39 15.754C53.894 15.47 53.506 15.076 53.226 14.572C52.95 14.068 52.812 13.488 52.812 12.832C52.812 12.124 52.948 11.51 53.22 10.99C53.492 10.466 53.87 10.06 54.354 9.772C54.838 9.484 55.398 9.34 56.034 9.34C56.706 9.34 57.276 9.498 57.744 9.814C58.216 10.126 58.566 10.568 58.794 11.14C59.022 11.712 59.108 12.386 59.052 13.162H57.618V12.634C57.614 11.93 57.49 11.416 57.246 11.092C57.002 10.768 56.618 10.606 56.094 10.606C55.502 10.606 55.062 10.79 54.774 11.158C54.486 11.522 54.342 12.056 54.342 12.76C54.342 13.416 54.486 13.924 54.774 14.284C55.062 14.644 55.482 14.824 56.034 14.824C56.39 14.824 56.696 14.746 56.952 14.59C57.212 14.43 57.412 14.2 57.552 13.9L58.98 14.332C58.732 14.916 58.348 15.37 57.828 15.694C57.312 16.018 56.742 16.18 56.118 16.18ZM53.886 13.162V12.07H58.344V13.162H53.886ZM60.3908 16V9.52H61.6688V11.098L61.5128 10.894C61.5928 10.678 61.6988 10.482 61.8308 10.306C61.9668 10.126 62.1288 9.978 62.3168 9.862C62.4768 9.754 62.6528 9.67 62.8448 9.61C63.0408 9.546 63.2408 9.508 63.4448 9.496C63.6488 9.48 63.8468 9.488 64.0388 9.52V10.87C63.8468 10.814 63.6248 10.796 63.3728 10.816C63.1248 10.836 62.9008 10.906 62.7008 11.026C62.5008 11.134 62.3368 11.272 62.2088 11.44C62.0848 11.608 61.9928 11.8 61.9328 12.016C61.8728 12.228 61.8428 12.458 61.8428 12.706V16H60.3908ZM67.4514 16.18C66.6514 16.18 66.0014 16 65.5014 15.64C65.0014 15.28 64.6974 14.774 64.5894 14.122L66.0654 13.894C66.1414 14.214 66.3094 14.466 66.5694 14.65C66.8294 14.834 67.1574 14.926 67.5534 14.926C67.9014 14.926 68.1694 14.858 68.3574 14.722C68.5494 14.582 68.6454 14.392 68.6454 14.152C68.6454 14.004 68.6094 13.886 68.5374 13.798C68.4694 13.706 68.3174 13.618 68.0814 13.534C67.8454 13.45 67.4834 13.344 66.9954 13.216C66.4514 13.072 66.0194 12.918 65.6994 12.754C65.3794 12.586 65.1494 12.388 65.0094 12.16C64.8694 11.932 64.7994 11.656 64.7994 11.332C64.7994 10.928 64.9054 10.576 65.1174 10.276C65.3294 9.976 65.6254 9.746 66.0054 9.586C66.3854 9.422 66.8334 9.34 67.3494 9.34C67.8534 9.34 68.2994 9.418 68.6874 9.574C69.0794 9.73 69.3954 9.952 69.6354 10.24C69.8754 10.528 70.0234 10.866 70.0794 11.254L68.6034 11.518C68.5674 11.242 68.4414 11.024 68.2254 10.864C68.0134 10.704 67.7294 10.614 67.3734 10.594C67.0334 10.574 66.7594 10.626 66.5514 10.75C66.3434 10.87 66.2394 11.04 66.2394 11.26C66.2394 11.384 66.2814 11.49 66.3654 11.578C66.4494 11.666 66.6174 11.754 66.8694 11.842C67.1254 11.93 67.5054 12.038 68.0094 12.166C68.5254 12.298 68.9374 12.45 69.2454 12.622C69.5574 12.79 69.7814 12.992 69.9174 13.228C70.0574 13.464 70.1274 13.75 70.1274 14.086C70.1274 14.738 69.8894 15.25 69.4134 15.622C68.9414 15.994 68.2874 16.18 67.4514 16.18ZM76.0796 11.86V7.36H76.7516L78.3956 10.102L80.0396 7.36H80.7116L80.7176 11.86H79.9376V8.926L78.6476 11.122H78.1436L76.8596 8.926V11.86H76.0796ZM72.8696 11.86V8.116H71.3216V7.36H75.2276V8.116H73.6856V11.86H72.8696Z"
                                                    fill="white"></path>
                                                <defs>
                                                    <linearGradient id="paint0_linear_666_62002" x1="79.4063" y1="-5.17647"
                                                                    x2="6.40824" y2="36.4682" gradientUnits="userSpaceOnUse">
                                                        <stop offset="0.196272" stop-color="#053940"></stop>
                                                        <stop offset="0.489583" stop-color="#00745F"></stop>
                                                        <stop offset="0.720273" stop-color="#033F46"></stop>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        @endif

                                        @if (($pfUsefulData['is_super_agent'] ?? 0) === 1)
                                            <span class="badge super-agent-badge"
                                                  style="font-weight: 500;">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                     class="r-1xlzkty r-1472mwg r-lrsllp">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M13.9096 6.45575L12.4054 3.26454H12.407C12.2402 2.91182 11.7607 2.91182 11.5955 3.26454L10.0897 6.45575C10.024 6.59516 9.8973 6.69258 9.74977 6.71441L6.38697 7.22669C6.01654 7.28379 5.86901 7.7608 6.13681 8.03457L8.5695 10.5187C8.67534 10.6279 8.72505 10.7857 8.69939 10.9386L8.1253 14.4456C8.06115 14.8319 8.44923 15.1275 8.78118 14.9444L11.7896 13.2883C11.9211 13.2161 12.0782 13.2161 12.2097 13.2883L15.2181 14.9444C15.5501 15.1275 15.9381 14.8319 15.874 14.4456L15.2999 10.9386C15.2742 10.7841 15.3239 10.6279 15.4298 10.5187L17.8625 8.03457C18.1319 7.7608 17.9828 7.28379 17.6123 7.22669L14.2495 6.71441C14.102 6.69258 13.9753 6.59516 13.9096 6.45575ZM15.6043 18.9989V17.9316C15.6043 17.5384 15.2024 17.2206 14.7063 17.2206H13.3601V15.0859H10.6661V17.2206H9.31989C8.82375 17.2206 8.42188 17.5384 8.42188 17.9316V18.9989H15.6043ZM16.0909 19H7.90909C7.40683 19 7 19.2978 7 19.6661V21H17V19.6661C17 19.2978 16.5932 19 16.0909 19Z"
                                                          fill="currentColor"></path>
                                                </svg>
                                                Super Agent
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-0 mt-2">
                    <div class="row row-cols-xxl-3 row-cols-md-4 row-cols-1 g-2">
                        @foreach ($targets ?? [] as $target)
                            @php
                                $previousMonthTargetIndex = array_search(
                                    $target->kpi,
                                    array_column($previousMonthTargets?->toArray(), 'kpi'),
                                );
                                $previousMonthTarget = null;
                                if ($previousMonthTargetIndex !== false) {
                                    $previousMonthTarget = $previousMonthTargets[$previousMonthTargetIndex];
                                }
                            @endphp

                            @php
                                try{
                                    $achieved_percentage =  0;
                                    //calculate achieved percentage
                                    if ($target->result->achieved && $target->target) {
                                        $achieved_percentage =  ($target->result->achieved / $target->target) * 100;
                                        $achieved_percentage = $achieved_percentage >= 100 ? 100 : round($achieved_percentage, 2);
                                    }
                                    }catch(\Exception $e){
                                        $achieved_percentage = 0;
                                    }


                            @endphp

                            <div class="col">
                                <div class="card card-animate overflow-hidden cursor-pointer"
                                     onclick="toggleColumnCards('{{ $target->kpi }}')">
                                    <div class="position-absolute start-0" style="z-index: 0;">
                                        <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120"
                                             width="200" height="120">
                                            <style>
                                                .s0 {
                                                    opacity: .05;
                                                    fill: var(--vz-primary)
                                                }
                                            </style>
                                            <path id="Shape 8" class="s0"
                                                  d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z"></path>
                                        </svg>
                                    </div>
                                    <div class="card-body p-2" style="z-index:1 ;">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-3"> {{ ucwords(str_replace('_', ' ', $target->kpi)) }}</p>
                                                <h4 class="fs-16 fw-semibold ff-secondary mb-0"><span>{{ formatNumberMetricPrefixes(preg_replace('/[^0-9.]/', '', $target->result->achieved ?? '0')) }}/{{ formatNumberMetricPrefixes(preg_replace('/[^0-9.]/', '', $target->target ?? '0')) }}</span>
                                                </h4>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div id="{{ $target->kpi }}_chart" class="target_radial_charts"
                                                     data-achieved="{{ $achieved_percentage }}"></div>
                                            </div>
                                        </div>
                                        <div class="mb-0 text-muted mt-2 d-flex justify-content-between">
                                            <div>
                                                <span
                                                    class="badge bg-light text-info mb-0">{{ formatNumberMetricPrefixes(preg_replace('/[^0-9]/', '', $previousMonthTarget->result->achieved ?? '0')) }}</span>
                                                prev. month
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center">
                                                @php
                                                    $previousMonthAchievedValue = $previousMonthTarget?->result?->achieved ?? 0;
                                                    $thisMonthAchievedValue = $target->result->achieved ?? 0;

                                                    if ($previousMonthAchievedValue != 0) {
                                                        $percentageChange = (($thisMonthAchievedValue - $previousMonthAchievedValue) / $previousMonthAchievedValue) * 100;
                                                    } else {
                                                        $percentageChange = $thisMonthAchievedValue > 0 ? 100 : 0;
                                                    }

                                                @endphp
                                                @if (($percentageChange ?? 0) >  0)
                                                    <span
                                                        class="badge bg-light text-success mb-0">{{ round($percentageChange, 2) }}%</span>
                                                    <i
                                                        class="ri-arrow-up-circle-line text-success fs-12 float-end align-middle"></i>
                                                @else
                                                    <span
                                                        class="badge bg-light text-danger mb-0">{{ round($percentageChange, 2) }}%</span>
                                                    <i
                                                        class="ri-arrow-down-circle-line text-danger fs-12 float-end align-middle"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row row-cols-xxl-3 row-cols-md-4 row-cols-1 g-2 mt-1">
                        @foreach ($otherReports ?? [] as $otherReport)
                            @php
                                $previousMonthReportIndex = array_search(
                                    $otherReport->kpi,
                                    array_column($previousMonthOtherReports?->toArray(), 'kpi'),
                                );
                                $previousMonthReport = null;
                                if ($previousMonthReportIndex !== false) {
                                    $previousMonthReport = $previousMonthOtherReports[$previousMonthReportIndex];
                                }
                            @endphp
                            <div class="col my-0">
                                <div class="card card-animate cursor-pointer"
                                     onclick="toggleColumnCards('{{ $otherReport->kpi }}')">
                                    <div class="card-body p-2">
                                        <div>
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-3"> {{ ucwords(str_replace('_', ' ', $otherReport->kpi)) }}</p>
                                            <div>
                                                <h2 class="mt-2 text-center ff-secondary fw-semibold"><span
                                                        data-target="{{ formatNumberMetricPrefixes($otherReport->count ?? 0) }}">{{ formatNumberMetricPrefixes($otherReport->count ?? 0) }}</span>
                                                </h2>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="mb-0 text-muted"><span
                                                        class="badge bg-light text-info mb-0">{{ formatNumberMetricPrefixes($previousMonthReport->count ?? 0) }}</span>
                                                    pre. month
                                                </div>
                                                <div class="d-flex justify-content-end align-items-center gap-1">
                                                    @if (($otherReport->count ?? 0) > ($previousMonthReport->count ?? 0))
                                                        <span
                                                            class="badge bg-light text-success mb-0">{{ formatNumberMetricPrefixes(($otherReport->count ?? 0) - ($previousMonthReport->count ?? 0)) }}</span>
                                                        <i
                                                            class="ri-arrow-up-circle-line text-success fs-12 float-end align-middle"></i>
                                                    @else
                                                        <span
                                                            class="badge bg-light text-danger mb-0">{{ formatNumberMetricPrefixes(($otherReport->count ?? 0) - ($previousMonthReport->count ?? 0)) }}</span>
                                                        <i
                                                            class="ri-arrow-down-circle-line text-danger fs-12 float-end align-middle"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-12 my-0">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div>
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Unpublished Listings This Month</p>
                                        <div>
                                            <h2 class="mt-2 text-center ff-secondary fw-semibold"><span
                                                    data-target="{{$unpublished_listings_count}}">{{ formatNumberMetricPrefixes($unpublished_listings_count ?? 0) }}</span>
                                            </h2>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="mb-0 text-muted"><span
                                                    class="badge bg-light text-info mb-0">{{ $previousMonthUnpublishedListingsCount }}</span>
                                                pre. month
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center gap-1">

                                                @php
                                                    $previousMonthAchievedValue = $previousMonthUnpublishedListingsCount;
                                                    $thisMonthAchievedValue = $unpublished_listings_count;

                                                    if ($previousMonthAchievedValue != 0) {
                                                        $percentageChange = (($thisMonthAchievedValue - $previousMonthAchievedValue) / $previousMonthAchievedValue) * 100;
                                                    } else {
                                                        $percentageChange = $thisMonthAchievedValue > 0 ? 100 : 0;
                                                    }

                                                @endphp
                                                @if (($percentageChange ?? 0) >  0)
                                                    <span
                                                        class="badge bg-light text-success mb-0">{{ $percentageChange }}%</span>
                                                    <i
                                                        class="ri-arrow-up-circle-line text-success fs-12 float-end align-middle"></i>
                                                @else
                                                    <span
                                                        class="badge bg-light text-danger mb-0">{{ $percentageChange }}%</span>
                                                    <i
                                                        class="ri-arrow-down-circle-line text-danger fs-12 float-end align-middle"></i>
                                                @endif
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
        <div class="col-6">
            <div class="card bg-transparent h-100 p-0" style="box-shadow: none;">
                <div class="card h-100">
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1 oveflow-hidden">
                            <p class="text-muted text-truncates mb-0"><span
                                    id="breakdown-title">{{ ucwords(str_replace('_', ' ', $targets[0]->kpi ?? '')) }}</span>
                                Breakdown</p>
                        </div>
                    </div>
                    <div class="card-body" style="height: 267.5px;">
                        <div id="breakdown-pie-chart"></div>
                    </div>
                    <div class="card-body" style="height: 500px">
                        <div class="row mt-5">
                            <div class="col-6">
                                <ul id="breakdown-list-one"></ul>
                            </div>
                            <div class="col-6">
                                <ul id="breakdown-list-two"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row mt-2">
        @foreach ($targets ?? [] as $index => $target)
            <div class="col-12 target-cols {{ $target->kpi }}-cols">
                <div class="card h-100">
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1 oveflow-hidden">
                            <p class="text-muted text-truncates mb-0">
                                {{ ucwords(str_replace('_', ' ', $target->kpi ?? '')) }}
                                Day Wise Report</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="{{ $target->kpi }}-date-wise-chart" class="date-wise-chart"
                            data-dataset="{{ json_encode($target->result->daily_data ?? []) }}" style="height: 300px">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @foreach ($otherReports ?? [] as $index => $otherReport)
            <div
                class="col-12 target-cols {{ $otherReport->kpi }}-cols">
                <div class="card h-100">
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1 oveflow-hidden">
                            <p class="text-muted text-truncates mb-0">
                                {{ ucwords(str_replace('_', ' ', $otherReport->kpi)) }}
                                Day Wise Report</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="{{ $otherReport->kpi }}-date-wise-chart" class="date-wise-chart"
                             data-dataset="{{ json_encode($otherReport->daily_data ?? []) }}"
                             style="height: 300px"></div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="mb-4">
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Leads By Listings</h5>
        </div>
        <div class="card-body">
            <div class="text-center" id="leadsByListingsChartLoader">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <div id="leadsByListingsChart" class="d-none" style="width: 100%; height: 600px;"></div>
        </div>
    </div>
    <?php /*
    <div class="card " id="pf-card">
        <div class="card-header">


            <div class="d-flex justify-content-between align-items-center gap-3">
                <div style="width: 100px;">
                    <svg viewBox="0 0 1078 438">
                        <title>Logo-En</title>
                        <path fill="#ea3934" fill-rule="nonzero"
                              d="M144.3.7C223.6.7 288 65.5 288 145.5c0 12.5-1.6 24.6-4.5 36.2-3.3 12.8-8.3 25-14.7 36.2-6.3 11-14 21.1-22.9 30-26 26.2-61.9 42.4-101.5 42.4v-1.9c-1.4-41.3-19.1-78.4-46.7-105-25.4-24.5-59.4-40.1-96.8-41.9 1-38.4 16.9-73 42-98.4C68.7 16.9 104.6.7 144.3.7zM.7 435.1c79.3 0 143.7-64.8 143.7-144.8H.7v144.8zm650.9.3h21V299l-22 4v45.5c-7.6-6.8-17.4-10.8-28-10.8-25.6 0-47.3 22.4-47.3 50.1 0 27.7 21.4 50.1 46.9 50.1 11.4 0 21.6-4.6 29.4-12.2v9.7zm128-18.4l-17.8-12.6c-5.2 8-14 12.4-23.6 12.4-13.8 0-24.6-9-27.6-21.2h71.7c.4-3.2.6-6 .6-9.4 0-27.6-19-48.5-46.5-48.5-29.2 0-49.1 22.4-49.1 50.1 0 27.7 21.6 50.1 50.7 50.1 17.3-.1 32.4-8.1 41.6-20.9zm75.1-56.3v-20.6c-3.4-.6-6-1-9.4-1-12.8 0-23 6.4-26.2 16.6v-16.6l-21.6 4v92.3h22v-74.7h35.2zm-337.7-2c13.6 0 21.6 8.4 21.6 25.6v51.1h22v-54.1c0-23.8-12.8-43.7-38.7-43.7-12 0-22.4 4.6-30.2 12.2V339l-21 4v92.3h22v-63.9c5.1-7.7 13.7-12.7 24.3-12.7zm109.4 58.1c-16 0-28.6-13-28.6-29s12.6-29 28.6-29c10.2 0 19.2 5.4 24.2 13.6v30.8c-5 8.2-14 13.6-24.2 13.6zm109.9-59.1c12.6 0 21.4 8 23.6 20h-49.5c2.9-11.6 12.1-20 25.9-20zm-305.6 77.7h22v-95.3h-22v95.3zM335.5 300v135.4h23v-54.1h49.1v-22.2h-49.1v-36.9h55.7V300h-78.7zm106-1.8c-8 0-14.8 6.6-14.8 14.8 0 8.2 6.8 15 14.8 15 8.2 0 15.2-6.8 15.2-15s-7-14.8-15.2-14.8zm228.1-58.4c25.6 0 47.3-22.4 47.3-50.1 0-27.6-21.4-50.1-46.9-50.1-11.2 0-21.4 4.6-29.2 12V141l-21 4v128.6h22v-44.5c7.6 6.7 17.2 10.7 27.8 10.7zm152.2-20.9L804 206.3c-5.2 8-14 12.4-23.6 12.4-13.6 0-24.2-8.8-27.6-20.8h71.7c.4-3.4.6-6.2.6-9.8 0-27.6-19-48.5-46.5-48.5-29.2 0-49.1 22.4-49.1 50.1 0 27.6 21.6 50.1 50.7 50.1 17.4 0 32.4-8 41.6-20.9zm-320.6-56.2V142c-3.4-.6-6-1-9.4-1-12.6 0-22.8 6.4-26 16.6V141l-21.6 4v92.3H466v-74.7h35.2v.1zm395.8 0V142c-3.4-.6-6-1-9.4-1-12.6 0-22.8 6.4-26 16.6V141l-21.8 4v92.3h22v-74.7H897v.1zm75.9 70.7l-8.6-19c-4.2 2-10 4.4-16.8 4.4-11.8 0-17.8-7.4-17.8-19v-37.1H966V142h-36.3v-27.6l-21.8 4v81.3c0 24.4 13.4 40.1 38.9 40.1 10.8 0 19.9-3.6 26.1-6.4zm-417.4-14.7c-15.2 0-27.4-12.6-27.4-29s12.2-29 27.4-29c15 0 27.4 12.6 27.4 29s-12.4 29-27.4 29zm110.3 0c-10 0-19-5.2-24-13.2v-31.6c5-8 14-13.2 24-13.2 16 0 28.6 13 28.6 29s-12.6 29-28.6 29zm112.8-58.9c12.8 0 21.4 8 23.6 19.8h-49.5c2.9-11.3 12.1-19.8 25.9-19.8zm243.1 113.8l56.1-131.6h-24.4l-26.8 64.9-27.4-64.9h-24.6l40.3 91.3-17 40.3h23.8zm-466.2-33.8c27.2 0 49.7-22 49.7-50.1 0-28-22.4-50.1-49.7-50.1-27.4 0-49.9 22-49.9 50.1 0 28 22.4 50.1 49.9 50.1zM384.6 124.2c12.6 0 21.8 8.8 21.8 20.2 0 11.6-9.4 21-22.2 21h-25.4v-41.3h25.8v.1zm.8-22.2h-49.9v135.4h23.2v-49.7h25.8c25.2 0 45.7-19 45.7-43.3.1-24-20-42.4-44.8-42.4z">
                        </path>
                    </svg>
                </div>
                <div class="alert bg-dark-subtle p-1">
                    @if($pfData)
                        <div>
                            Date: {{ $pfData->start_date }} - {{ $pfData->end_date }}
                        </div>
                    @endif
                    @if($pfData)
                        <div>
                            Updated At: {{ $pfData->portal_data_updated_at }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Badges</th>
                            <th>Live Properties</th>
                            <th>PF Verified Listing Rate</th>
                            <th>AVG Listing Duration</th>
                            <th>Leads</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="">

                                    @if (($pfUsefulData['is_super_agent'] ?? "") === 1)
                                        <span class="badge rounded-pill bg-dark-subtle text-body fs-14"
                                          style="font-weight: 400;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 class="r-1xlzkty r-1472mwg r-lrsllp">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                      d="M13.9096 6.45575L12.4054 3.26454H12.407C12.2402 2.91182 11.7607 2.91182 11.5955 3.26454L10.0897 6.45575C10.024 6.59516 9.8973 6.69258 9.74977 6.71441L6.38697 7.22669C6.01654 7.28379 5.86901 7.7608 6.13681 8.03457L8.5695 10.5187C8.67534 10.6279 8.72505 10.7857 8.69939 10.9386L8.1253 14.4456C8.06115 14.8319 8.44923 15.1275 8.78118 14.9444L11.7896 13.2883C11.9211 13.2161 12.0782 13.2161 12.2097 13.2883L15.2181 14.9444C15.5501 15.1275 15.9381 14.8319 15.874 14.4456L15.2999 10.9386C15.2742 10.7841 15.3239 10.6279 15.4298 10.5187L17.8625 8.03457C18.1319 7.7608 17.9828 7.28379 17.6123 7.22669L14.2495 6.71441C14.102 6.69258 13.9753 6.59516 13.9096 6.45575ZM15.6043 18.9989V17.9316C15.6043 17.5384 15.2024 17.2206 14.7063 17.2206H13.3601V15.0859H10.6661V17.2206H9.31989C8.82375 17.2206 8.42188 17.5384 8.42188 17.9316V18.9989H15.6043ZM16.0909 19H7.90909C7.40683 19 7 19.2978 7 19.6661V21H17V19.6661C17 19.2978 16.5932 19 16.0909 19Z"
                                                      fill="currentColor"></path>
                                            </svg>
                                            Super Agent
                                        </span>
                                    @endif
                                    <div class="mt-1">
                                        PF Verified: {{ ($pfUsefulData['is_verified'] ?? '') == 1 ? 'Yes' : 'No' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="fs-16">{{ ($pfUsefulData['number_of_properties'] ?? 0) }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center">
                                    <div>
                                        <div>
                                            <div class="d-flex justify-content-center align-items-center gap-1"><span
                                                    class="fs-16">{{ ($pfUsefulData['verified_listing_rate'] ?? 0) }}%</span> Verified Listing Rate
                                            </div>
                                            <div class="progress animated-progress custom-progress customized-progress">
                                                <div class="progress-bar bg-{{ ($pfUsefulData['verified_listing_rate'] ?? 0) >= 85 ? 'success' : (($pfUsefulData['verified_listing_rate'] ?? 0) >= 65 ? 'warning' : 'danger') }}" role="progressbar"
                                                     style="width: {{ ($pfUsefulData['verified_listing_rate'] ?? 0) }}%"
                                                     aria-valuenow="{{ ($pfUsefulData['verified_listing_rate'] ?? 0) }}" aria-valuemin="0"
                                                     aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="fs-16">{{ ($pfUsefulData['avg_listing_duration'] ?? 0) }} days</span>
                            </td>
                            <td class="d-non">
                                <ul>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="ri-user-2-line"></i>
                                        <span
                                            class="fs-16">{{ ($pfUsefulData['total_leads'] ?? 0) }}</span>
                                        <div>
                                            Total Leads
                                        </div>
                                    </li>

                                    <li class="d-flex align-items-center gap-2"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-content="<div>Response Rate: {{ ($pfUsefulData['response_rate_emails'] ?? 0) }}%</div>"
                                        data-bs-html="true"
                                    >
                                        <i class="ri-mail-line"></i>
                                        <span
                                            class="fs-16">{{ ($pfUsefulData['total_email_leads'] ?? 0) }}</span>
                                        <div>
                                            Total Email Leads
                                        </div>
                                    </li>

                                    <li class="d-flex align-items-center gap-2"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-content="<div>Response Rate: {{ ($pfUsefulData['response_rate_calls'] ?? 0) }}%</div>"
                                        data-bs-html="true"
                                    >
                                        <i class="ri-phone-line"></i>
                                        <span
                                            class="fs-16">{{ ($pfUsefulData['total_call_leads'] ?? 0) }}</span>
                                        <div>
                                            Total Call Leads
                                        </div>
                                    </li>
                                    @php
                                        $responseTime = ($pfUsefulData['response_time_whatsapp'] ?? 0);

                                        if ($responseTime == 0) {
                                            $responseTime = 'No Response';
                                        } else {
                                            if ($responseTime > 0) {
                                                $responseTime =  formatSeconds($responseTime);
                                            } else {
                                                $responseTime = '-';
                                            }
                                        }
                                    @endphp
                                    <li class="d-flex align-items-center gap-2"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover"
                                        data-bs-content="<div>Response Rate: {{ ($pfUsefulData['response_rate_whatsapp'] ?? 0) }}%<br>Response Time:{{ $responseTime }}</div>"
                                        data-bs-html="true"
                                    >
                                        <i class="ri-whatsapp-line"></i>
                                        <span class="fs-16">{{ ($pfUsefulData['total_whatsapp_leads'] ?? 0) }}</span>
                                        <div>
                                            Total WhatsApp Leads
                                        </div>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="accordion accordion-flush" id="accordionPFAddInfo">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionPFAddInfoCollapse" aria-expanded="false"
                                        aria-controls="accordionPFAddInfoCollapse">
                                    <h5>Additional Information</h5>
                                </button>
                            </h2>
                            <div id="accordionPFAddInfoCollapse" class="accordion-collapse collapse"
                                 aria-labelledby="accordionPFAddInfoCollapse"
                                 data-bs-parent="#accordionPFAddInfo">
                                <div class="accordion-body">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-center align-items-center gap-5">
                                                <div>
                                                    <div id="pf_listing_quality_score_chart"></div>
                                                    <div class="text-center"> Listing Quality Score</div>
                                                </div>
                                                <div>
                                                    <div id="pf_response_chart"></div>
                                                    <div class="text-center"> Overall Response Rate</div>
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
    </div>
    */ ?>
    <div class="card" id="bayut-card">
        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center gap-3">
                <img src="{{asset('assets/images/bayut/bayut-logo.svg')}}"
                     class="thumbnail" style="    width: 100px;">
                <div class="alert bg-dark-subtle p-1">
                    @if($bayutData)
                        <div>
                            Date: {{ $bayutData->start_date }} - {{ $bayutData->end_date }}
                        </div>
                    @endif
                    @if($bayutLatestData)
                        <div>
                            Updated At: {{ $bayutLatestData->portal_data_updated_at }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Badges</th>
                            <th>TruBroker™ Progress:
                                {{ date('F', strtotime('+1 month', strtotime(now()))) }}
                            </th>
                            <th>Listings</th>
                            <th class="d-non">Call Answer Rate</th>
                            <th class="d-non">Whatsapp Response</th>
                            <th class="d-non">Products Applied</th>
                            <th class="d-non">Leads</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="d-flex justify-content-start align-items-center gap-1">
                                    @if(($bayutLatestUsefulData['trubroker'] ?? '') == 'Active')
                                        <div class="" style="padding-left: 4px; padding-right: 4px;">
                                            <svg width="84" height="22" viewBox="0 0 84 22" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg" format="plural">
                                                <rect width="84" height="22" rx="5"
                                                      fill="url(#paint0_linear_666_62002)"></rect>
                                                <path
                                                    d="M11.066 16V8.374H8.12V7.36H15.08V8.374H12.134V16H11.066ZM15.4377 16V9.52H16.3917V11.086L16.2357 10.882C16.3117 10.682 16.4097 10.498 16.5297 10.33C16.6537 10.162 16.7917 10.024 16.9437 9.916C17.1117 9.78 17.3017 9.676 17.5137 9.604C17.7257 9.532 17.9417 9.49 18.1617 9.478C18.3817 9.462 18.5877 9.476 18.7797 9.52V10.522C18.5557 10.462 18.3097 10.446 18.0417 10.474C17.7737 10.502 17.5257 10.6 17.2977 10.768C17.0897 10.916 16.9277 11.096 16.8117 11.308C16.6997 11.52 16.6217 11.75 16.5777 11.998C16.5337 12.242 16.5117 12.492 16.5117 12.748V16H15.4377ZM22.3742 16.168C21.9582 16.168 21.6002 16.102 21.3002 15.97C21.0042 15.838 20.7582 15.662 20.5622 15.442C20.3662 15.222 20.2122 14.978 20.1002 14.71C19.9882 14.442 19.9082 14.168 19.8602 13.888C19.8162 13.608 19.7942 13.346 19.7942 13.102V9.52H20.8742V12.766C20.8742 13.07 20.9022 13.364 20.9582 13.648C21.0142 13.932 21.1082 14.188 21.2402 14.416C21.3762 14.644 21.5562 14.824 21.7802 14.956C22.0082 15.088 22.2922 15.154 22.6322 15.154C22.9002 15.154 23.1442 15.108 23.3642 15.016C23.5842 14.924 23.7722 14.788 23.9282 14.608C24.0842 14.424 24.2042 14.192 24.2882 13.912C24.3722 13.632 24.4142 13.306 24.4142 12.934L25.1162 13.15C25.1162 13.774 25.0022 14.312 24.7742 14.764C24.5502 15.216 24.2322 15.564 23.8202 15.808C23.4122 16.048 22.9302 16.168 22.3742 16.168ZM24.5342 16V14.266H24.4142V9.52H25.4882V16H24.5342ZM27.1681 16V7.36H30.5941C31.1781 7.36 31.6561 7.478 32.0281 7.714C32.4041 7.946 32.6821 8.244 32.8621 8.608C33.0461 8.972 33.1381 9.348 33.1381 9.736C33.1381 10.212 33.0241 10.616 32.7961 10.948C32.5721 11.28 32.2661 11.504 31.8781 11.62V11.32C32.4221 11.444 32.8341 11.702 33.1141 12.094C33.3981 12.486 33.5401 12.932 33.5401 13.432C33.5401 13.944 33.4421 14.392 33.2461 14.776C33.0501 15.16 32.7581 15.46 32.3701 15.676C31.9861 15.892 31.5101 16 30.9421 16H27.1681ZM28.6321 14.638H30.7621C31.0141 14.638 31.2401 14.588 31.4401 14.488C31.6401 14.384 31.7961 14.24 31.9081 14.056C32.0241 13.868 32.0821 13.646 32.0821 13.39C32.0821 13.158 32.0321 12.95 31.9321 12.766C31.8321 12.582 31.6861 12.436 31.4941 12.328C31.3021 12.216 31.0721 12.16 30.8041 12.16H28.6321V14.638ZM28.6321 10.81H30.5761C30.7841 10.81 30.9701 10.77 31.1341 10.69C31.2981 10.61 31.4281 10.492 31.5241 10.336C31.6201 10.18 31.6681 9.988 31.6681 9.76C31.6681 9.46 31.5721 9.21 31.3801 9.01C31.1881 8.81 30.9201 8.71 30.5761 8.71H28.6321V10.81ZM34.7384 16V9.52H36.0164V11.098L35.8604 10.894C35.9404 10.678 36.0464 10.482 36.1784 10.306C36.3144 10.126 36.4764 9.978 36.6644 9.862C36.8244 9.754 37.0004 9.67 37.1924 9.61C37.3884 9.546 37.5884 9.508 37.7924 9.496C37.9964 9.48 38.1944 9.488 38.3864 9.52V10.87C38.1944 10.814 37.9724 10.796 37.7204 10.816C37.4724 10.836 37.2484 10.906 37.0484 11.026C36.8484 11.134 36.6844 11.272 36.5564 11.44C36.4324 11.608 36.3404 11.8 36.2804 12.016C36.2204 12.228 36.1904 12.458 36.1904 12.706V16H34.7384ZM42.2762 16.18C41.6282 16.18 41.0622 16.034 40.5782 15.742C40.0942 15.45 39.7182 15.048 39.4502 14.536C39.1862 14.02 39.0542 13.428 39.0542 12.76C39.0542 12.08 39.1902 11.484 39.4622 10.972C39.7342 10.46 40.1122 10.06 40.5962 9.772C41.0802 9.484 41.6402 9.34 42.2762 9.34C42.9282 9.34 43.4962 9.486 43.9802 9.778C44.4642 10.07 44.8402 10.474 45.1082 10.99C45.3762 11.502 45.5102 12.092 45.5102 12.76C45.5102 13.432 45.3742 14.026 45.1022 14.542C44.8342 15.054 44.4582 15.456 43.9742 15.748C43.4902 16.036 42.9242 16.18 42.2762 16.18ZM42.2762 14.824C42.8522 14.824 43.2802 14.632 43.5602 14.248C43.8402 13.864 43.9802 13.368 43.9802 12.76C43.9802 12.132 43.8382 11.632 43.5542 11.26C43.2702 10.884 42.8442 10.696 42.2762 10.696C41.8882 10.696 41.5682 10.784 41.3162 10.96C41.0682 11.132 40.8842 11.374 40.7642 11.686C40.6442 11.994 40.5842 12.352 40.5842 12.76C40.5842 13.388 40.7262 13.89 41.0102 14.266C41.2982 14.638 41.7202 14.824 42.2762 14.824ZM46.8202 16L46.8322 7.36H48.2962V12.64L50.6662 9.52H52.4722L49.9582 12.76L52.6882 16H50.7742L48.2962 12.88V16H46.8202ZM56.118 16.18C55.462 16.18 54.886 16.038 54.39 15.754C53.894 15.47 53.506 15.076 53.226 14.572C52.95 14.068 52.812 13.488 52.812 12.832C52.812 12.124 52.948 11.51 53.22 10.99C53.492 10.466 53.87 10.06 54.354 9.772C54.838 9.484 55.398 9.34 56.034 9.34C56.706 9.34 57.276 9.498 57.744 9.814C58.216 10.126 58.566 10.568 58.794 11.14C59.022 11.712 59.108 12.386 59.052 13.162H57.618V12.634C57.614 11.93 57.49 11.416 57.246 11.092C57.002 10.768 56.618 10.606 56.094 10.606C55.502 10.606 55.062 10.79 54.774 11.158C54.486 11.522 54.342 12.056 54.342 12.76C54.342 13.416 54.486 13.924 54.774 14.284C55.062 14.644 55.482 14.824 56.034 14.824C56.39 14.824 56.696 14.746 56.952 14.59C57.212 14.43 57.412 14.2 57.552 13.9L58.98 14.332C58.732 14.916 58.348 15.37 57.828 15.694C57.312 16.018 56.742 16.18 56.118 16.18ZM53.886 13.162V12.07H58.344V13.162H53.886ZM60.3908 16V9.52H61.6688V11.098L61.5128 10.894C61.5928 10.678 61.6988 10.482 61.8308 10.306C61.9668 10.126 62.1288 9.978 62.3168 9.862C62.4768 9.754 62.6528 9.67 62.8448 9.61C63.0408 9.546 63.2408 9.508 63.4448 9.496C63.6488 9.48 63.8468 9.488 64.0388 9.52V10.87C63.8468 10.814 63.6248 10.796 63.3728 10.816C63.1248 10.836 62.9008 10.906 62.7008 11.026C62.5008 11.134 62.3368 11.272 62.2088 11.44C62.0848 11.608 61.9928 11.8 61.9328 12.016C61.8728 12.228 61.8428 12.458 61.8428 12.706V16H60.3908ZM67.4514 16.18C66.6514 16.18 66.0014 16 65.5014 15.64C65.0014 15.28 64.6974 14.774 64.5894 14.122L66.0654 13.894C66.1414 14.214 66.3094 14.466 66.5694 14.65C66.8294 14.834 67.1574 14.926 67.5534 14.926C67.9014 14.926 68.1694 14.858 68.3574 14.722C68.5494 14.582 68.6454 14.392 68.6454 14.152C68.6454 14.004 68.6094 13.886 68.5374 13.798C68.4694 13.706 68.3174 13.618 68.0814 13.534C67.8454 13.45 67.4834 13.344 66.9954 13.216C66.4514 13.072 66.0194 12.918 65.6994 12.754C65.3794 12.586 65.1494 12.388 65.0094 12.16C64.8694 11.932 64.7994 11.656 64.7994 11.332C64.7994 10.928 64.9054 10.576 65.1174 10.276C65.3294 9.976 65.6254 9.746 66.0054 9.586C66.3854 9.422 66.8334 9.34 67.3494 9.34C67.8534 9.34 68.2994 9.418 68.6874 9.574C69.0794 9.73 69.3954 9.952 69.6354 10.24C69.8754 10.528 70.0234 10.866 70.0794 11.254L68.6034 11.518C68.5674 11.242 68.4414 11.024 68.2254 10.864C68.0134 10.704 67.7294 10.614 67.3734 10.594C67.0334 10.574 66.7594 10.626 66.5514 10.75C66.3434 10.87 66.2394 11.04 66.2394 11.26C66.2394 11.384 66.2814 11.49 66.3654 11.578C66.4494 11.666 66.6174 11.754 66.8694 11.842C67.1254 11.93 67.5054 12.038 68.0094 12.166C68.5254 12.298 68.9374 12.45 69.2454 12.622C69.5574 12.79 69.7814 12.992 69.9174 13.228C70.0574 13.464 70.1274 13.75 70.1274 14.086C70.1274 14.738 69.8894 15.25 69.4134 15.622C68.9414 15.994 68.2874 16.18 67.4514 16.18ZM76.0796 11.86V7.36H76.7516L78.3956 10.102L80.0396 7.36H80.7116L80.7176 11.86H79.9376V8.926L78.6476 11.122H78.1436L76.8596 8.926V11.86H76.0796ZM72.8696 11.86V8.116H71.3216V7.36H75.2276V8.116H73.6856V11.86H72.8696Z"
                                                    fill="white"></path>
                                                <defs>
                                                    <linearGradient id="paint0_linear_666_62002" x1="79.4063"
                                                                    y1="-5.17647" x2="6.40824" y2="36.4682"
                                                                    gradientUnits="userSpaceOnUse">
                                                        <stop offset="0.196272" stop-color="#053940"></stop>
                                                        <stop offset="0.489583" stop-color="#00745F"></stop>
                                                        <stop offset="0.720273" stop-color="#033F46"></stop>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                    @endif
                                    @if(($bayutLatestUsefulData['quality_lister'] ?? '') == 'Active')
                                        <div>
                                            <span class="quality-listers" title="Quality Listers">
                                                <span>
                                                    <svg width="1em" height="1em" viewBox="0 0 18 18"
                                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                         aria-hidden="true" focusable="false" class="">
                                                        <path
                                                            d="M5.7406 6.57245L9.00797 15.7129L12.2579 6.57175L5.7406 6.57245Z"
                                                            fill="#64B7E3"></path>
                                                        <path
                                                            d="M12.2579 6.57055L5.7406 6.56986L7.2363 1.99998H10.7563L12.2579 6.57055Z"
                                                            fill="#90CCEB"></path>
                                                        <path d="M7.24553 2L5.78875 6.57055H1L4.85116 2H7.24553Z"
                                                              fill="#BCE0F3"></path>
                                                        <path
                                                            d="M10.7522 2L12.209 6.57055H16.9977L13.1466 2H10.7522Z"
                                                            fill="#64B7E3"></path>
                                                        <path
                                                            d="M8.99855 15.7129L5.78875 6.57175H1L8.99855 15.7129Z"
                                                            fill="#90CCEB"></path>
                                                        <path
                                                            d="M9.00175 15.7129L12.2116 6.57175H17.0003L9.00175 15.7129Z"
                                                            fill="#2399D8"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </div>
                                    @endif

                                    @if(($bayutLatestUsefulData['responsive_broker'] ?? '') == 'Active')
                                        <div>
                                            <span class="responsive-broker" title="Responsive Broker">
                                                <span>
                                                    <svg width="1em" height="1em" viewBox="0 0 18 18"
                                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                         aria-hidden="true" focusable="false" class="">
                                                        <path
                                                            d="M14.128 15.4592C12.7141 15.4592 11.3358 15.1441 9.99309 14.5139C8.65041 13.8837 7.46118 13.0506 6.4254 12.0149C5.38962 10.9791 4.55661 9.78985 3.92637 8.44717C3.29614 7.10449 2.98102 5.72619 2.98102 4.31227C2.98102 4.11498 3.04678 3.95057 3.17831 3.81904C3.30984 3.68751 3.47425 3.62175 3.67154 3.62175H6.33497C6.48842 3.62175 6.62543 3.67107 6.746 3.76972C6.86657 3.86836 6.93781 3.99441 6.95973 4.14786L7.3872 6.4496C7.40912 6.60305 7.40638 6.74279 7.37898 6.86884C7.35157 6.99489 7.28855 7.10723 7.1899 7.20588L5.61157 8.8171C6.07192 9.60626 6.64735 10.3461 7.33787 11.0366C8.02839 11.7271 8.79016 12.3245 9.62317 12.8287L11.1686 11.2832C11.2673 11.1846 11.3961 11.1106 11.555 11.0613C11.7139 11.012 11.8701 10.9983 12.0235 11.0202L14.2924 11.4805C14.4459 11.5134 14.5719 11.5874 14.6705 11.7025C14.7692 11.8176 14.8185 11.9518 14.8185 12.1053V14.7687C14.8185 14.966 14.7527 15.1304 14.6212 15.262C14.4897 15.3935 14.3253 15.4592 14.128 15.4592Z"
                                                            fill="#B16AB8"></path>
                                                        <path
                                                            d="M5.61385 8.82003C6.07387 9.60793 6.64864 10.3466 7.33818 11.0362C8.02771 11.7257 8.78828 12.3224 9.6199 12.8261L9.62221 12.8284L8.74573 13.8426C7.90176 13.3265 7.12841 12.7171 6.4257 12.0144C5.74498 11.3337 5.15183 10.5867 4.64627 9.7734L5.61089 8.81708L5.61385 8.82003Z"
                                                            fill="#B16AB8"></path>
                                                        <path
                                                            d="M16 5.65218H12.3478V9.30437L13.4891 6.79349L16 5.65218Z"
                                                            fill="#B16AB8"></path>
                                                        <path d="M16 5.65218H12.3478V2L13.4891 4.51088L16 5.65218Z"
                                                              fill="#DEBFE0"></path>
                                                        <path
                                                            d="M8.69559 5.65218H12.3478V9.30437L11.2065 6.79349L8.69559 5.65218Z"
                                                            fill="#82278A"></path>
                                                        <path
                                                            d="M8.69559 5.65218H12.3478V2L11.2065 4.51088L8.69559 5.65218Z"
                                                            fill="#B16AB8"></path>
                                                        <path
                                                            d="M4.43515 11.7393L5.19602 13.4132L6.86993 14.1741L5.19602 14.935L4.43514 16.6089L3.67427 14.935L2.00036 14.1741L3.67427 13.4132L4.43515 11.7393Z"
                                                            fill="#C894CC"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ date('F') }} Activations
                                <br>
                                <span class="fs-16"><b>{{ ($bayutLatestUsefulData['validation_activations'] ?? '') }}</span>
                            </td>
                            <td>
                                <ul>
                                    <li class="d-flex align-items-center gap-2"><span class="fs-16">{{ ($bayutLatestUsefulData['total_active_listings'] ?? 0) }}</span>
                                        Live
                                    </li>
                                    <li class="d-flex align-items-center gap-2"><span class="fs-16">{{ ($bayutLatestUsefulData['active_trucheck_listings'] ?? 0) }}</span>
                                        TruCheck™
                                    </li>
                                    <li class="d-flex align-items-center gap-2"><span class="fs-16">{{ ($bayutLatestUsefulData['active_checked_listings'] ?? 0) }}</span>
                                        Checked
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center"
                                     data-bs-toggle="popover"
                                     data-bs-trigger="hover"
                                     data-bs-content="<div>
                                                    Proxy Number: {{ ($bayutUsefulData['proxy_number'] ?? '') }}<br>
                                                    Agent Number: {{ ($bayutUsefulData['mobile'] ?? '') }}<br>
                                                     Not Connected Calls:{{ ($bayutUsefulData['not_connected_call'] ?? 0) }}<br>
                                                     Missed Calls:{{ ($bayutUsefulData['missed_calls'] ?? 0) }}<br>
                                                     Answered Calls:{{ ($bayutUsefulData['answered_calls'] ?? 0) }}<br>
                                                     Total Calls:{{ ($bayutUsefulData['total_calls'] ?? 0) }}
                                                    </div>"
                                     data-bs-html="true"
                                >
                                    <div>
                                        <div class="progress-ring-container">
                                            <svg width="50" height="50" class="progress-ring">
                                                <circle class="background" cx="25" cy="25" r="20"></circle>
                                                <circle class="progress-{{ ($bayutUsefulData['call_answer_rate'] ?? 0) >= 85 ? 'success' : (($bayutUsefulData['call_answer_rate'] ?? 0) >= 65 ? 'warning' : 'danger') }}" cx="25" cy="25" r="20"
                                                        stroke-dasharray="125.66"
                                                        stroke-dashoffset="{{ 125.66 * (1 - ($bayutUsefulData['call_answer_rate'] ?? 0) / 100) }}"></circle>
                                            </svg>
                                            <text x="50%" y="50%" class="percentage-text">{{ ($bayutUsefulData['call_answer_rate'] ?? 0) }}%</text>
                                        </div>
                                    </div>
                                    <ul>
                                        <li class="d-flex align-items-center gap-2">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M10.6375 11.25C9.38333 11.25 8.16086 10.9704 6.97008 10.4112C5.77892 9.85233 4.72406 9.11344 3.8055 8.1945C2.88656 7.27594 2.14767 6.22108 1.58883 5.02992C1.02961 3.83914 0.75 2.61667 0.75 1.3625C0.75 1.1875 0.808333 1.04167 0.925 0.925C1.04167 0.808333 1.1875 0.75 1.3625 0.75H3.725C3.86111 0.75 3.98264 0.79375 4.08958 0.88125C4.19653 0.96875 4.25972 1.08056 4.27917 1.21667L4.65833 3.25833C4.67778 3.39444 4.67544 3.51831 4.65133 3.62992C4.62683 3.74192 4.57083 3.84167 4.48333 3.92917L3.08333 5.35833C3.49167 6.05833 4.00208 6.71458 4.61458 7.32708C5.22708 7.93958 5.90278 8.46944 6.64167 8.91667L8.0125 7.54583C8.1 7.45833 8.21433 7.39261 8.3555 7.34867C8.49628 7.30511 8.63472 7.29306 8.77083 7.3125L10.7833 7.72083C10.9194 7.75 11.0312 7.81553 11.1187 7.91742C11.2062 8.01969 11.25 8.13889 11.25 8.275V10.6375C11.25 10.8125 11.1917 10.9583 11.075 11.075C10.9583 11.1917 10.8125 11.25 10.6375 11.25Z"
                                                    fill="#748AA1"></path>
                                            </svg>
                                            <span class="fs-16">{{ ($bayutUsefulData['answered_calls'] ?? 0) }}</span>
                                            Answered
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.00013 5.65005L4.11263 2.76255V4.27922H2.94596V0.779215H6.44596V1.94588H4.9293L6.98555 4.00213L10.2814 0.706299L11.1126 1.53755L7.00013 5.65005ZM7.00013 6.00005C8.14735 6.00005 9.27746 6.23105 10.3905 6.69305C11.5039 7.15466 12.4932 7.84727 13.3585 8.77088C13.4751 8.88755 13.5335 9.02366 13.5335 9.17922C13.5335 9.33477 13.4751 9.47088 13.3585 9.58755L12.0168 10.9C11.9099 11.007 11.786 11.0653 11.6452 11.075C11.504 11.0848 11.3751 11.0459 11.2585 10.9584L9.5668 9.67505C9.48902 9.61671 9.43069 9.54633 9.3918 9.46388C9.35291 9.38105 9.33346 9.29588 9.33346 9.20838V7.54588C8.96402 7.42921 8.58485 7.33685 8.19596 7.2688C7.80707 7.20074 7.40846 7.16672 7.00013 7.16672C6.5918 7.16672 6.19319 7.20074 5.8043 7.2688C5.41541 7.33685 5.03624 7.42921 4.6668 7.54588V9.20838C4.6668 9.29588 4.64969 9.38105 4.61546 9.46388C4.58163 9.54633 4.52582 9.61671 4.44805 9.67505L2.7418 10.9584C2.62513 11.0459 2.49641 11.0848 2.35563 11.075C2.21446 11.0653 2.09041 11.007 1.98346 10.9L0.641797 9.58755C0.52513 9.47088 0.466797 9.33477 0.466797 9.17922C0.466797 9.02366 0.52513 8.88755 0.641797 8.77088C1.49735 7.84727 2.48416 7.15466 3.60221 6.69305C4.72027 6.23105 5.85291 6.00005 7.00013 6.00005Z"
                                                    fill="#748AA1"></path>
                                            </svg>
                                            <span class="fs-16">{{ ($bayutUsefulData['missed_calls'] ?? 0) }}</span>
                                            Missed
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div
                                    data-bs-toggle="popover"
                                    data-bs-trigger="hover"
                                    data-bs-content="<div>
                                                    Delivered Messages: {{ ($bayutUsefulData['total_enquires'] ?? 0) }}<br>
                                                    Answered: {{ ($bayutUsefulData['unanswered_enquires'] ?? 0) }}<br>
                                                     Unanswered:{{ ($bayutUsefulData['total_enquires'] ?? 0) - ($bayutUsefulData['unanswered_enquires'] ?? 0) }}
                                                    </div>"
                                    data-bs-html="true"
                                >
                                    <div class="d-flex justify-content-center align-items-center gap-1"><span
                                            class="fs-16">{{ ($bayutUsefulData['whatsapp_response_rate'] ?? 0) }}%</span> Response Rate
                                    </div>
                                    <div class="progress animated-progress custom-progress customized-progress">
                                        <div class="progress-bar bg-{{ ($bayutUsefulData['whatsapp_response_rate'] ?? 0) >= 85 ? 'success' : (($bayutUsefulData['whatsapp_response_rate'] ?? 0) >= 65 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{($bayutUsefulData['whatsapp_response_rate'] ?? 0)}}%"
                                             aria-valuenow="{{ ($bayutUsefulData['whatsapp_response_rate'] ?? 0) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-center gap-1">
                                        <div>
                                            <svg width="1em" height="1em" viewBox="0 0 14 15" fill="currentColor"
                                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                                 focusable="false" class="">
                                                <mask id="mask0_217_65699" maskUnits="userSpaceOnUse" x="0" y="0"
                                                      width="14" height="15">
                                                    <rect y="0.984863" width="14" height="14" fill="#D9D9D9"></rect>
                                                </mask>
                                                <g mask="url(#mask0_217_65699)">
                                                    <path
                                                        d="M7 13.2349C6.28056 13.2349 5.60243 13.0963 4.96563 12.8192C4.32882 12.5422 3.77222 12.1654 3.29583 11.689C2.81944 11.2126 2.44271 10.656 2.16563 10.0192C1.88854 9.38243 1.75 8.70431 1.75 7.98486C1.75 7.55709 1.79861 7.14146 1.89583 6.73799C1.99306 6.33452 2.13403 5.95535 2.31875 5.60049C2.50347 5.24563 2.72465 4.91507 2.98229 4.60882C3.23993 4.30257 3.52917 4.02792 3.85 3.78486L7.81667 7.75153L7 8.5682L3.85 5.4182C3.55833 5.7682 3.32986 6.15952 3.16458 6.59215C2.99931 7.02479 2.91667 7.48903 2.91667 7.98486C2.91667 9.11264 3.31528 10.0751 4.1125 10.8724C4.90972 11.6696 5.87222 12.0682 7 12.0682C8.12778 12.0682 9.09028 11.6696 9.8875 10.8724C10.6847 10.0751 11.0833 9.11264 11.0833 7.98486C11.0833 6.94459 10.7503 6.04771 10.0844 5.29424C9.4184 4.54077 8.58472 4.09597 7.58333 3.95986V5.0682H6.41667V2.73486H7C7.71944 2.73486 8.39757 2.8734 9.03437 3.15049C9.67118 3.42757 10.2278 3.80431 10.7042 4.2807C11.1806 4.75709 11.5573 5.31368 11.8344 5.95049C12.1115 6.58729 12.25 7.26542 12.25 7.98486C12.25 8.70431 12.1115 9.38243 11.8344 10.0192C11.5573 10.656 11.1806 11.2126 10.7042 11.689C10.2278 12.1654 9.67118 12.5422 9.03437 12.8192C8.39757 13.0963 7.71944 13.2349 7 13.2349ZM7 11.4849C6.83472 11.4849 6.69618 11.429 6.58437 11.3172C6.47257 11.2053 6.41667 11.0668 6.41667 10.9015C6.41667 10.7363 6.47257 10.5977 6.58437 10.4859C6.69618 10.3741 6.83472 10.3182 7 10.3182C7.16528 10.3182 7.30382 10.3741 7.41563 10.4859C7.52743 10.5977 7.58333 10.7363 7.58333 10.9015C7.58333 11.0668 7.52743 11.2053 7.41563 11.3172C7.30382 11.429 7.16528 11.4849 7 11.4849ZM9.91667 8.5682C9.75139 8.5682 9.61285 8.51229 9.50104 8.40049C9.38924 8.28868 9.33333 8.15014 9.33333 7.98486C9.33333 7.81959 9.38924 7.68104 9.50104 7.56924C9.61285 7.45743 9.75139 7.40153 9.91667 7.40153C10.0819 7.40153 10.2205 7.45743 10.3323 7.56924C10.4441 7.68104 10.5 7.81959 10.5 7.98486C10.5 8.15014 10.4441 8.28868 10.3323 8.40049C10.2205 8.51229 10.0819 8.5682 9.91667 8.5682ZM4.08333 8.5682C3.91806 8.5682 3.77951 8.51229 3.66771 8.40049C3.5559 8.28868 3.5 8.15014 3.5 7.98486C3.5 7.81959 3.5559 7.68104 3.66771 7.56924C3.77951 7.45743 3.91806 7.40153 4.08333 7.40153C4.24861 7.40153 4.38715 7.45743 4.49896 7.56924C4.61076 7.68104 4.66667 7.81959 4.66667 7.98486C4.66667 8.15014 4.61076 8.28868 4.49896 8.40049C4.38715 8.51229 4.24861 8.5682 4.08333 8.5682Z"
                                                        fill="black" fill-opacity="0.65"></path>
                                                </g>
                                            </svg>
                                            Response Time: {{ ($bayutUsefulData['whatsapp_response_time'] ?? 0) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <ul>
                                    <li class="d-flex align-items-center gap-2">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16"
                                             fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                             aria-hidden="true" focusable="false" class="">
                                            <path
                                                d="M8 16C12.4183 16 16 12.4183 16 8C16 3.58172 12.4183 0 8 0C3.58172 0 0 3.58172 0 8C0 12.4183 3.58172 16 8 16Z"
                                                fill="#2399D8"></path>
                                            <path
                                                d="M14.0176 10.5766H7.71753C7.91753 9.8766 8.11738 9.17656 8.31738 8.47656C8.71738 8.17656 9.11758 7.97661 9.51758 7.77661C9.61758 7.67661 9.81758 7.6766 10.0176 7.5766H10.1174C10.1174 7.6766 10.2174 7.97661 9.61743 9.27661C9.41743 9.57661 9.51753 9.77659 9.71753 9.87659C10.0175 10.0766 10.1175 9.87661 10.7175 9.27661C11.1175 8.77661 11.5176 8.27659 12.0176 7.87659C12.4176 7.67659 12.5174 7.67658 12.6174 7.67658C12.7174 7.67658 12.8176 8.07661 12.5176 8.77661C12.4176 8.97661 12.5175 9.17661 12.7175 9.27661C12.9175 9.37661 13.1175 9.2766 13.2175 9.0766C13.5175 8.1766 13.5176 7.3766 13.0176 7.0766C12.8176 6.8766 12.3174 6.67658 11.6174 7.17658C11.4174 7.37658 11.2176 7.57661 11.0176 7.77661C11.1176 7.57661 10.9174 7.17656 10.8174 6.97656C10.4174 6.47656 9.61748 6.77659 9.41748 6.87659C9.21748 6.97659 8.91743 7.07661 8.61743 7.27661C8.71743 6.97661 8.71738 6.77659 8.81738 6.37659C8.81738 6.27659 8.91748 6.1766 8.91748 6.0766C9.51748 5.8766 10.0174 5.4766 10.3174 5.0766C11.4174 3.5766 11.3174 2.77656 11.1174 2.47656C11.0174 2.17656 10.7174 1.97656 10.3174 1.97656C9.71738 1.97656 9.21748 2.37656 8.91748 2.97656C8.51748 3.87656 8.31743 4.77658 8.11743 5.67658C7.31743 6.07658 6.41758 6.37656 5.51758 6.47656C5.31758 6.47656 5.11743 6.67661 5.11743 6.77661C5.11743 6.97661 5.31758 7.17658 5.51758 7.17658C5.51758 7.17658 5.51743 7.17658 5.61743 7.17658C6.41743 7.17658 7.21748 6.9766 7.91748 6.5766C7.81748 6.9766 7.71743 7.47659 7.61743 7.87659C6.61743 8.57659 5.81758 9.37659 5.01758 10.3766H2.21753C2.01753 10.3766 2.01758 10.4766 2.01758 10.5766C2.01758 10.6766 2.11753 10.7766 2.21753 10.7766H4.81738C4.51738 11.4766 4.01758 12.6766 4.51758 13.2766C4.71758 13.4766 4.91738 13.5766 5.31738 13.5766C5.41738 13.5766 5.51743 13.5766 5.61743 13.5766C6.51743 13.3766 7.21753 12.2766 7.71753 10.7766H14.2175C14.4175 10.7766 14.4175 10.6766 14.4175 10.5766C14.3175 10.6766 14.2176 10.5766 14.0176 10.5766ZM9.51758 3.47656C9.71758 3.07656 10.0175 2.87659 10.2175 2.87659C10.3175 2.87659 10.3174 2.87656 10.3174 2.97656C10.4174 3.17656 10.4176 3.77661 9.51758 4.77661C9.41758 4.97661 9.11748 5.17661 8.91748 5.27661C9.01748 4.67661 9.21758 4.07656 9.51758 3.47656ZM7.31738 9.27661C7.21738 9.77661 7.11748 10.1766 6.91748 10.6766H6.01758C6.31758 10.0766 6.81738 9.67661 7.31738 9.27661ZM5.31738 13.0766C5.21738 13.0766 5.11743 13.0766 5.11743 13.0766C5.01743 12.3766 5.31753 11.6766 5.71753 11.0766H6.81738C6.31738 12.1766 5.81738 12.9766 5.31738 13.0766Z"
                                                fill="white"></path>
                                        </svg>
                                        <span class="fs-16">{{ ($bayutLatestUsefulData['active_signature_listings'] ?? 0) }}</span>
                                        Signature
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16"
                                             fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                             aria-hidden="true" focusable="false" class="">
                                            <g clip-path="url(#clip0_2460_166931)">
                                                <path
                                                    d="M16 8C16 3.58172 12.4183 0 8 0C3.58172 0 0 3.58172 0 8C0 12.4183 3.58172 16 8 16C12.4183 16 16 12.4183 16 8Z"
                                                    fill="#EC4C47"></path>
                                                <path
                                                    d="M7.99998 14.3996C11.5346 14.3996 14.4 11.5342 14.4 7.99961C14.4 4.46499 11.5346 1.59961 7.99998 1.59961C4.46535 1.59961 1.59998 4.46499 1.59998 7.99961C1.59998 11.5342 4.46535 14.3996 7.99998 14.3996Z"
                                                    fill="#EC4C47"></path>
                                                <path
                                                    d="M10.0143 7.13724C10.0143 7.13724 9.54099 7.38372 9.35499 6.49457C9.35499 6.49457 9.10547 5.7812 9.35915 5.70664C9.35915 5.70664 9.24035 5.0132 8.19227 6.39947C7.71944 7.03634 7.28516 7.70096 6.89176 8.38977C6.89176 8.38977 5.86401 10.1472 5.51276 10.353C5.51276 10.353 4.6068 11.5642 4.01162 11.2746C4.01162 11.2746 3.69105 11.2031 3.98025 11.6267C3.98025 11.6267 4.86847 13.0377 6.77992 11.0888C6.77992 11.0888 9.95195 8.92489 10.0147 7.13419L10.0143 7.13724ZM7.43775 9.95713C7.43187 9.95329 7.42682 9.94841 7.42292 9.94257C7.41902 9.93681 7.41635 9.93025 7.41506 9.92337C7.41084 9.90697 7.41111 9.88969 7.41585 9.87345C7.4206 9.85721 7.42964 9.84249 7.44203 9.83097C7.87837 9.50585 8.26283 9.11649 8.58219 8.67593C8.60163 8.64817 8.63035 8.64649 8.64683 8.67265C8.65483 8.68777 8.65851 8.70481 8.65755 8.72185C8.65659 8.73897 8.65107 8.75545 8.64147 8.76969C8.31379 9.22193 7.91934 9.62185 7.47158 9.95561C7.46679 9.95937 7.46107 9.96169 7.45504 9.96241C7.44901 9.96313 7.44291 9.96217 7.43739 9.95961L7.43775 9.95713ZM9.36299 8.35937C8.84603 9.07161 8.22419 9.70153 7.51872 10.2277C7.51371 10.2318 7.50758 10.2343 7.50113 10.2348C7.49469 10.2355 7.48822 10.234 7.48257 10.2309C7.47669 10.2271 7.47164 10.2222 7.46774 10.2164C7.46384 10.2106 7.46116 10.204 7.45988 10.1972C7.45565 10.1807 7.45592 10.1635 7.46067 10.1472C7.46542 10.131 7.47446 10.1163 7.48684 10.1047C8.18243 9.58737 8.79499 8.96681 9.30331 8.26457C9.30691 8.25857 9.31171 8.25337 9.31755 8.24953C9.32339 8.24569 9.33003 8.24329 9.33699 8.24241C9.34331 8.24233 9.34963 8.24401 9.35507 8.24721C9.36051 8.25049 9.36499 8.25513 9.36803 8.26073C9.37699 8.27513 9.38115 8.29201 9.37987 8.30897C9.37923 8.32657 9.37379 8.34369 9.36411 8.35849L9.36299 8.35937Z"
                                                    fill="white"></path>
                                                <path
                                                    d="M8.36824 5.24645C8.36824 5.24645 7.13598 5.40911 9.44024 7.39371C9.44024 7.39371 10.3187 7.74943 10.4942 7.22811C10.557 7.15733 10.6023 7.07287 10.6266 6.98148C10.651 6.89009 10.6536 6.79427 10.6344 6.70167C10.6166 6.60976 10.5774 6.52335 10.52 6.44947C10.4625 6.37558 10.3884 6.31632 10.3037 6.27651C10.3037 6.27651 10.7965 3.57658 12.3238 4.30291L12.4974 3.80594C12.4974 3.80594 10.9626 2.78768 9.44368 5.60861C9.3144 5.45487 9.14536 5.33961 8.95504 5.27543C8.76472 5.21126 8.5604 5.20063 8.36448 5.24472L8.36824 5.24645Z"
                                                    fill="white"></path>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2460_166931">
                                                    <rect width="16" height="16" fill="white">
                                                    </rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <span class="fs-16">{{ ($bayutLatestUsefulData['active_hot_listings'] ?? 0) }}</span>
                                        Hot
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="ri-radio-button-fill text-success fs-14"></i>
                                        <span class="fs-16">{{ ($bayutLatestUsefulData['active_basic_listings'] ?? 0) }}</span>
                                        Basic
                                    </li>
                                </ul>
                            </td>
                            <td>

                                <span class="fs-16">
                                    {{ ($bayutUsefulData['leads_received'] ?? 0) }}
                                    <i class="ri-information-line"
                                       data-bs-toggle="popover"
                                       data-bs-trigger="hover"
                                       data-bs-content="<div>
                                                    Leads Breakdown<br><br>
                                                    Phone Calls: {{ ($bayutUsefulData['total_calls'] ?? 0) }}<br>
                                                     WhatsApp Delivered Messages:{{ ($bayutUsefulData['total_enquires'] ?? 0) }}<br>
                                                     WhatsApp Sent Messages:{{ ($bayutUsefulData['total_enquires'] ?? 0) }}<br>
                                                    </div>"
                                       data-bs-html="true"
                                    ></i>
                                </span>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <div class="accordion accordion-flush d-none" id="accordionBayutAddInfo">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionBayutAddInfoCollapse" aria-expanded="false"
                                        aria-controls="accordionBayutAddInfoCollapse">
                                    <h5>Additional Information</h5>
                                </button>
                            </h2>
                            <div id="accordionBayutAddInfoCollapse" class="accordion-collapse collapse"
                                 aria-labelledby="accordionBayutAddInfoCollapse"
                                 data-bs-parent="#accordionBayutAddInfo">
                                <div class="accordion-body">

                                    <div class="row">
                                        <div class="col-4">
                                            <ul>
                                                <li class="d-flex justify-content-start align-items-center gap-1">Total
                                                    Listing:<span
                                                        class="fs-16">0</span>
                                                </li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">No. Of
                                                    Premium: <span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">No. Of
                                                    Featured: <span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">
                                                    Re-listed: <span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">New
                                                    Published:<span class="fs-16">0(0%)</span></li>
                                            </ul>
                                        </div>
                                        <div class="col-4">
                                            <ul>
                                                <li class="d-flex justify-content-start align-items-center gap-1">New
                                                    Listings:<span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">
                                                    Verified Listings:<span class="fs-16">0.0%</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">% Of
                                                    Verified Listings:<span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">
                                                    Checked:<span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">
                                                    Checked Listing%:<span class="fs-16">0</span></li>
                                            </ul>
                                        </div>
                                        <div class="col-4">
                                            <ul>
                                                <li class="d-flex justify-content-start align-items-center gap-1">
                                                    TruChecked - Offplan:<span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">
                                                    TruChecked - Ready:<span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">Total
                                                    TruChecked%:<span class="fs-16">0</span></li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">
                                                    Eligible For TruCheck:<span class="fs-16">0</span></li>
                                            </ul>
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
    <div class="card" id="dubizzle-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <img src="https://dbz-monolith-media.dubizzle.com/images/location_switcher/logos/dubizzle-logo.png"
                     class="thumbnail" style="    width: 100px;">
                <div class="alert bg-dark-subtle p-1">
                    @if($dubizzleData)
                        <div>
                            Date: {{ $dubizzleData->start_date }} - {{ $dubizzleData->end_date }}
                        </div>
                    @endif
                    @if($dubizzleLatestData)
                        <div>
                            Updated At: {{ $dubizzleLatestData->portal_data_updated_at }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <thead>
                        <tr>

                            <th>Badges</th>
                            <th>Listings</th>
                            <th>Call Answer Rate</th>
                            <th>Whatsapp Response</th>
                            <th>Products Applied</th>
                            <th>Leads</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="">
                                    @if (($dubizzleLatestUsefulData['progress_achieved_quality_lister_badge'] ?? 0))
                                        <div>
                                            <span class="quality-listers" title="Quality Listers">
                                                <span>
                                                    <svg width="1em" height="1em" viewBox="0 0 18 18"
                                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                         aria-hidden="true" focusable="false" class="">
                                                        <path
                                                            d="M5.7406 6.57245L9.00797 15.7129L12.2579 6.57175L5.7406 6.57245Z"
                                                            fill="#64B7E3"></path>
                                                        <path
                                                            d="M12.2579 6.57055L5.7406 6.56986L7.2363 1.99998H10.7563L12.2579 6.57055Z"
                                                            fill="#90CCEB"></path>
                                                        <path d="M7.24553 2L5.78875 6.57055H1L4.85116 2H7.24553Z"
                                                              fill="#BCE0F3"></path>
                                                        <path
                                                            d="M10.7522 2L12.209 6.57055H16.9977L13.1466 2H10.7522Z"
                                                            fill="#64B7E3"></path>
                                                        <path
                                                            d="M8.99855 15.7129L5.78875 6.57175H1L8.99855 15.7129Z"
                                                            fill="#90CCEB"></path>
                                                        <path
                                                            d="M9.00175 15.7129L12.2116 6.57175H17.0003L9.00175 15.7129Z"
                                                            fill="#2399D8"></path>
                                                    </svg>
                                                </span>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="mt-1">
                                        Active Agent:
                                        {{ ($dubizzleLatestUsefulData['is_active_agent'] ?? 0) == 1 ? 'Yes' : 'No' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <ul>
                                    <li class="d-flex align-items-center gap-2"><span class="fs-16">{{ ($dubizzleLatestUsefulData['num_live_listings'] ?? 0) }}</span>
                                        Live
                                    </li>
                                    <li class="d-flex align-items-center gap-2"><span class="fs-16">{{ ($dubizzleLatestUsefulData['verifiedListingsCount'] ?? 0) }}</span>
                                        Verified
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center"
                                     data-bs-toggle="popover"
                                     data-bs-trigger="hover"
                                     data-bs-content="<div>
                                                    Proxy Number: {{ ($dubizzleUsefulData['proxy_phone'] ?? '') }}<br>
                                                    Agent Number:{{ str_replace(['["', '"]'], "", $dubizzleUsefulData['cell_numbers'] ?? "") }}<br>
                                                     Not Connected Calls:{{ ($dubizzleUsefulData['count_calls_notconnected'] ?? 0) }}<br>
                                                     Missed Calls:{{ ($dubizzleUsefulData['count_calls_missed'] ?? 0) }}<br>
                                                     Answered Calls:{{ ($dubizzleUsefulData['count_calls_connected'] ?? 0) }}<br>
                                                     Total Calls:{{ ($dubizzleUsefulData['count_calls_total'] ?? 0) }}
                                                    </div>"
                                     data-bs-html="true"
                                >
                                    <div>
                                        <div class="progress-ring-container">
                                            <svg width="50" height="50" class="progress-ring">
                                                <circle class="background" cx="25" cy="25" r="20"></circle>
                                                <circle class="progress-{{ ($dubizzleUsefulData['call_answer_rate'] ?? 0) >= 85 ? 'success' : (($dubizzleUsefulData['call_answer_rate'] ?? 0) >= 65 ? 'warning' : 'danger') }}" cx="25" cy="25" r="20"
                                                        stroke-dasharray="125.66"
                                                        stroke-dashoffset="{{ 125.66 * (1 - ($dubizzleUsefulData['call_answer_rate'] ?? 0) / 100) }}"></circle>
                                            </svg>
                                            <text x="50%" y="50%" class="percentage-text">{{ ($dubizzleUsefulData['call_answer_rate'] ?? 0) }}%</text>
                                        </div>
                                    </div>
                                    <ul>
                                        <li class="d-flex align-items-center gap-2">
                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M10.6375 11.25C9.38333 11.25 8.16086 10.9704 6.97008 10.4112C5.77892 9.85233 4.72406 9.11344 3.8055 8.1945C2.88656 7.27594 2.14767 6.22108 1.58883 5.02992C1.02961 3.83914 0.75 2.61667 0.75 1.3625C0.75 1.1875 0.808333 1.04167 0.925 0.925C1.04167 0.808333 1.1875 0.75 1.3625 0.75H3.725C3.86111 0.75 3.98264 0.79375 4.08958 0.88125C4.19653 0.96875 4.25972 1.08056 4.27917 1.21667L4.65833 3.25833C4.67778 3.39444 4.67544 3.51831 4.65133 3.62992C4.62683 3.74192 4.57083 3.84167 4.48333 3.92917L3.08333 5.35833C3.49167 6.05833 4.00208 6.71458 4.61458 7.32708C5.22708 7.93958 5.90278 8.46944 6.64167 8.91667L8.0125 7.54583C8.1 7.45833 8.21433 7.39261 8.3555 7.34867C8.49628 7.30511 8.63472 7.29306 8.77083 7.3125L10.7833 7.72083C10.9194 7.75 11.0312 7.81553 11.1187 7.91742C11.2062 8.01969 11.25 8.13889 11.25 8.275V10.6375C11.25 10.8125 11.1917 10.9583 11.075 11.075C10.9583 11.1917 10.8125 11.25 10.6375 11.25Z"
                                                    fill="#748AA1"></path>
                                            </svg>
                                            <span class="fs-16">{{ ($dubizzleUsefulData['count_calls_connected'] ?? 0) }}</span>
                                            Answered
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.00013 5.65005L4.11263 2.76255V4.27922H2.94596V0.779215H6.44596V1.94588H4.9293L6.98555 4.00213L10.2814 0.706299L11.1126 1.53755L7.00013 5.65005ZM7.00013 6.00005C8.14735 6.00005 9.27746 6.23105 10.3905 6.69305C11.5039 7.15466 12.4932 7.84727 13.3585 8.77088C13.4751 8.88755 13.5335 9.02366 13.5335 9.17922C13.5335 9.33477 13.4751 9.47088 13.3585 9.58755L12.0168 10.9C11.9099 11.007 11.786 11.0653 11.6452 11.075C11.504 11.0848 11.3751 11.0459 11.2585 10.9584L9.5668 9.67505C9.48902 9.61671 9.43069 9.54633 9.3918 9.46388C9.35291 9.38105 9.33346 9.29588 9.33346 9.20838V7.54588C8.96402 7.42921 8.58485 7.33685 8.19596 7.2688C7.80707 7.20074 7.40846 7.16672 7.00013 7.16672C6.5918 7.16672 6.19319 7.20074 5.8043 7.2688C5.41541 7.33685 5.03624 7.42921 4.6668 7.54588V9.20838C4.6668 9.29588 4.64969 9.38105 4.61546 9.46388C4.58163 9.54633 4.52582 9.61671 4.44805 9.67505L2.7418 10.9584C2.62513 11.0459 2.49641 11.0848 2.35563 11.075C2.21446 11.0653 2.09041 11.007 1.98346 10.9L0.641797 9.58755C0.52513 9.47088 0.466797 9.33477 0.466797 9.17922C0.466797 9.02366 0.52513 8.88755 0.641797 8.77088C1.49735 7.84727 2.48416 7.15466 3.60221 6.69305C4.72027 6.23105 5.85291 6.00005 7.00013 6.00005Z"
                                                    fill="#748AA1"></path>
                                            </svg>
                                            <span class="fs-16">{{ ($dubizzleUsefulData['count_calls_missed'] ?? 0) }}</span>
                                            Missed
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div
                                    data-bs-toggle="popover"
                                    data-bs-trigger="hover"
                                    data-bs-content="<div>
                                                    Delivered Messages: {{ ($dubizzleUsefulData['count_whatsapp_msgs_delivered'] ?? 0) }}<br>
                                                    Answered: {{ ($dubizzleUsefulData['count_whatsapp_msgs_answered'] ?? 0) }}<br>
                                                     Unanswered:{{ ($dubizzleUsefulData['count_whatsapp_msgs_delivered'] ?? 0) - ($dubizzleUsefulData['count_whatsapp_msgs_answered'] ?? 0) }}
                                                    </div>"
                                    data-bs-html="true"
                                >
                                    <div class="d-flex justify-content-center align-items-center gap-1"><span
                                            class="fs-16">{{ ($dubizzleUsefulData['response_rate'] ?? 0) }}%</span> Response Rate
                                    </div>
                                    <div class="progress animated-progress custom-progress customized-progress">
                                        <div class="progress-bar bg-{{ ($dubizzleUsefulData['response_rate'] ?? 0) >= 85 ? 'success' : (($dubizzleUsefulData['response_rate'] ?? 0) >= 65 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ ($dubizzleUsefulData['response_rate'] ?? 0) }}%"
                                             aria-valuenow="{{ ($dubizzleUsefulData['response_rate'] ?? 0) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-center gap-1">
                                        <div>
                                            <svg width="1em" height="1em" viewBox="0 0 14 15" fill="currentColor"
                                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                                 focusable="false" class="">
                                                <mask id="mask0_217_65699" maskUnits="userSpaceOnUse" x="0" y="0"
                                                      width="14" height="15">
                                                    <rect y="0.984863" width="14" height="14" fill="#D9D9D9"></rect>
                                                </mask>
                                                <g mask="url(#mask0_217_65699)">
                                                    <path
                                                        d="M7 13.2349C6.28056 13.2349 5.60243 13.0963 4.96563 12.8192C4.32882 12.5422 3.77222 12.1654 3.29583 11.689C2.81944 11.2126 2.44271 10.656 2.16563 10.0192C1.88854 9.38243 1.75 8.70431 1.75 7.98486C1.75 7.55709 1.79861 7.14146 1.89583 6.73799C1.99306 6.33452 2.13403 5.95535 2.31875 5.60049C2.50347 5.24563 2.72465 4.91507 2.98229 4.60882C3.23993 4.30257 3.52917 4.02792 3.85 3.78486L7.81667 7.75153L7 8.5682L3.85 5.4182C3.55833 5.7682 3.32986 6.15952 3.16458 6.59215C2.99931 7.02479 2.91667 7.48903 2.91667 7.98486C2.91667 9.11264 3.31528 10.0751 4.1125 10.8724C4.90972 11.6696 5.87222 12.0682 7 12.0682C8.12778 12.0682 9.09028 11.6696 9.8875 10.8724C10.6847 10.0751 11.0833 9.11264 11.0833 7.98486C11.0833 6.94459 10.7503 6.04771 10.0844 5.29424C9.4184 4.54077 8.58472 4.09597 7.58333 3.95986V5.0682H6.41667V2.73486H7C7.71944 2.73486 8.39757 2.8734 9.03437 3.15049C9.67118 3.42757 10.2278 3.80431 10.7042 4.2807C11.1806 4.75709 11.5573 5.31368 11.8344 5.95049C12.1115 6.58729 12.25 7.26542 12.25 7.98486C12.25 8.70431 12.1115 9.38243 11.8344 10.0192C11.5573 10.656 11.1806 11.2126 10.7042 11.689C10.2278 12.1654 9.67118 12.5422 9.03437 12.8192C8.39757 13.0963 7.71944 13.2349 7 13.2349ZM7 11.4849C6.83472 11.4849 6.69618 11.429 6.58437 11.3172C6.47257 11.2053 6.41667 11.0668 6.41667 10.9015C6.41667 10.7363 6.47257 10.5977 6.58437 10.4859C6.69618 10.3741 6.83472 10.3182 7 10.3182C7.16528 10.3182 7.30382 10.3741 7.41563 10.4859C7.52743 10.5977 7.58333 10.7363 7.58333 10.9015C7.58333 11.0668 7.52743 11.2053 7.41563 11.3172C7.30382 11.429 7.16528 11.4849 7 11.4849ZM9.91667 8.5682C9.75139 8.5682 9.61285 8.51229 9.50104 8.40049C9.38924 8.28868 9.33333 8.15014 9.33333 7.98486C9.33333 7.81959 9.38924 7.68104 9.50104 7.56924C9.61285 7.45743 9.75139 7.40153 9.91667 7.40153C10.0819 7.40153 10.2205 7.45743 10.3323 7.56924C10.4441 7.68104 10.5 7.81959 10.5 7.98486C10.5 8.15014 10.4441 8.28868 10.3323 8.40049C10.2205 8.51229 10.0819 8.5682 9.91667 8.5682ZM4.08333 8.5682C3.91806 8.5682 3.77951 8.51229 3.66771 8.40049C3.5559 8.28868 3.5 8.15014 3.5 7.98486C3.5 7.81959 3.5559 7.68104 3.66771 7.56924C3.77951 7.45743 3.91806 7.40153 4.08333 7.40153C4.24861 7.40153 4.38715 7.45743 4.49896 7.56924C4.61076 7.68104 4.66667 7.81959 4.66667 7.98486C4.66667 8.15014 4.61076 8.28868 4.49896 8.40049C4.38715 8.51229 4.24861 8.5682 4.08333 8.5682Z"
                                                        fill="black" fill-opacity="0.65"></path>
                                                </g>
                                            </svg>
                                            Response Time: @php
                                                $seconds = ($dubizzleUsefulData['avg_response_time'] ?? 0);

                                                $hours = floor($seconds / 3600);
                                                $minutes = floor(($seconds % 3600) / 60);
                                                $seconds = $seconds % 60;

                                                echo "$hours hours, $minutes minutes, $seconds seconds";
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <ul>
                                    <li class="d-flex align-items-center gap-2">
                                        <svg width="1em" height="1em" viewBox="0 0 17 16" fill="currentColor"
                                             xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"
                                             class="">
                                            <path
                                                d="M8.82153 16C13.2398 16 16.8215 12.4183 16.8215 8C16.8215 3.58172 13.2398 0 8.82153 0C4.40326 0 0.821533 3.58172 0.821533 8C0.821533 12.4183 4.40326 16 8.82153 16Z"
                                                fill="#FFA726"></path>
                                            <path
                                                d="M7.56758 12.7998V9.9438H9.60758C11.3356 9.9438 12.2956 8.7678 12.2956 7.3758C12.2956 5.9718 11.3476 4.7958 9.60758 4.7958H5.86358V12.7998H7.56758ZM9.36758 8.4438H7.56758V6.2958H9.36758C10.0396 6.2958 10.5556 6.7038 10.5556 7.3758C10.5556 8.0358 10.0396 8.4438 9.36758 8.4438Z"
                                                fill="#FFF3E0" transform="translate(0.3, -0.5)"></path>
                                        </svg>
                                        <span class="fs-16">{{ ($dubizzleLatestUsefulData['num_live_listings_featured'] ?? 0) }}</span>
                                        Premium
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                                             xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"
                                             class="">
                                            <path
                                                d="M8 16C12.4183 16 16 12.4183 16 8C16 3.58172 12.4183 0 8 0C3.58172 0 0 3.58172 0 8C0 12.4183 3.58172 16 8 16Z"
                                                fill="black"></path>
                                            <path
                                                d="M7.68517 5.79831V7.75498H10.1632V8.73765H7.68517V11.4303H6.45117V4.82031H10.6205V5.79831H7.68517Z"
                                                fill="white"></path>
                                        </svg>
                                        <span class="fs-16">{{ ($dubizzleLatestUsefulData['quota_featured'] ?? 0) }}</span>
                                        Featured
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="ri-radio-button-fill text-info fs-14"></i>
                                        <span class="fs-16">{{ ($dubizzleLatestUsefulData['num_live_listings_standard'] ?? 0) }}</span>
                                        Standard
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <span class="fs-16">
                                    {{ ($dubizzleUsefulData['total_leads'] ?? 0) }}
                                    <i class="ri-information-line"
                                       data-bs-toggle="popover"
                                       data-bs-trigger="hover"
                                       data-bs-content="<div>
                                                    Leads Breakdown<br><br>
                                                    Phone Calls: {{ ($dubizzleUsefulData['phone_leads'] ?? 0) }}<br>
                                                    Phone Views: {{ ($dubizzleUsefulData['phone_views'] ?? 0) }}<br>
                                                     WhatsApp Delivered Messages:{{ ($dubizzleUsefulData['count_whatsapp_msgs_delivered'] ?? 0) }}<br>
                                                     WhatsApp Sent Messages:{{ ($dubizzleUsefulData['count_whatsapp_msgs_delivered'] ?? 0) }}<br>
                                                     WhatsApp Views:{{ ($dubizzleUsefulData['whatsapp_views'] ?? 0) }}<br>
                                                     Email:{{ ($dubizzleUsefulData['email_leads'] ?? 0) }}<br>
                                                     SMS:{{ ($dubizzleUsefulData['sms_leads'] ?? 0) }}
                                                    </div>"
                                       data-bs-html="true"
                                    ></i>
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="accordion accordion-flush" id="accordionDubizzleAddInfo">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionDubizzleAddInfoCollapse" aria-expanded="false"
                                        aria-controls="accordionDubizzleAddInfoCollapse">
                                    <h5>Additional Information</h5>
                                </button>
                            </h2>
                            <div id="accordionDubizzleAddInfoCollapse" class="accordion-collapse collapse"
                                 aria-labelledby="accordionDubizzleAddInfoCollapse"
                                 data-bs-parent="#accordionDubizzleAddInfo">
                                <div class="accordion-body">

                                    <div class="row">
                                        <div class="col-4">
                                            <ul>
                                                <li class="d-flex justify-content-start align-items-center gap-1">Call
                                                    Tracking Enabled:<span
                                                        class="fs-16">{{ ($dubizzleUsefulData['is_call_tracking_enabled'] ?? 0) == 1 ? 'Yes' : 'No' }}</span>
                                                </li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">Prev
                                                    Month Responsive Agent: <span
                                                        class="fs-16">{{ ($dubizzleUsefulData['is_responsive_agent_badge_permitted_prev_month'] ?? 0) == 1 ? 'Yes' : 'No' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-4">
                                            <ul>
                                                <li class="d-flex justify-content-start align-items-center gap-1">For
                                                    Sale:<span
                                                        class="fs-16">{{ ($dubizzleUsefulData['sale_count'] ?? 0) }}</span>
                                                </li>
                                                <li class="d-flex justify-content-start align-items-center gap-1">For
                                                    Rent:<span
                                                        class="fs-16">{{ ($dubizzleUsefulData['rent_count'] ?? 0) }}</span>
                                                </li>
                                            </ul>
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
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/plugins/widgets.js') }}"></script>

    <script src="{{ URL::asset('assets/libs/echarts/echarts.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @section('global_scripts')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"
                integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endsection
    @yield('pageDT')
    @stack('in_page_scripts')
    <script>
        let filtersData = [];
        $(document).on("change", "#month", function () {
            window.location = "{{ route('reports.group.kpi-v2.view', $agent->id) }}?month=" +
                convertMonthToDateFormat($(this).val())
        })

        $(document).ready(function () {
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize all popovers on page load
                var popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                var popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
            });

            $("#month").datepicker({
                format: {
                    toDisplay: function (date, format, language) {
                        var d = new Date(date);
                        var options = {
                            year: 'numeric',
                            month: 'long'
                        };
                        return d.toLocaleDateString(language, options);
                    },
                    toValue: function (date, format, language) {
                        var d = new Date(date);
                        var year = d.getFullYear();
                        var month = (d.getMonth() + 1).toString().padStart(2, '0');
                        return `${year}-${month}`;
                    }
                },
                startView: "months",
                minViewMode: "months",
                autoclose: true,
            });
        })

        const toggleColumnCards = (key) => {
            $(".target-cols").addClass("d-none");
            $(`.${key}-cols`).removeClass("d-none");
            getBreakdown(key)

            $("#breakdown-title").html(key.toLowerCase().replace(/(?:^|\s|_)\w/g, (match) => match.toUpperCase())
                .replace(/_/g, ' '));
        }

        const renderDateWiseChart = (elementId, shouldHide = false) => {
            const dataset = $(`#${elementId}`).data("dataset")

            const categories = [];
            const data = [];

            let isArray = false;

            Object.keys(dataset ?? []).map((key, index) => {
                isArray = typeof dataset[key] == "object";
                categories.push(key)
                data.push(dataset[key])
            })

            const getOption = () => {
                if (isArray) {
                    const series = [];
                    const legends = [];
                    if (elementId.includes("sales_target")) {
                        data.map(item => {
                            item.map(item1 => {
                                if (item1.title == "Average Rental Revenue" || item1.title == "Average Sales Revenue") {
                                    const index = series.findIndex(s => {
                                        return s.name === item1.title
                                    })
                                    if (index !== -1) {
                                        series[index].data.push(item1.count)
                                    } else {
                                        series.push({
                                            name: item1.title,
                                            data: [item1.count],
                                            stack: 'Total',
                                            type: 'line',
                                        })
                                    }
                                    legends.push(item1.title);
                                }
                            })
                        })
                    } else {
                        data.map(item => {
                            item.map(item1 => {
                                const index = series.findIndex(s => {
                                    return s.name === item1.title
                                })
                                if (index !== -1) {
                                    series[index].data.push(item1.count)
                                } else {
                                    series.push({
                                        name: item1.title,
                                        data: [item1.count],
                                        stack: 'Total',
                                        type: 'line',
                                    })
                                }
                                legends.push(item1.title);
                            })

                        })
                    }


                    return {
                        legend: {
                            data: [...new Set(legends)]
                        },
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {type: 'shadow'},
                            formatter: function (params) {
                                let tooltip = params[0].axisValue + '<br/>';
                                params.forEach(item => {
                                    tooltip += item.marker + item.seriesName + ': ' + item.data + '<br/>';
                                });
                                return tooltip;
                            }
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            // bottom: '3%',
                            containLabel: true
                        },
                        toolbox: {
                            feature: {
                                saveAsImage: {}
                            }
                        },
                        xAxis: {
                            type: 'category',
                            boundaryGap: false,
                            data: categories,
                            axisLabel: {
                                rotate: 30,
                                interval: 0,
                                formatter: function(value) {
                                    return moment(value).format('L');
                                },
                            }
                        },
                        yAxis: {
                            type: 'value'
                        },
                        series: series,
                        dataZoom: [
                            {
                                type: 'slider',
                                show: true,
                                xAxisIndex: [0],
                                start: 0,
                                end: 50,
                                bottom: 40
                            },
                            {
                                type: 'inside',
                                zoomOnMouseWheel: false,
                                moveOnMouseMove: false,
                                xAxisIndex: [0],
                                start: 0,
                                end: 100
                            }
                        ],
                    };
                }

                return {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {type: 'shadow'},
                        formatter: function (params) {
                            let tooltip = params[0].axisValue + '<br/>';
                            params.forEach(item => {
                                tooltip += item.marker + item.seriesName + ': ' + item.data + '<br/>';
                            });
                            return tooltip;
                        }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        // bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        data: categories,
                        axisLabel: {
                            rotate: 30,
                            interval: 0,
                            formatter: function(value) {
                                return moment(value).format('L');
                            },
                        }
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            data: data,
                            type: 'line',
                        }
                    ],
                    dataZoom: [
                        {
                            type: 'slider',
                            show: true,
                            xAxisIndex: [0],
                            start: 0,
                            end: 50,
                            bottom: 40
                        },
                        {
                            type: 'inside',
                            zoomOnMouseWheel: false,
                            moveOnMouseMove: false,
                            xAxisIndex: [0],
                            start: 0,
                            end: 100
                        }
                    ]
                };
            }


            const options = getOption();

            const chart = echarts.init(document.getElementById(elementId));
            chart.setOption(options);

            if (shouldHide) {
                $(`#${elementId}`).closest(".target-cols").addClass("d-none")
            }
        }

        $(document).ready(function () {
            $(".date-wise-chart").each(function (index) {
                renderDateWiseChart($(this).attr("id"), index !== 0)
            })
        })

        const renderPieChart = (data) => {
            $("#breakdown-pie-chart").html("");

            let labels = [];
            let series = [];

            data.map(item => {
                labels.push(item.name);
                series.push(parseFloat(Number(item.value).toFixed(2)));
            })

            let options = {
                series: series,
                chart: {
                    width: 500,
                    height: 250,
                    type: 'pie',
                },
                labels: labels,
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    show: true,
                },
                responsive: [{
                    breakpoint: 120,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center',
                        }
                    }
                }]
            };
            new ApexCharts(document.querySelector("#breakdown-pie-chart"), options).render();
        }

        const getBreakdown = (kpi) => {
            renderPieChart([])
            $("#breakdown-list-one").html("");
            $("#breakdown-list-two").html("");
            $.ajax({
                url: "{{ route('reports.group.kpi-v2.get-breakdown-data') }}",
                type: 'GET',
                data: {
                    agent_id: "{{ $agent->id }}",
                    month: convertMonthToDateFormat($("#month").val()),
                    kpi: kpi,
                },
                success: function (response) {
                    if (response.status == 1) {
                        let data = response.data;

                        const chartData = [];

                        const firstListLastIndex = Math.round(data?.length / 2) - 1;
                        let breakdownListOne = "";
                        let breakdownListTwo = "";
                        data?.map((item, index) => {
                            if (kpi == "sales_target") {
                                if (item.title == "Average Rental Revenue" || item.title == "Average Sales Revenue") {
                                    //fix number toFixed(2)

                                    chartData.push({
                                        name: item.title,
                                        value: item.count
                                    })
                                }
                            } else {
                                chartData.push({
                                    name: item.title,
                                    value: item.count
                                })
                            }

                            const isPercent = item.title.toLowerCase().includes("percent");

                            const breakdownListItem = `<li class="d-flex align-items-center gap-2 my-1">
                                        <span class="fs-16">${isPercent ? parseFloat(item.count).toFixed(2) : formatNumberMetricPrefixes(item.count)}${isPercent ? "%" : ""}</span>
                                        <small>${item.title}</small>
                                    </li>`;
                            if (index <= firstListLastIndex) {
                                breakdownListOne += breakdownListItem;
                            } else {
                                breakdownListTwo += breakdownListItem;
                            }
                        })
                        renderPieChart(chartData)
                        $("#breakdown-list-one").html(breakdownListOne)
                        $("#breakdown-list-two").html(breakdownListTwo)
                    }
                }
            })
        }
        getBreakdown("{{ $targets[0]->kpi ?? ($otherReports[0]->kpi ?? 'live_listings') }}")

        const radialBar = (val, elementId) => {
            var options = {
                series: [val], // Data value
                chart: {
                    height: 120,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '70%', // Size of the hollow area
                        },
                        track: {
                            background: '#f2f2f2', // Background color of the track
                        },
                        dataLabels: {
                            name: {
                                show: false, // Hide the name
                            },
                            value: {
                                color: '#333', // Color of the value text
                                fontSize: '16px',
                                show: true,
                            }
                        }
                    },
                },
                colors: ['#90EE90'], // Light green color for the bar
            };

            var chart = new ApexCharts(document.querySelector("#" + elementId), options);
            chart.render();
        }

        radialBar("{{ ($pfUsefulData['overall_responsiveness'] ?? 0) }}", 'pf_response_chart')
        radialBar("{{ ($pfUsefulData['avg_quality_score'] ?? 0) }}", 'pf_listing_quality_score_chart')

        const fixedColors = {
            "PUBLISHED": "#45cb85", // Example color for Status1
            "UNPUBLISHED": "#f06548", // Example color for Status2
            "DRAFT": "#4b38b3", // Example color for Status3
            "PENDING": "#ffbe0b", // Example color for Status4
            "REFUSED": "#e978601a", // Example color for Status5
            "POCKET": "#3577f1", // Example color for Status6
            "ARCHIVED": "#f065481a", // Example color for Status7
        };
        const fallbackPalette = [
            "#45cb85", "#f06548", "#4b38b3", "#ffbe0b", "#f06548", "#3577f1", "#f06548", "#33FFFF"
        ];


        const getListingData = () => {


            $.ajax({
                url: "{{ route('reports.group.kpi-v2.agent.listing.data') }}",
                type: 'GET',
                data: {
                    agent_id: "{{ $agent->id }}",
                    month: convertMonthToDateFormat($("#month").val()),
                },
                success: function (response) {
                    if (response.status == 1) {
                        $("#leadsByListingsChartLoader").addClass("d-none");
                        $("#leadsByListingsChart").removeClass("d-none");
                        let data = response.data;
                        let live_listings_with_lead_count = data.live_listing_lead_counts;
                        let listing_with_lead_source = data.listing_lead_with_source;
                        let listings = [];
                        let leads = [];
                        //key is ref_no and values is deal count
                        for (let key in live_listings_with_lead_count) {
                            listings.push(key);
                            leads.push(live_listings_with_lead_count[key]);
                        }


                        //  leadsByListingChart(listings,leads)
                        leadsByListingStackChart(data.ref_nos, data.sources, listing_with_lead_source)

                    }
                }
            })
        }
        const getListingMetaData = () => {


            $.ajax({
                url: "{{ route('reports.group.kpi-v2.agent.listing.meta.data') }}",
                type: 'GET',
                data: {
                    agent_id: "{{ $agent->id }}",
                },
                success: function (response) {
                    if (response.status == 1) {
                        console.log(response.data)
                    }
                }
            })
        }
        getListingMetaData();
        getListingData()
        const leadsByListingChart = (listings, leads) => {
            const option4 = {

                tooltip: {
                    trigger: 'axis',
                    axisPointer: {type: 'shadow'},
                    formatter: function (params) {
                        let tooltip = params[0].axisValue + '<br/>';
                        params.forEach(item => {
                            tooltip += item.marker + item.seriesName + ': ' + item.data + '<br/>';
                        });
                        return tooltip;
                    }
                },

                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '10%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: listings,
                    axisLabel: {
                        rotate: 45,
                        interval: 0
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Leads'
                },
                dataZoom: [
                    {
                        type: 'slider', // Slider for zooming
                        show: true,
                        xAxisIndex: [0],
                        start: 0,
                        end: 50
                    },
                    {
                        type: 'inside', // Enable zooming within the chart itself
                        xAxisIndex: [0],
                        start: 0,
                        end: 100
                    }
                ],
                series: [{
                    name: 'Lead Count',
                    type: 'bar',
                    data: leads, // Default to 1 for each listing
                    itemStyle: {
                        color: '#d3a188' // Default color for the series
                    }
                }]
            };

            // Initialize the chart
            const propertyStatusChart = echarts.init(document.getElementById('leadsByListingsChart'));
            propertyStatusChart.setOption(option4);
        }


        const leadsByListingStackChart = (listings, sources, rawData) => {
            // Convert raw data (object) to an array and sort by total_leads (descending)
            const data = Object.values(rawData).sort((a, b) => b.total_leads - a.total_leads);

            // Define colors for each lead source
            const sourceColor = {
                'Property Finder': '#FF5733',
                'Bayut': '#3498DB',
                'Dubizzle': '#2ECC71',
                'Other': '#F1C40F'
            };

            // Create series for stacked bars
            const series = sources.map(source => ({
                name: source,
                type: 'bar',
                stack: 'total',
                data: data.map(item => item.leads[source] || 0),
                itemStyle: {color: sourceColor[source] || '#CCCCCC'} // Default to gray if no color is found
            }));

            const option = {
                // tooltip: {
                //     trigger: 'axis',
                //     axisPointer: { type: 'shadow' },
                //     formatter: function (params) {
                //         let tooltip = params[0].axisValue + '<br/>';
                //         params.forEach(item => {
                //             tooltip += `${item.marker}${item.seriesName}: ${item.data}<br/>`;
                //         });
                //         return tooltip;
                //     }
                // },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {type: 'shadow'},
                    formatter: function (params) {
                        // Get the reference number from the hovered bar
                        let refNo = params[0].axisValue;

                        // Find the listing details from the data array
                        let listing = data.find(item => item.ref_no === refNo);

                        // Create tooltip content
                        let tooltip = `<div style="display: flex; align-items: center;">
                                        <img src="${listing.image}" alt="Property Image" style="width: 60px; height: 40px; margin-right: 8px; border-radius: 5px;">
                                        <div>
                                            <b>Unit No.: ${listing.unit_no}</b><br>
                                            <b>Location.: ${listing.location}</b>
                                            <br>
                                            ${params[0].axisValue} <br/>
                                `;

                        // Append lead sources and values
                        params.forEach(item => {
                            tooltip += `${item.marker} ${item.seriesName}: <b>${item.data}</b><br/>`;
                        });

                        tooltip += `</div></div>`;
                        return tooltip;
                    }
                },
                legend: {
                    data: sources,
                    // top: 'bottom'
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    // bottom: '10%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: data.map(item => item.ref_no), // Updated to sorted order
                    axisLabel: {
                        rotate: 30,
                        interval: 0
                    }
                },
                yAxis: {
                    type: 'value',
                    name: 'Leads'
                },
                dataZoom: [
                    {
                        type: 'slider',
                        show: true,
                        xAxisIndex: [0],
                        start: 0,
                        end: 50,
                        bottom: 40
                    },
                    {
                        type: 'inside',
                        zoomOnMouseWheel: false,
                        moveOnMouseMove: false,
                        xAxisIndex: [0],
                        start: 0,
                        end: 100
                    }
                ],
                series
            };

            // Initialize the chart
            const chart = echarts.init(document.getElementById('leadsByListingsChart'));
            chart.setOption(option);
        };


        function convertMonthToDateFormat(dateString) {
            // Parse the input string in "F Y" format (e.g., "January 2025")
            const parts = dateString.split(' ');

            if (parts.length !== 2) {
                throw new Error("Invalid date format. Expected format: 'Month YYYY'");
            }

            const monthName = parts[0];
            const year = parts[1];

            // Map month names to their corresponding numbers
            const monthMap = {
                January: '01',
                February: '02',
                March: '03',
                April: '04',
                May: '05',
                June: '06',
                July: '07',
                August: '08',
                September: '09',
                October: '10',
                November: '11',
                December: '12',
            };

            const month = monthMap[monthName];

            if (!month || !/^\d{4}$/.test(year)) {
                throw new Error("Invalid date components. Ensure the format is 'Month YYYY' and the year is valid.");
            }

            return `${year}-${month}`;
        }

        $(document).ready(function () {
            $(".target_radial_charts").each(function () {
                const achieved = parseFloat($(this).data("achieved"));

                let color = achieved >= 75 ? "#45cb85" : (achieved >= 50 ? "#ffbe0b" : "#f06548");
                let options = {
                    series: [achieved],
                    chart: {
                        type: "radialBar",
                        width: 75,
                        sparkline: {enabled: !0}
                    },
                    dataLabels: {enabled: !1},
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                margin: 0,
                                size: "70%"
                            },
                            track: {
                                margin: 1
                            },
                            dataLabels: {
                                show: !0,
                                name: {
                                    show: !1
                                },
                                value: {
                                    show: !0,
                                    fontSize: "12px",
                                    fontWeight: 600,
                                    offsetY: 5
                                }
                            }
                        }
                    },
                    colors: [color]
                }
                new ApexCharts(this, options).render();
            })
        })
        $('#kpiReportSidebar').addClass('active');
        // $("#sidebarReports").addClass('collapse show');
        // $('#sidebarAgentKpiV2Report').addClass('active');
        // $("#reportsActive").addClass('active');
    </script>
@endsection

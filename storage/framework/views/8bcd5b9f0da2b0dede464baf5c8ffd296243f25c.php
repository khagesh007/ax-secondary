<?php $__env->startSection('title'); ?>
    Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="<?php echo e(URL::asset('assets/libs/jsvectormap/jsvectormap.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(URL::asset('assets/libs/swiper/swiper.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <style>
        #pf_rank_meter, #bayut_rank_meter,#dubbizle_rank_meter {
            max-width: 600px;
            max-height: 400px;
            /* Add other styles as needed */
        }
    </style>
    <style>
        .morris-chart {
            font-family: Arial, sans-serif;
        }

        .morris-chart .morris-label {
            font-size: 14px;
            fill: #333333;
        }
    </style>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">

        <div class="col">

            <div class="h-100">

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Request Log</h4>

                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn show" target="_blank" href="<?php echo e(route('admin.security.request.log')); ?>" >
                                            <span class=" fs-10">View All</span>
                                        </a>

                                    </div>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="text-center uk-vertical-align-middle mt-4" id="loader_requestLogOverview">
                                    <div class="spinner-border text-success mt-5 mb-5"
                                         role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div class=" table-card">

                                    <table class="table table-nowrap table-centered align-middle">


                                        <tbody id="requestLogOverview">


                                        </tbody><!-- end tbody -->

                                    </table><!-- end table -->
                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Telescope Log</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn show" target="_blank" href="<?php echo e(URL::to('/')); ?>/telescope" >
                                            <span class=" fs-10">View All</span>
                                        </a>

                                    </div>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">
                                <div class="text-center uk-vertical-align-middle mt-4" id="loader_telescopeOverview">
                                    <div class="spinner-border text-success mt-5 mb-5"
                                         role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div class=" table-card">
                                    <table class="table table-nowrap table-centered align-middle">


                                        <tbody id="telescopeOverview">


                                        </tbody><!-- end tbody -->

                                    </table><!-- end table -->
                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col-xl-4">
                        <div class="card" style="min-height: 346px;">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Recent Activities</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn show" href="#" data-bs-toggle="dropdown"
                                           aria-haspopup="true" aria-expanded="true">
                                            <span class="text-muted fs-10">View All</span>
                                        </a>

                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <div>
                                    <button type="button" onclick="filterEvents('')"
                                            class="btn btn-soft-secondary btn-sm">
                                        ALL
                                    </button>



                                </div>
                                <div>

                                    <div class="pe-2 me-n1 mb-3" data-simplebar="init" style="height: 190px">
                                        <div class="simplebar-wrapper" style="margin: 0px -8px 0px 0px;">
                                            <div class="simplebar-height-auto-observer-wrapper">
                                                <div class="simplebar-height-auto-observer"></div>
                                            </div>
                                            <div class="simplebar-mask">
                                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                    <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                                         aria-label="scrollable content"
                                                         style="height: 100%; overflow: hidden scroll;">
                                                        <div class="simplebar-content"
                                                             style="padding: 0px 8px 0px 0px;">
                                                            <div id="upcoming-event-list1">


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="simplebar-placeholder"
                                                 style="width: auto; height: 2511px;"></div>
                                        </div>
                                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                                        </div>
                                        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                            <div class="simplebar-scrollbar"
                                                 style="height: 63px; display: block; transform: translate3d(0px, 24px, 0px);"></div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>

                </div>







            </div> <!-- end .h-100-->

        </div> <!-- end col -->


    </div>
    <?php echo $__env->make('admin.events.include.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/echarts/echarts.min.js')); ?>"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="<?php echo e(URL::asset('/assets/libs/chart.js/chart.js.min.js')); ?>"></script>
    <!-- apexcharts -->
    <script src="<?php echo e(URL::asset('/assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/libs/swiper/swiper.min.js')); ?>"></script>
    <!-- dashboard init -->
    <script src="<?php echo e(URL::asset('/assets/js/pages/dashboard-ecommerce.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/widgets.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/plugins/event.js')); ?>?v=<?php echo e(time()); ?>"></script>



    <script>
        let isEventPage = false;

        function getTelescopeData() {
            $.ajax({
                url: "<?php echo e(route('admin.security.telescope.overview')); ?>",
                type: "GET",
                success: function (result) {
                   if(result.status == '1'){
                       let html = '';
                       let data = result.data;
                       //loop through data and create html
                          $.each(data, function (key, value) {
                              //remove underscore from key and make first letter capital
                                key = key.replace(/_/g, " ");
                                key = key.charAt(0).toUpperCase() + key.slice(1);
                            html += '<tr><td>'+key+'</td><td><ul>';
                                $.each(value,function (key1, val){
                                    html += '<li>'+key1+' : '+val+'</li>';
                              });

                            html += '</ul></td></tr>';
                          });


                       $("#loader_telescopeOverview").html('');
                       $("#telescopeOverview").html(html);

                   }
                }
            });
        }
        function getRequestData() {
            $.ajax({
                url: "<?php echo e(route('admin.security.request.log.overview')); ?>",
                type: "GET",
                success: function (result) {
                   if(result.status == '1'){
                       let html = '';
                       let data = result.data;
                       //loop through data and create html
                          $.each(data, function (key, value) {
                              //remove underscore from key and make first letter capital
                                key = key.replace(/_/g, " ");
                                key = key.charAt(0).toUpperCase() + key.slice(1);
                            html += '<tr><td>'+key+'</td><td><ul>';
                                $.each(value,function (key1, val){
                                    html += '<li>'+key1+' : '+val+'</li>';
                              });

                            html += '</ul></td></tr>';
                          });


                       $("#loader_requestLogOverview").html('');
                       $("#requestLogOverview").html(html);

                   }
                }
            });
        }
        getTelescopeData();
        getRequestData();

        $('#sidebarSecurityActive').addClass('active');
        $('#sidebarSecurity').addClass('collapse show');
        $('#sidebarSecurityIndex').addClass('active');


    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/security/index.blade.php ENDPATH**/ ?>
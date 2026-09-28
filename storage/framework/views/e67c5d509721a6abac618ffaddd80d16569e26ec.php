<?php $__env->startSection('title'); ?>
    KPI
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .apexcharts-canvas {
            margin-left: auto;
            margin-right: auto;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Team Key Performance Indicator</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KPI</a></li>
                            <li class="breadcrumb-item"><a
                                    href="<?php echo e(route('reports.kpi-v3.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Setup</li>
                        </ol>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="d-flex gap-2">
                            <?php
                        $month = request()->get('month') ?? date('F Y');
                            ?>
                            <input type="text" class="form-control" name="month" value="<?php echo e($month); ?>"
                                   id="target-month" placeholder="Select Month">
                        </div>
                        <div class="flex-grow-2">
                            <button type="button" class="btn btn-primary"
                            <?php if(date('Y-m', strtotime($month)) < date('Y-m')): ?>
                                disabled
                            <?php endif; ?>
                            id="save-button">Save KPI</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row ">
        <div class="col-12">
            <form class="" id="bulk-target-form">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-start gap-3">
                            <div class="form-check">
                                <input class="form-check-input target-type-radio-input" type="radio"
                                       name="target_settings_type" id="bulk-target-settings" value="bulk" checked>
                                <label class="form-check-label" for="bulk-target-settings">
                                    Set Bulk Target
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input target-type-radio-input" type="radio"
                                       id="individual-target-settings" name="target_settings_type" value="individual">
                                <label class="form-check-label" for="individual-target-settings">
                                    Set Target for Teams
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body d-non" id="bulk-target-settings-card-body">
                        <div class="row">
                            <div class="col-6 d-none">
                                <div>
                                    <label for="bulk-target-settings-rental_deals" class="form-label">Rental Deals</label>
                                    <input type="text" class="form-control" name="targets[rental_deals]"
                                           id="bulk-target-settings-rental_deals" placeholder="Enter Rental Deals">
                                </div>
                            </div>
                            <div class="col-6 d-none">
                                <div>
                                    <label for="bulk-target-settings-sales_deals" class="form-label">Sales Deals</label>
                                    <input type="text" class="form-control" name="targets[sales_deals]"
                                           id="bulk-target-settings-sales_deals" placeholder="Sales Deals">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6 mt-2">
                                <div>
                                    <label for="bulk-target-settings-sales_value" class="form-label">Sales Target Value</label>
                                    <input type="text" class="form-control" name="targets[sales_value]"
                                           id="bulk-target-settings-sales_value"
                                           placeholder="Sales Target Value">
                                    <small class="text-danger">Note: It must be like this 4300000 for 4.3M</small>
                                </div>
                            </div>
                            <div class="col-6 d-none">
                                <div>
                                    <label for="bulk-target-settings-gross_revenue" class="form-label">Gross revenue</label>
                                    <input type="text" class="form-control" name="targets[gross_revenue]"
                                           id="bulk-target-settings-gross_revenue" placeholder="Number of Gross Revenue">
                                </div>
                            </div>
                            <div class="col-6 d-none">
                                <div>
                                    <label for="bulk-target-settings-avg_rental_commission" class="form-label">Average rental
                                        commission</label>
                                    <input type="text" class="form-control" name="targets[avg_rental_commission]"
                                           id="bulk-target-settings-avg_rental_commission"
                                           placeholder="Number of Average Rental Commission">
                                </div>
                            </div>
                            <div class="col-6 mt-2 d-none">
                                <div>
                                    <label for="bulk-target-settings-avg_sales_commission" class="form-label">Average sales
                                        commission</label>
                                    <input type="text" class="form-control" name="targets[avg_sales_commission]"
                                           id="bulk-target-settings-avg_sales_commission"
                                           placeholder="Number of Average Sales Commission">
                                </div>
                            </div>
                            <div class="col-6 mt-2 d-none">
                                <div>
                                    <label for="bulk-target-settings-avg_rental_revenue" class="form-label">Average rental
                                        revenue</label>
                                    <input type="text" class="form-control" name="targets[avg_rental_revenue]"
                                           id="bulk-target-settings-avg_rental_revenue"
                                           placeholder="Number of Average Rental Revenue">
                                </div>
                            </div>
                            <div class="col-6 mt-2 d-none">
                                <div>
                                    <label for="bulk-target-settings-avg_sales_revenue" class="form-label">Average sales
                                        revenue</label>
                                    <input type="text" class="form-control" name="targets[avg_sales_revenue]"
                                           id="bulk-target-settings-avg_sales_revenue"
                                           placeholder="Number of Average Sales Revenue">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <div class="d-flex justify-content-start align-items-center gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input bulk-target-type-radio-input" type="radio" name="assign_to" id="bulk-target-settings-to-all-groups" value="all" checked>
                                        <label class="form-check-label" for="bulk-target-settings-to-all-groups">
                                            To all teams
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input bulk-target-type-radio-input" type="radio" name="assign_to" id="bulk-target-settings-to-selected-groups" value="selected">
                                        <label class="form-check-label" for="bulk-target-settings-to-selected-groups">
                                            To selected teams
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-2 d-none">
                                    <select id="group_ids" name="group_ids[]" required class="form-select select2" multiple>
                                        <option value="" disabled>Select Teams</option>
                                        <?php $__currentLoopData = $groups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($group->id); ?>"><?php echo e($group->name); ?> (<?php echo e($group->user->full_name); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="col-12 d-none" id="individual-target-form-section">
            <form id="individual-target-form">
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded-circle fs-3 ax-text">
                                                <?php echo e($group?->name); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-2 name">
                                        <a href="<?php echo e(route('reports.team.kpi-v2.get.report.page',['id'=>$group?->id])); ?>" class="text-primary" target="_blank"> (<?php echo e($group?->user?->full_name); ?>)</a>
                                        <input type="hidden" name="groups[]" class="group-target-settings-<?php echo e($group->id); ?>-inputs" value="<?php echo e($group->id); ?>">
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-2">
                                    <div class="form-check">
                                        <input class="form-check-input group-target-settings-na" type="checkbox"
                                               id="group-target-settings-<?php echo e($group?->id); ?>-na"
                                               data-id="<?php echo e($group->id); ?>">
                                        <label class="form-check-label" for="group-target-settings-<?php echo e($group->id); ?>-na">
                                            Not Applicable (NA)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="group-<?php echo e($group->id); ?>-card-body">
                            <div class="row">
                                <div class="col-6 d-none">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="group-target-settings-<?php echo e($group->id); ?>-rental_deals" class="form-label">Rental Deals</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                            <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-rental_deals-progress">
                                            </div>
                                            <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-rental_deals-count"></div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][rental_deals]"
                                           id="group-target-settings-<?php echo e($group->id); ?>-rental_deals" placeholder="Number of Rental Deals">
                                </div>
                                <div class="col-6 d-none">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="group-target-settings-<?php echo e($group->id); ?>-sales_deals" class="form-label">Sales Deals</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                            <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-sales_deals-progress">
                                            </div>
                                            <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-sales_deals-count"></div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][sales_deals]"
                                           id="group-target-settings-<?php echo e($group->id); ?>-sales_deals" placeholder="Number of Sales Deals">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6 d-none">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="group-target-settings-<?php echo e($group->id); ?>-gross_revenue" class="form-label">Gross Revenue</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                            <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-gross_revenue-progress">
                                            </div>
                                            <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-gross_revenue-count"></div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][gross_revenue]"
                                           id="group-target-settings-<?php echo e($group->id); ?>-gross_revenue"
                                           placeholder="Number of Gross Revenue">
                                </div>
                                <div class="col-6 d-none">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="group-target-settings-<?php echo e($group->id); ?>-avg_rental_commission" class="form-label">Average Rental Commission</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                            <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-avg_rental_commission-progress">
                                            </div>
                                            <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-avg_rental_commission-count"></div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][avg_rental_commission]"
                                           id="group-target-settings-<?php echo e($group->id); ?>-avg_rental_commission"
                                           placeholder="Number of Average Rental Commission">
                                </div>
                                <div class="col-6 mt-2 d-none">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="group-target-settings-<?php echo e($group->id); ?>-avg_sales_commission" class="form-label">Average Sales Commission</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                            <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-avg_sales_commission-progress">
                                            </div>
                                            <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-avg_sales_commission-count"></div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][avg_sales_commission]"
                                           id="group-target-settings-<?php echo e($group->id); ?>-avg_sales_commission"
                                           placeholder="Number of Average Sales Commission">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="row">

                                        <div class="col-12 mt-2 ">
                                            <div class="d-flex justify-content-between align-items-center gap-5">
                                                <label for="group-target-settings-<?php echo e($group->id); ?>-sales_value" class="form-label">Sales Target Value (In Million)</label>
                                                <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                                    <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-sales_value-progress">
                                                    </div>
                                                    <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-sales_value-count"></div>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][sales_value]"
                                                   id="group-target-settings-<?php echo e($group->id); ?>-sales_value"
                                                   placeholder="Sales Target Value">
                                            <small class="text-danger">Note: It must be like this 4300000 for 4.3M</small>
                                        </div>

                                        <div class="col-12 mt-2 d-none">
                                            <div class="d-flex justify-content-between align-items-center gap-5">
                                                <label for="group-target-settings-<?php echo e($group->id); ?>-average_transaction_value" class="form-label">Average Transaction Value (In Million)</label>
                                                <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                                    <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-average_transaction_value-progress">
                                                    </div>
                                                    <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-average_transaction_value-count"></div>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][average_transaction_value]"
                                                   id="group-target-settings-<?php echo e($group->id); ?>-average_transaction_value"
                                                   placeholder="Average Transaction Value">
                                        </div>



                                <div class="col-12 mt-2 d-none">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="group-target-settings-<?php echo e($group->id); ?>-avg_rental_revenue" class="form-label">Average Rental Revenue</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                            <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-avg_rental_revenue-progress">
                                            </div>
                                            <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-avg_rental_revenue-count"></div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][avg_rental_revenue]"
                                           id="group-target-settings-<?php echo e($group->id); ?>-avg_rental_revenue"
                                           placeholder="Number of Average Rental Revenue">
                                </div>
                                <div class="col-12 mt-2 d-none">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="group-target-settings-<?php echo e($group->id); ?>-avg_sales_revenue" class="form-label">Average Sales Revenue</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center gap-2">
                                            <div class="progress animated-progress custom-progress mb-1" style="width: 200px" id="group-target-settings-<?php echo e($group->id); ?>-avg_sales_revenue-progress">
                                            </div>
                                            <div class="float-end" id="group-target-settings-<?php echo e($group->id); ?>-avg_sales_revenue-count"></div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control group-target-settings-<?php echo e($group->id); ?>-inputs" name="targets[<?php echo e($group->id); ?>][avg_sales_revenue]"
                                           id="group-target-settings-<?php echo e($group->id); ?>-avg_sales_revenue"
                                           placeholder="Number of Average Sales Revenue">
                                </div>
                                </div>
                                </div>
                                <div class="col-6">
                                    <p class="text-center"><?php echo e($month); ?> Report</p>
                                    <div class="text-center">
                                    <div  id="comparePieChart<?php echo e($group->id); ?>" class="mx-auto" style="height: 200px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?php echo e(asset('assets/libs/cleave.js/cleave.js.min.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('in_page_scripts'); ?>
    <script>

        $(document).on("change", ".bulk-target-type-radio-input", function (event){
            const value = $(this).val()
            if (value === "all"){
                $("#group_ids").parent().addClass("d-none")
            }

            if (value === "selected"){
                $("#group_ids").parent().removeClass("d-none")
            }
        })
        $(document).ready(function () {
            new Cleave('#bulk-target-settings-sales_value', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        })
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
            $("#target-month").datepicker({
                format: {
                    toDisplay: function (date, format, language) {
                        var d = new Date(date);
                        var options = { year: 'numeric', month: 'long' };
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
                // startDate: new Date(),
                autoclose: true,
            });
        })
        $(document).on("change", ".target-type-radio-input", function (event) {
            const value = $(this).val()
            if (value === "bulk") {
                $("#bulk-target-settings-card-body").removeClass("d-none")
                $("#individual-target-form-section").addClass("d-none")
            }

            if (value === "individual") {
                $("#bulk-target-settings-card-body").addClass("d-none")
                $("#individual-target-form-section").removeClass("d-none")
            }
        })




        $(document).on("change", ".group-target-settings-na", function () {
            const id = $(this).data("id")
            if (this.checked) {
                $(`#group-${id}-card-body`).addClass("d-none")
                $(`.group-target-settings-${id}-inputs`).prop("disabled", true)
            } else {
                $(`.group-target-settings-${id}-inputs`).prop("disabled", false)
                $(`#group-${id}-card-body`).removeClass("d-none")
            }
        })

        $(document).on("click", "#save-button", function () {
            let type = $("#individual-target-settings").prop("checked") ? "individual" : "bulk";
            let month = convertMonthToDateFormat($("#target-month").val());

            if (!month) {
                showToastify("Please select month", "error")
                return
            }
            if (type === "bulk") {
                $("#bulk-target-form").submit()
            }

            if (type === "individual") {
                $("#individual-target-form").submit()
            }
        })

        $(document).on("submit", "#bulk-target-form", function (e) {
            e.preventDefault();
            let formData = new FormData($(this)[0]);
            formData.append("month", convertMonthToDateFormat($("#target-month").val()))
            $.ajax({
                url: "<?php echo e(route('reports.team.kpi-v2.target.bulk.save')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    showToastify(result.msg, "success")
                    $('.inputs').val('');
                },
                error: function (jqXhr, json, errorThrown) {
                    console.log(jqXhr);
                    console.log(json);
                    console.log(errorThrown);

                    showToastify('Something went wrong...', "error")
                },
            });

        })

        $(document).on("submit", "#individual-target-form", function (e) {
            e.preventDefault();
            let formData = new FormData($(this)[0]);
            formData.append("month", convertMonthToDateFormat($("#target-month").val()))
            $.ajax({
                url: "<?php echo e(route('reports.team.kpi-v2.target.individual.save')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    showToastify(result.msg, "success")
                    $('.inputs').val('');
                },
                error: function (jqXhr, json, errorThrown) {
                    console.log(jqXhr);
                    console.log(json);
                    console.log(errorThrown);

                    showToastify('Something went wrong...', "error")
                },
            });

        })

        const getBgColor = (percentage) => {
            if (percentage >= 75) {
                return "success"
            } else if (percentage >= 50) {
                return "warning"
            } else {
                return "danger"
            }
        }
        $("#target-month").change(function () {
          //reload this page with month parameter
            window.location.href = "<?php echo e(route('reports.team.kpi-v2.team.setup')); ?>?month=" + $("#target-month").val();
        })
        const getThisMonthReport = () => {
            console.log("getThisMonthReport")
            let month = $("#target-month").val();
            //convert to Y-m format
            month = convertMonthToDateFormat(month);
            $.ajax({
                url: "<?php echo e(route('reports.team.kpi-v2.get-report')); ?>",
                type: 'GET',
                data: {
                    month: month,
                },
                success: function (response) {
                    if (response.status == 1) {
                        let data = response.data;
                        let currentTarget = 0;
                        Object.entries(data).forEach(([key, item]) => {
                            item.targets?.map(target => {
                                currentTarget = target.target;
                                currentTarget = currentTarget;
                                $(`#group-target-settings-${item.group.id}-${target.kpi}`).val(currentTarget)
                            });
                        })

                    } else {

                    }
                }
            })
        }

        const getReport = () => {
            const subtractMonth = (monthStr) => {
                let [year, month] = monthStr.split('-').map(Number);

                let date = new Date(year, month - 1);

                date.setMonth(date.getMonth() - 1);

                let newYear = date.getFullYear();
                let newMonth = date.getMonth() + 1;

                newMonth = newMonth < 10 ? '0' + newMonth : newMonth;

                return `${newYear}-${newMonth}`;
            }

            $.ajax({
                url: "<?php echo e(route('reports.team.kpi-v2.get-report')); ?>",
                type: 'GET',
                data: {
                    month: convertMonthToDateFormat($("#target-month").val()),
                },
                success: function (response) {
                    if (response.status == 1) {
                        let data = response.data;
                        let calclulatedData = [];
                        Object.entries(data).forEach(([key, item]) => {
                            const pieChartData = []; let currentTarget = 0;
                            item.targets?.map(target => {
                                currentTarget = target.target;
                                currentTarget = currentTarget;
                                $(`#group-target-settings-${item.group.id}-${target.kpi}`).val(currentTarget)

                                new Cleave(`#group-target-settings-${item.group.id}-${target.kpi}`, {
                                    numeral: true,
                                    numeralThousandsGroupStyle: 'thousand'
                                });

                                $(`#group-target-settings-${item.group.id}-${target.kpi}-progress`).html(
                                    `<div class="progress-bar bg-${getBgColor(target.result?.achieved_percentage || 0)}" role="progressbar"
                                                     style="width: ${target.result?.achieved_percentage || 0}%"
                                                     aria-valuenow="${target.result?.achieved_percentage || 0}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100"></div>`);

                                $(`#group-target-settings-${item.group.id}-${target.kpi}-count`).html(`${formatNumberMetricPrefixes(target.result?.achieved) ?? 0}/${formatNumberMetricPrefixes(target.target)}`)

                                if (target.result){
                                    pieChartData.push({
                                        title: ucwords(target.kpi),
                                        count: parseFloat(target.result.achieved)
                                    })

                                }

                            })
                            comparePieChart(pieChartData, `comparePieChart${item.group.id}`);
                        })
                    } else {

                    }
                }
            })
        }

        getReport();
        // getThisMonthReport();
        const comparePieChart = (data,elementId) => {
            const labels = [];
            const series = [];
            data?.map(item => {
                labels.push(item.title)
                series.push(item.count)
            })
            var options = {
                series: series,
                chart: {
                    width: '200px',
                    height: '200px',
                    type: 'pie',
                },
                labels: labels,
                colors: ['#d57342', '#d3a188'],
                plotOptions: {
                    pie: {
                        dataLabels: {
                            offset: -5,
                        },
                    },
                },
                grid: {
                    padding: {
                        top: 0,
                        bottom: 0,
                        left: 0,
                        right: 0,
                    },
                },
                dataLabels: {
                    formatter(val, opts) {
                        const name = opts.w.globals.labels[opts.seriesIndex]
                        return [name, val.toFixed(1) + '%']
                    },
                },
                legend: {
                    show: false,
                },
            };

            var chart = new ApexCharts(document.getElementById(elementId), options);
            chart.render();

        }

        $("#kpiReportSidebar").addClass('active');
        // $('#sidebarTeamKpiV2Report').addClass('active');
        // $("#reportsActive").addClass('active');

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/kpi_v2/team/setup.blade.php ENDPATH**/ ?>
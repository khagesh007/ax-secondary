<?php $__env->startSection('title'); ?>
    KPI
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .custom-progress{
            height: 5px!important;
            padding: 0px!important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Key Performance Indicator</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">KPI</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e(route('reports.kpi-v3.dashboard')); ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active">Setup</li>
                        </ol>
                    </div>
                    <div class="">
                        <div class="d-flex gap-2">
                            <?php if($isSuperAdmin): ?>
                                <div style="width: 250px">
                                    <select id="manager_id" name="manager_id" class="form-select select2">
                                        <option value="">Select Manager</option>
                                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($group->user): ?>
                                                <option value="<?php echo e($group->user->id); ?>" data-groupid="<?php echo e($group->id); ?>" <?php echo e(($group->user->id == request("manager")) ? 'selected' : ''); ?>><?php echo e($group->name); ?> (<?php echo e($group->user->full_name); ?>)</option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <div>
                                    <input type="hidden" name="manager_id" id="manager_id" value="<?php echo e(auth()->id()); ?>">
                                </div>
                            <?php endif; ?>
                            <div>
                                <input type="text" class="form-control" name="month" value="<?php echo e(request("month") ? date("F Y", strtotime(request("month"))) : date('F Y')); ?>" id="targetMonth" placeholder="Select Month">
                            </div>
                            <div class="">
                                <button type="button" class="btn btn-primary" id="save-button">Save KPI</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form class="card" id="bulk-target-form">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-start gap-3">
                        <div class="form-check">
                            <input class="form-check-input target-type-radio-input" type="radio" name="target_settings_type" id="bulk-target-settings" value="bulk" checked>
                            <label class="form-check-label" for="bulk-target-settings">
                                Set Bulk Target
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input target-type-radio-input" type="radio" id="individual-target-settings" name="target_settings_type" value="individual">
                            <label class="form-check-label" for="individual-target-settings">
                                Set Target for Agents Individually
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="bulk-target-settings-card-body">
                    <div class="row">
                        <div class="col-6">
                            <div>
                                <label for="bulk-target-settings-sales_target" class="form-label">Sales Target</label>
                                <input type="text" class="form-control " name="targets[sales_target]" id="bulk-target-settings-sales_target" placeholder="Sales Target">
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <label for="bulk-target-settings-live_listings" class="form-label">Number of Listings (Live)</label>
                                <input type="text" class="form-control numeric-input" name="targets[live_listings]" id="bulk-target-settings-live_listings" placeholder="Number of Listings (Live)">
                            </div>
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <div>
                                <label for="bulk-target-settings-new_listings" class="form-label">Number of Listings (New)</label>
                                <input type="text" class="form-control numeric-input" name="targets[new_listings]" id="bulk-target-settings-new_listings" placeholder="Number of Listings (New)">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <div class="form-check">
                                    <input class="form-check-input bulk-target-type-radio-input" type="radio" name="assign_to" id="bulk-target-settings-to-all-agents" value="all" checked>
                                    <label class="form-check-label" for="bulk-target-settings-to-all-agents">
                                        To all my agents
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input bulk-target-type-radio-input" type="radio" name="assign_to" id="bulk-target-settings-to-selected-agents" value="selected">
                                    <label class="form-check-label" for="bulk-target-settings-to-selected-agents">
                                        To selected agents
                                    </label>
                                </div>
                            </div>
                            <div class="mt-2 d-none">
                                <select id="agent_ids" name="agent_ids[]" required class="form-select select2" multiple>
                                    <option value="" disabled>Select Agents</option>
                                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->full_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form id="individual-target-form">
                <?php $__currentLoopData = $agents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card agent-card d-none">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e($agent->thumbnail); ?>" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1 ms-2 name">
                                        <a href="<?php echo e(route('reports.group.kpi-v2.view', $agent->id)); ?>" class="text-primary" target="_blank"><?php echo e($agent->full_name); ?></a>
                                        <input type="hidden" name="agents[]" class="agent-target-settings-<?php echo e($agent->id); ?>-inputs" value="<?php echo e($agent->id); ?>">
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-2">
                                    <div class="form-check">
                                        <input class="form-check-input agent-target-settings-na" type="checkbox" id="agent-target-settings-<?php echo e($agent->id); ?>-na" data-id="<?php echo e($agent->id); ?>">
                                        <label class="form-check-label" for="agent-target-settings-<?php echo e($agent->id); ?>-na">
                                            Not Applicable (NA)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body agent-card-body" id="agent-<?php echo e($agent->id); ?>-card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="agent-target-settings-<?php echo e($agent->id); ?>-sales_target" class="form-label">Sales Target</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center">
                                            <div class="progress animated-progress custom-progress" style="width: 200px" id="agent-target-settings-<?php echo e($agent->id); ?>-sales_target-progress">
                                            </div>
                                            <div id="agent-target-settings-<?php echo e($agent->id); ?>-sales_target-badge"></div>
                                            <small class="float-end" id="agent-target-settings-<?php echo e($agent->id); ?>-sales_target-count"></small>
                                        </div>
                                    </div>
                                    <input type="text" name="targets[<?php echo e($agent->id); ?>][sales_target]" class="form-control cleave  agent-target-settings-sales_target-inputs agent-target-settings-<?php echo e($agent->id); ?>-inputs" id="agent-target-settings-<?php echo e($agent->id); ?>-sales_target" placeholder="Sales Target">
                                <small class="text-danger">Note: It must be like this 4300000 for 4.3M</small>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="agent-target-settings-<?php echo e($agent->id); ?>-live_listings" class="form-label">Number of Listings (Live)</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center">
                                            <div class="progress animated-progress custom-progress" style="width: 200px" id="agent-target-settings-<?php echo e($agent->id); ?>-live_listings-progress">
                                            </div>
                                            <div id="agent-target-settings-<?php echo e($agent->id); ?>-live_listings-badge"></div>
                                            <small class="float-end" id="agent-target-settings-<?php echo e($agent->id); ?>-live_listings-count"></small>
                                        </div>
                                    </div>
                                    <input type="text" name="targets[<?php echo e($agent->id); ?>][live_listings]" class="form-control numeric-input agent-target-settings-live_listings-inputs agent-target-settings-<?php echo e($agent->id); ?>-inputs" id="agent-target-settings-<?php echo e($agent->id); ?>-live_listings" placeholder="Number of Listings (Live)">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center gap-5">
                                        <label for="agent-target-settings-<?php echo e($agent->id); ?>-new_listings" class="form-label">Number of Listings (New)</label>
                                        <div class="flex-grow-1 d-flex justify-content-end align-items-center">
                                            <div class="progress animated-progress custom-progress" style="width: 200px" id="agent-target-settings-<?php echo e($agent->id); ?>-new_listings-progress">
                                            </div>
                                            <div id="agent-target-settings-<?php echo e($agent->id); ?>-new_listings-badge"></div>
                                            <small class="float-end" id="agent-target-settings-<?php echo e($agent->id); ?>-new_listings-count"></small>
                                        </div>
                                    </div>
                                    <input type="text" name="targets[<?php echo e($agent->id); ?>][new_listings]" class="form-control numeric-input agent-target-settings-new_listings-inputs agent-target-settings-<?php echo e($agent->id); ?>-inputs" id="agent-target-settings-<?php echo e($agent->id); ?>-new_listings" placeholder="Number of Listings (New)">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <div class="d">
                                        <label for="agent-target-settings-<?php echo e($agent->id); ?>-leads" class="form-label">Leads & Deals</label>
                                        <div class="pt-2">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-truncate text-muted fs-14 mb-0"><i
                                                            class="mdi mdi-circle align-middle text-info me-2"></i>Leads
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <p class="mb-0" id="agent-target-settings-<?php echo e($agent->id); ?>-leads-count">0</p>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-truncate text-muted fs-14 mb-0"><i
                                                            class="mdi mdi-circle align-middle text-success me-2"></i>Deals
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <p class="mb-0" id="agent-target-settings-<?php echo e($agent->id); ?>-deals-count">0</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d">
                                        <label for="agent-target-settings-<?php echo e($agent->id); ?>-calls" class="form-label">Calls: <span id="agent-target-settings-<?php echo e($agent->id); ?>-calls-count">0</span></label>
                                        <div class="">
                                            <div class="progress progress-lg rounded-pill" id="agent-target-settings-<?php echo e($agent->id); ?>-calls-progress-list">
                                            </div>
                                        </div>
                                        <div class="pt-2" id="agent-target-settings-<?php echo e($agent->id); ?>-calls-count-list"></div>
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
    <!-- dashboard init -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?php echo e(asset('assets/libs/cleave.js/cleave.js.min.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('in_page_scripts'); ?>
    <script>
        $(document).ready(function () {
            new Cleave('.cleave', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        })


        $(document).on("change", "#manager_id", function (){
            window.location = "<?php echo e(route('reports.group.kpi-v2.setup')); ?>?manager=" + $(this).val()
        })

        $(document).on("change", ".target-type-radio-input", function (event){
            const value = $(this).val()
            if (value === "bulk"){
                $("#bulk-target-settings-card-body").removeClass("d-none")
                $(".agent-card").addClass("d-none")
            }

            if (value === "individual"){
                $("#bulk-target-settings-card-body").addClass("d-none")
                $(".agent-card").removeClass("d-none")
            }
        })

        $(document).on("change", ".bulk-target-type-radio-input", function (event){
            const value = $(this).val()
            if (value === "all"){
                $("#agent_ids").parent().addClass("d-none")
            }

            if (value === "selected"){
                $("#agent_ids").parent().removeClass("d-none")
            }
        })

        $(document).on("change", ".agent-target-settings-na", function (){
            const id = $(this).data("id")
            if (this.checked){
                $(`#agent-${id}-card-body`).addClass("d-none")
                $(`.agent-target-settings-${id}-inputs`).prop("disabled", true)
            }else{
                $(`.agent-target-settings-${id}-inputs`).prop("disabled", false)
                $(`#agent-${id}-card-body`).removeClass("d-none")
            }
        })

        $(document).on("click", "#save-button",  function (){
            let type = $("#individual-target-settings").prop("checked") ? "individual" : "bulk";
            let month = convertMonthToDateFormat($("#targetMonth").val());

            if (!month){
                showToastify("Please select month", "error")
                return
            }
            if(type === "bulk"){
                $("#bulk-target-form").submit()
            }

            if(type === "individual"){
                $("#individual-target-form").submit()
            }
        })

        $(document).on("submit", "#bulk-target-form", function(e) {
            e.preventDefault();
            if (!$("#manager_id").val()){
                showToastify('Please select manager', "error");
                return
            }
            let formData = new FormData($(this)[0]);
            formData.append("month", convertMonthToDateFormat($("#targetMonth").val()))
            formData.append("manager_id", $("#manager_id").val())
            $.ajax({
                url: "<?php echo e(route('reports.group.kpi-v2.save-bulk-target')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    showToastify(result.msg, "success")
                    $('.inputs').val('');
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log(jqXhr);
                    console.log(json);
                    console.log(errorThrown);

                    showToastify('Something went wrong...', "error")
                },
            });

        })

        flatpickr('#targetMonth', {
            "minDate": new Date(),
            enableTime: false,
            dateFormat: "F Y",
        });

        $(document).on("submit", "#individual-target-form", function(e) {
            e.preventDefault();
            if (!$("#manager_id").val()){
                showToastify('Please select manager', "error");
                return
            }
            if(!$("#targetMonth").val()){
                showToastify('Please select a date', "error");
                return
            }
            let formData = new FormData($(this)[0]);
            console.log('Before convert '+$("#targetMonth").val())
            let selected_date = convertMonthToDateFormat($("#targetMonth").val());
            console.log('after convert '+selected_date)
            formData.append("month", selected_date)

            formData.append("manager_id", $("#manager_id").val())
            $.ajax({
                url: "<?php echo e(route('reports.group.kpi-v2.save-individual-target')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    showToastify(result.msg, "success")
                    $('.inputs').val('');
                },
                error: function(jqXhr, json, errorThrown) {
                    console.log(jqXhr);
                    console.log(json);
                    console.log(errorThrown);

                    showToastify('Something went wrong...', "error")
                },
            });

        })

        const getBgColor = (percentage) => {
            if (percentage >= 75){
                return "success"
            }else if(percentage >= 50){
                return "warning"
            }else {
                return "danger"
            }
        }

        // function convertMonthToDateFormat(dateString) {
        //     let date = new Date(dateString);
        //     let year = date.getFullYear();
        //
        //     let month = (date.getMonth() + 1).toString().padStart(2, '0');
        //     return `${year}-${month}`;
        // }

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


        $(document).on("change", "#targetMonth", function (){
            if (convertMonthToDateFormat($(this).val()) < "<?php echo e(date('Y-m')); ?>"){
                $("#save-button").prop("disabled", true)
            }else{
                $("#save-button").prop("disabled", false)
            }
            getReport("<?php echo e($selectedGroup->id ?? ''); ?>");
        })

        const getReport = (groupId) => {
            const subtractMonth = (monthStr) => {
                let [year, month] = monthStr.split('-').map(Number);

                let date = new Date(year, month - 1);

                date.setMonth(date.getMonth() - 1);

                let newYear = date.getFullYear();
                let newMonth = date.getMonth() + 1;

                newMonth = newMonth < 10 ? '0' + newMonth : newMonth;

                return `${newYear}-${newMonth}`;
            }

            if (groupId){
                $.ajax({
                    url: "<?php echo e(route('reports.group.kpi-v2.get-report')); ?>",
                    type: 'GET',
                    data: {
                        group_id: groupId,
                        month: convertMonthToDateFormat($("#targetMonth").val()),
                    },
                    success: function(response) {
                        if(response.status == 1) {
                            let data = response.data;

                            const getCallColor = (title) => {
                                if (title === "AX Contacts Calls"){
                                    return "primary"
                                }if (title === "AX Contacts WhatsApp Messages"){
                                    return "info"
                                }if (title === "Dialed Calls"){
                                    return "success"
                                }if (title === "Received Calls"){
                                    return "secondary"
                                }if (title === "Answer Rate"){
                                    return "warning"
                                }
                                return "danger"
                            }

                            Object.entries(data).forEach(([key, item]) => {
                                $(`#agent-target-settings-${item.agent.id}-leads-count`).html(`0`)
                                $(`#agent-target-settings-${item.agent.id}-deals-count`).html(`0`)
                                $(`#agent-target-settings-${item.agent.id}-calls-count`).html(`0`)

                                $(`#agent-target-settings-${item.agent.id}-live_listings`).val("")
                                $(`#agent-target-settings-${item.agent.id}-new_listings`).val("")
                                $(`#agent-target-settings-${item.agent.id}-sales_target`).val("")

                                $(`#agent-target-settings-${item.agent.id}-live_listings-count`).html(`0/0`)
                                $(`#agent-target-settings-${item.agent.id}-new_listings-count`).html(`0/0`)
                                $(`#agent-target-settings-${item.agent.id}-sales_target-count`).html(`0/0`)

                                $(`#agent-target-settings-${item.agent.id}-live_listings-progress`).html("");
                                $(`#agent-target-settings-${item.agent.id}-new_listings-progress`).html("");
                                $(`#agent-target-settings-${item.agent.id}-sales_target-progress`).html("");

                                $(`#agent-target-settings-${item.agent.id}-live_listings-badge`).html("");
                                $(`#agent-target-settings-${item.agent.id}-new_listings-badge`).html("");
                                $(`#agent-target-settings-${item.agent.id}-sales_target-badge`).html("");
                                item.targets?.map(target => {
                                    $(`#agent-target-settings-${target.agent_id}-${target.kpi}`).val(target.target)

                                    new Cleave(`#agent-target-settings-${target.agent_id}-${target.kpi}`, {
                                        numeral: true,
                                        numeralThousandsGroupStyle: 'thousand'
                                    });
                                    $(`#agent-target-settings-${target.agent_id}-${target.kpi}-progress`).html(
                                        `<div class="progress-bar bg-${getBgColor(target.result?.achieved_percentage || 0)}" role="progressbar"
                                                     style="width: ${target.result?.achieved_percentage || 0}%"
                                                     aria-valuenow="${target.result?.achieved_percentage || 0}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100"></div>`);
                                    $(`#agent-target-settings-${target.agent_id}-${target.kpi}-badge`).html(
                                        `<span class="badge bg-${getBgColor(target.result?.achieved_percentage || 0)}-subtle text-${getBgColor(target.result?.achieved_percentage || 0)}" title="Percentage of achieved">
                                                    ${target.result?.achieved_percentage || 0}%
                                                </span>`);

                                    $(`#agent-target-settings-${target.agent_id}-${target.kpi}-count`).html(`${formatNumberMetricPrefixes(parseFloat((target.result?.achieved || "0").replace(/[^\d]/g, '')))}/${formatNumberMetricPrefixes(parseFloat((target.target || "0").replace(/[^\d]/g, '')))}`)
                                })


                                let callsProgressHtml = "";
                                let callsCountHtml = "";

                                item.other_reports?.map(report => {
                                    if (report.kpi === "calls"){
                                        report?.data.map(rd => {

                                            const percentage = calculatePercentage(report.count, rd.count)
                                            callsProgressHtml += `<div class="progress-bar bg-${getCallColor(rd.title)}" role="progressbar" style="width: ${percentage}%"
                                                     aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100"></div>`;

                                            callsCountHtml += `<div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <p class="text-truncate text-muted fs-14 mb-0"><i
                                                            class="mdi mdi-circle align-middle text-${getCallColor(rd.title)} me-2"></i>${rd.title}
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <p class="mb-0">${formatNumberMetricPrefixes(rd.count)}(${percentage}%)</p>
                                                </div>
                                            </div>`;
                                        })
                                        $(`#agent-target-settings-${report.agent_id}-calls-count`).html(formatNumberMetricPrefixes(report.count || 0))
                                    }else{
                                        $(`#agent-target-settings-${item.agent.id}-${report.kpi}-count`).html(`${formatNumberMetricPrefixes(report.count || 0)}`)
                                    }
                                })

                                $(`#agent-target-settings-${item.agent.id}-calls-progress-list`).html(callsProgressHtml)
                                $(`#agent-target-settings-${item.agent.id}-calls-count-list`).html(callsCountHtml)


                            })
                        }else{

                        }
                    }
                })
            }
        }

        getReport("<?php echo e($selectedGroup->id ?? ''); ?>");

        function initPriceInput(price = "") {
          //  $("#bulk-target-settings-sales_target").val(price).trigger("change");
            new Cleave('#bulk-target-settings-sales_target', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
            new Cleave('.cleave', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }

        initPriceInput();
     const numericInputs = () =>{
         document.addEventListener('input', function (event) {
             if (event.target.matches('.numeric-input')) {
                 event.target.value = event.target.value.replace(/[^0-9]/g, '');
             }
         });

         document.addEventListener('paste', function (event) {
             if (event.target.matches('.numeric-input')) {
                 let pasteData = event.clipboardData.getData('text').replace(/[^0-9]/g, '');
                 event.preventDefault();
                 event.target.value = pasteData;
             }
         });

         document.addEventListener('keydown', function (event) {
             if (event.target.matches('.numeric-input')) {
                 // Allow backspace (8), delete (46), and arrow keys (37-40)
                 if ([8, 46, 37, 38, 39, 40].includes(event.keyCode)) {
                     return;
                 }
                 // Prevent non-numeric keys
                 if (event.key < '0' || event.key > '9') {
                     event.preventDefault();
                 }
             }
         });
     }
        numericInputs()

        $("#kpiReportSidebar").addClass('active');
        // $('#sidebarAgentKpiV2Report').addClass('active');
        // $("#reportsActive").addClass('active');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/kpi_v2/setup.blade.php ENDPATH**/ ?>
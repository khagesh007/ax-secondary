<?php $__env->startSection('title'); ?>
    Quiz
<?php $__env->stopSection(); ?>
<?php $__env->startSection('style'); ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    
    <style>
        .img-link .active {
            border: 3px solid #d3a188 !important;
        }

        .theme-active {
            border: 2px solid #ff232357 !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Quizzes</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Quizzes</a></li>
                            <li class="breadcrumb-item active">Quiz List</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Create')): ?>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#addModal"
                                        class="btn btn-success float-right action-add addModal" style="float-right"
                                        id="newBtn">
                                    <i class="ri-add-line align-middle me-1"></i> Add new
                                </button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Delete')): ?>
                                <button class="btn btn-soft-danger action-delete d-none" onclick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Edit')): ?>
                            <button type="button" class="btn  btn-soft-warning text-danger d-none duplicateQuiz" title="Duplicate This Quiz">
                                <i class="mdi mdi-content-duplicate"></i>
                            </button>
                            <button id="editBtn" type="button" class="btn btn-info action-edit d-none">Edit</button>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Delete')): ?>
                            <button class="btn btn-soft-danger action-delete d-none" id="deleteBtn"><i
                                    class="ri-delete-bin-2-line"></i></button>
                                    <?php endif; ?>
                            <button type="button" id="closeBtn" class="btn btn-light action-close d-none"
                                    data-bs-dismiss="modal">Close
                            </button>
                            <button id="saveBtn" type="submit" class="btn btn-primary action-save d-none">Submit
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <div class="">
                    <div class=" row">
                        <div>
                            <form id="mainForm" enctype="multipart/form-data" class="needs-validation" novalidate>

                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body mt-3">
                                            <div class="row g-3">
                                                <div class="col-6 row">
                                                    <div class="col-3 mt-2">
                                                        <label for="title" class="form-label">Name*</label>
                                                    </div>
                                                    <div class="col-9 mt-2">
                                                        <input type="hidden" name="id" id="id" value="">
                                                        <input type="text" class="form-control inputs" id="name"
                                                               name="name" placeholder="Enter name" required>
                                                        <div class="invalid-feedback">
                                                            Please enter the name.
                                                        </div>
                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="title" class="form-label">Description*</label>
                                                    </div>
                                                    <div class="col-9 mt-2">
                                                <textarea class="form-control inputs" id="description"
                                                          placeholder="Enter description" name="description" rows="6"
                                                          maxlength="2000"></textarea>
                                                        <div class="invalid-feedback">
                                                            Please enter the description.
                                                        </div>
                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="role_id" class="form-label">Trial Quiz
                                                            <span class="d-none" id="trial_quiz_id_required">*</span>
                                                        </label>


                                                    </div>
                                                    <div class="col-9 mt-2">
                                                        <select class="form-control inputs" name="trial_quiz_id"
                                                                id="trial_quiz_id">
                                                            <option value="" selected disabled>Select Trial Quiz
                                                            </option>

                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Please select a trial quiz.
                                                        </div>
                                                        <small class="text-muted">Note: Not needed for a trial quiz.</small>

                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="quiz_level_id" class="form-label">Level*</label>


                                                    </div>
                                                    <div class="col-9 mt-2">
                                                        <select class="form-control inputs" name="quiz_level_id"
                                                                id="quiz_level_id" required>
                                                            <option selected value="" disabled>Select Level</option>
                                                            <?php $__currentLoopData = $quiz_levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($level->id); ?>"><?php echo e($level->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Please select the level.
                                                        </div>
                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="role_id" class="form-label">For Roles</label>


                                                    </div>
                                                    <div class="col-9 mt-2">
                                                        <select class="form-control inputs" name="role_id[]"
                                                                id="role_id" multiple>
                                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>


                                                    <div class="col-3 mt-2">
                                                        <label for="thumbnail" class="form-label">Thumbnail</label>
                                                    </div>
                                                    <div class="col-9 mt-2">
                                                               <div class="input-group">
                                                                <input type="file" name="thumbnail" class="form-control inputs" id="thumbnail"
                                                                accept="image/png, image/jpeg, image/gif"
                                                                aria-describedby="thumbGroupFileBtn" aria-label="Upload">
                                                                <button class="btn btn-outline-primary inputs" type="button"
                                                                onclick="removeSelectedFile()"
                                                                id="thumbGroupFileBtn"><i class=" ri-delete-bin-line"></i></button>
                                                            </div>
                                                    </div>

                                                    <div class="col-3 mt-2">
                                                        <label class="form-check-label"
                                                               for="statusCheckbox">Status</label>
                                                    </div>
                                                    <div class="col-9 mt-2">
                                                        <div
                                                            class="form-check form-switch form-switch-lg form-switch-success"
                                                            dir="ltr">
                                                            <input type="checkbox" name="status"
                                                                   class="form-check-input inputCheckbox"
                                                                   id="statusCheckbox" checked>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-6 row">


                                                    <div class="col-2">
                                                        <label class="form-label" for="rules">Rules</label>
                                                    </div>
                                                    <div class="col-10">

                                                        <textarea class=" inputs" id="rules"  name="rules"></textarea>

                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="title" class="form-label">Time Limit for questions (sec.)</label>
                                                    </div>
                                                    <div class="col-3 mt-2">

                                                        <input type="text" class="form-control inputs"
                                                               id="question_time"
                                                               required
                                                               name="meta[question_time]"
                                                               placeholder="Q. time in seconds ex. 15">

                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="title" class="form-label">Right Answer Marks*</label>
                                                    </div>
                                                    <div class="col-3 mt-2">

                                                        <input type="text" class="form-control inputs"
                                                               id="question_mark"
                                                               name="meta[question_mark]"
                                                               required
                                                               placeholder="Enter default per question marks">

                                                    </div>

                                                    <div class="col-3 mt-2">
                                                        <label for="title" class="form-label">Unanswered Marks*</label>
                                                    </div>
                                                    <div class="col-3 mt-2">

                                                        <input type="text" class="form-control inputs" id="skip_mark"
                                                               name="meta[skip_mark]"
                                                               required
                                                               placeholder="Enter default skip question marks">

                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="title" class="form-label">Passing Marks*</label>
                                                    </div>
                                                    <div class="col-3 mt-2">

                                                        <input type="text" class="form-control inputs" id="passing_mark"
                                                               name="meta[passing_mark]"
                                                               required
                                                               placeholder="Enter minimum passing marks">

                                                    </div>
                                                    <div class="col-3 mt-2">
                                                        <label for="title" class="form-label">Negative Marking*</label>
                                                    </div>
                                                    <div class="col-3 mt-2">

                                                        <div
                                                        class="form-check form-switch form-switch-lg form-switch-success"
                                                        dir="ltr">
                                                        <input type="checkbox" name="negative_marking"
                                                               class="form-check-input level_negative_marking inputCheckbox"
                                                               id="negative_marking" checked>
                                                        </div>

                                                    </div>
                                                    <div class="col-3 mt-2 negative_mark">
                                                        <label for="title" class="form-label">Negative Marks*</label>
                                                    </div>
                                                    <div class="col-3 mt-2 negative_mark">

                                                        <input type="text" class="form-control inputs"
                                                               id="negative_marks"
                                                               name="meta[negative_marks]"
                                                               placeholder="Enter default negative marks">

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="themeSelectorArea">
                                                <div class="col-12  bg-light border p-1 px-2 mt-3 mb-2">
                                                    <h4>Select Quiz Theme</h4>
                                                    <input type="text" id="theme" class="d-none inputs"
                                                           name="quiz_theme" value="theme1" required>
                                                </div>
                                                <div class="col-md-3 d-none">

                                                    <input type="radio" id="quiz_theme1" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme1" checked>
                                                    <label for="quiz_theme1" data-theme="theme1"
                                                           class="img-link cursor-pointer themeSelector label_theme1 theme-active">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme1.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>

                                                </div>
                                                <div class="col-md-3 d-none">
                                                    <input type="radio" id="quiz_theme2" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme2">
                                                    <label for="quiz_theme2" data-theme="theme2"
                                                           class="img-link cursor-pointer themeSelector label_theme2">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme2.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>
                                                </div>
                                                <div class="col-md-3 d-none">
                                                    <input type="radio" id="quiz_theme3" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme3">
                                                    <label for="quiz_theme3" data-theme="theme3"
                                                           class="img-link cursor-pointer themeSelector label_theme3">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme3.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>
                                                </div>
                                                <div class="col-md-3 d-none">
                                                    <input type="radio" id="quiz_theme4" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme4">
                                                    <label for="quiz_theme4" data-theme="theme4"
                                                           class="img-link cursor-pointer themeSelector label_theme4">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme4.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <input type="radio" id="quiz_theme5" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme5">
                                                    <label for="quiz_theme5" data-theme="theme5"
                                                           class="img-link cursor-pointer themeSelector label_theme5">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme5.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>
                                                    <div class="text-center"><b>General</b></div>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <input type="radio" id="quiz_theme6" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme6">
                                                    <label for="quiz_theme6" data-theme="theme6"
                                                           class="img-link cursor-pointer themeSelector label_theme6">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme6.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>
                                                    <div class="text-center"><b>Real Estate</b></div>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <input type="radio" id="quiz_theme7" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme7">
                                                    <label for="quiz_theme7" data-theme="theme7"
                                                           class="img-link cursor-pointer themeSelector label_theme7">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme7.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>
                                                    <div class="text-center"><b>AX CAPITAL </b></div>
                                                </div>
                                                <div class="col-md-3 mt-2">
                                                    <input type="radio" id="quiz_theme8" class="d-none inputs"
                                                           name="quiz_theme_radio" value="theme8">
                                                    <label for="quiz_theme8" data-theme="theme8"
                                                           class="img-link cursor-pointer themeSelector label_theme8">
                                                        <img src="<?php echo e(asset('theme/quiz/image/theme8.png')); ?>"
                                                             class="img-thumbnail theme" width="250"/>
                                                    </label>
                                                    <div class="text-center"><b>Dubai</b></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end col-->
                                <button type="submit" class="d-none" id="submitBtn"></button>

                                <!--end col-->
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">

                    <table id="quizTable" class="table align-middle" style="width-100%">
                        <thead class="table-light">
                        <tr class="text-uppercase">
                            <th scope="col" style="width-50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Thumbnail</th>
                            <th>Name</th>
                            <th>Level</th>
                            <th>Ratings</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <tbody class="form-check-all">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php echo $__env->make('admin.partials.datatable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script src="<?php echo e(asset('assets/js/pages/form-editor.init.js')); ?>"></script>
    

    <script>
        let isUpdate = false;
        let isUpdateLimit = false;
        let viewRoute = "<?php echo e(route('admin.quiz.view',['id'=>'1'])); ?>";
        viewRoute = viewRoute.slice(0, -2);
        let rulesPage = "<?php echo e(route('admin.quiz.rules',['id'=>'1'])); ?>";
        rulesPage = rulesPage.slice(0, -2);
        let reportView = "<?php echo e(route('admin.quiz.report.view',['id'=>'1'])); ?>";
        reportView = reportView.slice(0, -2);



        $(document).ready(function () {
            setTimeout(function() {
                $('<div class="flex-shrink-0 inline-blk" id="all_listing_div"><div class="form-check form-switch form-switch-right form-switch-md"><label for="file-input-showcode" class="form-label text-muted">Show Trial Quiz</label><input class="form-check-input code-switcher" type="checkbox" id="show_trial_quiz"></div></div>')
                .insertBefore('.dt-buttons');


        }, 300);




            editor =  ClassicEditor
    .create(document.querySelector('#rules'), {
        toolbar: {
            items: [
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                'bulletedList',
                'numberedList',
                'blockQuote',
                'insertTable',
                'undo',
                'redo'
            ]
        },
        language: 'en',
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
            ]
        },
        removePlugins: ['MediaEmbed', 'ImageToolbar']
    })
    .then(editor => {
        window.editor = editor;
    })
    .catch(error => {
        console.error(error);
    });




        $(".level_negative_marking").change(function () {
        console.log($(this).val());
        //check if this checkbox is checked
        if($(this).is(':checked')){
            $(".negative_mark").removeClass('d-none');
            $("#negative_marks").prop('required', true);
        } else {
            $("#negative_marks").prop('required', false);
            // Uncomment the following line if you want to reset the value to 0
            // $("#negative_marks").val('0');
            $(".negative_mark").addClass('d-none');
        }
    });

            $(".commonSelect2").select2({
                dropdownParent: $('#mainForm'),
                placeholder: function () {
                    $(this).data('placeholder');
                },
                allowClear: true,

            });
            $("#quiz_level_id").select2({
                dropdownParent: $('#mainForm'),
                placeholder: 'Select Level',
                allowClear: true,

            });
            $("#role_id").select2({
                placeholder: 'Select Role',
                allowClear: true,
                closeOnSelect: true,
                dropdownParent: $('#mainForm'),

            });
            $("#trial_quiz_id").select2({
                placeholder: 'Select quiz for trial',
                allowClear: true,
                closeOnSelect: true,
                dropdownParent: $('#mainForm'),

            });
            $(".themeSelector").click(function () {
                $(".themeSelector").removeClass('theme-active');
                $(this).addClass('theme-active');
                let theme = $(this).data('theme');
                $("#theme").val(theme);
            })
            $("#quiz_level_id").change(function () {
                if ($(this).val() == 1) {
                    $("#trial_quiz_id").val('').trigger('change');
                    $("#trial_quiz_id").prop('disabled', true);
                    $("#trial_quiz_id").prop('required', false);
                    // $("#trial_quiz_id_required").addClass('d-none');
                    $("#themeSelectorArea").hide();

                } else {
                    $("#trial_quiz_id").prop('disabled', false);
                   // $("#trial_quiz_id").prop('required', true);
                   // $("#trial_quiz_id_required").removeClass('d-none');
                    $("#themeSelectorArea").show();
                }
            })

        });
        function toggleEditMode(isEditable) {
            if (editor) {
                if (isEditable) {
                    editor.enableReadOnlyMode('');
                } else {
                    editor.disableReadOnlyMode('');
                }
            }
        }
        function roundToTwoDecimalPlaces(value) {

                if (isNaN(value)) {
                    return 0;
                }
            const roundedValue = parseFloat(value.toFixed(2));
            return roundedValue;
}
            function roundToTwoDecimalPlaces(value) {
            if (typeof value !== 'number' && isNaN(parseFloat(value))) {
                return 0;
            }
            const numericValue = typeof value === 'number' ? value : parseFloat(value);
            if (!isFinite(numericValue)) {
                return 0;
            }
            const roundedValue = Math.round(numericValue * 100) / 100;
            return roundedValue;
            }
        let dataTable = $('#quizTable').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',
            }],

            order: [
                [3, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
            ajax: {
                url: "<?php echo e(route('admin.quiz.datatable')); ?>",
                type: "post",
                data: function (d) {
                    d.show_trial = $('#show_trial_quiz').is(':checked') ? 1 : 0;
                    },
                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append(
                        '<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                    );
                    $("#dataTable_processing").css("display", "none");
                }
            },
            columns: [

                {
                    data: function (data) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    },
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    sortable: false
                },

                {
                    data: function (data) {
                        if(data.thumbnail == null || data.thumbnail == 'null'){
                        return `<img src="`+laravelAssetUrl+`theme/quiz/image/`+data.theme+`.png" class="avatar-sm rounded" alt="">`;
                        }else{
                        let thumb = (data.thumbnail != null && data.thumbnail != 'null') ? data.thumbnail : 'https://dummyimage.com/300';
                        return '<img src="' + thumb + '" class="avatar-sm rounded" alt="">';
                        }
                    },
                    name: 'thumbnail',
                    searchable: false,
                    sortable: false
                },

                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'quiz_level.name',
                    name: 'quiz_level.id',
                    sortable: false
                },


                {
                    sortable: false,
                    name: 'id',
                    data: function (data) {

                        return `<div class="gap-2 align-items-center mx-auto my-3 my-lg-0">
<div class="badge bg-success text-bg-success"><i class="mdi mdi-star me-1"></i>${roundToTwoDecimalPlaces(data.rating) || 0}</div>
<div class="text-muted">${data.rated_only_count} Ratings</div></div>`;
                    },


                },
                {
                    sortable: true,
                    data: function (data) {
                        if (!data.created_by)
                            return `<div class="flex-grow-1"><p class="mb-0"><small>${moment(data.created_by).fromNow()}</small></p></div>`;
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.created_by.id}"  title="${data.created_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.created_by.name}<p class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;
                    },
                    name: 'created_at',
                },
                {
                    sortable: true,
                    data: function (data) {
                        if (!data.updated_by)
                            return `<div class="flex-grow-1"><p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>`;
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.name}<p class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`;
                    },
                    name: 'updated_at',

                },
                {

                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    data: function (data) {
                        let disabled = true;
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Edit')): ?>
                            disabled = false;
                        <?php endif; ?>
                        if (data.status == '1') {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} type="checkbox" data-id="${data.id}" data-do="0" name="status" class="form-check-input status" checked></div>`;
                        } else {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} data-id="${data.id}" data-do="1" type="checkbox" name="status" class="form-check-input status"></div>`;
                        }
                    },
                    name: 'status',
                },
                {
                    sortable: false,
                    data: function (data) {
                        return `<div class="dropdown d-inline-block">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="${viewRoute + '/' + data.id}"
                                    target="_blank"
                                    class="dropdown-item edit-item-btn edit-link"><i class="ri-eye-line align-bottom me-2 text-muted"></i> View</a></li>
                                <li>
                                    <a href="javascript:void(0)"   class="dropdown-item" onclick="viewData(${data.id})">
                                        <i class="ri-edit-line align-bottom me-2 text-muted"></i> Edit
                                        </a>
                                    </li>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Report')): ?>
                                    <li>
                                    <a  href="${reportView + '/' + data.id}"
                                    target="_blank"
                                    class="dropdown-item">
                                        <i class="ri-file-chart-line align-bottom me-2 text-muted"></i> Report
                                    </a>
                                </li>
                                <?php endif; ?>
                            <li>
                                    <a  href="${rulesPage + '/' + data.id}"
                                    target="_blank"
                                    class="dropdown-item">
                                        <i class="ri-play-line align-bottom me-2 text-muted"></i> Test
                                    </a>
                                </li>
                            </ul>
                        </div>`;


                    },
                    name: 'created_at',
                },
            ],
        }); // end datatable init


        dataTable.on('draw.dt', function () {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        $(document).on('click','#show_trial_quiz',function () {
            if ($(this).is(':checked')) {
                console.log('checked')
                dataTable.draw();
            } else {
                console.log('unchecked')
                dataTable.draw();
            }
        });

        $('#quizTable tbody').on('change', 'input.status', function () {
            // change property category status based on button class and taking id from button data attribute
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Edit')): ?>
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "<?php echo e(route('admin.quiz.update.status')); ?>",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.status == '1') {
                        showToastify("Quiz Status updated successfully", "success")
                        dataTable.row().draw();
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            <?php endif; ?>
        });


        $(document).on('click', '.addModal', function (event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Commission Slab Create')): ?>
                isUpdate = false;
            //  initColorPicker("#color-picker-parent", "#color_code")
            $('#modal_title').html('Add');
            $("#id").val("")
            $('#mainForm').trigger('reset');
            $('#mainForm').removeClass('was-validated');
            <?php endif; ?>
        });

        $(document).on('click', '#deleteBtn', function (event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Commission Slab Delete')): ?>
            deleteData($('#id').val());
            <?php endif; ?>
        });


        $(document).on('click', '#editBtn', function (event) {

            $('#newBtn').addClass('d-none');
            $('#saveBtn').removeClass('d-none');
            $('#addSlabLimits').addClass('d-none');
            editData($('#id').val());

        });


        $(document).on('click', '.edit-link', function (
            event) { //edit link click event it will fetch single cateogry details and put it on form
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Sources Edit')): ?>
            let id = $(this).data('id');
            $('#id').val(id);
            editData(id);
            <?php endif; ?>
        });


        $(document).on('click', '.delete-link', function (event) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Commission Slab Delete')): ?>
            let id = $(this).data('id');
            deleteData(id);
            <?php endif; ?>
        });
        function removeSelectedFile() {
            const fileInput = document.getElementById('thumbnail');
            fileInput.value = '';
            const clone = fileInput.cloneNode();
            fileInput.replaceWith(clone);
             // Clear the selected file
        }

        function editData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Sources Edit')): ?>
                isUpdate = true; // set isUpdate to true so that we can identify that this is update request
            $.ajax({
                url: "<?php echo e(route('admin.quiz.get')); ?>",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function (result) {
                    if (result.status == '1') {
                        $('#editBtn').addClass('d-none');
                        $('#deleteBtn').addClass('d-none');
                        $('.duplicateQuiz').addClass('d-none');
                        $('#submitBtn').removeClass('d-none');
                        $('#mainForm').trigger('reset');
                        dataObj = result.data;

                        Object.keys(result.data).map((index) => {
                            if (index == 'image' || index == 'thumbnail') {
                                // $(`#${index}`).val(result.data[index]).trigger("change")
                            } else {
                                $(`#${index}`).val(result.data[index]).trigger("change")

                            }
                        })
                        let meta = JSON.parse(result.data.meta);
                        Object.keys(meta).map((item) => {
                            $(`#${item}`).val(meta[item]).trigger("change")
                        })
                        $(".themeSelector").removeClass('theme-active');
                        $("quiz_"+dataObj.theme).prop('checked', true)
                            $(".label_"+dataObj.theme).addClass('theme-active');
                            drawTrialQuiz(result.trial_quiz, dataObj.trial_quiz_id);

                       // $('#rules').html(dataObj.rules);
                      //  CKEDITOR.instances['rules'].setData(dataObj.rules)

                        //$("#role_id").val("").trigger("change");

                        if (result.data.status == 1 && result.data.status > 0) {
                            document.getElementById("statusCheckbox").checked = true;
                        } else {
                            document.getElementById("statusCheckbox").checked = false;
                        }

                        if (result.data.negative_marking == 1 && result.data.negative_marks > 0) {
                          //  document.getElementById("negative_marking").checked = true;
                            document.getElementById("negative_marking").value = 'on';
                        } else {
                           // $(".negative_mark").addClass('d-none');
                           // document.getElementById("negative_marking").checked = false;
                        }
                        editor.setData( dataObj.rules );
                        toggleEditMode(false);
                        $('.inputs').attr('disabled', false);
                        $('.inputCheckbox').attr('disabled', false);
                        // if(result.data.image){
                        //     alert('result.data.image')
                        // }
                        $('#addModal').modal('show');
                        $('#mainForm').removeClass('was-validated');
                        $('#modal_title').html('Edit');
                        document.getElementById('themeSelectorArea').style.pointerEvents = 'auto';

                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
            <?php endif; ?>
        }

        // this Delete function

        function deleteData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Commission Slab Delete')): ?>
            Swal.fire({

html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to Delete this Quiz? </p></div></div>',
showCancelButton: !0,
confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
confirmButtonText: "Yes, Delete It!",
cancelButtonClass: "btn btn-soft-success w-xs mb-1",
buttonsStyling: !1,
showCloseButton: !0


})
.then(function(t) {
    if (t.isConfirmed) { // on confirm delete the category
                        $.ajax({
                            url: "<?php echo e(route('admin.quiz.delete')); ?>",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id: id
                            },
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Slab deleted successfully", "success");
                                    $("#closeBtn").trigger("click");
                                    dataTable.row()
                                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes

                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function (result) {
                                console.log(result);
                            }
                        });
                    } else { //if delete confirmation is cancelled
                        swal("Contact source is safe!");
                    }
                });
            <?php endif; ?>
        }

        $(document).on('submit', '#mainForm', function (event) {
            console.log('submitting')
            $("#submitBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "<?php echo e(route('admin.quiz.update')); ?>" :
                    "<?php echo e(route('admin.quiz.store')); ?>",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $('.inputs').attr('disabled', true);
                        $("#submitBtn").prop('disabled', false);
                        //  viewData(result.data.id);
                        $("#closeBtn").trigger("click");
                        dataTable.row()
                            .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate =
                            false; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#submitBtn").prop('disabled', false);
                        $.each(result.errors, function (index, item) {

                            showToastify(item[0], "error")
                        })

                    } else {

                        $("#submitBtn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $("#submitBtn").prop('disabled', false);
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });
        const getTrialQuiz = () =>{
            $.ajax({
                url: "<?php echo e(route('admin.quiz.get.unused.trial.quiz')); ?>",
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    if (result.status == '1') {
                        drawTrialQuiz(result.data, null);
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }
        const  drawTrialQuiz = (quizzes = [], selected = null) =>{
            if(quizzes.length == 0){
                $("#trial_quiz_id").val("").trigger("change");
                return;
            }
            $("#trial_quiz_id").html("").trigger("change");
            let html = '';
            quizzes.forEach((quiz) => {
                html += `<option value="${quiz.id}" ${selected == quiz.id ? 'selected' : ''}>${quiz.name}</option>`;
            });
            $("#trial_quiz_id").html(html);
            if(selected){
                $("#trial_quiz_id").val(selected).trigger("change");
            }else{
                $("#trial_quiz_id").val("").trigger("change");
            }
        }

        function deleteMultiple() {
            if (ids_array = [], document.getElementsByName("chk_child").forEach((function (e) {
                if (1 == e.checked) {
                    var t = e.value;
                    ids_array.push(t)

                }
            })), "undefined" != typeof ids_array && ids_array.length > 0) {

                swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple Slabs. Once deleted, you will not be able to use these Slabs!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "<?php echo e(route('admin.work.commission.slab.delete')); ?>",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    ids: ids_array
                                },
                                success: function (result) {
                                    if (result.status == '1') {
                                        showToastify(result.msg, "success")
                                        $('.multi_options').remove();
                                        dataTable.row().draw();
                                    } else {
                                        swal("Whoops", result.msg, "error");
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function (result) {
                                    console.log(result);
                                }
                            });
                            document.getElementById("checkAll").checked = !1
                        } else {
                            showToastify("sources is safe!", "error")
                        }
                    });

            } else {
                showToastify("Please select at least one checkbox", "error")
            }

        }


        // checkbox select all functionality
        var checkAll = document.getElementById("checkAll");
        checkAll && (checkAll.onclick = function () {
            var e = document.querySelectorAll('.form-check-all input[name="chk_child"]');
            1 == checkAll.checked ? e.forEach((function (e) {
                e.checked = !0
                //e.closest("tr").classList.add("table-active")
            })) : e.forEach((function (e) {
                e.checked = !1
                //e.closest("tr").classList.remove("table-active")
            }))
        });

        $(document).on("click", "#closeBtn", function () {
            toggleForm(false)
            mode = 'view';
            $('#dropdownMenuLink1').addClass('d-none');
            $('#addSlabLimits').addClass('d-none');


        })

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn, .duplicateQuiz,  #bulkAssignBtn").addClass("d-none");
                $("#newBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }

        $(document).on("click", "#saveBtn", function () {
            $("#submitBtn").trigger("click")
        })

        function viewData(id) {
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz View')): ?>
            $.ajax({
                url: "<?php echo e(route('admin.quiz.get')); ?>",
                type: 'GET',
                dataType: 'json',
                data: {
                    id
                },
                success: function (result) {

                    if (result.status == '1') {
                        $('#editBtn').removeClass('d-none');
                        $('.duplicateQuiz').removeClass('d-none');
                        $('#saveBtn').addClass('d-none');
                        $('#closeBtn').removeClass('d-none');
                        $('#deleteBtn').removeClass('d-none');
                        $('#mainForm').trigger('reset');
                        toggleForm(true)
                        dataObj = result.data;
                        Object.keys(result.data).map((index) => {
                            if (index == 'image' || index == 'thumbnail') {
                            } else {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            }
                        })
                        let meta = JSON.parse(result.data.meta);
                        Object.keys(meta).map((item) => {
                            $(`#${item}`).val(meta[item]).trigger("change")
                        })
                        drawTrialQuiz(result.trial_quiz, dataObj.trial_quiz_id);
                        // $('#role_id').val(2).trigger("change");
                        let selectedRoles = [];
                        if (dataObj.roles.length > 0) {
                            for (let i = 0; i < dataObj.roles.length; i++) {
                                $('#role_id').val(dataObj.roles[i].role_id).trigger("change")
                                selectedRoles.push(dataObj.roles[i].role_id);
                            }
                            $('#role_id').val(selectedRoles).trigger("change")
                        }
                        if (result.data.status == 1 && result.data.status > 0) {
                            document.getElementById("statusCheckbox").checked = true;
                        } else {
                            document.getElementById("statusCheckbox").checked = false;
                        }

                        // if (result.data.negative_marking == 1 && result.data.negative_marks > 0) {
                        
                        // } else {
                        //     $(".negative_mark").addClass('d-none');
                        //     document.getElementById("negative_marking").checked = false;
                        // }
                        editor.setData( dataObj.rules );
                        toggleEditMode(true);
                        document.getElementById('themeSelectorArea').style.pointerEvents = 'none';
                        $('.inputs').attr('disabled', true);
                        $('.inputCheckbox').attr('disabled', true);
                        $(".themeSelector").removeClass('theme-active');
                        $("quiz_"+dataObj.theme).prop('checked', true)
                        $(".label_"+dataObj.theme).addClass('theme-active');
                        $('#mainForm').removeClass('was-validated');
                        $('#modal_title').html('Edit');

                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
            <?php endif; ?>
        }


        $('#newBtn').click(function () {
            isUpdate = false;
            mode = 'create';
            $('#newBtn').addClass('d-none');


            getTrialQuiz();
            editor.setData('');
            toggleEditMode(false);
            $(".inputs").prop("disabled", false)
            $('.inputCheckbox').attr('disabled', false);
            $(".inputs").val("").trigger("change")
            $(".inputs").removeClass("is-invalid")
            toggleForm(true)
            $('#saveBtn').removeClass('d-none');
            $('#editBtn').addClass('d-none');
            $('#deleteBtn').addClass('d-none');
            $('.duplicateQuiz').addClass('d-none');
            $('#closeBtn').removeClass('d-none');
            $('#addSlabLimits').addClass('d-none');
            $('#appendLimits').html('')
            $("#negative_marking").prop('checked', false);
            $(".negative_mark").removeClass('d-none');
        });

        $(document).ready(function () {
            $(document).on('click', '.slabEdit', function () {
                $("#slabLimit").modal('show');
                $("#slabItemForm").trigger('reset');
                let id = $(this).data('id');
                let lower = $(this).data('lower');
                let higher = $(this).data('higher');
                let slab = $(this).data('slab');
                let commission_type = $(this).data('commission_type');
                let commission_value = $(this).data('commission_value');
                $('#commission_limit_id').val(id);
                $('#slab_id').val(slab);
                $('#from_value').val(lower);
                $('#to_value').val(higher);
                $('#limit_commission_type').val(commission_type);
                $('#limit_commission_value').val(commission_value);
                isUpdateLimit = true;


            })
        })




        /**
         * 1st parameter is Model path
         * 2nd Parameter is Record ID
         * 3rd Parameter is Module name
         * 4th Parameter is Datatable name = Nullable
         * 5th Parameter is return URL = Nullable
         */

        function deleteDataGeneral(action_title, model, id, reload_dt = null, redirect_url = null) {
            let title = "Are you sure you want to " + action_title + " this Record?";

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-brand',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            })

            swalWithBootstrapButtons.fire({
                title: title,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    $.ajax({
                        url: "<?php echo e(route('admin.general.delete.delete_general')); ?>",
                        type: 'POST',
                        dataType: "JSON",
                        data: {
                            "_token": "<?php echo e(csrf_token()); ?>",
                            "id": id,
                            "model": model
                        }
                    }).done(function (response) {
                        if (response.success) {
                            showToastify('Record Deleted Successfully', "success")
                            dataTable.row().draw(); //

                            if (reload_dt != null) {
                                dataTable.row().draw();

                            }
                            dataTable.row().draw();
                        }
                        if (response.error) {

                            showToastify(response.message, "Error")
                            dataTable.row().draw(); //

                        } else if (response.error_related_data) {

                            // console.log(response.error_related_data);
                            let ul = '<ul class="delete_related_dta kt-nav">';
                            $.each(response.error_related_data, function (key, value) {
                                ul += '<li class="kt-nav__item" style="list-decoration:none">' +
                                    key + ': ' + value + '</li>';

                            });

                            ul += '</ul>';

                            swalWithBootstrapButtons.fire({
                                title: "Can't Delete! Related Data Exists",
                                html: ul,
                                showCancelButton: true,
                                reverseButtons: true,
                                confirmButtonText: 'Delete all related data!',
                            }).then((result) => {
                                if (result.value) {

                                    $.ajax({
                                        url: "<?php echo e(route('admin.general.delete.delete_listing_hard')); ?>",
                                        type: 'POST',
                                        dataType: "JSON",
                                        data: {
                                            "_token": "<?php echo e(csrf_token()); ?>",
                                            "data_id": id,
                                            "model": model
                                        }
                                    }).done(function (response) {
                                        dataTable.row().draw(); //
                                        showToastify(response.message, "success")

                                    });


                                } // end if
                            }) // end then

                        } // end else
                        // swal("Deleted!",response.message, "success")
                    });


                }
            })


        }


        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow

        dataTable.on('click', 'tbody tr', function (e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 6 || $cell.index() === 7 || $cell.index() === 8 || $cell.index() === 9) {
                    // console.log($cell);
                } else {
                    viewData(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click

        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Sources Delete')): ?>
                    <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                    <?php endif; ?>
                    </div>
                </div>
           </div>`).insertAfter('.dt-buttons');
                }
            } else {
                $('.multi_options').remove();

            }
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
            updateMultiOption();
        })
        $(document).on('change', 'tbody tr .selectable-checkbox', function () {

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });
        dataTable.on('draw.dt', function () {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });


        const optionFormat = (item) => {
            if (!item.id) {
                return item.text;
            }

            var span = document.createElement('span');
            var template = '';

            template += '<span value="1" class="badge fw-bolder ';
            template += '' + item.element.getAttribute('data-kt-rich-content-class') + '">';
            template += '' + item.text + '</span>';


            span.innerHTML = template;

            return $(span);
        }

        $('.generalDelete').click(function () {

            let source_id = $(this).data('id');
            let key = $(this).data('key');
            let model = $(this).data('model');

            $.ajax({
                url: "<?php echo e(route('admin.general.paramerter.delete.general')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: source_id,
                    key: key,
                    model: model
                },
                success: function (result) {
                    editData(source_id)
                    dataTable.row()
                        .draw(); // redraw the datatable show we don't need to reload the page to see the changes
                },
                error: function (result) {
                    console.log(result);
                }
            })
        });

        let duplicateQuiz = "<?php echo e(asset('images/animated/duplicate.gif')); ?>";
       $(".duplicateQuiz").click(function () { Swal.fire({

html: `<div class="mt-3"><img src="`+duplicateQuiz+`"  style="width:100px;height:100px"/><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to make copy of this Quiz ?</p></div></div>`,
showCancelButton: !0,
confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
confirmButtonText: "Yes, Duplicate It!",
cancelButtonClass: "btn btn-soft-success w-xs mb-1",
buttonsStyling: !1,
showCloseButton: !0


})
.then(function(t) {
    if (t.isConfirmed) {
            let id = $(this).data('id');
            $.ajax({
                url: "<?php echo e(route('admin.quiz.duplicate')); ?>",
                type: 'POST',
                dataType: 'json',
                data: {
                    id: $("#id").val()
                },
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });

        }else{
                        console.log('dont delete')
                    }
                })


        });




        $("#sideBarQuiz").addClass('collapse show');
        $("#sidebarQuizActive").addClass('active');
        $("#sidebarQuizList").addClass('active');

        //
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/quiz/datatable.blade.php ENDPATH**/ ?>
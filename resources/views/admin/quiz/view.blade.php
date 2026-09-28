@extends('admin.layouts.master')
@section('title')
    Quiz
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> --}}
    <style>
        .img-link .active {
            border: 3px solid #d3a188 !important;
        }

        .btn {
            box-shadow: 0px 0px 0px rgb(255 255 255 / 0%) !important;
        }

        .removeOptionSpan {
            position: absolute;
            right: 0;
            margin-top: -33px;
        }

        .optionInput {
            width: 90% !important;
        }
        .dt-buttons{
            display: none !important;
        }
    </style>
@endsection
@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="ri-home-5-fill"></i></a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.quiz.list')}}">Quiz</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{$quiz->name}}</li>
    </ol>
</nav>
    <div class="row">
        <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4>{{$quiz->name}}</h4>
                <p>{{$quiz->description}}</p>
            </div>
        </div>
    </div>
    </div>
    <div class="row card-body row-cols-md-3 row-cols-1">


        <div class="col col-lg bg-white border-end">
            <div class="py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">Questions <i
                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-space-ship-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value"
                                               data-target="{{$statics['total_questions']}}">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-md-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">Total Marks <i
                        class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-exchange-dollar-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0">{{$total_marks}}</h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-md-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    Max Score <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-pulse-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value" data-target="{{$max_marks ?? 0}}">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white border-end">
            <div class="mt-3 mt-lg-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    Quiz Users <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-trophy-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0">{{$attempt_counts}}</h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
        <div class="col col-lg bg-white">
            <div class="mt-3 mt-lg-0 py-4 px-3">
                <h5 class="text-muted text-uppercase fs-13">
                    Quiz Completed <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i>
                </h5>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="ri-service-line display-6 text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h2 class="mb-0"><span class="counter-value" data-target="{{$statics['completed_quiz']}}">0</span></h2>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
    </div>

    <div class="col-12">
        <div class="collapse slideIn" id="collapseContactForm">
            <div class="">
                <div class=" row">
                    <div>


                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab"
                               aria-selected="true">
                                Questions
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab"
                               onclick="quizUserHistory()"
                               href="#product1" role="tab" aria-selected="false"
                               tabindex="-1">
                                Quiz Users History
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#messages" role="tab" aria-selected="false"
                               tabindex="-1">
                                Feedback
                            </a>
                        </li>

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content  text-muted">
                        <div class="tab-pane active show" id="home" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">
                                        <div class="tab-pane fade active show" id="levelTabContent0" role="tabpanel"
                                             aria-labelledby="v-pills-home-tab">
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <div class="align-items-center d-flex">
                                                        <h5 class="card-title mb-0 flex-grow-1"></h5>
                                                        <div class="flex-shrink-0">

                                                            <button id="openQuestionModalBtns" data-id=""
                                                                    class="btn btn-primary float-end openQuestionModalBtn">
                                                                <i class="ri-add-line"></i>
                                                                Add Question
                                                            </button>

                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="accordion" id="default-accordion-example">


                                                @foreach($questions ?? [] as $qstn)
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header d-flex justify-content-between align-items-center"
                                                            id="questionTabHeading{{$qstn->id}}">
                                                            <button
                                                                class="accordion-button @if($loop->index != 0) collapsed @endif"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#questionTabCollapse{{$qstn->id}}"
                                                                @if($loop->index == 0) aria-expanded="true"
                                                                @else aria-expanded="false"
                                                                @endif aria-controls="questionTabCollapse{{$qstn->id}}">
                                                                Q.{{$loop?->index+1}} {{$qstn?->question}}
                                                                <span class="float-end">
                                                           <button type="button" class="btn btn-link waves-effect"
                                                                  onclick="getQuestion('{{$qstn?->id}}')">
                                                               <i class="ri-edit-box-line"></i>
                                                           </button>
                                                           <button type="button"
                                                                   onclick="deleteQuestion('{{$qstn?->id}}')"
                                                                   class="btn btn-link waves-effect"
                                                                   href="javascript:void(0)"><i
                                                                   class="ri-delete-bin-7-line"></i></button>
                                                        </span>
                                                            </button>
                                                        </h2>
                                                        <div id="questionTabCollapse{{$qstn->id}}"
                                                             class="accordion-collapse collapse @if($loop->index == 0) show @endif"
                                                             aria-labelledby="questionTabHeading{{$qstn->id}}"
                                                             data-bs-parent="#default-accordion-example">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    @if($qstn->answers->count() > 0)
                                                                        @php $qn = 1; @endphp
                                                                        @foreach($qstn->answers as $answers)
                                                                            <div class="col-6 mb-3">

                                                                                <div
                                                                                    class="alert alert-light alert-dismissible @if($answers->is_correct === 1) bg-soft-success  @else bg-light @endif text-body alert-label-icon fade show"
                                                                                    role="alert">
                                                                                    <i class="ri-edit-circle-line label-icon"></i>
                                                                                    {{$answers->answer}}
                                                                                </div>
                                                                            </div>
                                                                            @php $qn++; @endphp
                                                                        @endforeach

                                                                        @php

                                                                            $media_url = $qstn?->image_url != null ? $qstn->image_url : ($qstn->video_url != null ? $qstn->video_url : $qstn->youtube_url);
                                                                        @endphp
                                                                        @if($media_url != null)
                                                                            <div class="col-6 mb-3">

                                                                                <a target="_blank" href="{{$media_url}}"
                                                                                   type="button"
                                                                                   class="btn btn-sm btn-primary waves-effect waves-light">Media</a>
                                                                            </div>
                                                                        @endif
                                                                    @endif


                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>


                                    </div>
                                </div><!--  end col -->
                            </div>
                        </div>
                        <div class="tab-pane" id="product1" role="tabpanel">
                            @include('admin.quiz.include.quizUsers')
                        </div>
                        <div class="tab-pane" id="messages" role="tabpanel">
                            @include('admin.quiz.include.feedback')
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('admin.quiz.include.modal')
@endsection
@section('script')
    @include('admin.partials.datatable')
    <script>
        let isLevelUpdate = false;
        let isQuestionUpdate = false;
        let answerCounter = 2;
        let viewAttemptRoute = "{{route('admin.quiz.user.attempt.view',':id')}}";
        $(".level_negative_marking").change(function () {
            if ($(this).val() == 1) {
                $("#form_negative_mark").removeClass('d-none');
            } else {
                $("#form_negative_mark").addClass('d-none');
            }
        });


        //// question related functions
        $(".openQuestionModalBtn").click(function () {
            $("#question_form_level_id").val($(this).data('id'))

            $("#questionForm").trigger('reset');
            $("#questionFormModal").modal('show');
            $(".option").val('');
        });

        const getQuestion = (id) => {
            $.ajax({
                url: "{{ route('admin.quiz.question.get') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function (result) {
                    if (result.status == '1') {
                        $("#question_form_id").val(result.data.id);
                        $("#level_question").val(result.data.question);
                        $("#level_question_description").val(result.data.description);
                        $("#question_time").val(result.data.time);
                        $("#question_passing_grade").val(result.data.marks);
                        $("#question_skip_grade").val(result.data.skipped_marks);
                        $("#negative_marks").val(result.data.negative_marks);

                        $("#question_image_url").val(result.data.image_url);
                        $("#question_video_url").val(result.data.video_url);
                        $("#question_youtube_video_url").val(result.data.youtube_url);
                        $("#questionFormModal").modal('show');
                        $("#questionOptions").html('');
                        let options = result.data.answers;
                        answerCounter = 0;
                        options.forEach(function (item) {
                            answerCounter++;
                            let uuid = uuidv4();
                            $("#questionOptions").append(``);
                            $("#questionOptions").append(`<div class="col-6 mt-2" id="${uuid}">
                        <div class="form-check form-radio-primary mb-3">
                            <input class="form-check-input mt-2 option${uuid}value" type="radio" name="correct_answer" value="${item.answer}" ${item.is_correct === 1 ? 'checked' : ''}>
                            <input type="text" class="form-control optionInput option" id="option${uuid}" name="option[]" placeholder="Enter Option" value="${item.answer}">
                            <span class="removeOptionSpan">
                                <button type="button" class="btn btn-sm btn-soft-danger removeOption" onclick="deleteQuestionOption('`+item.id+`')"><i class="ri ri-delete-bin-line"></i></button>
                            </span>
                        </div>
                    </div>`);
                        });

                        isQuestionUpdate = true;
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }

        $(document).on('submit', '#questionForm', function (event) {
            $("#levelSaveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isQuestionUpdate ? "{{ route('admin.quiz.question.update') }}" :
                    "{{ route('admin.quiz.question.store') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        $("#questionFormModal").modal('hide');
                        showToastify(result.msg, "success")
                        $('.levelinputs').attr('disabled', true);
                        $("#levelSaveBtn").prop('disabled', false);
                        //  viewData(result.data.id);
                        $("#levelcloseBtn").trigger("click");
                        location.reload();
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
        $("#addOption").click(function () {
            let uuid = uuidv4();
            answerCounter++;
            $("#questionOptions").append(`<div class="col-6 mt-2" id="${uuid}">
                        <div class="form-check form-radio-primary mb-3">
                            <input class="form-check-input mt-2 option${uuid}value" type="radio" name="correct_answer" value="${answerCounter}" id="option1">
                            <input type="text" class="form-control optionInput option" id="option${uuid}" name="option[]" placeholder="Enter Option">
                            <span class="removeOptionSpan">
                                <button type="button" class="btn btn-sm btn-soft-danger removeOption" ><i class="ri ri-delete-bin-line"></i></button>
                            </span>
                        </div>
                    </div>`);
        });
        $(document).on("change", ".option", function () {
            let value = $(this).val();
            let id = $(this).attr('id');
            $(`.${id}value`).val(value);
        });
        $(document).on('click', '.removeOption', function () {
            $(this).closest('.col-6').remove();
        });
        const deleteQuestion = (id) => {
            Swal.fire({

                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to Delete this Question ?</p></div></div>',
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
                            url: "{{ route('admin.quiz.question.delete') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id: id
                            },
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Question deleted successfully", "success");
                                    $("#closeBtn").trigger("click");
                                    location.reload();
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function (result) {
                                console.log(result);
                            }
                        });
                    } else { //if delete confirmation is cancelled

                        //showToastify("Question not deleted", "success");
                    }
                });
        }
        const deleteQuestionOption = (id) => {
            Swal.fire({

                html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you Sure You want to Delete this Status ?</p></div></div>',
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
                            url: "{{ route('admin.quiz.question.option.delete') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id: id
                            },
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Question option deleted successfully", "success");

                                } else { // something went wrong on backend
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


        }
        const quizUserHistory = () =>{
            if ($.fn.DataTable.isDataTable('#quizHistoryTable')) {
                $('#quizHistoryTable').DataTable().destroy();
            }
            let dataTable = $('#quizHistoryTable').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',


                order: [
                    [8, "desc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                ajax: {
                    url: "{{ route('admin.quiz.user.list') }}",
                    type: "post",
                    data: function (d) {

                        d.quiz_id = "{{$quiz->id}}";
                        d.quiz_user_id = "";
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
                            return `<div  class="d-flex">
 <img src="${data.user.thumb_url}" alt="" class="avatar-xs rounded-3 me-2">
                <div>
                    <h5 class="fs-13 mb-0">${data.user.name}</h5>
                    <p class="fs-12 mb-0 text-muted">${data?.user?.role_list[0]}</p>
                </div></div>`;
                        },
                        searchable: false,
                        sortable: false
                    }, {
                        data: function (data) {
                            let start_time = moment.utc(data.created_at);
                        let end_time = data.completed_at !=null ?  moment.utc(data.completed_at) : moment.utc(data.updated_at);
                       let duration = moment.duration(end_time.diff(start_time));
                        return duration.hours() + 'h ' + duration.minutes() + 'm ' + duration.seconds() + 's';
                        },
                        searchable: false,
                        sortable: false
                    },

                    {
                        data: function (data) {
                            return data.answers_count;
                        },
                        name: 'id',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: function (data) {
                            return data.correct_answers;
                        },
                        name: 'id',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: function (data) {
                            return data.wrong_answers;
                        },
                        name: 'wrong_answers',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: function (data) {
                            return data.skipped_answers;
                        },
                        name: 'skipped_answers',
                        searchable: false,
                        sortable: false
                    },{
                        data: function (data) {
                            return data.total_marks;
                        },
                        name: 'total_marks',
                        searchable: false,
                        sortable: true
                    },
                    {
                        sortable: true,
                        data: function (data) {

                            if (data.result === 'pass') {
                                return `<span class="badge border border-success text-success">Passed</span>`;
                            } else {
                                return `<span class="badge border border-danger text-danger">Failed</span>`;
                            }
                        },
                        name: 'updated_at',

                    },
                    {
                        sortable: true,
                        data: function (data) {
                            let start_time = moment.utc(data.created_at);
                        let end_time = data.completed_at !=null ?  moment.utc(data.completed_at) : moment.utc(data.updated_at);
                       let duration = moment.duration(end_time.diff(start_time));
                       let durationInHours = duration.asHours();
                       if(durationInHours > 0 && data.status === 'pending'){
                            return `<span class="badge bg-danger">Unfinished</span>`;
                       }
                            if (data.status === 'completed') {
                                return `<span class="badge bg-success">Completed</span>`;
                            } else {
                                return `<span class="badge bg-warning">In Progress</span>`;
                            }
                        },
                        name: 'updated_at',

                    },
                    {

                        orderable: false,
                        visible: true,
                        sortable: true,
                        /*This will hide particular column*/
                        data: function (data) {
                           return moment(data.created_at).format('DD/MM/YYYY h:mm A');
                        },
                        name: 'created_at',
                    },
                    {
                        sortable: false,
                        data: function (data) {
                            let viewAttRoute = viewAttemptRoute.replace(':id', data.id);
                            return `<a href="${viewAttRoute}" title="View Details" class="btn action-edit edit-link" ><i class="ri-eye-line"></i></a>`;

                        },
                        name: 'created_at',
                    },
                ],
            }); // end datatable init
        }
        const quizFeedbackTable = () =>{
            if ($.fn.DataTable.isDataTable('#feedbackDatatble')) {
                $('#feedbackDatatble').DataTable().destroy();
            }
            let dataTable = $('#feedbackDatatble').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                order: [
                    [0, "desc"]
                ],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                dom: `<'row'<'col-sm-6 text-left'B> <'col-sm-6 text-right'f>>
            <'row'<'col-sm-12'tr>>
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
                ajax: {
                    url: "{{ route('admin.quiz.user.list') }}",
                    type: "post",
                    data: function (d) {

                        d.quiz_id = "{{$quiz->id}}";
                        d.quiz_user_id = "";
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
                            return data.updated_at;
                        },
                        name: 'updated_at',
                        visible: false,
                        searchable: false,
                        sortable: false
                    },

                    {
                        data: function (data) {
                            return `<div  class="d-flex">
                                <img src="${data.user.thumb_url}" alt="" class="avatar-xs rounded-3 me-2">
                                                <div>
                                                    <h5 class="fs-13 mb-0">${data.user.name}</h5>
                                                    <p class="fs-12 mb-0 text-muted">${data?.user?.role_list[0]}</p>
                                                </div></div>`;
                        },
                        searchable: false,
                        sortable: false
                    }, {
                        data: function (data) {
                            if(data.rating != null){
                            return ` <div class="d-flex gap-4 mt-0 text-muted mx-auto">
                            <div><i class="ri-star-line text-warning me-1 align-bottom"></i> <span
                                    class="badge bg-danger-subtle text-danger">${data.rating || '0'} Star</span></div>
                        </div>`;
                            }else{
                                return `-`;
                            }
                        },
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: function (data) {
                            return ` <div class="text-muted">${data.feedback || ''}</div>`;
                        },
                        name: 'feedback',
                        searchable: false,
                        sortable: false
                    }


                ],
            }); // end datatable init
        }

        quizFeedbackTable();
        $("#sideBarQuiz").addClass('collapse show');
        $("#sidebarQuizActive").addClass('active');
        $("#sidebarQuizList").addClass('active');

        //
    </script>
@endsection

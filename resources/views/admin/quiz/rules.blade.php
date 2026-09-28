@extends('admin.layouts.master')
@section('title')
    {{$quiz->name}} | Rules
@endsection
@section('style')
<style>
    .list-group-item {
    margin-bottom: 0;
    background: #d3a18882 !important;
    width: 100%;
    font-size: 16px;
    font-weight: 400;
    padding: .7rem 0.5rem !important;
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
    @php
        $time = $time != 0 ? $time/60 : 0;
        $time = round($time, 2);
    @endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    {{$quiz->name}}
                    <br>
                    <p class="text-muted">
                        {{$quiz->description}}
                        <span class="float-end">Passing Marks <span class="badge rounded-pill border border-success text-success "> {{$quiz_meta?->passing_mark ?? ''}}</span>
                    </span>
                    </p>

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-10 offset-2 scrollable-div" style="min-height: 565px;">
                            <p>Rules:</p>
                            <p>{!! $quiz->rules  !!}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <ul class="list-group list-group-horizontal-md justify-content-center">
                                <li class="list-group-item">
                                    <i class=" ri-question-line"></i>
                                    Total Question : <b>{{$total_questions}}</b></li>
                                <li class="list-group-item">
                                    <i class=" ri-time-line"></i>
                                    Time : <b>{{$time}}min</b></li>
                                <li class="list-group-item">
                                    <i class=" ri-check-double-line"></i>
                                    Correct Answer marks: <b>{{$quiz_meta->question_mark}}</b></li>
                                <li class="list-group-item">
                                    <i class="ri-close-line"></i>
                                    Incorrect Answer marks: <b>-{{$quiz_meta->negative_marks}}</b></li>
                                <li class="list-group-item">
                                    <i class="ri-spam-line"></i>
                                    Unanswered marks: <b>{{$quiz_meta->skip_mark}}</b></li>

                            </ul>

                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12">
                            <p>
                                Note: By clicking on start you have agreed that you have read and accepted the rules of the quiz.
                            </p>
                        </div>
                        <div class="col-12">
                            <a href="{{route('admin.quiz.index')}}" class="btn btn-primary">Back</a>
                            <button type="submit" onclick="startQuiz()" name="submit" class="btn btn-primary float-end">Start The Quiz</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')


    {{-- <script src=" "></script> --}}


    <script>

        let time = "{{$time ?? 0}}";
        let quiz_id = "{{$quiz->id}}";
        @if($quiz->trial_quiz_id != null)
            @if($attempt == 0 || $attempt == '0')
            quiz_id = "{{$quiz->trial_quiz_id}}";
                @else
                quiz_id = "{{$quiz->id}}";
                @endif
                    @else
                    quiz_id = "{{$quiz->id}}";
                @endif

        function startQuiz() {

            Swal.fire({

                html: '<div class="mt-3"><img src="{{asset('images/animated/question.gif')}}" style="width:100px; height:100px;"> <div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to start the quiz this will take '+time+'min ?</p></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                confirmButtonText: "Yes, Start!",
                cancelButtonClass: "btn btn-light w-xs mb-1",
                buttonsStyling: !1,
                showCloseButton: !0


            })
                .then(function(t) {
                    if (t.isConfirmed) { // on confirm delete the category
                        $.ajax({
                            url: "{{ route('admin.quiz.start') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                quiz_id : quiz_id,
                            },
                            success: function(result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Quiz has been started", "success")
                                 let url = result.url;
                                    //redirect to next page
                                   window.location.replace(url);
                                } else { // something went wrong on backend
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function(result) {
                                console.log(result);
                            }
                        });
                    }
                })

        }

        $("#sideBarQuiz").addClass('collapse show');
        $("#sidebarQuizActive").addClass('active');
        $("#sidebarQuizList").addClass('active');
    </script>
@endsection

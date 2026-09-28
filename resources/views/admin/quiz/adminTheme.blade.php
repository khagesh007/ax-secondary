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
    font-size: 14px;
    font-weight: 400;
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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <b>{{$quiz->name}}</b>
                    <p>
                        <div class="alert alert-warning alert-border-left alert-dismissible fade show" role="alert">
                            <i class="ri-alert-line me-1 align-middle fs-16"></i>Before we begin with the main quiz, we want to make sure that you understand the rules of this quiz. So please read and answer the following questions carefully. This will not affect your marks on the main quiz, but you won't be able to attempt the main quiz if you don't understand the rules.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                    </p>
                </div>
                <div class="card-body" style="    min-height: 520px;
                ">


                            <div id="quizIndex">
                                <index :quiz="{{$quiz}}" :attempt="{{$attempt}}"></index>
                            </div>

                </div>

            </div>
        </div>
    </div>

@endsection
@section('script')

<script src="{{asset('vue/js/quiz.js')}}"></script>
    {{-- <script src=" "></script> --}}


    <script>


        $("#sideBarQuiz").addClass('collapse show');
        $("#sidebarQuizActive").addClass('active');
        $("#sidebarQuizList").addClass('active');
    </script>
@endsection

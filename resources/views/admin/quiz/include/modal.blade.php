<!-- Default Modals -->
<div id="levelModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Add/Update Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="levelForm" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12">
                            <label for="level">Level Name</label>
                            <input type="hidden" name="quiz_id" value="{{$quiz->id}}">
                            <input type="text" class="form-control" id="level_name" name="name"
                                   placeholder="Enter Level Name">
                        </div>

                        <div class="col-12 mt-2">
                            <label for="level">Description</label>
                            <textarea class="form-control" id="level_description" name="description"
                                      placeholder="Enter Description"></textarea>
                        </div>

                        <div class="col-6 mt-2">
                            <label for="level">Time</label>
                            <input type="text" class="form-control" id="level_time" name="time"
                                   placeholder="Enter Time"></input>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="level">Passing Grade</label>
                            <input type="text" class="form-control" id="level_passing_grade" name="passing_grade"
                                   placeholder="Enter Passing Grade"></input>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" id="levelcloseBtn" class="btn btn-light" data-bs-dismiss="modal">Close
                    </button>
                    <button type="submit" id="levelSaveBtn" class="btn btn-primary ">Save Changes</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Default Modals -->
<div id="questionFormModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Add/Update Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="questionForm" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12">
                            <label for="level">Question</label>
                            <input type="hidden" name="quiz_id" value="{{$quiz->id}}">
                            <input type="hidden" name="level_id" id="question_form_level_id">
                            <input type="hidden" name="id" id="question_form_id">
                            <input type="text" class="form-control" id="level_question" required name="question"
                                   placeholder="Enter Question Title">
                        </div>

                        <div class="col-12 mt-2">
                            <label for="level">Description</label>
                            <textarea class="form-control" id="level_question_description" name="description"
                                      placeholder="Enter Description"></textarea>
                        </div>

                        <div class="col-2 mt-2">
                            <label for="level">Time (In Sec.)</label>
                            <input type="number" class="form-control" id="question_time" name="time"
                            required
                                   placeholder="Ex. 15 (sec/question)" value="{{$quiz_meta->question_time}}">
                        </div>
                        <div class="col-2 mt-2">
                            <label for="question_passing_grade">Marks</label>
                            <input type="number" class="form-control" id="question_passing_grade" name="marks"
                            required
                                   placeholder="Enter Passing Grade" value="{{$quiz_meta->question_mark}}">
                        </div>
                        <div class="col-3 mt-2">
                            <label for="question_skip_grade">Unanswered Marks</label>
                            <input type="number" class="form-control" id="question_skip_grade" name="skip_marks"
                            required
                                   placeholder="Skip/unanswered marks" value="{{$quiz_meta?->skip_mark ?? ''}}">
                        </div>
                        <div class="col-3 mt-2">
                            <label for="level">Negative Marking</label>
                            <br>
                            <input type="radio" class="level_negative_marking"
                            name="negative_marking" value="1"
                                   checked>
                            Yes
                            <input type="radio" class="level_negative_marking" name="negative_marking" value="0"> No
                        </div>
                        <div class="col-2 mt-2" id="form_negative_mark">
                            <label for="level">Negative Marks</label>

                            <input type="number" class="form-control" id="negative_marks" name="negative_marks"
                                   placeholder="Enter Negative Mark" value="{{$quiz_meta->negative_marks}}">
                        </div>
                    </div>
                    <hr>
                    <h6>Answers/Options</h6>
                    <div class="row" id="questionOptions">
                        <div class="col-6 mt-2">
                            <div class="form-check form-radio-primary mb-3">
                                <input class="form-check-input mt-2 option1value" type="radio" name="correct_answer">
                                <input type="text" class="form-control option" id="option1" name="option[]"
                                       placeholder="Enter Option">
                            </div>
                        </div>
                        <div class="col-6 mt-2">
                            <div class="form-check form-radio-primary mb-3">
                                <input class="form-check-input mt-2 option2value" type="radio" name="correct_answer">
                                <input type="text" class="form-control option" id="option2" name="option[]"
                                       placeholder="Enter Option">
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-6 mt-2">
                            <button type="button" class="btn btn-sm btn-primary" id="addOption"><i
                                    class="ri ri-add-line"></i> Add Option
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h5>Provide Only one of the following.</h5>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="image">Question Image</label>
                            <input type="file" class="form-control" id="question_image" name="image"
                                   title="select image file"
                            >
                        </div>

                        <div class="col-6 mt-2">
                            <label for="image">Question Image Url</label>
                            <input type="text" class="form-control" id="question_image_url" name="image_url"
                                   placeholder="Enter image url"
                            >
                        </div>
                        <div class="col-6 mt-2">
                            <label for="image">Question Youtube Video</label>
                            <input type="text" class="form-control" id="question_youtube_video_url" name="youtube_url"
                                   placeholder="Enter youtube video url"
                            >
                        </div>
                        <div class="col-6 mt-2">
                            <label for="image">Question Video</label>
                            <input type="text" class="form-control" id="question_video_url" name="video_url"
                                   placeholder="Any external video url"
                            >
                        </div>

                        <div class="col-12 mt-2">
                            <p class="text-muted">
                                <strong>Note:</strong> If you are providing video url then please consider increasing the time for the question. (Video time + Question time)
                            </p>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" id="questionCloseBtn" class="btn btn-light" data-bs-dismiss="modal">Close
                    </button>
                    <button type="submit" id="levelSaveBtn" class="btn btn-primary ">Save Changes</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

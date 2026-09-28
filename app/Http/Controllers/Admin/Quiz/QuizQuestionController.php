<?php

namespace App\Http\Controllers\Admin\Quiz;


use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizLevel;
use App\Models\QuizMedia;
use App\Models\QuizQAnswer;
use App\Models\QuizQuestion;
use App\Models\Region;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class QuizQuestionController extends Controller
{
use FileTrait;

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'question' => 'required|string',
            'description' => 'nullable|string',
            'quiz_id' => 'required|integer',
            'correct_answer' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

            $obj = new QuizQuestion();
            $obj->question = $request->question;
            $obj->description = $request->description;
            $obj->quiz_id = $request->quiz_id;
            $obj->negative_marking = $request->negative_marking == '1' ? 1 : 0;
            $obj->marks = $request->marks ?? 0;
            $obj->time = $request->time;
            $obj->negative_marks    = $request->negative_marks ?? 0;
            $obj->skipped_marks    = $request->skip_marks;
           // $obj->correct_answer = $request->correct_answer;
            $obj->image_url = $request->image_url;
            $obj->video_url = $request->video_url;
            $obj->youtube_url = $request->youtube_url;
            $obj->created_by_id = \Auth::user()->id;
            if($obj->save()){
                $answers = $request->option;

                foreach ($answers as $answer){
                    $answer = trim($answer);
                    if($answer == ''){
                        continue;
                    }else{
                    $ans = new QuizQAnswer();
                    $ans->quiz_question_id = $obj->id;
                    $ans->answer = $answer;
                    $ans->is_correct = $request->correct_answer === $answer ? 1 : 0;
                    $ans->save();
                    }

                }
                $image = '';
                if ($request->has('image')) {
                    $image = $this->uploadImage($request->file("image"));
                    $obj->image_url = $image;
                    $obj->save();


                    $media = new QuizMedia();
                    $media->model_type = QuizQuestion::class;
                    $media->model_id = $obj->id;
                    $media->title = $obj->question;
                    $media->path = $image;
                    $media->extension = $request->file("image")->getClientOriginalExtension();
                    $media->size = $request->file("image")->getSize();
                    $media->type = 'Photos';
                    $media->status = 1;
                    $media->created_by_id = \Auth::user()->id;
                    $media->save();
                }

                $result = array('status' => '1', 'msg' => 'Quiz question created successfully');
            }else{
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }


        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|exists:quiz_questions,id',
            'question' => 'nullable|string',
            'description' => 'nullable|string',
            'quiz_id' => 'required|integer',
//            'correct_answer' => 'required|string',
//            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

            $obj = QuizQuestion::find($request->id);
            $obj->question = $request->question;
            $obj->description = $request->description;
            $obj->quiz_id = $request->quiz_id;
            $obj->negative_marking = $request->negative_marking == '1' ? 1 : 0;
            $obj->marks = $request->marks;
            $obj->time = $request->time;
            $obj->negative_marks    = $request->negative_marks;
            $obj->skipped_marks    = $request->skip_marks;
            // $obj->correct_answer = $request->correct_answer;
            $obj->image_url = $request->image_url;
            $obj->video_url = $request->video_url;
            $obj->youtube_url = $request->youtube_url;
            $obj->created_by_id = \Auth::user()->id;
            if($obj->save()){
                $answers = $request->option;
                foreach ($answers as $answer){
//                    $ans = new QuizQAnswer();
//                    $ans->quiz_question_id = $obj->id;
//                    $ans->answer = $answer;
//                    $ans->is_correct = $request->correct_answer === $answer ? 1 : 0;
//                    $ans->save();

                    QuizQAnswer::updateOrCreate(['quiz_question_id' => $obj->id,  'answer' => $answer], [
                        'answer' => $answer,
                        'is_correct' => $request->correct_answer === $answer ? 1 : 0,
                    ]);

                }
                $image = '';
                if ($request->has('image')) {
                    $image = $this->uploadImage($request->file("image"));
                    $obj->image_url = $image;
                    $obj->save();


                    $media = new QuizMedia();
                    $media->model_type = QuizQuestion::class;
                    $media->model_id = $obj->id;
                    $media->title = $obj->question;
                    $media->path = $image;
                    $media->extension = $request->file("image")->getClientOriginalExtension();
                    $media->size = $request->file("image")->getSize();
                    $media->type = 'Photos';
                    $media->status = 1;
                    $media->created_by_id = \Auth::user()->id;
                    $media->save();
                }

                $result = array('status' => '1', 'msg' => 'Quiz question updated successfully');
            }else{
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }


        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|string',

        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {
            $question = QuizQuestion::find($request->id);
            if($question){
                $question->delete();
                $result = array('status' => '1', 'msg' => 'Quiz question deleted successfully');
            }else{
                $result = array('status' => '0', 'msg' => 'Quiz question not found');

            }
        }catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function getAllQuestions(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'quiz_id' => 'required|integer',
            'attempt_id' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {
            $data['questions'] = QuizQuestion::where('quiz_id',$request->quiz_id)->with('answers')->get();
            $data['attempt'] = QuizAttempt::where('id',$request->attempt_id)->first();
            $data['attempted_question_count'] = QuizAttemptAnswer::where('quiz_attempt_id',$request->attempt_id)->count();
            $result = array('status' => '1', 'msg' => 'Quiz questions fetched successfully', 'data' => $data);
        }catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);

    }

    public function saveAnswer(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'quiz_id' => 'required|integer',
            'skipped' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {
            $question = QuizQuestion::where('id',$request->question_id)->first();
            $answer = QuizQAnswer::where('quiz_question_id',$request->question_id)->where('is_correct',true)->first();
            $attempt = QuizAttempt::find($request->attempt_id);
            $obj = QuizAttemptAnswer::where('quiz_attempt_id',$request->attempt_id)->where('quiz_question_id',$request->question_id)->first() ?? new QuizAttemptAnswer();
            $obj->quiz_attempt_id   = $request->attempt_id;
            $obj->quiz_question_id  = $request->question_id;
            $obj->quiz_q_answer_id  = $request->answer;
            $obj->is_correct        = $answer->id == $request->answer ? true : false;
            $obj->is_skipped        = $request->skipped === true;
            $obj->marks             = 0;
            $obj->negative_marks = 0;
            $obj->time = 0;
            $obj->created_by_id = \Auth::user()->id;
            $obj->updated_by_id = \Auth::user()->id;
            if($obj->save()){

                if($obj->is_skipped === true){
                   $attempt->skipped_answers += 1;
                   $attempt->save();
                }
            }
            if($answer->id != $request->answer){
                if($obj->is_skipped === false) {
                    $attempt->wrong_answers += 1;
                    if ($question->negative_marking == true) {
                        $attempt->total_marks = $attempt->total_marks - $question->negative_marks;
                    }
                    $attempt->save();
                    $obj->is_correct = 2;
                    $obj->save();
                    $result = array('status' => '2', 'msg' => 'Wrong answer selected', 'correct_answer' => $answer);
                    return json_encode($result);
                }else{
                    $result = array('status' => '1', 'msg' => 'Answer saved successfully','correct_answer' => $answer);
                    return json_encode($result);
                }


            }else{
                $attempt->correct_answers += 1;
                $attempt->total_marks += $question->marks;
                $attempt->save();
            }
            $result = array('status' => '1', 'msg' => 'Answer saved successfully','correct_answer' => $answer);
        }catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function getQuestion(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {
            $question = QuizQuestion::where('id',$request->id)->with(['answers'])->first();
            $result = array('status' => '1', 'msg' => 'Quiz question fetched successfully', 'data' => $question);
        }catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);

    }
    public function deleteQuestionOption(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|integer|exists:quiz_q_answers,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }
        if(QuizQAnswer::find($request->id)->delete()){
            $result = array('status' => '1', 'msg' => 'Option deleted successfully');
        }else{
            $result = array('status' => '0', 'msg' => 'Option not found');
        }
        return json_encode($result);
    }

}

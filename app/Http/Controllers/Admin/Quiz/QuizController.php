<?php

namespace App\Http\Controllers\Admin\Quiz;


use App\Models\ModelRole;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizLevel;
use App\Models\QuizMedia;
use App\Models\QuizQuestion;
use App\Models\Region;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
class QuizController extends Controller
{
    use FileTrait;
    public function index(Request $request)
    {
        abort_if(Gate::denies("Quiz"), 401);
        $data['attempt'] = QuizAttempt::where('user_id',\Auth::user()->id)->where('id',$request->attempt)->first();
        $data['quiz'] = Quiz::where('status', 1)->where('id', $data['attempt']->quiz_id)->first();
        if(isset($request->theme) && $request->theme === 'theme0'){
            return view('admin.quiz.adminTheme')->with($data);
        }else{
            return view('admin.quiz.index')->with($data);
        }

    }
    public function list()
    {
        abort_if(Gate::denies("Quiz List"), 401);
        $data['roles'] = Role::where("name", "<>", "Super Admin")->get();
        $data['quiz_levels'] = QuizLevel::where('status', 1)->get();
        $data['trail_quiz'] = Quiz::where('status', 1)->where('main_quiz_id',null)->where('quiz_level_id', 1)->get();
        return view('admin.quiz.datatable')->with($data);
    }
    public function view($id)
    {
        abort_if(Gate::denies("Quiz View"), 401);
        $data['quiz'] = Quiz::where("id",$id)->withCount('ratedOnly')->first();
        $data['quiz_meta'] = json_decode($data['quiz']->meta) ?? [];
        $data['questions'] = QuizQuestion::where("quiz_id",$id)->with('answers')->get() ?? [];
        $data['statics'] = [
            'total_levels' => 0,
            'total_questions' => QuizQuestion::where("quiz_id",$id)->count(),
            'completed_quiz' => QuizAttempt::where('quiz_id',$id)->where('status','completed')->count(),

            ];
        $data['attempts'] = QuizAttempt::where('quiz_id',$id)->with('user')->withCount('answers')->orderBy('id','DESC')->get() ?? [];
        $data['attempt_counts'] = QuizAttempt::where('quiz_id',$id)->with('user')->orderBy('id','DESC')->count() ?? 0;
       $data['total_marks'] = QuizQuestion::where("quiz_id",$id)->sum('marks') ?? 0;
       $data['max_marks'] = QuizAttempt::where("quiz_id",$id)->max('total_marks') ?? 0;
        return view('admin.quiz.view')->with($data);
    }
    public function reportView($id)
    {
        abort_if(Gate::denies("Quiz View"), 401);
        $data['quiz'] = Quiz::where("id",$id)->withCount('ratedOnly')->first();
        $data['quiz_meta'] = json_decode($data['quiz']->meta) ?? [];

        $data['statics'] = [
            'total_levels' => 0,
            'total_questions' => QuizQuestion::where("quiz_id",$id)->count(),
            'completed_quiz' => QuizAttempt::where('quiz_id',$id)->where('status','completed')->count()];
        $data['attempt_counts'] = QuizAttempt::where('quiz_id',$id)->count() ?? 0;
        $data['attempted_user_counts'] = QuizAttempt::where('quiz_id', $id)->distinct('user_id')->count('user_id') ?? 0;
       $data['total_marks'] = QuizQuestion::where("quiz_id",$id)->sum('marks') ?? 0;
       $data['max_marks'] = QuizAttempt::where("quiz_id",$id)->max('total_marks') ?? 0;
       $data['top_scores_5'] = QuizAttempt::where("quiz_id",$id)->where('status','completed')->with('user')->orderBy('total_marks','DESC')->limit(5)->get() ?? [];
       $data['top_loser_5'] = QuizAttempt::where("quiz_id",$id)->where('status','completed')->with('user')->orderBy('total_marks','ASC')->limit(5)->get() ?? [];
       $data['recent_attempts'] = QuizAttempt::where("quiz_id",$id)->with('user')->orderBy('id','DESC')->limit(5)->get() ?? [];
        return view('admin.quiz.reportView')->with($data);
    }
    public function dashboard()
    {
        abort_if(Gate::denies("Quiz View"), 401);
        $data['statics'] = [
            'total_quiz' => Quiz::count(),
            'total_attempt' => QuizAttempt::count(),
            'total_completed_attempt' => QuizAttempt::where('status','completed')->count(),
            'total_failed_attempt' => QuizAttempt::where('status','fail')->count(),
            'total_passed_attempt' => QuizAttempt::where('result','pass')->count(),
            'total_questions' => 0,
            'completed_quiz' => 0
        ];

        return view('admin.quiz.dashboard')->with($data);
    }
    public function rules($id)
    {
        abort_if(Gate::denies("Quiz View"), 401);
        $data['quiz'] = Quiz::where("id",$id)->first();
        $data['trial_quiz'] = Quiz::where('id',$data['quiz']->trial_quiz_id)->first();
        // if(!empty($trial_quiz) && QuizAttempt::where('quiz_id',$trial_quiz->id)->where('user_id',\Auth::user()->id)->count() == 0){
        //     $data['quiz'] = $trial_quiz;
        // }
        $data['attempt'] = QuizAttempt::where('quiz_id',$data['trial_quiz']?->id)->where('status','completed')->where('user_id',\Auth::user()->id)->count();
        $data['time'] = QuizQuestion::where("quiz_id",$id)->sum('time');
        $data['total_questions'] = QuizQuestion::where("quiz_id",$id)->count();
        $data['quiz_meta'] = json_decode($data['quiz']->meta) ?? [];
        return view('admin.quiz.rules')->with($data);
    }
    public function feedback(Request $request)
    {

       // $data['quiz'] = Quiz::where("id",$id)->first();

       $data['attempt'] = QuizAttempt::where('id',$request->attempt)->first();
       $data['quiz'] = Quiz::where('id',$data['attempt']->quiz_id)->first();
       $data['target_quiz'] = Quiz::where('trial_quiz_id',$data['quiz']->id)->first() ?? null;
       if(empty($data['attempt'])) {
           abort(404);
       }
        return view('admin.quiz.thankyou')->with($data);
    }


    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'status' => 'nullable',
            'quiz_level_id' => 'required|integer',
            'rules' => 'nullable|string',
            'meta' => 'nullable',
            'quiz_theme' => 'nullable|string',
            'trial_quiz_id' => 'nullable|integer',


        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        if($request->trial_quiz_id != null && ($request->quiz_theme == null || $request->quiz_theme == '')){
            $result = array('status' => '0', 'msg' => 'Please select a Theme for this quiz');
            return json_encode($result);
        }

//        try {

        // if(Quiz::where('trial_quiz_id',$request->trial_quiz_id)->count() > 0){
        //     $result = array('status' => '0', 'msg' => 'Main Quiz already created for this trial quiz');
        //     return json_encode($result);
        // }



            $obj = new Quiz();
            $obj->name = $request->name;
            $obj->description = $request->description;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->negative_marking = ($request->negative_marking == 'on' || $request->negative_marking == '1')  ? 1 : 0;
            $obj->quiz_level_id = $request->quiz_level_id;
            $obj->rules = $request->rules;
            $obj->trial_quiz_id = $request->trial_quiz_id;
            $obj->meta = json_encode($request->meta);
            $obj->theme = $request->quiz_theme ?? 'theme1';
            $obj->created_by_id = \Auth::user()->id;
            if($obj->save()){
                if ($request->has('thumbnail')) {
                    $image = $this->uploadImage($request->file("thumbnail"),'1200','800','quiz','quiz/thumbnail');
                    $obj->thumbnail = $image;
                    $obj->save();



                    $media = new QuizMedia();
                    $media->model_type = Quiz::class;
                    $media->model_id = $obj->id;
                    $media->title =  $request->name;
                    $media->path = $image;
                    $media->extension = $request->file("thumbnail")->getClientOriginalExtension();
                    $media->size = $request->file("thumbnail")->getSize();
                    $media->type = 'Photos';
                    $media->status = 1;
                    $media->created_by_id = \Auth::user()->id;
                    $media->save();
                }
                $trial_quiz = Quiz::where('id',$request->trial_quiz_id)->first();
                if(!empty($trial_quiz)){
                    $trial_quiz->main_quiz_id = $obj->id;
                    $trial_quiz->save();

                }
                foreach ($request->role_id ?? [] as $role){
                   ModelRole::updateOrCreate([
                       'model_id' => $obj->id,
                       'model_type' => Quiz::class,
                       'role_id' => $role
                   ]);
                }


            }

            $result = array('status' => '1', 'msg' => 'Quiz created successfully');
//        } catch (\Exception $exception) {
//            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
//        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|exists:quizzes,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'status' => 'nullable',
            'quiz_level_id' => 'required|integer',
            'rules' => 'nullable|string',
            'meta' => 'nullable',
            'quiz_theme' => 'nullable|string',
            'trial_quiz_id' => 'nullable|integer',


        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

        $obj = Quiz::find($request->id);
        $obj->name = $request->name;
        $obj->description = $request->description;
        $obj->status = $request->status == 'on' ? 1 : 0;
        $obj->negative_marking = $request->negative_marking == 'on' ? 1 : 0;
        $obj->quiz_level_id = $request->quiz_level_id;
        $obj->trial_quiz_id = $request->trial_quiz_id;
        $obj->rules = $request->rules;
        $obj->meta = json_encode($request->meta);
        $obj->theme = $request->quiz_theme ?? 'theme1';
        $obj->updated_by_id = \Auth::user()->id;
        if($obj->save()){
            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"),'1200','800','quiz','quiz/thumbnail');
                $obj->thumbnail = $image;
                $obj->save();


                $media = new QuizMedia();
                $media->model_type = Quiz::class;
                $media->model_id = $obj->id;
                $media->title =  $request->name;
                $media->path = $image;
                $media->extension = $request->file("thumbnail")->getClientOriginalExtension();
                $media->size = $request->file("thumbnail")->getSize();
                $media->type = 'Photos';
                $media->status = 1;
                $media->created_by_id = \Auth::user()->id;
                $media->save();
            }
            $trial_quiz = Quiz::where('id',$request->trial_quiz_id)->first();
            if($trial_quiz){
                $trial_quiz->main_quiz_id = $obj->id;
                $trial_quiz->save();
            }
            foreach ($request->role_id ?? [] as $role){
                ModelRole::updateOrCreate([
                    'model_id' => $obj->id,
                    'model_type' => Quiz::class,
                    'role_id' => $role
                ]);
            }


        }

        $result = array('status' => '1', 'msg' => 'Quiz created successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function datatableRes(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'updated_at',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Quiz::when($request->show_trial==1,function($q){
            $q->where('main_quiz_id',null);
        })->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = Quiz::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->where(function ($q) use ($request) {
                if($request->show_trial==1){
                    $q->orWhere('quiz_level_id',1);
                }else{
                    $q->orWhere('quiz_level_id','!=','1');

                }
            })->with('created_by', 'updated_by','quiz_level')
            ->withCount('ratedOnly');
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Quiz::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->where(function ($q) use ($request) {
                if($request->show_trial==1){
                    $q->orWhere('quiz_level_id',1);
                }else{
                    $q->orWhere('quiz_level_id','!=','1');

                }
            })
            ->with('created_by', 'updated_by','quiz_level')
            ->withCount('ratedOnly');
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->offset($request['start'])->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }
    public function start(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'quiz_id' => 'required',
            'attempt_id' => 'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }
        $quiz = Quiz::where('id',$request->quiz_id)->first();
        $attempt = new QuizAttempt();
        $attempt->quiz_id = $request->quiz_id;
        $attempt->user_id = \Auth::user()->id;
        $attempt->status = 'pending';
        if($attempt->save()){

            if($quiz->attempt_id != null || $quiz->attempt_id != ''){
               $attempt = QuizAttempt::where('id',$quiz->attempt_id)->first();
                $attempt->status = 'completed';
                $attempt->completed_at = Carbon::now()->setTimezone('UTC');
                $attempt->save();
            }

            $theme = $quiz->quiz_level_id == 1 ?'theme0' : $quiz->theme;
            $link = route('admin.quiz.index').'?theme='.$theme.'&attempt='.$attempt->id;
            $result = array('status' => '1', 'msg' => 'Quiz started successfully', 'url' => $link);
        }else{
            $result = ['status' => '0','msg'=>'Something went wrong!!'];
        }
        return response()->json($result);
    }

    public function get(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:quizzes,id',
        ]);
        try {
            $data = Quiz::where('id',$request->id)->with('roles')->first();
            $selected_trial_quiz = Quiz::where('id',$data->trial_quiz_id)->first();
            $unselected_trial_quiz = Quiz::where('main_quiz_id',null)->where('quiz_level_id','1')->where('status','1')->get();
            //merge the trial quiz with the selected trial quiz
            $trial_quiz = $selected_trial_quiz ? $unselected_trial_quiz->merge([$selected_trial_quiz]) : $unselected_trial_quiz;
            $result = array('status' => '1', 'msg' => 'Quiz fetched successfully', 'data' => $data,'trial_quiz'=>$trial_quiz);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function getUnusedTrialQuiz(Request $request)
    {

        try {
            $data = Quiz::where('quiz_level_id','1')->where('status','1')->where('main_quiz_id',null)->get();
            $result = array('status' => '1', 'msg' => 'Quiz fetched successfully', 'data' => $data);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:quizzes,id',
            'action' => 'required',
        ]);

        $status = Quiz::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Quiz status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function feedbackStore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'rating' => 'nullable',
            'attempt_id' => 'nullable',
            'feedback' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

            $attempt = QuizAttempt::where('id',$request->attempt_id)->first();
            $quiz = Quiz::find($attempt->quiz_id);
            $meta = json_decode($quiz->meta);
            $passing_marks = $meta->passing_mark ?? 0;
            $attempt->rating = $request->rating;
            $attempt->feedback = $request->feedback;
            $attempt->completed_at = Carbon::now()->setTimezone('UTC');
            $attempt->status = 'completed';
            $attempt->result = $passing_marks <= $attempt->total_marks ? 'pass' : 'fail';
            if($attempt->save()){
                $avg_rating = QuizAttempt::where('quiz_id',$attempt->quiz_id)->where('rating','<>',null)->avg('rating');
                $quiz->rating = $avg_rating;
                $quiz->save();
                $result = array('status' => '1', 'msg' => 'Feedback submitted successfully');
            }else{
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }


        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function duplicate(Request $request){
        $request->validate([
            'id' => 'required|exists:quizzes,id',
        ]);
        $quiz = Quiz::find($request->id);
        $new_quiz = $quiz->replicate();
        $new_quiz->name = $quiz->name.' - Copy';
        $new_quiz->status = 1;
        $new_quiz->trial_quiz_id = null;
        $new_quiz->main_quiz_id = null;
        $new_quiz->rating = 0;
        $new_quiz->created_by_id = \Auth::user()->id;
        $new_quiz->save();
        $questions = QuizQuestion::where('quiz_id',$quiz->id)->with('answers')->get();
        foreach ($questions as $question){
            $new_question = $question->replicate();
            $new_question->quiz_id = $new_quiz->id;
            $new_question->save();
            $answers = $question->answers;
            foreach ($answers as $answer){
                $new_answer = $answer->replicate();
                $new_answer->quiz_question_id = $new_question->id;
                $new_answer->save();
            }
        }
        $result = array('status' => '1', 'msg' => 'Quiz duplicated successfully');
        return json_encode($result);
    }
    public function getMainQuiz(Request $request){
        $request->validate([
            'id' => 'required|exists:quizzes,id',
        ]);

        $main_quiz = Quiz::where('trial_quiz_id',$request->id)->first();
        $result = array('status' => '1', 'msg' => 'Main Quiz fetched successfully', 'data' => $main_quiz);
        return json_encode($result);
    }

    public function delete(Request $request){
        $request->validate([
            'id' => 'required|exists:quizzes,id',
        ]);
        $quiz = Quiz::find($request->id);
        if($quiz->delete()){
            $result = array('status' => '1', 'msg' => 'Quiz deleted successfully');
        }else{
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

}

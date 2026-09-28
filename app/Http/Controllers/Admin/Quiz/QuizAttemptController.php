<?php

namespace App\Http\Controllers\Admin\Quiz;


use App\Models\ModelRole;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizLevel;
use App\Models\QuizMedia;
use App\Models\QuizQuestion;
use App\Models\QuizAttemptAnswer;
use App\Models\Region;
use App\Models\User;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class QuizAttemptController extends Controller
{
    public function view($id)
    {
        $data['attempt'] = QuizAttempt::where('id',$id)->withCount('answers')->first();
        $data['quiz'] = Quiz::find($data['attempt']->quiz_id);
        $data['questions'] = QuizQuestion::where('quiz_id', $data['quiz']->id)->get();
        $data['attempt_answers'] = collect(QuizAttemptAnswer::where('quiz_attempt_id', $data['attempt']->id)->get())->keyBy('quiz_question_id');
        $data['user'] = User::where('id',$data['attempt']->user_id)->first();
        $data['completed_quiz'] = QuizAttempt::where('user_id',$data['attempt']->user_id)->where('status','completed')->count();
        $data['total_score'] = QuizAttempt::where('user_id',$data['attempt']->user_id)->where('status','completed')->sum('total_marks');
        $data['quiz_total_marks'] = QuizQuestion::where("quiz_id",$data['quiz']->id)->sum('marks') ?? 0;
        return view('admin.quiz.resultView')->with($data);
    }
    public function youtubeIframe(Request $request)
    {
        $youtube_video_url = $request->video_url;
        $youtube_video_id = explode("?v=", $youtube_video_url);
        $data['video_id'] = $youtube_video_id;
        return view('admin.quiz.youtube')->with($data);
    }
    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'updated_at',
            6 => 'total_marks',
            7 => 'status',
            8 => 'created_at',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = QuizAttempt::where('quiz_id',$request->quiz_id)->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = QuizAttempt::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })
            ->when($request->quiz_user_id!='', function ($q) use ($request) {
                $q->where('user_id', $request->quiz_user_id);
            })->where('quiz_id',$request->quiz_id)
            ->with('user')->withCount('answers');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = QuizAttempt::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->when($request->quiz_user_id!='', function ($q) use ($request) {
                $q->where('user_id', $request->quiz_user_id);
            })->where('quiz_id',$request->quiz_id)->with('user')->withCount('answers');
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
    public function userQuizHisotry(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            4 => 'total_marks',
            5 => 'status',
            6 => 'created_at',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = QuizAttempt::when($request->quiz_id!='', function ($q) use ($request) {
            $q->where('quiz_id', $request->quiz_id);
        })->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = QuizAttempt::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })
            ->when($request->quiz_user_id!='', function ($q) use ($request) {
                $q->where('user_id', $request->quiz_user_id);
            })
            ->when($request->quiz_id!='', function ($q) use ($request) {
                $q->where('quiz_id', $request->quiz_id);
            })
            ->with('user','quiz')->withCount('answers');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = QuizAttempt::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->when($request->quiz_user_id!='', function ($q) use ($request) {
                $q->where('user_id', $request->quiz_user_id);
            })
            ->when($request->quiz_id!='', function ($q) use ($request) {
                $q->where('quiz_id', $request->quiz_id);
            })
            ->with('user','quiz')->withCount('answers');
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

    public function getRunningAttemptData(Request $request)
    {
        $data['attempt'] = QuizAttempt::where('id',$request->attempt_id)->first();
        $data['attempted_question_count'] = QuizAttemptAnswer::where('quiz_attempt_id',$request->attempt_id)->count();
        return response()->json($data);
    }


}

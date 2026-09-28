<?php

namespace App\Http\Controllers\Admin\Quiz;


use App\Models\ModelRole;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizLevel;
use App\Models\QuizMedia;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\Region;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
class QuizReportController extends Controller
{


    public function getQuizData1(Request $request)
    {
        try{
            $quiz_id = $request->quiz_id ?? '';
        $quiz_data = [
            'completed' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                $q->where('id', $quiz_id);
            })->where('status', 'completed')->count() ?? 0,
            'unfinished' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                $q->where('id', $quiz_id);
            })->where('status', '!=','completed')->count() ?? 0,
            'passed' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                $q->where('id', $quiz_id);
            })->where('result', 'pass')->count() ?? 0,
            'failed' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                $q->where('id', $quiz_id);
            })->where('result', 'fail')->count() ?? 0
                ];

                $result = [
                    'status' => 'success',
                    'data' => $quiz_data
                ];
                return response()->json($result);
            }catch (\Exception $e){
                $data = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                return response()->json($data);
            }
    }
    public function getQuizData2(Request $request)
    {
        try{
            $quiz_id = $request->quiz_id;
            $usersWithMultipleAttempts = QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                $q->where('id', $quiz_id);
            })->groupBy('user_id')->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');
            $usersWithMultipleAttemptsCompleted = QuizAttempt::whereIn('user_id', $usersWithMultipleAttempts)
            ->when($quiz_id!='', function ($q) use ($quiz_id) {
                $q->where('id', $quiz_id);
            })
            ->distinct('user_id')
            ->count('user_id') ?? 0;
            $quiz_total_marks = QuizQuestion::where('quiz_id', $quiz_id)->sum('marks');
        $quiz_data = [
            'system_user_count' => User::where('status',true)->count() ?? 0,
            'attempted_user_count' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('id', $quiz_id);
                })->distinct('user_id')->count('user_id') ?? 0,
            'quiz_completed_users' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('id', $quiz_id);
                })->where('status', 'completed')->distinct('user_id')->count('user_id') ?? 0,
            'multi_attempt_completed' => $usersWithMultipleAttemptsCompleted ?? 0,
            'total_attempt' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('id', $quiz_id);
                })->count() ?? 0,
            'completed' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('quiz_id', $quiz_id);
                })->where('status', 'completed')->count() ?? 0,
            'incomplete' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('quiz_id', $quiz_id);
                })->where('status', '!=','completed')->count() ?? 0,
            'hundred_percent_marks' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('id', $quiz_id);
                })->where('total_marks', $quiz_total_marks)->distinct('user_id')->count('user_id') ?? 0,
            'passed' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('id', $quiz_id);
                })->where('result', 'pass')->count() ?? 0,
            'failed' => QuizAttempt::when($quiz_id!='', function ($q) use ($quiz_id) {
                    $q->where('id', $quiz_id);
                })->where('result', 'fail')->count() ?? 0
                ];

                $result = [
                    'status' => 'success',
                    'data' => $quiz_data
                ];
                return response()->json($result);
            }catch (\Exception $e){
                $data = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                return response()->json($data);
            }
    }
    public function getQuizData3(Request $request)
    {
        // this will fetch to 5 which has most attempts
        try{
            $topQuizzes = Quiz::withCount('attempt')
                        ->where('quiz_level_id','!=','1')
                        ->orderBy('attempt_count', 'desc')
                        ->take(5)
                        ->get();
                        $result = [
                            'status' => 'success',
                            'data' => $topQuizzes
                        ];
                        return response()->json($result);

        }catch (\Exception $e){
            $data = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
            return response()->json($data);
        }


    }
    public function userQuizHisotry(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            2 => 'time_taken',
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
    public function last30DaysData(Request $request)
    {
        $dates = [];
        $currentDate = new \DateTime();
        for ($i = 0; $i < 10; $i++) {
            $date = clone $currentDate;
            $date->modify("-$i days");
            $dates[] = $date->format('Y-m-d');
        }
        $attempts = [];
        foreach ($dates as $date) {
            $attempts[$date] = [
              //  QuizAttempt::whereDate('created_at', $date)->count() ?? 0,
                QuizAttempt::whereDate('created_at', $date)->where('result', 'pass')->count() ?? 0,
                QuizAttempt::whereDate('created_at', $date)->where('result', 'fail')->count() ?? 0,
                QuizAttempt::whereDate('created_at', $date)->where('status', 'pending')->count() ?? 0,
                QuizAttempt::whereDate('created_at', $date)->where('status', 'completed')->count() ?? 0,
            ];
        }
        $data['dates'] = $dates;
        $data['attempts'] = $attempts;

        $result = [
            'status' => 'success',
            'data' => $data
        ];
        return response()->json($result);

    }
}

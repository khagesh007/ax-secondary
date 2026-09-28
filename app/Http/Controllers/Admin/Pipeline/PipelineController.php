<?php

namespace App\Http\Controllers\Admin\Pipeline;



use App\Models\Pipeline;
use App\Models\User;
use App\Models\Group;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PipelineController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies("Pipeline List"), 403);

        return view('admin.pipeline.index');
    }
 public function getTeamUser($parent_id): array
    {
        return User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $parent_id)
            ->where('status', true)->pluck('id')->toArray();
    }
    public function save(Request $request)
    {
//        if (Gate::denies('create-pipeline')) {
//            abort(403);
//        }
        try{
        $pipelines = $request->pipelines;
    if(!empty($pipelines)){
        foreach ($pipelines as $pipelineData) {
            $pipeline = ($pipelineData['id'] !=null && $pipelineData['id'] !="") ? Pipeline::find($pipelineData['id']) : new Pipeline();
            $pipeline->user_id = $pipelineData['user_id'] ?? null;
            $pipeline->deal_type = $pipelineData['deal_type'] ?? null;
            $pipeline->property_details = $pipelineData['property_details'] ?? null;
            $pipeline->deal_value = $pipelineData['deal_value'] ?? null;
            $pipeline->commission_type = $pipelineData['commission_type'] ?? null;
            $pipeline->commission_value = $pipelineData['commission_value'] ?? null;
            $pipeline->commission = $pipelineData['commission'] ?? null;
            $pipeline->comment = $pipelineData['comment'] ?? null;
            $pipeline->priority = $pipelineData['priority'] ?? 'Low';
            $pipeline->closing_date = isset($pipelineData['closing_date']) ? date('Y-m-d', strtotime($pipelineData['closing_date'])) : null;
            $pipeline->status =  isset($pipelineData['status']) ? $pipelineData['status']  : 'Active';
            $pipeline->source_id = isset($pipelineData['source_id']) ? (int)$pipelineData['source_id'] : null;
            $pipeline->manager_comment = isset($pipelineData['manager_comment']) ? Str::limit($pipelineData['manager_comment'], 255) : null;
            $pipeline->reason_for_loss = isset($pipelineData['reason_for_loss']) ? Str::limit($pipelineData['reason_for_loss'], 255) : null;
            $pipeline->save();
        }
    }

        return response()->json([
            'status' => 'success',
            'message' => 'Pipeline created successfully',
        ]);
    }
    catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create pipeline: ' . $e->getMessage(),
        ], 500);
    }
    }

    public function list(Request $request)
    {
//        if (Gate::denies('view-pipeline')) {
//            abort(403);
//        }
    if(auth()->user()->hasRole(["Agent", "Primary Agent"])) {
    $user_ids = [auth()->user()->id];
    }else {
        $selected_group = Group::where('name', $request->department)->with('user')->first();
        $user_ids = [];
        if ($selected_group) {
            $user_ids = $this->getTeamUser($selected_group->user->id);
        } else {
            $user_ids = [];
        }
    }
        $user_id = $request->user_id != 'null' ? $request->user_id : '';
        $user_ids = $user_id != '' ? [$user_id] : $user_ids;
        $pipelines = Pipeline::when($request->status != 'null', function ($query) use ($request) {
            $query->where('status', $request->status);
        })->when(count($user_ids) > 0, function ($query) use ($user_ids) {
        $query->whereIn('user_id', $user_ids);
    })->with(['agent', 'source','agent.parent','agent.parent.group','createdBy','updatedBy'])->orderBy('id','DESC')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Pipelines retrieved successfully',
            'data' => $pipelines
        ]);
    }



    public function getAgents(Request $request)
    {
//        if (Gate::denies('view-pipeline')) {
//            abort(403);
//        }
        $notAdmin   = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $department = $request->query('department', 'secondary');
        $search = $request->query('q', '');

        if($department == 'secondary' && $notAdmin){
            $child_users_id = getUserChildrenIds(auth()->user()->id);
            $filter_by_users  = true;
        }else if($department == 'secondary' && !$notAdmin){
            $child_users_id = [];
            $filter_by_users  = false;
        }else{
            $group = Group::where('name', $department)->with(['user'])->first();
            $child_users_id = $this->getTeamUser($group->user->id);
            $filter_by_users  = true;
        }
        $agents = User::whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })
        ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
        ->where('status',true)
        ->when($filter_by_users, function ($q) use ($child_users_id) {
                $q->whereIn("id", $child_users_id);
            })
            ->with(['parent','parent.group'])
            ->get();
        $result = [
            "status" => "success",
            "message" => "Agents retrieved successfully",
            "data" => $agents

//        ->map(function ($agent) {
//                return [
//                    'id' => $agent->id,
//                    'name' => $agent->name,
//                    'email' => $agent->email,
//                    'avatar' => $agent->thumb_url,
//                ];
//            })
        ];
        return response()->json($result);
    }

    public function delete(Request $request, $id)
    {


        $pipeline = Pipeline::findOrFail($id);
        $pipeline->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pipeline deleted successfully',
        ]);
    }




}

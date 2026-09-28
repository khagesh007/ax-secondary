<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\AjaxController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Roles List"), 401);
        $data['permissions'] = Permission::where("status", true)->get(["id", "name", "group_name"])->groupBy("group_name");

        return view('admin.user.role')->with($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "permissions" => "nullable|array",
            "permissions.*" => "nullable|exists:permissions,id",
            "name" => "required|string",
        ]);

        try {
            $role = Role::create($validatedData);
            $role->syncPermissions($request->permissions);
            $result = array('status' => '1', 'msg' => 'Role added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'name',
            3 => 'updated_at',
            4 => 'guard_name',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Role::count();
        $totalFiltered = $totalData;

        // if (!empty($searchKeyWord)) {
        //     $data = Role::where(function ($q) use ($searchKeyWord) {
        //         $q->where('name', 'LIKE', '%'.$searchKeyWord.'%');
        //         $q->orWhere('guard_name', 'LIKE', '%'.$searchKeyWord.'%');
        //     })->with('permissions')->get();
        // } else {
        //     $data = Role::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
        //         ->offset($request['start'])
        //         ->limit($request['length'])
        //         ->with('permissions')
        //         ->get();
        // }

        if (!empty($searchKeyWord)) {
            $data_filtered = Role::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                $q->orWhere('guard_name', 'LIKE', '%'.$searchKeyWord.'%');
            })->with('permissions');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Role::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('permissions');
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

    public function getRole(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:roles,id',
        ]);
        try {
            $cat = Role::find($request->id);
            $cat->load("permissions");
            $result = array('status' => '1', 'msg' => 'Role details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }





    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:roles',
        ]);

        $category = Role::find($request->id);
        if ($category->delete()) {
            $result = array('status' => '1', 'msg' => 'Role deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (Role::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Roles deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
    public function update(Request $request){
        $request->validate([
            "permissions" => "nullable|array",
            "permissions.*" => "nullable|exists:permissions,id",
            "name" => "required|string",
        ]);
        try {

            $role = Role::find($request->id);
            $old_name  = $role->name;
            $oldPermissions = $role->permissions()->pluck('name')->toArray();
            $orignal = array_merge(['name'=>$old_name],['permissions'=>$oldPermissions]);
            $role->name = $request->name;
            $role->save();
            $new_name = $role->name;
            $role->syncPermissions($request->permissions);
            $newPermissions= $role->permissions()->pluck('name')->toArray();
            $newData = array_merge(['name'=>$new_name],['permissions'=>$newPermissions]);

            $arr = ['attributes' => $newData, 'old' => $orignal];

            activity()->performedOn($role)->causedBy(auth()->user()->id)->withProperties($arr)->event('updated')
                ->log(auth()->user()->first_name . ' has updated User ' . $role->id);




            // activity()
            // ->performedOn($role)
            // ->withProperties($logAttributes)
            // ->event('updated')
            // ->causedBy(auth()->user()->id)
            // ->log(auth()->user()->first_name . ' has updated Role ' . $role->id);


            $result = array('status' => '1', 'msg' => 'Permissions updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!! '.$exception->getMessage());
        }
        return json_encode($result);
    }



    public function getActivityLog(Request $request)
    {
        $params = new Request();
        $params->replace($request->all());
        $res = (new AjaxController())->roleActivityLog($params);

        if ($res->original['data'] != null) {
            $datas = [];
            foreach ($res->original['data'] as $data) {
                $properties['old'] = isset($data->properties['old']) ? leadColumnsReadable($data?->properties['old']) : [];
                $properties['attributes'] = isset($data->properties['attributes']) ? leadColumnsReadable($data?->properties['attributes']) : [];
                $data->properties = $properties;
                $datas[] = $data;

            }
        }
        return response()->json(['data' => $datas]);
    }
}

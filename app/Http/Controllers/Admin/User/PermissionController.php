<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Permission List"), 401);
        return view('admin.user.permission');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required|string",
            "group_name" => "nullable|string",
        ]);

        $validatedData["slug"] = Str::slug($request->name, "-");
        try {
            $role = Permission::create($validatedData);
            $result = array('status' => '1', 'msg' => 'Permission added successfully');
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
            3 => 'slug',
            4 => 'group_name',
            5 => 'updated_at',
            6 => 'guard_name',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Permission::count();
        $totalFiltered = $totalData;

     

        if (!empty($searchKeyWord)) {
            $data_filtered = Permission::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                $q->orWhere('guard_name', 'LIKE', '%'.$searchKeyWord.'%');
            });

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Permission::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"]);
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

    public function getPermission(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:permissions,id',
        ]);
        try {
            $cat = Permission::find($request->id);
            $result = array('status' => '1', 'msg' => 'Permission details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:permissions",
            "name" => "required|string",
            "group_name" => "nullable|string",
        ]);

        $validatedData["slug"] = Str::slug($request->name);
        try {
            $role = Permission::find($request->id);
            $role->update($validatedData);
            $result = array('status' => '1', 'msg' => 'Permission details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }


    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:permissions',
        ]);

        $category = Permission::find($request->id);
        if ($category->delete()) {
            $result = array('status' => '1', 'msg' => 'Permission deleted successfully');
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
            if (Permission::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Permissions deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}

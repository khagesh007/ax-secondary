<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies("Users View Permission"), 401);
        $data['permissions'] = Permission::where("status", true)->get(["id", "name", "group_name"])->groupBy("group_name");
        if (auth()->user()->hasRole("Super Admin")) {
            $data['roles'] = Role::all();
        } else {
            $data['roles'] = Role::where("name", "<>", "Super Admin")->get();
        }
       if($request->user_id) {
           $data['user'] = User::where('id', $request->user_id)->first() ??  abort_if('User not found',404);

       }else{
           abort_if('User not found',404);
       }
        return view('admin.user.userPermission')->with($data);

    }
    public function getRolePermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'id' =>'required|array',
            'id.*' => 'required|exists:roles,id',
        ]);
        try {
            $roles = Role::whereIn('id',$request->id)->get();
            $permissions = $roles->flatMap(function ($role) {
                return $role->permissions;
            })->unique();

            $result = array('status' => '1', 'msg' => 'Role details fetched successfully', 'data' => $permissions);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function getUserPermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        try {
            // get all permissions by user model using model_has_permissions table
            $user = User::where('id',$request->user_id)->first();
            $permissionbyUser = $user->permissions;
            $result = array('status' => '1', 'msg' => 'Role details fetched successfully', 'data' => $permissionbyUser);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function updatePermissions(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            "permissions" => "nullable|array",
            "permissions.*" => "nullable|exists:permissions,id",
//            "roles" => "nullable|array",
//            "roles.*" => "nullable|exists:roles,id",
        ]);
        try {
            $user = User::where('id',$request->user_id)->first();
//            $roles = Role::whereIn('id',$request->roles)->get();
//            $user->syncRoles($request->roles);
            $user->syncPermissions($request->permissions);

            $result = array('status' => '1', 'msg' => 'Permissions updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
}

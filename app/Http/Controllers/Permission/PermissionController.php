<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permission= Permission::latest()->get();
        
       return view('admin.Permission.index',['permissions'=>$permission]);
       
    }
   
    public function store(Request $request)
    {
         // validation 
         $request->validate([
            'name'=>'required|min:2',
        ]);
        $permission = Permission::create(['name'=>$request->name]);
        return redirect()->back()->with('message','Permission created Successfully !!!');
       
    }



    public function destroy(Permission $permission,$id)
    {
        Permission::where('id', $id)->delete();
        
        return redirect()->back()->with('message','Permission deleted !!!');
    }
}

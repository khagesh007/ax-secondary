<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $user= User::latest()->get();
        return view('admin.user.index',['users'=>$user]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|min:2|max:50',
            'email' => 'required|email|unique:users',
            'password'=>'required'
           // 'image'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
          
        ]);
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'avatar'=>$request->avatar,
            'password'=> bcrypt($request->password), 
        ]);
        // $user->syncRoles($request->roles);
        return redirect()->back()->with('message','User created Successfully !!!');
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        
        return redirect()->back()->with('message','User Successfully !!!');
    }

   
}

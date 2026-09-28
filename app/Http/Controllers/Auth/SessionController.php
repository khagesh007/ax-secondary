<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Hash;
use Auth;
use Session;

class SessionController extends Controller
{

public function checkSession(Request $request){
    if(Auth::check()) {
        return response()->json(['status' =>1]);
    }else{
        return response()->json(['status' =>0,'token'=>(csrf_token())]);
    }
}

    public function ajaxlogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1;
        if (Auth::attempt($credentials,$remember)) {
            activity()->causedBy(Auth::user()->id)->log('User Logged In');
            return response()->json(['status' =>1,'token'=>(csrf_token()),'msg'=>'Session resumed successfully']);

        }else{
            return  response()->json(['status' =>0,'url'=>route('login'),'msg'=>'Failed to login']);
        }


    }
}

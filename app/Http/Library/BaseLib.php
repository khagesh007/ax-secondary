<?php

namespace App\Http\Library;

class BaseLib
{
    public function sendResponse($status = false, $message = '', $data = '', $code = 404, $page = 1, $per_page = 10)
    {
        return compact('status','message','data','code');
    }

    public function authUser()
    {
        return true;
        //return auth()->guard('api')->user();
    }

    public function authCheck()
    {
        return auth()->guard('api')->check();
    }
}

<?php

namespace App\Http\Tasks\Api;

class BaseTask
{
    public function sendResponse($status = false, $message = '', $data = '', $code = 404, $request = [])
    {
        return compact('status','message','data','code', 'request');
    }

    public function authUser()
    {
        return auth()->guard('api')->user();
    }

    public function authCheck()
    {
        return auth()->guard('api')->check();
    }
}

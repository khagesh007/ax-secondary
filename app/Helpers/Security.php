<?php


function ajaxOnly($request)
{


    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;

    if(!$isAjax) {
        abort_if(true, 403, 'Unauthorized action');
    }
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (!$request->ajax()) {
        abort_if(true, 403, 'Unauthorized action.');
    }
}

function isBlackListed($number){
    $numbers = [
        '0000000000',
    ];
    return in_array($number,$numbers) ? true : false;
}



if (!function_exists('checkBlocklist')) {
    function checkBlocklist($number,$model,$request)
    {
        $formatted_number = validatePhoneNumber($number);
        $blocklist = \App\Models\BlacklistedNumber::whereRaw("RIGHT(number, 8) = RIGHT(?, 8)", [$formatted_number])->first();
        if ($blocklist) {
            if($request!=null){
                $req = json_encode($request->all());
            }else{
                $req = null;
            }
            $black = \App\Models\BlacklistedNumberHistory::create([
                'model' => $model,
                'user_id' => auth()->user()->id ?? null,
                'blacklisted_number_id' => $blocklist->id,
                'data' => $req,
            ]);
            if(auth()->user()){
                $message = auth()->user()->full_name. " tried to contact " .$formatted_number. " (blacklisted) at ".date('Y-m-d H:i:s').' for '.$model .'.';

            }else{
                $message = "A blocked number attempted to make contact at " . date('Y-m-d H:i:s') . " for " . $model . ".";
            }
            info($message);
            return true;
        }
        else{
            return false;
        }
    }
}


if (!function_exists('checkBlocklistAPI')) {
    function checkBlocklistAPI($number,$model,$message)
    {
        $formatted_number = validatePhoneNumber($number);
        $blocklist = \App\Models\BlacklistedNumber::whereRaw("RIGHT(number, 8) = RIGHT(?, 8)", [$formatted_number])->first();
        if ($blocklist) {
            $black = \App\Models\BlacklistedNumberHistory::create([
                'model' => $model,
                'user_id' =>  null,
                'blacklisted_number_id' => $blocklist->id,
                'data' => json_encode($message),
            ]);
            $message = "A blocked number attempted to make contact via API at " . date('Y-m-d H:i:s') . " for " . $model . ".";
            info($message);
            return true;
        }
        else{
            return false;
        }
    }
}


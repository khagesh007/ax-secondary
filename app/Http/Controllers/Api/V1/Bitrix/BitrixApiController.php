<?php

namespace App\Http\Controllers\Api\V1\Bitrix;

use App\Http\Controllers\Api\BaseController;
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class BitrixApiController extends BaseController
{

    //
    public function __construct()
    {

    }

    /**
     *
     *
     * Bitrix Login
     * @header Accept application/json

     * @group Bitrix Login
     * @bodyParam email email required email of the Agent is required. Example: abc@axcapital.ae
     * @bodyParam bitrix_id integer required Bitrix ID of the Agent is required . Example: 222392
     * @bodyParam department string required department of the user is required. Example: Exclusive Offplan
     *
     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "One time login link created",
    "data": {
    "redirect_url": "https://secondary.axcrm.ae/bitrix/FFyrEDhVhnzfQzfvImzMrtIAInEXBGAq",
    "token": "FFyrEDhVhnzfQzfvImzMrtIAInEXBGAq"
    }
    }
     *
     *
     * @response 422 scenario="Validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "email": [
    "The email field is required."
    ],
    "bitrix_id": [
    "The bitrix id field is required."
    ],
    "department": [
    "The department field is required."
    ]
    }
    }
     */

    public function bitrixLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'bitrix_id' => 'required',
            'department' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = User::where('email', $request->email)->where('bitrix_id', $request->bitrix_id)->first();
            if ($user) {

                // if(!isset($request->department)){
                //     if(!$user->hasRole(['Agent', 'Super Admin', 'Admin', 'Team Leader', 'Line Manager','Sales Director','Telesales' ,'Telesales Head','Head of Secondary'])){
                //         $user->assignRole('No Access User');
                //         $user->save();
                //     }
                // }

                // if ($request->department == 'Exclusive Offplan' || $request->department == 'offplan') {
                //     if(!$user->hasRole(['Agent', 'Super Admin', 'Admin', 'Team Leader', 'Line Manager','Sales Director','Telesales' ,'Telesales Head','Head of Secondary'])){
                //     $user->assignRole('Primary Agent');
                //     $user->save();
                // }else{
                //     if(!$user->hasRole(['Agent', 'Super Admin', 'Admin', 'Team Leader', 'Line Manager','Sales Director','Telesales' ,'Telesales Head','Head of Secondary'])){
                //         $user->assignRole('Primary Agent');
                //         $user->save();
                // }
                // if (!$user->hasRole(['Agent', 'Super Admin', 'Admin', 'Team Leader', 'Line Manager', 'Sales Director', 'Telesales', 'Telesales Head', 'Head of Secondary', 'CEO', "Admin - Abu Dhabi"])) {
                $orignal = $user->getOriginal();
                if ($user->roles->isEmpty()) {

                    if (isset($request->department) && ($request->department == 'Exclusive Offplan' || $request->department == 'offplan' || $request->department == 'Primary')) {
                        $user->assignRole('Primary Agent');
                    } else {
                        $user->assignRole('No Access User');
                    }
                    $user->save();

                }




                // $user->first_name = $user->first_name . rand(1, 100);
                // $user->save();

                $existingToken = AuthToken::where('user_id', $user->id)->first();
                if ($existingToken) {
                    $existingToken->delete();
                }
                $auth_token = generateAlpha(32);
                AuthToken::create([
                    'user_id' => $user->id,
                    'bitrix_id' => $user->bitrix_id,
                    'token' => $auth_token,
                ]);
                $redirect_url = route('public.bitrixLogin', ['slug' => $auth_token]);
                $redirect_url = stripslashes($redirect_url); // Remove backslashes, if any
                $redirect_url = $redirect_url.'?authenticator=false';
                $data = ['redirect_url' => $redirect_url, 'token' => $auth_token];
                return $this->sendResponse(true, 'One time login link created', $data, 200);
            } else {
                return $this->sendResponse(false, 'User Not Found', null, 200);
            }

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in bitrixLogin function in BitrixApiController.php " . $th->getMessage());
            return $this->sendResponse(false, 'Something Went Wrong.' . $th->getMessage(), null, 200);

        }

    }
}

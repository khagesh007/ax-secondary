<?php

namespace App\Http\Controllers\Api\V1\AxContacts;

use App\Http\Controllers\Api\BaseController;
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class AxContactsApiController extends BaseController
{
    public function axContactsLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
            'get_location' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = User::where('email', $request->email)->where('password', $request->password)->first();
            if ($user) {

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

                $redirect_url = route('public.ContactsLogin', ['slug' => $auth_token]);
                $redirect_url = stripslashes($redirect_url); // Remove backslashes, if any

                $data = ['redirect_url' => $redirect_url, 'token' => $auth_token];

                if (isset($request->get_location) && $request->get_location == true) {
                    $data['location'] = $user?->locations;
                    $data['location'] = $user?->locations?->map(function ($location) {
                        return $location['parentable_type']::where('id', $location['parentable_id'])->first();
                    });
                }
                return $this->sendResponse(true, 'One time login link created', $data, 200);
            } else {
                return $this->sendResponse(false, 'User Not Found', null, 200);
            }

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in AxContacts function in AxContactsApiController.php " . $th->getMessage());
            return $this->sendResponse(false, 'Something Went Wrong.' . $th->getMessage(), null, 200);

        }

    }
}

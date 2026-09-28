<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserPortalResource;
use App\Jobs\ProcessUser;
use App\Models\CoordinateRecording;
use App\Models\User;
use App\Traits\FileTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Validator;
use Illuminate\Support\Facades\URL;

class UserApiV2Controller extends BaseController
{

    public function getAgents(Request $request)
    {
        if($request->api_token != 'fYlQO814tothodaCEdmdpCommercial'){
            return $this->sendResponse(false, 'Invalid Token', [], 401);
        }



        $agents = User::whereHas('roles', function ($q) {
            $q->where('name', 'agent');
        })->when($request->commercial, function ($q) use ($request) {
                $q->where('entity', 'Commercial');
            })
            ->where('status',true)
            ->get();
        return $this->sendResponse(true, 'Agents List', UserPortalResource::collection($agents), 200);

    }
}

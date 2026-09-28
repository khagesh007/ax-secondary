<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Jobs\AgentMailPass;
class ExtraController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function sendCredentialsToAgent(){
        
        $agents = User::where('status', 1)->whereNotNull("bitrix_id")->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent","Team Leader", "Line Manager", "Sales Director"]);
        })->get();
        
        foreach($agents as $agent){
            info("\nSent credentials to agent ".$agent->id);
            echo("\nSent credentials to agent ".$agent->id);
            echo"<br>";
            AgentMailPass::dispatch($agent);
        }
    }

}
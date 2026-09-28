<?php

namespace App\Http\Controllers\Front\Status;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Property;
use Illuminate\Http\Request;
class StatusController extends Controller
{

    public function screen1(){

        return view('front.status.screen1');
    }
    public function screen2(){

        return view('front.status.screen2');
    }
    public function screen3(){

        return view('front.status.screen3');
    }
    public function screen4(){

        return view('front.status.screen4');
    }
    public function screen5(){

        return view('front.status.screen5');
    }
}

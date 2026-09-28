<?php

namespace App\Http\Controllers\Admin\KpiV3;

use App\Http\Controllers\Controller;
use App\Models\KpiTarget;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;

class KpiV3TeamController extends Controller
{
    public function index()
    {
        return view('admin.kpi_v3.index');
    }
}

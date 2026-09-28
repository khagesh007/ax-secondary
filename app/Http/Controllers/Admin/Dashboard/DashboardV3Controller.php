<?php
namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardV3Controller extends Controller
{

    public function index()
    {
        if(auth()->user()->hasRole('Agent')) {
            return view('admin.dashboard.v3.agent');
        }
        $data['departments'] = [
            'secondary' => 'Secondary',
            'S1' => 'Secondary S1',
            'S2' => 'Secondary S2',
            'S3' => 'Secondary S3',
            'S4' => 'Secondary S4',
            'S5' => 'Secondary S5',
            'S6' => 'Secondary S6',
            'HOS' => 'HOS',

        ];
        return view('admin.dashboard.v3.index')->with($data);
    }

    public function agentDashboard()
    {
        return view('admin.dashboard.v3.agent');
    }

}

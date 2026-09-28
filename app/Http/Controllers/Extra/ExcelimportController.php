<?php

namespace App\Http\Controllers\Extra;

use App\Imports\BayutAgentInsightImport;
use App\Models\Location;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Excel;
use App\Imports\ImportLandlord;
use App\Imports\ImportLeads;
use App\Imports\ImportUsers;
use App\Imports\ImportProperty;

class ExcelimportController extends Controller
{
    public function index()
    {
        return abort(404);
        return view('admin.import.index');
    }

    public function importLandlord(Request $request)
    {
        return abort(404);

        if(Excel::import(new ImportLandlord, $request->file('file'))){
            return response()->json(['status'=>'1','message' => 'Landlords imported successfully'], 200);
        }else{
            return response()->json(['status'=>'0','message' => 'Something went wrong'], 500);
        }
    }
    public function importLeads(Request $request)
    {
        return abort(404);

        if (Excel::import(new ImportLeads, $request->file('file'))) {
            return response()->json(['status' => '1', 'message' => 'Leads imported successfully'], 200);
        } else {
            return response()->json(['status' => '0', 'message' => 'Something went wrong'], 500);
        }
    }
    public function importProperty(Request $request)
    {

        return abort(404);
        if (Excel::import(new ImportProperty, $request->file('file'))) {
            return response()->json(['status' => '1', 'message' => 'Property imported successfully'], 200);
        } else {
            return response()->json(['status' => '0', 'message' => 'Something went wrong'], 500);
        }
    }
    public function importBayutAgentInsight(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if(Excel::import(new BayutAgentInsightImport, $request->file('file'))){
            return response()->json(['status' => '1', 'msg' => 'Property imported successfully'], 200);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Something went wrong'], 500);
        }

    }
}

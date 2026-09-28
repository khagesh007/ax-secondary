<?php

namespace App\Http\Controllers\Api\V1\Source;


use App\Http\Controllers\Api\BaseController;
use App\Models\LeadSource;

class SourceController extends BaseController
{
    public function getLeadSourceList()
    {
        $data = LeadSource::where('status', '1')->get();
        $result = array('status' => '1', 'msg' => "Lead Source fetched successfully.", 'data' => $data);
        return response()->json($result);
    }
}